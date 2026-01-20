<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController
{
    /**
     * Show event details and registration form.
     */
    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }
}
