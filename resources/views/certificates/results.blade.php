@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <h2 class="text-xl font-semibold mb-4">Hasil pencarian: "{{ $q }}"</h2>

    @if($tickets->isEmpty())
        <div class="p-4 bg-yellow-50 rounded">Tidak ditemukan tiket untuk query tersebut.</div>
    @else
        <div class="space-y-3">
            @foreach($tickets as $t)
                <div class="p-3 border rounded flex items-center justify-between">
                    <div>
                        <div class="font-medium">{{ $t->name }}</div>
                        <div class="text-sm text-gray-600">{{ $t->email }} • {{ $t->phone }} • {{ $t->event->name }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('certificate.download', $t) }}" class="px-3 py-2 bg-green-600 text-white rounded">Download</a>
                        <div class="text-sm text-gray-600">Downloaded: {{ $t->certificate_downloads ?? 0 }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
