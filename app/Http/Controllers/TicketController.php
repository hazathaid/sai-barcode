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
        $ticket = Ticket::with('event')->where('qr_token', $token)->firstOrFail();

        return view('tickets.show', compact('ticket'));
    }
}
