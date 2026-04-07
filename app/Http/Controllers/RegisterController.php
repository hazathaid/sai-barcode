<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;

class RegisterController
{
    /**
     * Store a registration (ticket) for an event.
     */
    public function store(Request $request, Event $event)
    {
        // Log raw request payload for debugging client submissions
        Log::info('Registration payload', $request->all());

        // Remove any empty child entries (e.g. from added-but-empty JS groups)
        $registrantType = $request->input('registrant_type', 'parent');
        $rawChildren = $request->input('children', []);
        if ($registrantType === 'parent' && is_array($rawChildren)) {
            $filtered = array_values(array_filter($rawChildren, function ($c) {
                return isset($c['name']) && trim($c['name']) !== '';
            }));
            $request->merge(['children' => $filtered]);
        }

        $data = $request->validate([
            'registrant_type' => ['required', 'in:parent,fasil,external'],
            'parent_title' => ['nullable', 'in:Ayah,Bunda'],
            'parent_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'required_without:phone', 'max:255'],
            'phone' => ['nullable', 'string', 'required_without:email', 'max:50'],
            'children' => ['exclude_unless:registrant_type,parent', 'required', 'array', 'min:1'],
            'children.*.name' => ['exclude_unless:registrant_type,parent', 'required', 'string', 'max:255'],
            'children.*.class_room' => ['nullable', 'string', 'max:255'],
            'bukti_bayar' => ['required_if:registrant_type,external', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // If the event is configured to accept external registrants only,
        // reject other registrant types early and show a friendly message.
        if (!empty($event->external_only)) {
            $submittedType = $request->input('registrant_type', 'parent');
            if ($submittedType !== 'external') {
                return back()
                    ->withInput()
                    ->withErrors(['registrant_type' => 'Pendaftaran untuk event ini hanya menerima peserta eksternal.']);
            }
        }
        if ($event->status !== 'published') {
            abort(403, 'Event is not open for registration.');
        }

        // --- NEW: reject duplicates per event (email/phone) ---
        $email = isset($data['email']) ? trim((string) $data['email']) : '';
        $phone = isset($data['phone']) ? trim((string) $data['phone']) : '';

        if ($email !== '') {
            $existsByEmail = Ticket::where('event_id', $event->id)
                ->where('email', $email)
                ->exists();

            if ($existsByEmail) {
                return back()
                    ->withInput()
                    ->withErrors(['email' => 'Email sudah terdaftar untuk event ini.']);
            }
        }

        if ($phone !== '') {
            $existsByPhone = Ticket::where('event_id', $event->id)
                ->where('phone', $phone)
                ->exists();

            if ($existsByPhone) {
                return back()
                    ->withInput()
                    ->withErrors(['phone' => 'Nomor telepon sudah terdaftar untuk event ini.']);
            }
        }
        // --- END NEW ---

        // Create a single ticket for the parent containing children as JSON and parent title
        $children = [];
        if (($data['registrant_type'] ?? 'parent') === 'parent') {
            $children = array_map(function ($c) {
                $class = isset($c['class_room']) ? trim((string) $c['class_room']) : null;
                if ($class === '') {
                    $class = null;
                }

                return [
                    'name' => $c['name'] ?? null,
                    'class_room' => $class,
                ];
            }, $data['children']);
        }

        // also store legacy `kelas` column as the first child's class, if present
        $legacyKelas = null;
        foreach ($children as $c) {
            if (! empty($c['class_room'])) {
                $legacyKelas = $c['class_room'];
                break;
            }
        }
        try {
            // Handle bukti bayar (proof of payment) upload for external registrant type
            $buktiBayarPath = null;
            if ($data['registrant_type'] === 'external' && $request->hasFile('bukti_bayar')) {
                $buktiBayarPath = $request->file('bukti_bayar')->store('bukti_bayar', 'public');
            }
            $ticket = Ticket::create([
                'event_id' => $event->id,
                'name' => $data['parent_name'],
                'parent_name' => $data['parent_name'],
                'parent_title' => ($data['registrant_type'] ?? 'parent') === 'fasil' ? null : ($data['parent_title'] ?? null),
                'registrant_type' => $data['registrant_type'] ?? 'parent',
                'bukti_bayar' => $buktiBayarPath,
                'email' => $email !== '' ? $email : null,
                'phone' => $phone !== '' ? $phone : null,
                'children' => $children,
                'kelas' => $legacyKelas,
                'qr_token' => bin2hex(random_bytes(32)),
            ]);
        } catch (QueryException $e) {
            // Prevent raw 500s for unexpected DB issues
            Log::error('Ticket create failed', [
                'event_id' => $event->id,
                'email' => $email,
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            $message = 'Pendaftaran gagal. Silakan coba lagi atau gunakan email/telepon lain.';
            $errors = [];

            if ($email !== '') {
                $errors['email'] = $message;
            }

            if ($phone !== '') {
                $errors['phone'] = $message;
            }

            if (empty($errors)) {
                // Fall back to a form-level error if no identifier was provided
                $errors['registration'] = $message;
            }

            return back()
                ->withInput()
                ->withErrors($errors);
        }
        // TODO: dispatch email job to send ticket/QR to attendee

        return redirect()->route('tickets.show', ['token' => $ticket->qr_token]);
    }
}
