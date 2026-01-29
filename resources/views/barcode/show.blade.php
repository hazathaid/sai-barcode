<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Barcode</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>body{font-family:system-ui,Segoe UI,Helvetica,Arial;background:#f7f7fb;color:#111;padding:24px} .card{max-width:520px;margin:24px auto;padding:18px;background:#fff;border:1px solid #eee;border-radius:8px;text-align:center} img{max-width:100%;height:auto;background:#fff;padding:8px;border-radius:6px}</style>
</head>
<body>
  <div class="card">
    <h2>Tiket untuk {{ $ticket->name ?? '—' }}</h2>
    <p class="muted">Event: {{ $ticket->event->name ?? ($ticket->event_id ?? '—') }}</p>
    <div style="margin:18px 0">
      <img src="{{ url('/barcode/'.$ticket->id) }}" alt="barcode">
    </div>
    <p><a href="{{ route('barcode.index') }}">Cari lagi</a></p>
  </div>
</body>
</html>
