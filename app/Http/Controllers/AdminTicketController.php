<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
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
                  // also search inside children JSON for child name or class
                  ->orWhere('children', 'like', "%{$search}%");
            });
        }

        if ($classId = $request->query('class_id')) {
            $classRoom = ClassRoom::find($classId);
            if ($classRoom) {
                $escapedName = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $classRoom->name);
                $query->where('children', 'like', '%"class_room":"' . $escapedName . '"%');
            }
        }

        $perPage = (int) $request->query('per_page', 25);
        $perPage = in_array($perPage, [10,25,50,100]) ? $perPage : 25;

        $tickets = $query->paginate($perPage)->withQueryString();

        $classRooms = ClassRoom::orderBy('name')->get();

        return view('admin.tickets.index', compact('event', 'tickets', 'classRooms'));
    }
}
