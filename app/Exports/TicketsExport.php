<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TicketsExport implements FromCollection, WithHeadings
{
    protected $eventId;

    public function __construct($eventId = null)
    {
        $this->eventId = $eventId;
    }

    public function headings(): array
    {
        return [
            'no',
            'event name',
            'name',
            'email',
            'phone',
            'sudah chekin atau belum',
            'waktu checkin',
        ];
    }

    public function collection()
    {
        $query = Ticket::with('event', 'attendance')->orderBy('id');

        if ($this->eventId) {
            $query->where('event_id', $this->eventId);
        }

        $tickets = $query->get();

        $rows = $tickets->map(function (Ticket $ticket, $index) {
            $checked = $ticket->checked_in_at || ($ticket->attendance?->checked_in_at ?? null);
            $checkedLabel = $checked ? 'Sudah' : 'Belum';
            $checkedAt = $ticket->checked_in_at ? $ticket->checked_in_at->format('d-m-y H:i:s') : ($ticket->attendance?->checked_in_at?->format('d-m-y H:i:s') ?? null);

            return [
                $index + 1,
                $ticket->event?->name ?? null,
                $ticket->name,
                $ticket->email,
                $ticket->phone,
                $checkedLabel,
                $checkedAt,
            ];
        });

        return $rows->values();
    }
}
