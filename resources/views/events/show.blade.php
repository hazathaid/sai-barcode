<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $event->name }}</title>
    <style>body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;padding:20px;max-width:820px;margin:auto}label{display:block;margin-top:10px}input{width:100%;padding:8px;margin-top:4px}button{margin-top:12px;padding:8px 12px}</style>
</head>
<body>
    <h1>{{ $event->name }}</h1>

    <p>
        <strong>Date:</strong>
        {{ $event->starts_at->format('j M Y H:i') }}
        @if($event->ends_at)
            - {{ $event->ends_at->format('j M Y H:i') }}
        @endif
    </p>

    <p><strong>Location:</strong> {{ $event->location ?? '—' }}</p>

    @if ($errors->any())
        <div style="color:#a00;border:1px solid #f5c6cb;padding:10px;background:#fff0f0">
            <strong>There were some problems with your input:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('events.register', ['event' => $event->slug]) }}">
        @csrf

        <label for="name">Name</label>
        <input id="name" name="name" type="text" required value="{{ old('name') }}">

        <label for="email">Email</label>
        <input id="email" name="email" type="email" required value="{{ old('email') }}">

        <label for="phone">Phone</label>
        <input id="phone" name="phone" type="text" value="{{ old('phone') }}">

        <button type="submit">Register</button>
    </form>

</body>
</html>
