<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login</title>
    @if (env('USE_TAILWIND_CDN'))
        <script src="https://cdn.tailwindcss.com"></script>
    @else
        @vite(['resources/css/app.css','resources/js/app.js'])
    @endif
    <style>body{background-color:#f8fafc}</style>
</head>
<body class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-semibold text-gray-800 mb-4">Admin Login</h1>

        @if($errors->any())
            <div class="mb-4 p-3 rounded border border-red-200 bg-red-50 text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" name="email" type="email" required value="{{ old('email') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-200">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" name="password" type="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-200">
            </div>

            <div class="flex items-center justify-between text-sm text-gray-600">
                <label class="flex items-center gap-2"><input type="checkbox" name="remember" class="rounded"> Remember me</label>
                <a href="#" class="text-indigo-600 hover:underline">Need help?</a>
            </div>

            <div>
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
