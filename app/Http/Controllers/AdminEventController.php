<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
            'certificate_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
            'certificate_font' => 'nullable|file|max:10240',
            'certificate_font_size' => 'nullable|integer|min:6|max:200',
            'certificate_text_x_pct' => 'nullable|numeric|min:0|max:100',
            'certificate_text_y_pct' => 'nullable|numeric|min:0|max:100',
            'certificate_font_color' => ['nullable','regex:/^#([A-Fa-f0-9]{6})$/'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            $data['image'] = $path;
        }

        if ($request->hasFile('certificate_image')) {
            $path = $request->file('certificate_image')->store('certificates/images', 'public');
            $data['certificate_image'] = $path;
        }

        if ($request->hasFile('certificate_font')) {
            $font = $request->file('certificate_font');
            $ext = strtolower($font->getClientOriginalExtension());
            $clientMime = $font->getClientMimeType();
            $mime = $font->getMimeType();

            Log::info('certificate_font upload (pre-store)', [
                'clientMime' => $clientMime,
                'mime' => $mime,
                'originalExtension' => $ext,
                'originalName' => $font->getClientOriginalName(),
                'size' => $font->getSize(),
                'isValid' => $font->isValid(),
            ]);

            $allowed = ['ttf','otf','woff','woff2','ttc'];
            if (! in_array($ext, $allowed)) {
                return redirect()->back()->withErrors(['certificate_font' => "Uploaded font extension '{$ext}' is not allowed. Detected mime: {$clientMime}"])->withInput();
            }

            if (config('app.debug')) {
                Log::debug('certificate_font debug', [
                    'clientMime' => $clientMime,
                    'mime' => $mime,
                    'ext' => $ext,
                    'originalName' => $font->getClientOriginalName(),
                    'size' => $font->getSize(),
                    'isValid' => $font->isValid(),
                ]);
            }

            $path = $font->store('certificates/fonts', 'public');
            $data['certificate_font'] = $path;
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
            'certificate_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
            'certificate_font' => 'nullable|file|max:10240',
            'certificate_font_size' => 'nullable|integer|min:6|max:200',
            'certificate_text_x_pct' => 'nullable|numeric|min:0|max:100',
            'certificate_text_y_pct' => 'nullable|numeric|min:0|max:100',
            'certificate_font_color' => ['nullable','regex:/^#([A-Fa-f0-9]{6})$/'],
        ]);

        if ($request->hasFile('image')) {
            // delete old image if exists
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $path = $request->file('image')->store('events', 'public');
            $data['image'] = $path;
        }

        if ($request->hasFile('certificate_image')) {
            if ($event->certificate_image) {
                Storage::disk('public')->delete($event->certificate_image);
            }
            $path = $request->file('certificate_image')->store('certificates/images', 'public');
            $data['certificate_image'] = $path;
        }

        if ($request->hasFile('certificate_font')) {
            $font = $request->file('certificate_font');
            $ext = strtolower($font->getClientOriginalExtension());
            $clientMime = $font->getClientMimeType();
            $mime = $font->getMimeType();

            Log::info('certificate_font upload (pre-store)', [
                'clientMime' => $clientMime,
                'mime' => $mime,
                'originalExtension' => $ext,
                'originalName' => $font->getClientOriginalName(),
                'size' => $font->getSize(),
                'isValid' => $font->isValid(),
            ]);

            $allowed = ['ttf','otf','woff','woff2','ttc'];
            if (! in_array($ext, $allowed)) {
                return redirect()->back()->withErrors(['certificate_font' => "Uploaded font extension '{$ext}' is not allowed. Detected mime: {$clientMime}"])->withInput();
            }

            if (config('app.debug')) {
                Log::debug('certificate_font debug', [
                    'clientMime' => $clientMime,
                    'mime' => $mime,
                    'ext' => $ext,
                    'originalName' => $font->getClientOriginalName(),
                    'size' => $font->getSize(),
                    'isValid' => $font->isValid(),
                ]);
            }

            if ($event->certificate_font) {
                Storage::disk('public')->delete($event->certificate_font);
            }
            $path = $font->store('certificates/fonts', 'public');
            $data['certificate_font'] = $path;
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
