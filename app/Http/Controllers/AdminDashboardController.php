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

        $recentRegistrations = Ticket::with('event')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('events', 'totalRegistrations', 'checkedIn', 'recentRegistrations'));
    }
}
