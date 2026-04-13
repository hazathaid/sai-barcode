@extends('layouts.admin')

@section('page-title','Class check-ins')

@section('content')
<div class="max-w-5xl mx-auto">
    <h1 class="text-2xl font-semibold mb-4">Laporan: Jumlah peserta (anak) yang sudah check-in per kelas</h1>

    <form method="GET" class="mb-4">
        <div class="flex items-center gap-3">
            <label class="text-sm text-gray-600">Event</label>
            <select name="event_id" class="px-3 py-2 border rounded">
                <option value="">Semua event</option>
                @foreach($events as $ev)
                    <option value="{{ $ev->id }}" {{ (string)($eventId ?? '') === (string)$ev->id ? 'selected' : '' }}>{{ $ev->name }} — {{ $ev->starts_at->format('j M Y') }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded">Filter</button>
        </div>
    </form>

    <x-admin.card>
        <div class="mb-3 text-sm text-gray-600">Total peserta (anak) ter-check-in: <strong>{{ $totalParticipants ?? 0 }}</strong></div>

        <table class="w-full table-auto">
            <thead>
                <tr class="text-left text-sm text-gray-600">
                    <th class="py-2">Kelas</th>
                    <th class="py-2">Jumlah ter-check-in</th>
                </tr>
            </thead>
            <tbody>
                @forelse($counts as $class => $count)
                    <tr class="border-t">
                        <td class="py-2">{{ $class }}</td>
                        <td class="py-2 font-medium">{{ $count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="py-4" colspan="2">Belum ada data check-in untuk kelas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-admin.card>

    @if(!empty($unspecifiedNames) && count($unspecifiedNames))
        <div class="mt-4">
            <x-admin.card>
                <h4 class="text-lg font-medium">Daftar anak tanpa kelas (Unspecified)</h4>
                <details class="mt-2">
                    <summary class="text-sm text-indigo-600 cursor-pointer">Tampilkan nama anak ({{ count($unspecifiedNames) }})</summary>
                    <ul class="list-disc pl-6 mt-3 text-sm">
                        @foreach($unspecifiedNames as $childName)
                            <li>{{ $childName }}</li>
                        @endforeach
                    </ul>
                </details>
            </x-admin.card>
        </div>
    @endif
</div>
@endsection
