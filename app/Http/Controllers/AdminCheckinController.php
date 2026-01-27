<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
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

        $ticket = Ticket::with(['event', 'attendance'])->where('event_id', $event->id)->where('qr_token', $token)->first();

        if (! $ticket) {
            return response()->json(['status' => 'INVALID', 'message' => 'QR tidak valid'], 404);
        }

        if ($ticket->attendance) {
            return response()->json([
                'status' => 'ALREADY',
                'message' => 'Sudah check-in pada ' . $ticket->attendance->checked_in_at->format('j M Y H:i'),
                'ticket' => ['name' => $ticket->name, 'email' => $ticket->email],
                'attendance' => ['checked_in_at' => $ticket->attendance->checked_in_at->toDateTimeString(), 'checked_in_by' => $ticket->attendance->checked_in_by],
            ]);
        }

        $now = now();
        $adminId = auth()->id();

        try {
            DB::beginTransaction();

            // create attendance (unique ticket_id enforced at DB level)
            $attendance = Attendance::create([
                'ticket_id' => $ticket->id,
                'checked_in_by' => $adminId,
                'checked_in_at' => $now,
            ]);

            // update ticket checked_in_at for quick lookups
            Ticket::where('id', $ticket->id)->update(['checked_in_at' => $now]);

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            // Unique violation or race — treat as already checked in
            $ticket->refresh();
            if ($ticket->attendance) {
                return response()->json([
                    'status' => 'ALREADY',
                    'message' => 'Sudah check-in pada ' . $ticket->attendance->checked_in_at->format('j M Y H:i'),
                    'ticket' => ['name' => $ticket->name, 'email' => $ticket->email],
                ]);
            }

            return response()->json(['status' => 'ERROR', 'message' => 'Gagal melakukan check-in'], 500);
        }

        return response()->json([
            'status' => 'OK',
            'message' => 'Check-in sukses: ' . $ticket->name,
            'ticket' => ['name' => $ticket->name, 'email' => $ticket->email],
            'attendance' => ['checked_in_at' => $now->toDateTimeString(), 'checked_in_by' => $adminId],
        ]);
    }
}
