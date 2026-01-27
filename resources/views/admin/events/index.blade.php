@extends('layouts.admin')

@section('page-title','Events')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-semibold">Events</h3>
        <a href="{{ route('admin.events.create') }}" class="px-3 py-2 bg-indigo-600 text-white rounded-lg">New Event</a>
    </div>

    <x-admin.card>
        <div class="overflow-x-auto">
            <table class="w-full text-left table-auto">
                <thead>
                    <tr class="text-sm text-gray-600">
                        <th class="p-3">#</th>
                        <th class="p-3">Name</th>
                        <th class="p-3">Starts At</th>
                        <th class="p-3">Location</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                        <tr class="border-t">
                            <td class="p-3">{{ $loop->iteration + ($events->currentPage()-1)*$events->perPage() }}</td>
                            <td class="p-3">{{ $event->name }}</td>
                            <td class="p-3">{{ $event->starts_at->format('j M Y H:i') }}</td>
                            <td class="p-3">{{ $event->location ?? '—' }}</td>
                            <td class="p-3">{{ $event->status }}</td>
                            <td class="p-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.events.tickets', $event) }}" class="px-2 py-1 rounded-lg bg-indigo-50 text-indigo-700">Tickets</a>
                                    <a href="{{ route('admin.events.scanner', $event) }}" class="px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700">Scanner</a>
                                    <a href="{{ route('admin.events.edit', $event) }}" class="px-2 py-1 rounded-lg bg-gray-100">Edit</a>
                                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Delete event?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-2 py-1 rounded-lg bg-rose-50 text-rose-700">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $events->links() }}
        </div>
    </x-admin.card>
</div>
@endsection
