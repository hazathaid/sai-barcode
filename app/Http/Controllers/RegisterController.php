<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
            'registrant_type' => ['required', 'in:parent,fasil'],
            'parent_title' => ['nullable', 'in:Ayah,Bunda'],
            'parent_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'required_without:phone', 'max:255'],
            'phone' => ['nullable', 'string', 'required_without:email', 'max:50'],
            'children' => ['exclude_unless:registrant_type,parent', 'required', 'array', 'min:1'],
            'children.*.name' => ['exclude_unless:registrant_type,parent', 'required', 'string', 'max:255'],
            'children.*.class_room' => ['nullable', 'string', 'max:255'],
        ]);

        if ($event->status !== 'published') {
            abort(403, 'Event is not open for registration.');
        }

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

        $ticket = Ticket::create([
            'event_id' => $event->id,
            'name' => $data['parent_name'],
            'parent_name' => $data['parent_name'],
            'parent_title' => ($data['registrant_type'] ?? 'parent') === 'fasil' ? null : ($data['parent_title'] ?? null),
            'registrant_type' => $data['registrant_type'] ?? 'parent',
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'children' => $children,
            'kelas' => $legacyKelas,
            'qr_token' => bin2hex(random_bytes(32)),
        ]);

        // TODO: dispatch email job to send ticket/QR to attendee

        return redirect()->route('tickets.show', ['token' => $ticket->qr_token]);
    }
}
