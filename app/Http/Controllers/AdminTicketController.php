<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class AdminTicketController
{
    /**
     * Display a paginated list of tickets for an event.
     */
    public function index(Request $request, Event $event)
    {
        $query = $event->tickets()->with(['attendance.admin'])->orderByDesc('created_at');

        if ($search = $request->query('q')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('qr_token', 'like', "%{$search}%");
            });
        }

        $tickets = $query->paginate(25)->withQueryString();

        return view('admin.tickets.index', compact('event','tickets'));
    }
}
