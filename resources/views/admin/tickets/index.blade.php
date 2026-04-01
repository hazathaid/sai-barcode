@extends('layouts.admin')

@section('page-title','Tickets')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-start justify-between mb-4">
        <div>
            <h3 class="text-2xl font-bold">Tickets — {{ $event->name }}</h3>
            <p class="text-sm text-gray-500 mt-1">Daftar peserta dan status kehadiran acara</p>
        </div>

        <form method="GET" class="flex items-center gap-2">
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
                </svg>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search name or email" class="pl-9 pr-3 py-2 border rounded-lg" />
            </div>
            <select name="per_page" class="border rounded-lg px-2 py-2 text-sm">
                @foreach([10,25,50,100] as $n)
                    <option value="{{ $n }}" {{ request('per_page',25) == $n ? 'selected' : '' }}>{{ $n }} / page</option>
                @endforeach
            </select>
            <button class="px-3 py-2 bg-indigo-600 text-white rounded-lg">Search</button>
        </form>
    </div>

    <x-admin.card class="shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left table-auto min-w-max">
                <thead>
                    <tr class="text-sm text-gray-600">
                        <th class="p-3 sticky top-0 bg-white/95 backdrop-blur z-10 text-xs uppercase tracking-wide">#</th>
                        <th class="p-3 sticky top-0 bg-white/95 backdrop-blur z-10 text-xs uppercase tracking-wide">Type</th>
                        <th class="p-3 sticky top-0 bg-white/95 backdrop-blur z-10 text-xs uppercase tracking-wide">Bukti Bayar</th>
                        <th class="p-3 sticky top-0 bg-white/95 backdrop-blur z-10 text-xs uppercase tracking-wide">Ortu</th>
                        <th class="p-3 sticky top-0 bg-white/95 backdrop-blur z-10 text-xs uppercase tracking-wide">Kontak</th>
                        <th class="p-3 sticky top-0 bg-white/95 backdrop-blur z-10 text-xs uppercase tracking-wide">Anak (Kelas)</th>
                        <th class="p-3 sticky top-0 bg-white/95 backdrop-blur z-10 text-xs uppercase tracking-wide">Status</th>
                        <th class="p-3 sticky top-0 bg-white/95 backdrop-blur z-10 text-xs uppercase tracking-wide">Checked in at</th>
                        <th class="p-3 sticky top-0 bg-white/95 backdrop-blur z-10 text-xs uppercase tracking-wide">Ambil Makan</th>
                        <th class="p-3 sticky top-0 bg-white/95 backdrop-blur z-10 text-xs uppercase tracking-wide">Waktu Ambil Makan</th>
                        <th class="p-3 sticky top-0 bg-white/95 backdrop-blur z-10 text-xs uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($tickets as $ticket)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-3 align-top">{{ $loop->iteration + ($tickets->currentPage()-1)*$tickets->perPage() }}</td>
                            <td class="p-3 align-top">
                                @if(($ticket->registrant_type ?? 'parent') === 'fasil')
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-sky-100 text-sky-800">Fasil</span>
                                @elseif(($ticket->registrant_type ?? 'parent') === 'external')
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-orange-100 text-orange-800">External</span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-violet-100 text-violet-800">Orang Tua</span>
                                @endif
                            </td>
                            <td class="p-3 align-top text-sm">
                                @if(($ticket->registrant_type ?? 'parent') === 'external' && $ticket->bukti_bayar)
                                    <a href="{{ asset('storage/' . $ticket->bukti_bayar) }}" target="_blank" class="text-indigo-600 hover:underline">Lihat</a>
                                @else
                                    —
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
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-amber-100 text-amber-800">✅ Sudah</span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-rose-100 text-rose-800">✖️ Belum</span>
                                @endif
                            </td>
                            <td class="p-3 align-top">{{ $ticket->attendance ? $ticket->attendance->checked_in_at->format('j M Y H:i') : '—' }}</td>
                            <td class="p-3 align-top">
                                @if($ticket->meal_taken)
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-emerald-100 text-emerald-800">🍽️ Sudah</span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-rose-100 text-rose-800">⏳ Belum</span>
                                @endif
                            </td>
                            <td class="p-3 align-top">{{ $ticket->meal_taken_at ? $ticket->meal_taken_at->format('j M Y H:i') : '—' }}</td>
                            <td class="p-3 align-top">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('tickets.show', ['token' => $ticket->qr_token]) }}" class="text-sm text-indigo-600 hover:underline">Lihat</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Menampilkan
                <strong>{{ $tickets->firstItem() ?? 0 }}</strong>
                sampai
                <strong>{{ $tickets->lastItem() ?? 0 }}</strong>
                dari
                <strong>{{ $tickets->total() }}</strong>
            </div>
            <div>
                {{ $tickets->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </x-admin.card>
</div>
@endsection
