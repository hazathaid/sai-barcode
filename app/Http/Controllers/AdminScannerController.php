<?php

namespace App\Http\Controllers;

use App\Models\Event;

class AdminScannerController
{
    /**
     * Show camera-based scanner for an event.
     */
    public function show(Event $event)
    {
        return view('admin.scanner', compact('event'));
    }
}
