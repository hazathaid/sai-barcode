<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'external_only' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            $data['image'] = $path;
        }

        // ensure external_only is set (checkbox may be absent when unchecked)
        $data['external_only'] = $request->has('external_only');

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
            'external_only' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($request->hasFile('image')) {
            // delete old image if exists
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $path = $request->file('image')->store('events', 'public');
            $data['image'] = $path;
        }

        // ensure external_only is set (checkbox may be absent when unchecked)
        $data['external_only'] = $request->has('external_only');

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success','Event updated');
    }

    public function destroy(Event $event)
    {
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }
        $event->delete();
        return redirect()->route('admin.events.index')->with('success','Event deleted');
    }
}
