@extends('layouts.app')

@section('title', 'E-Sertifikat')

@section('content')
<div class="overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-gray-200">
    {{-- Hero --}}
    <div class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-fuchsia-500 px-8 py-10 text-white">
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-16 -left-8 h-44 w-44 rounded-full bg-white/10"></div>
        <div class="relative">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 text-3xl shadow-inner">🎓</div>
            <h1 class="mt-4 text-3xl font-bold tracking-tight">E-Sertifikat</h1>
            <p class="mt-2 max-w-lg text-sm text-white/80">
                Unduh sertifikat elektronik Anda dengan memasukkan nomor HP atau email yang digunakan saat pendaftaran.
            </p>
        </div>
    </div>

    {{-- Form --}}
    <div class="p-6 sm:p-8">
        @if(session('error'))
            <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('certificate.search') }}" class="space-y-5">
            @csrf

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Pilih Event</label>
                <select name="event_id" required
                    class="block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200">
                    <option value="">-- Pilih Event --</option>
                    @forelse($events as $ev)
                        <option value="{{ $ev->id }}" {{ old('event_id') == $ev->id ? 'selected' : '' }}>{{ $ev->name }}</option>
                    @empty
                        <option value="">Belum ada event dengan sertifikat</option>
                    @endforelse
                </select>
                @error('event_id')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

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
                <span>Cari Sertifikat</span>
            </button>
        </form>

        <div class="mt-6 flex items-start gap-3 rounded-xl bg-amber-50 px-4 py-3 text-xs text-amber-800 ring-1 ring-amber-100">
            <span class="text-base leading-none">ℹ️</span>
            <p>Pastikan Anda sudah melakukan check-in pada acara. Data yang dicari harus sesuai dengan nomor HP atau email saat pendaftaran.</p>
        </div>
    </div>
</div>
@endsection
