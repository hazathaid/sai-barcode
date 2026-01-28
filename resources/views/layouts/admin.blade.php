<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>
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
    <div class="flex">
        {{-- Sidebar --}}
        <aside id="sidebar" class="hidden md:block md:w-64 bg-white border-r border-gray-200 min-h-screen">
            <div class="p-4">
                <a href="/" class="text-lg font-semibold text-indigo-600">{{ config('app.name') }}</a>
            </div>
            <nav class="px-4 py-2">
                <a href="{{ route('admin.dashboard') ?? '#' }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-50">
                    <span class="text-indigo-600">🏠</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.events.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-50">
                    <span class="text-indigo-600">📋</span>
                    <span>Events</span>
                </a>
            </nav>
        </aside>

        <div class="flex-1 min-h-screen">
            {{-- Top navbar --}}
            <header class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto flex items-center justify-between p-4 gap-4">
                    <div class="flex items-center gap-3">
                        <button id="sidebarToggle" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                            ☰
                        </button>
                        <h2 class="text-lg font-semibold">@yield('page-title', 'Admin')</h2>
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="text-sm text-gray-600 hover:text-indigo-600">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function(){
            const sb = document.getElementById('sidebar');
            if (!sb) return;
            sb.classList.toggle('hidden');
        });
    </script>
    @stack('scripts')
</body>
</html>
