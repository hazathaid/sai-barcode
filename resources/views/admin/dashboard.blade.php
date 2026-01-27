@extends('layouts.admin')

@section('page-title','Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <x-admin.card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Total registrations</div>
                    <div class="mt-2 text-2xl font-semibold">{{ $totalRegistrations ?? 128 }}</div>
                </div>
                <div class="text-indigo-600 text-3xl">🧾</div>
            </div>
        </x-admin.card>

        <x-admin.card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Checked-in</div>
                    <div class="mt-2 text-2xl font-semibold">{{ $checkedIn ?? 42 }}</div>
                </div>
                <div class="text-emerald-600 text-3xl">✅</div>
            </div>
        </x-admin.card>

        <x-admin.card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Remaining</div>
                    <div class="mt-2 text-2xl font-semibold">{{ ($totalRegistrations ?? 128) - ($checkedIn ?? 42) }}</div>
                </div>
                <div class="text-amber-500 text-3xl">🕒</div>
            </div>
        </x-admin.card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <x-admin.card class="lg:col-span-2">
            <h3 class="text-lg font-semibold mb-2">Recent registrations</h3>
            <div class="divide-y divide-gray-100">
                {{-- example rows --}}
                <div class="py-3 flex items-center justify-between">
                    <div>
                        <div class="font-medium">John Doe</div>
                        <div class="text-sm text-gray-500">Seminar Laravel QR 2026 — john@example.com</div>
                    </div>
                    <div class="text-sm text-gray-500">{{ now()->subHour()->format('H:i') }}</div>
                </div>
            </div>
        </x-admin.card>

        <x-admin.card>
            <h3 class="text-lg font-semibold mb-2">Quick actions</h3>
            <div class="flex flex-col gap-2">
                <a href="#" class="inline-block text-indigo-600 hover:underline">Create event</a>
                <a href="#" class="inline-block text-indigo-600 hover:underline">Export attendees</a>
            </div>
        </x-admin.card>
    </div>

    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-2">Events</h3>
        <x-admin.card>
            <div class="divide-y divide-gray-100">
                @forelse($events as $ev)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ $ev->name }}</div>
                            <div class="text-sm text-gray-500">{{ $ev->starts_at->format('j M Y H:i') }} — {{ $ev->location ?? '—' }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.events.tickets', $ev) }}" class="px-3 py-2 rounded-lg bg-indigo-600 text-white">Tickets</a>
                        </div>
                    </div>
                @empty
                    <div class="py-3">No events yet.</div>
                @endforelse
            </div>
        </x-admin.card>
    </div>
</div>
@endsection
