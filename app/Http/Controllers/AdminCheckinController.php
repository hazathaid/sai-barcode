<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\CheckIn;
use Illuminate\Http\JsonResponse;

class AdminCheckinController
{
    /**
     * Handle check-in via qr_token for an event.
     */
    public function checkin(Request $request, Event $event): JsonResponse
    {
        $data = $request->validate([
            'qr_token' => ['required', 'string'],
            'device_info' => ['nullable', 'string'],
        ]);

        $token = trim($data['qr_token']);

        $ticket = Ticket::where('event_id', $event->id)->where('qr_token', $token)->first();

        if (! $ticket) {
            return response()->json(['status' => 'INVALID', 'message' => 'QR tidak valid'], 404);
        }

        if ($ticket->checked_in_at) {
            return response()->json([
                'status' => 'ALREADY',
                'message' => 'Sudah check-in pada ' . $ticket->checked_in_at->format('j M Y H:i'),
                'ticket' => ['name' => $ticket->name, 'email' => $ticket->email],
            ]);
        }

        // Attempt atomic update to avoid race conditions
        $now = now();
        $updated = Ticket::where('id', $ticket->id)->whereNull('checked_in_at')->update(['checked_in_at' => $now]);

        if (! $updated) {
            // someone else likely checked-in at the same time
            $ticket->refresh();
            return response()->json([
                'status' => 'ALREADY',
                'message' => 'Sudah check-in pada ' . ($ticket->checked_in_at ? $ticket->checked_in_at->format('j M Y H:i') : '—'),
                'ticket' => ['name' => $ticket->name, 'email' => $ticket->email],
            ]);
        }

        // create check_in record
        $checkIn = CheckIn::create([
            'event_id' => $event->id,
            'ticket_id' => $ticket->id,
            'admin_user_id' => null,
            'checked_in_at' => $now,
            'ip_address' => $request->ip(),
            'device_info' => $data['device_info'] ?? null,
        ]);

        return response()->json([
            'status' => 'OK',
            'message' => 'Check-in sukses: ' . $ticket->name,
            'ticket' => ['name' => $ticket->name, 'email' => $ticket->email],
        ]);
    }
}
