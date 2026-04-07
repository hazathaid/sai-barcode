@extends('layouts.admin')

@section('page-title','Edit Event')

@section('content')
<div class="max-w-3xl mx-auto">
    <x-admin.card>
        <h3 class="text-lg font-semibold mb-4">Edit Event</h3>
        <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid gap-4">
                <div>
                    <label class="block text-sm font-medium">Name</label>
                    <input name="name" value="{{ old('name', $event->name) }}" class="mt-1 block w-full rounded-lg border p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium">Slug</label>
                    <input name="slug" value="{{ old('slug', $event->slug) }}" class="mt-1 block w-full rounded-lg border p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium">Starts At</label>
                    <input name="starts_at" value="{{ old('starts_at', $event->starts_at->format('Y-m-d\TH:i')) }}" type="datetime-local" class="mt-1 block w-full rounded-lg border p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium">Ends At</label>
                    <input name="ends_at" value="{{ old('ends_at', optional($event->ends_at)->format('Y-m-d\TH:i')) }}" type="datetime-local" class="mt-1 block w-full rounded-lg border p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium">Location</label>
                    <input name="location" value="{{ old('location', $event->location) }}" class="mt-1 block w-full rounded-lg border p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium">Status</label>
                    <select name="status" class="mt-1 block w-full rounded-lg border p-2">
                        <option value="published" {{ $event->status === 'published' ? 'selected' : '' }}>published</option>
                        <option value="draft" {{ $event->status === 'draft' ? 'selected' : '' }}>draft</option>
                        <option value="closed" {{ $event->status === 'closed' ? 'selected' : '' }}>closed</option>
                    </select>
                </div>

                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="external_only" value="1" class="mr-2" {{ old('external_only', $event->external_only) ? 'checked' : '' }}>
                        <span class="text-sm">Registrasi hanya untuk external (non-internal)</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium">Image</label>
                    @if($event->image)
                        @php
                            $imgUrl = filter_var($event->image, FILTER_VALIDATE_URL) ? $event->image : asset('storage/' . ltrim($event->image, '/'));
                        @endphp
                        <div class="mb-2">
                            <img src="{{ $imgUrl }}" alt="{{ $event->name }}" class="w-48 h-32 object-cover rounded">
                        </div>
                    @endif
                    <input type="file" name="image" accept="image/*" class="mt-1 block w-full">
                </div>

                <div class="flex justify-end">
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Save</button>
                </div>
            </div>
        </form>
    </x-admin.card>
</div>
@endsection
