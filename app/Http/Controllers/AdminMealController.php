<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class AdminMealController
{
    /**
     * Handle meal pickup (ambil makan) via qr_token for an event.
     */
    public function takeMeal(Request $request, Event $event): JsonResponse
    {
        $data = $request->validate([
            'qr_token' => ['required', 'string'],
            'device_info' => ['nullable', 'string'],
        ]);

        $token = trim($data['qr_token']);

        $ticket = Ticket::with('attendance')->where('event_id', $event->id)->where('qr_token', $token)->first();

        if (! $ticket) {
            return response()->json(['status' => 'INVALID', 'message' => 'QR tidak valid'], 404);
        }

        // only allow meal pickup for tickets that have checked in
        if (! $ticket->attendance && ! $ticket->checked_in_at) {
            return response()->json([
                'status' => 'DENIED',
                'message' => 'Peserta belum check-in, tidak dapat mengambil makan'
            ], 403);
        }

        if ($ticket->meal_taken) {
            return response()->json([
                'status' => 'ALREADY',
                'message' => 'Sudah mengambil makan pada ' . ($ticket->meal_taken_at ? $ticket->meal_taken_at->format('j M Y H:i') : 'waktu tidak diketahui'),
                'ticket' => ['name' => $ticket->name, 'email' => $ticket->email],
            ]);
        }

        $now = now();

        try {
            DB::beginTransaction();

            // Mark ticket as meal taken
            $ticket->meal_taken = true;
            $ticket->meal_taken_at = $now;
            $ticket->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $ticket->refresh();
            if ($ticket->meal_taken) {
                return response()->json([
                    'status' => 'ALREADY',
                    'message' => 'Sudah mengambil makan pada ' . ($ticket->meal_taken_at ? $ticket->meal_taken_at->format('j M Y H:i') : 'waktu tidak diketahui'),
                    'ticket' => ['name' => $ticket->name, 'email' => $ticket->email],
                ]);
            }
            return response()->json(['status' => 'ERROR', 'message' => 'Gagal mencatat pengambilan makan'], 500);
        }

        return response()->json([
            'status' => 'OK',
            'message' => 'Pengambilan makan sukses: ' . $ticket->name,
            'ticket' => ['name' => $ticket->name, 'email' => $ticket->email],
            'meal' => ['meal_taken_at' => $now->toDateTimeString()],
        ]);
    }
}
