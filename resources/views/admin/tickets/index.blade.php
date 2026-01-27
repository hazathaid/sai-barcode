@extends('layouts.admin')

@section('page-title','Tickets')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-semibold">Tickets — {{ $event->name }}</h3>
        <form method="GET" class="flex items-center gap-2">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search name, email, token" class="px-3 py-2 border rounded-lg" />
            <button class="px-3 py-2 bg-indigo-600 text-white rounded-lg">Search</button>
        </form>
    </div>

    <x-admin.card>
        <div class="overflow-x-auto">
            <table class="w-full text-left table-auto">
                <thead>
                    <tr class="text-sm text-gray-600">
                        <th class="p-3">#</th>
                        <th class="p-3">Name</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Phone</th>
                        <th class="p-3">QR Token</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Checked in at</th>
                        <th class="p-3">Checked in by</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                        <tr class="border-t">
                            <td class="p-3 align-top">{{ $loop->iteration + ($tickets->currentPage()-1)*$tickets->perPage() }}</td>
                            <td class="p-3 align-top">{{ $ticket->name }}</td>
                            <td class="p-3 align-top">{{ $ticket->email }}</td>
                            <td class="p-3 align-top">{{ $ticket->phone }}</td>
                            <td class="p-3 align-top"><code class="text-xs text-gray-600">{{ \Illuminate\Support\Str::limit($ticket->qr_token, 24) }}</code></td>
                            <td class="p-3 align-top">
                                @if($ticket->attendance)
                                    <span class="inline-block px-2 py-1 rounded-lg text-sm bg-amber-50 text-amber-800">Already</span>
                                @else
                                    <span class="inline-block px-2 py-1 rounded-lg text-sm bg-emerald-50 text-emerald-800">Not checked-in</span>
                                @endif
                            </td>
                            <td class="p-3 align-top">{{ $ticket->attendance ? $ticket->attendance->checked_in_at->format('j M Y H:i') : '—' }}</td>
                            <td class="p-3 align-top">{{ $ticket->attendance && $ticket->attendance->admin ? $ticket->attendance->admin->name : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    </x-admin.card>
</div>
@endsection
