<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController
{
    /**
     * Display a ticket and its QR code.
     */
    public function show($token)
    {
        // Allow lookup by numeric ticket id or by qr_token
        if (is_numeric($token)) {
            $ticket = Ticket::with('event')->find($token);
        } else {
            $ticket = Ticket::with('event')->where('qr_token', $token)->first();
        }

        if (! $ticket) {
            return response()->view('tickets.notfound', ['token' => $token], 404);
        }

        return view('tickets.show', compact('ticket'));
    }
}
