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

        $ticket = $this->findTicketFromScanPayload($event, $token, ['event', 'attendance']);

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
