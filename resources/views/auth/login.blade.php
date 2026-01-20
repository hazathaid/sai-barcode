<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login</title>
    <style>body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;padding:20px;max-width:420px;margin:auto}label{display:block;margin-top:10px}input{width:100%;padding:8px;margin-top:4px}button{margin-top:12px;padding:8px 12px}</style>
</head>
<body>
    <h1>Login</h1>

    @if($errors->any())
        <div style="color:#a00">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required value="{{ old('email') }}">

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>

        <label><input type="checkbox" name="remember"> Remember me</label>

        <button type="submit">Login</button>
    </form>
</body>
</html>
