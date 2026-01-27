<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class AdminEventController
{
    public function index()
    {
        $events = Event::orderByDesc('starts_at')->paginate(20);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:events,slug',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published,closed',
        ]);

        Event::create($data);

        return redirect()->route('admin.events.index')->with('success','Event created');
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:events,slug,' . $event->id,
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published,closed',
        ]);

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success','Event updated');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success','Event deleted');
    }
}
