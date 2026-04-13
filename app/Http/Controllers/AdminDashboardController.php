<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;

class AdminDashboardController
{
    public function index()
    {
        $events = Event::orderByDesc('starts_at')->get();

        $totalRegistrations = Ticket::count();

        $checkedIn = Ticket::whereNotNull('checked_in_at')
            ->orWhereHas('attendance')
            ->count();

        $mealTaken = Ticket::where('meal_taken', true)->count();

        $recentRegistrations = Ticket::with('event')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Breakdown by registrant type
        $byType = Ticket::selectRaw('registrant_type, count(*) as total')
            ->groupBy('registrant_type')
            ->get()
            ->keyBy('registrant_type');

        $parentCount = $byType->get('parent')?->total ?? 0;
        $facilCount = $byType->get('fasil')?->total ?? 0;
        $externalCount = $byType->get('external')?->total ?? 0;

        return view('admin.dashboard', compact('events', 'totalRegistrations', 'checkedIn', 'mealTaken', 'recentRegistrations', 'parentCount', 'facilCount', 'externalCount'));
    }
}
