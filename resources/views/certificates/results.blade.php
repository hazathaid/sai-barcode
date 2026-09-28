@extends('layouts.app')

@section('title', 'Hasil Pencarian Sertifikat')

@section('content')
@php
    $backUrl = isset($event) && $event ? route('certificate.event', $event->slug) : route('certificate.index');
@endphp

<div class="space-y-5">
    <div class="overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-gray-200">
        <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-fuchsia-500 px-8 py-8 text-white">
            <a href="{{ $backUrl }}" class="inline-flex items-center gap-1 text-xs text-white/80 hover:text-white">
                &larr; Cari lagi
            </a>
            <h1 class="mt-3 text-2xl font-bold tracking-tight">Hasil Pencarian</h1>
            <p class="mt-1 text-sm text-white/80">
                Kata kunci: <span class="font-semibold text-white">"{{ $q }}"</span>
                @if(isset($event) && $event) &middot; {{ $event->name }} @endif
            </p>
        </div>
    </div>

    @if($tickets->isEmpty())
        <div class="rounded-3xl border border-rose-200 bg-white p-8 text-center shadow-sm">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-rose-50 text-3xl">😕</div>
            <h3 class="mt-4 text-lg font-semibold text-rose-700">Data tidak ditemukan</h3>
            <p class="mx-auto mt-2 max-w-md text-sm text-gray-600">
                Nomor Anda belum terdaftar sebagai penerima E-Sertifikat. Pastikan nomor HP/email sesuai dengan data saat pendaftaran, atau hubungi panitia acara.
            </p>
            <a href="{{ $backUrl }}"
                class="mt-5 inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                Coba lagi
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach($tickets as $t)
                <div class="flex flex-col gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:shadow-md sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 flex-none items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-lg font-bold uppercase text-white">
                            {{ mb_substr($t->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ $t->name }}</div>
                            <div class="mt-0.5 text-sm text-gray-500">{{ $t->email ?? '—' }} &middot; {{ $t->phone ?? '—' }}</div>
                            <div class="mt-1 text-xs text-indigo-600">{{ $t->event->name ?? '' }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 sm:flex-col sm:items-end">
                        <a href="{{ route('certificate.download', $t) }}"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700 sm:flex-none">
                            <span>⬇️</span>
                            <span>Download</span>
                        </a>
                        <span class="text-xs text-gray-400">Diunduh {{ $t->certificate_downloads ?? 0 }}x</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
