<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $event->name }}</title>
    @if (env('USE_TAILWIND_CDN'))
        <script src="https://cdn.tailwindcss.com"></script>
    @else
        @vite(['resources/css/app.css'])
    @endif
    <style>body{background-color:#f8fafc}</style>
</head>
<body class="min-h-screen py-10">
    <div class="max-w-5xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 bg-white p-6 rounded-lg shadow">
                <h1 class="text-2xl font-semibold text-gray-800">{{ $event->name }}</h1>

                <p class="mt-3 text-gray-600">
                    <strong class="text-gray-800">Date:</strong>
                    {{ $event->starts_at->format('j M Y H:i') }}
                    @if($event->ends_at)
                        - {{ $event->ends_at->format('j M Y H:i') }}
                    @endif
                </p>

                <p class="mt-2 text-gray-600"><strong class="text-gray-800">Location:</strong> {{ $event->location ?? '—' }}</p>

                <div class="mt-4 text-gray-700">
                    <p class="font-medium">Event Details</p>
                    <p class="mt-2 text-sm text-gray-600">Register to reserve your seat. After registration you'll get a ticket with a QR code to be scanned at the entrance.</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-medium text-gray-800">Register</h2>

                @if ($errors->any())
                    <div class="mt-3 p-3 rounded border border-red-200 bg-red-50 text-red-800 text-sm">
                        <strong>There were some problems with your input:</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('events.register', ['event' => $event->slug]) }}" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input id="name" name="name" type="text" required value="{{ old('name') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email" required value="{{ old('email') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    </div>

                    <div>
                        <label for="class_room" class="block text-sm font-medium text-gray-700">Kelas</label>
                        @if(isset($classrooms) && count($classrooms))
                            <select id="class_room" name="class_room" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classrooms as $cr)
                                    @php
                                        $class = $cr->name ?? (is_array($cr) && isset($cr['name']) ? $cr['name'] : $cr);
                                    @endphp
                                    <option value="{{ $class }}" {{ old('class_room') == $class ? 'selected' : '' }}>{{ $class }}</option>
                                @endforeach
                            </select>
                        @else
                            <input id="class_room" name="class_room" type="text" required value="{{ old('class_room') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        @endif
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    </div>

                    <div>
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
