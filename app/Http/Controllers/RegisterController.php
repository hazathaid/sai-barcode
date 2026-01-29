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
            'email' => ['nullable', 'email', 'required_without:phone', 'max:255'],
            'phone' => ['nullable', 'string', 'required_without:email', 'max:50'],
        ]);

        if ($event->status !== 'published') {
            abort(403, 'Event is not open for registration.');
        }

        // Always create a new ticket record so each registration has its own ticket id
        $ticket = Ticket::create([
            'event_id' => $event->id,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'kelas' => $data['class_room'] ?? null,
            'qr_token' => bin2hex(random_bytes(32)),
        ]);

        // TODO: dispatch email job to send ticket/QR to attendee

        return redirect()->route('tickets.show', ['token' => $ticket->qr_token]);
    }
}
