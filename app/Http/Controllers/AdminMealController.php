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

        $ticket = $this->findTicketFromScanPayload($event, $token, ['attendance']);

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

    /**
     * Resolve ticket from scanner payload. Supports:
     * - raw qr_token
     * - /t/{token} URL
     * - /barcode/{id} URL
     * - legacy payload id|code
     */
    private function findTicketFromScanPayload(Event $event, string $rawPayload, array $relations = []): ?Ticket
    {
        $payload = trim($rawPayload);
        $tokenCandidates = [$payload];
        $idCandidate = ctype_digit($payload) ? (int) $payload : null;

        if (preg_match('~(?:https?://[^/]+)?/t/([^/?#]+)~i', $payload, $match)) {
            $tokenCandidates[] = trim($match[1]);
        }

        if (preg_match('~(?:https?://[^/]+)?/barcode/(\d+)~i', $payload, $match)) {
            $idCandidate = (int) $match[1];
        }

        if (str_contains($payload, '|')) {
            [$left] = explode('|', $payload, 2);
            $left = trim($left);
            if (ctype_digit($left)) {
                $idCandidate = (int) $left;
            }
        }

        $tokenCandidates = array_values(array_unique(array_filter($tokenCandidates, fn ($value) => $value !== '')));

        $baseQuery = Ticket::with($relations)->where('event_id', $event->id);

        if (! empty($tokenCandidates)) {
            $ticket = (clone $baseQuery)->whereIn('qr_token', $tokenCandidates)->first();
            if ($ticket) {
                return $ticket;
            }
        }

        if ($idCandidate !== null) {
            return (clone $baseQuery)->where('id', $idCandidate)->first();
        }

        return null;
    }
}
