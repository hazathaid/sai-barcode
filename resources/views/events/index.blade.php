<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Events — {{ config('app.name') }}</title>
    @if (env('USE_TAILWIND_CDN'))
        <script src="https://cdn.tailwindcss.com"></script>
    @else
        @vite(['resources/css/app.css','resources/js/app.js'])
    @endif
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto p-6">
        <header class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Upcoming Events</h1>
            <div class="text-sm text-gray-600">Total: {{ $events->count() }}</div>
        </header>

        @if($events->isEmpty())
            <div class="bg-white p-6 rounded-lg shadow text-gray-600">No events found.</div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($events as $event)
                    <article class="bg-white rounded-lg shadow hover:shadow-md transition p-4 flex flex-col">
                        <div class="flex-1">
                            <h2 class="text-lg font-semibold text-gray-800">{{ $event->name }}</h2>
                            <p class="text-sm text-gray-600 mt-2">{{ $event->excerpt ?? (Str::limit(strip_tags($event->description ?? ''), 120)) }}</p>
                        </div>

                        <div class="mt-4 flex items-center justify-between text-sm text-gray-600">
                            <div>
                                <div><strong>Date:</strong></div>
                                <div class="text-gray-700">{{ $event->starts_at ? $event->starts_at->format('j M Y H:i') : 'TBA' }}</div>
                            </div>
                            <div class="text-right">
                                <div><strong>Location</strong></div>
                                <div class="text-gray-700">{{ $event->location ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            <a href="{{ route('events.show', ['event' => $event->slug]) }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Register</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        <footer class="mt-8 text-center text-sm text-gray-500">Powered by Hazatha</footer>
    </div>
</body>
</html>
