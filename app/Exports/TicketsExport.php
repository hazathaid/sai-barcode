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
            'Nama Event',
            'Type',
            'Tipe Orang Tua',
            'Nama Orang Tua',
            'Email',
            'Telepon',
            'Nama Anak',
            'Kelas',
            'Bukti Bayar',
            'Sudah Check-in atau Belum',
            'Waktu Check-in',
            'Ambil Makan',
            'Waktu Ambil Makan',
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

            $childrenNames = '';
            $childrenClasses = '';
            if (is_array($ticket->children ?? null)) {
                $childrenCollection = collect($ticket->children);

                $childrenNames = $childrenCollection
                    ->map(fn ($child) => trim((string) ($child['name'] ?? '')))
                    ->filter()
                    ->join('; ');

                $childrenClasses = $childrenCollection
                    ->map(fn ($child) => trim((string) ($child['class_room'] ?? '')))
                    ->filter()
                    ->join('; ');
            }

            // Determine registrant type label
            $typeLabel = 'Orang Tua';
            if (($ticket->registrant_type ?? 'parent') === 'fasil') {
                $typeLabel = 'Fasil';
            } elseif (($ticket->registrant_type ?? 'parent') === 'external') {
                $typeLabel = 'External';
            }

            return [
                $index + 1,
                $ticket->event?->name ?? null,
                $typeLabel,
                $ticket->parent_title,
                $ticket->parent_name,
                $ticket->email,
                $ticket->phone,
                $childrenNames,
                $childrenClasses,
                $ticket->bukti_bayar ? asset('storage/' . $ticket->bukti_bayar) : null,
                $checkedLabel,
                $checkedAt,
                ($ticket->meal_taken ? 'Sudah' : 'Belum'),
                $ticket->meal_taken_at ? $ticket->meal_taken_at->format('d-m-y H:i:s') : null,
            ];
        });

        return $rows->values();
    }
}
