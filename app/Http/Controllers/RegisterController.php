<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegisterController
{
    /**
     * Store a registration (ticket) for an event.
     */
    public function store(Request $request, Event $event)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        if ($event->status !== 'published') {
            abort(403, 'Event is not open for registration.');
        }

        $ticket = Ticket::firstOrCreate(
            ['event_id' => $event->id, 'email' => $data['email']],
            [
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'kelas' => $data['class_room'] ?? null,
                'qr_token' => bin2hex(random_bytes(32)),
            ]
        );

        // TODO: dispatch email job to send ticket/QR to attendee

        return redirect()->route('tickets.show', ['token' => $ticket->qr_token]);
    }
}
