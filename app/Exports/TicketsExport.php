<?php

namespace App\Exports;

use App\Models\Ticket;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TicketsExport implements FromCollection, WithHeadings
{
    protected $eventId;

    protected ?Collection $tickets = null;

    protected ?int $maxChildrenCount = null;

    public function __construct($eventId = null)
    {
        $this->eventId = $eventId;
    }

    public function headings(): array
    {
        $headings = [
            'no',
            'Nama Event',
            'Type',
            'Tipe Orang Tua',
            'Nama Orang Tua',
            'Email',
            'Telepon',
            'Bukti Bayar',
            'Sudah Check-in atau Belum',
            'Waktu Check-in',
            'Ambil Makan',
            'Waktu Ambil Makan',
        ];

        $childHeadings = [];
        for ($index = 1; $index <= $this->getMaxChildrenCount(); $index++) {
            $childHeadings[] = 'Anak ' . $index;
        }

        array_splice($headings, 7, 0, $childHeadings);

        return $headings;
    }

    public function collection()
    {
        $tickets = $this->getTickets();

        $rows = $tickets->map(function (Ticket $ticket, $index) {
            $checked = $ticket->checked_in_at || ($ticket->attendance?->checked_in_at ?? null);
            $checkedLabel = $checked ? 'Sudah' : 'Belum';
            $checkedAt = $ticket->checked_in_at ? $ticket->checked_in_at->format('d-m-y H:i:s') : ($ticket->attendance?->checked_in_at?->format('d-m-y H:i:s') ?? null);

            $children = collect(is_array($ticket->children ?? null) ? $ticket->children : [])->values();
            $childColumns = [];
            for ($childIndex = 0; $childIndex < $this->getMaxChildrenCount(); $childIndex++) {
                $child = $children->get($childIndex, []);
                $childName = trim((string) ($child['name'] ?? ''));
                $childClass = trim((string) ($child['class_room'] ?? ''));

                if ($childName !== '' && $childClass !== '') {
                    $childColumns[] = $childName . ' (' . $childClass . ')';
                } elseif ($childName !== '') {
                    $childColumns[] = $childName;
                } elseif ($childClass !== '') {
                    $childColumns[] = '(' . $childClass . ')';
                } else {
                    $childColumns[] = null;
                }
            }

            // Determine registrant type label
            $typeLabel = 'Orang Tua';
            if (($ticket->registrant_type ?? 'parent') === 'fasil') {
                $typeLabel = 'Fasil';
            } elseif (($ticket->registrant_type ?? 'parent') === 'external') {
                $typeLabel = 'External';
            }

            $row = [
                $index + 1,
                $ticket->event?->name ?? null,
                $typeLabel,
                $ticket->parent_title,
                $ticket->parent_name,
                $ticket->email,
                $ticket->phone,
                $ticket->bukti_bayar ? asset('storage/' . $ticket->bukti_bayar) : null,
                $checkedLabel,
                $checkedAt,
                ($ticket->meal_taken ? 'Sudah' : 'Belum'),
                $ticket->meal_taken_at ? $ticket->meal_taken_at->format('d-m-y H:i:s') : null,
            ];

            array_splice($row, 7, 0, $childColumns);

            return $row;
        });

        return $rows->values();
    }

    protected function getTickets(): Collection
    {
        if ($this->tickets !== null) {
            return $this->tickets;
        }

        $query = Ticket::with('event', 'attendance')->orderBy('id');

        if ($this->eventId) {
            $query->where('event_id', $this->eventId);
        }

        return $this->tickets = $query->get();
    }

    protected function getMaxChildrenCount(): int
    {
        if ($this->maxChildrenCount !== null) {
            return $this->maxChildrenCount;
        }

        $maxChildrenCount = $this->getTickets()
            ->map(function (Ticket $ticket) {
                return is_array($ticket->children ?? null) ? count($ticket->children) : 0;
            })
            ->max() ?? 0;

        return $this->maxChildrenCount = max(1, $maxChildrenCount);
    }
}
