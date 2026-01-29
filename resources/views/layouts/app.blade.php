<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @if (env('USE_TAILWIND_CDN'))
        <script src="https://cdn.tailwindcss.com"></script>
    @else
        @if (app()->environment('local'))
            @vite(['resources/css/app.css','resources/js/app.js'])
        @else
            <link rel="stylesheet" href="{{ asset('css/app.css') }}">
            <script src="{{ asset('js/app.js') }}" defer></script>
        @endif
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50 min-h-screen font-sans text-gray-800">
    <div class="min-h-screen flex items-center justify-center p-6">
        <main class="w-full max-w-3xl">@yield('content')</main>
    </div>
    @stack('scripts')
</body>
</html>
