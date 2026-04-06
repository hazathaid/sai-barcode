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
        if ($event->status !== 'published') {
            return response()->view('events.closed', compact('event'));
        }

        $classrooms = \App\Models\ClassRoom::all();

        return view('events.show', compact('event', 'classrooms'));
    }
}
