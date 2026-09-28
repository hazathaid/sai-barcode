@extends('layouts.app')

@section('title', 'E-Sertifikat - ' . $event->name)

@section('content')
@php
    $heroImage = null;
    if (!empty($event->image)) {
        $heroImage = filter_var($event->image, FILTER_VALIDATE_URL)
            ? $event->image
            : asset('storage/' . ltrim($event->image, '/'));
    }

    $templateImage = null;
    if (!empty($event->certificate_image)) {
        $templateImage = filter_var($event->certificate_image, FILTER_VALIDATE_URL)
            ? $event->certificate_image
            : asset('storage/' . ltrim($event->certificate_image, '/'));
    }
@endphp

<div class="overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-gray-200">
    {{-- Hero --}}
    <div class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-fuchsia-500 px-8 py-10 text-white">
        @if($heroImage)
            <img src="{{ $heroImage }}" alt="{{ $event->name }}" class="absolute inset-0 h-full w-full object-cover opacity-20">
        @endif
        <div class="relative">
            <a href="{{ route('certificate.index') }}" class="inline-flex items-center gap-1 text-xs text-white/80 hover:text-white">
                &larr; Semua Sertifikat
            </a>
            <div class="mt-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 text-3xl shadow-inner">🎓</div>
            <h1 class="mt-4 text-2xl font-bold tracking-tight sm:text-3xl">{{ $event->name }}</h1>
            <p class="mt-1 text-sm text-white/80">
                {{ $event->starts_at->format('j M Y') }}
                @if($event->location) &middot; {{ $event->location }} @endif
            </p>
        </div>
    </div>

    <div class="p-6 sm:p-8">
        @if($templateImage)
            <div class="mb-6">
                <p class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-500">Pratinjau sertifikat</p>
                <img src="{{ $templateImage }}" alt="Template sertifikat {{ $event->name }}"
                    class="w-full rounded-xl border border-gray-200 shadow-sm">
            </div>
        @else
            <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Template sertifikat untuk event ini belum diatur oleh panitia.
            </div>
        @endif

        <form method="POST" action="{{ route('certificate.search') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->id }}">

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">No HP / Email</label>
                <input name="query" value="{{ old('query') }}" required
                    placeholder="0812xxxx atau email@example.com"
                    class="block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200">
                @error('query')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:from-indigo-700 hover:to-purple-700">
                <span>🔎</span>
                <span>Cari &amp; Unduh Sertifikat</span>
            </button>
        </form>

        <div class="mt-6 flex items-start gap-3 rounded-xl bg-amber-50 px-4 py-3 text-xs text-amber-800 ring-1 ring-amber-100">
            <span class="text-base leading-none">ℹ️</span>
            <p>Gunakan nomor HP atau email yang terdaftar. Sertifikat hanya tersedia bagi peserta yang sudah check-in.</p>
        </div>
    </div>
</div>
@endsection
