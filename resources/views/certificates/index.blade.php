@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-4">
    <h2 class="text-xl font-semibold mb-4">Download E-sertifikat</h2>

    <form method="POST" action="{{ route('certificate.search') }}">
        @csrf
        <div class="grid gap-3">
            <div>
                <label class="block text-sm font-medium">Pilih Event (opsional)</label>
                <select name="event_id" class="mt-1 block w-full rounded border p-2" required>
                    <option value="">-- Pilih Event --</option>
                    @foreach($events as $ev)
                        <option value="{{ $ev->id }}">{{ $ev->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium">Masukan No HP / Email</label>
                <input name="query" class="mt-1 block w-full rounded border p-2" placeholder="0812xxxx atau email@example.com" required>
            </div>

            <div class="flex justify-end">
                <button class="px-4 py-2 bg-indigo-600 text-white rounded">Cari</button>
            </div>
        </div>
    </form>
</div>

@endsection
