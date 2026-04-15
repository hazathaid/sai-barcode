@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <h2 class="text-xl font-semibold mb-4">Hasil pencarian: "{{ $q }}"</h2>

    @if($tickets->isEmpty())
        <div class="p-6 bg-white border border-red-200 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold text-red-700 mb-2">Nomor Anda belum terdaftar sebagai penerima E‑Sertifikat</h3>
            <p class="text-sm text-gray-700 mb-3">Mohon maaf — kami tidak menemukan data kehadiran peserta yang cocok dengan informasi yang Anda masukkan.</p>
            <p class="text-sm text-gray-600">Info lebih lanjut, harap menghubungi panitia acara. Terima kasih.</p>
        </div>
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
