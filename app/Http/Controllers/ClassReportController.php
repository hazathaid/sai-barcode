<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Ticket;

class ClassReportController
{
    public function index(Request $request)
    {
        $eventId = $request->query('event_id');

        $events = Event::orderByDesc('starts_at')->get();

        $base = Ticket::query()->whereNotNull('checked_in_at')
            ->orWhereHas('attendance');

        if ($eventId) {
            $base->where('event_id', $eventId);
        }

        $tickets = $base->get(['children']);

        $counts = [];
        $totalParticipants = 0;
        $unspecifiedNames = [];

        foreach ($tickets as $ticket) {
            $children = $ticket->children ?? [];
            if (! is_array($children)) {
                continue;
            }
            foreach ($children as $child) {
                $raw = trim((string) ($child['class_room'] ?? ''));
                $name = trim((string) ($child['name'] ?? '—'));
                if ($raw === '') {
                    $class = 'Unspecified';
                    $counts[$class] = ($counts[$class] ?? 0) + 1;
                    $unspecifiedNames[] = $name;
                } else {
                    $class = $raw;
                    $counts[$class] = ($counts[$class] ?? 0) + 1;
                }
                $totalParticipants++;
            }
        }

        // sort descending
        arsort($counts);

        return view('admin.reports.classes', compact('events', 'counts', 'totalParticipants', 'eventId', 'unspecifiedNames'));
    }
}
