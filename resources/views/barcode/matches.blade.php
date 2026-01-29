@extends('layouts.app')

@section('title','Beberapa tiket ditemukan')

@section('content')
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm max-w-3xl mx-auto">
        <h1 class="text-2xl font-semibold text-center">Ditemukan beberapa tiket</h1>
        <p class="text-gray-600 text-center">Pilih salah satu untuk melihat barcode</p>

        <ul class="mt-4 space-y-4">
            @foreach($tickets as $t)
                <li class="flex items-center gap-4 bg-white border border-gray-100 rounded-lg p-4">
                    <img src="{{ url('/barcode/'.$t->id) }}" alt="barcode" class="h-28 w-28 object-contain bg-white p-2 rounded">
                    <div class="flex-1">
                        <div class="font-semibold">{{ $t->name ?? '—' }}</div>
                        <div class="text-sm text-gray-600">Event: {{ $t->event->name ?? ($t->event_id ?? '—') }} · Kode: {{ $t->code ?? $t->id }}</div>
                        <div class="mt-2"><a href="{{ route('barcode.show', ['ticket' => $t->id]) }}" class="text-indigo-600 hover:underline">Lihat detail</a></div>
                    </div>
                </li>
            @endforeach
        </ul>

        <p class="mt-6 text-center"><a href="{{ route('barcode.index') }}" class="text-indigo-600 hover:underline">Cari lagi</a></p>
    </div>
@endsection
