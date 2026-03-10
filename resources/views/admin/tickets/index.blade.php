@extends('layouts.admin')

@section('page-title','Tickets')

@section('content')
<div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-semibold">Tickets — {{ $event->name }}</h3>
        <form method="GET" class="flex items-center gap-2">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search name or email" class="px-3 py-2 border rounded-lg" />
            <button class="px-3 py-2 bg-indigo-600 text-white rounded-lg">Search</button>
        </form>
    </div>

    <x-admin.card>
        <div class="overflow-x-auto">
            <table class="w-full text-left table-auto min-w-max">
                <thead>
                    <tr class="text-sm text-gray-600">
                        <th class="p-3">#</th>
                        <th class="p-3">Type</th>
                        <th class="p-3">Ortu</th>
                        <th class="p-3">Kontak</th>
                        <th class="p-3">Anak (Kelas)</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Checked in at</th>
                        <th class="p-3">Ambil Makan</th>
                        <th class="p-3">Waktu Ambil Makan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                        <tr class="border-t">
                            <td class="p-3 align-top">{{ $loop->iteration + ($tickets->currentPage()-1)*$tickets->perPage() }}</td>
                            <td class="p-3 align-top">
                                @if(($ticket->registrant_type ?? 'parent') === 'fasil')
                                    <span class="inline-block px-2 py-1 rounded-lg text-sm bg-sky-50 text-sky-800">Fasil</span>
                                @else
                                    <span class="inline-block px-2 py-1 rounded-lg text-sm bg-violet-50 text-violet-800">Orang Tua</span>
                                @endif
                            </td>
                            <td class="p-3 align-top">@if($ticket->parent_title){{ $ticket->parent_title }}. @endif {{ $ticket->parent_name ?? $ticket->name }}</td>
                            <td class="p-3 align-top">{{ $ticket->email }}<br>{{ $ticket->phone }}</td>
                            <td class="p-3 align-top">
                                @if(is_array($ticket->children))
                                    <ul class="text-sm list-disc list-inside">
                                        @foreach($ticket->children as $c)
                                            <li>{{ $c['name'] ?? '—' }} @if(!empty($c['class_room'])) ({{ $c['class_room'] }}) @endif</li>
                                        @endforeach
                                    </ul>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="p-3 align-top">
                                @if($ticket->attendance)
                                    <span class="inline-block px-2 py-1 rounded-lg text-sm bg-amber-50 text-amber-800">Sudah</span>
                                @else
                                    <span class="inline-block px-2 py-1 rounded-lg text-sm bg-emerald-50 text-emerald-800">Belum</span>
                                @endif
                            </td>
                            <td class="p-3 align-top">{{ $ticket->attendance ? $ticket->attendance->checked_in_at->format('j M Y H:i') : '—' }}</td>
                            <td class="p-3 align-top">
                                @if($ticket->meal_taken)
                                    <span class="inline-block px-2 py-1 rounded-lg text-sm bg-emerald-50 text-emerald-800">Sudah</span>
                                @else
                                    <span class="inline-block px-2 py-1 rounded-lg text-sm bg-rose-50 text-rose-800">Belum</span>
                                @endif
                            </td>
                            <td class="p-3 align-top">{{ $ticket->meal_taken_at ? $ticket->meal_taken_at->format('j M Y H:i') : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    </x-admin.card>
</div>
@endsection
