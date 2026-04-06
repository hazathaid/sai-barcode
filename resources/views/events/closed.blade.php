<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Pendaftaran Ditutup - {{ $event->name }}</title>
    @if (env('USE_TAILWIND_CDN'))
        <script src="https://cdn.tailwindcss.com"></script>
    @else
        @vite(['resources/css/app.css'])
    @endif
</head>
<body class="min-h-screen bg-gray-50 py-10">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
            <h1 class="text-2xl font-semibold text-gray-900">Pendaftaran Ditutup</h1>
            <p class="mt-3 text-gray-600">
                Maaf, pendaftaran untuk event <span class="font-medium text-gray-800">{{ $event->name }}</span> saat ini belum dibuka atau sudah ditutup.
            </p>

            <div class="mt-6 inline-flex items-center rounded-full bg-amber-50 px-4 py-2 text-sm font-medium text-amber-700 border border-amber-200">
                Status Event: {{ ucfirst($event->status ?? 'draft') }}
            </div>

            <div class="mt-8">
                <a href="/" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                    Kembali ke Daftar Event
                </a>
            </div>
        </div>
    </div>
</body>
</html>
