<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;

class AdminDashboardController
{
    public function index()
    {
        // Only events created by this admin (or not yet claimed by anyone).
        $events = Event::where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->orWhereNull('user_id');
            })
            ->orderByDesc('starts_at')
            ->get();

        $eventIds = $events->pluck('id');

        $tickets = fn () => Ticket::whereIn('event_id', $eventIds);

        $totalRegistrations = $tickets()->count();

        $checkedIn = $tickets()->where(function ($query) {
            $query->whereNotNull('checked_in_at')->orWhereHas('attendance');
        })->count();

        $mealTaken = $tickets()->where('meal_taken', true)->count();

        $recentRegistrations = $tickets()->with('event')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Breakdown by registrant type
        $byType = $tickets()->selectRaw('registrant_type, count(*) as total')
            ->groupBy('registrant_type')
            ->get()
            ->keyBy('registrant_type');

        $parentCount = $byType->get('parent')?->total ?? 0;
        $facilCount = $byType->get('fasil')?->total ?? 0;
        $externalCount = $byType->get('external')?->total ?? 0;

        return view('admin.dashboard', compact('events', 'totalRegistrations', 'checkedIn', 'mealTaken', 'recentRegistrations', 'parentCount', 'facilCount', 'externalCount'));
    }
}
