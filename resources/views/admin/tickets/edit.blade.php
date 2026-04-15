@extends('layouts.admin')

@section('page-title','Edit Ticket')

@section('content')
<div class="max-w-2xl mx-auto p-4">
    <x-admin.card>
        <h3 class="text-lg font-semibold mb-4">Edit Ticket for {{ $event->name }}</h3>

        <form method="POST" action="{{ route('admin.events.tickets.update', [$event, $ticket]) }}">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded">
                    <ul class="text-sm text-red-700">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-4">
                <div>
                    <label class="block text-sm font-medium">Name</label>
                    <input name="name" value="{{ old('name', $ticket->name) }}" class="mt-1 block w-full rounded-lg border p-2">
                </div>

                <div class="flex justify-between mt-4">
                    <a href="{{ route('admin.events.tickets', $event) }}" class="px-3 py-2 bg-gray-100 rounded">Back</a>
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
                </div>
            </div>
        </form>
    </x-admin.card>
</div>
@endsection
