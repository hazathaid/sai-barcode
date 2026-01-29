<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Ticket - {{ $ticket->event->name }} - {{ $ticket->name }}</title>
    <style>
      body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;background:#f7f7fb;color:#111;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px}
      .card{max-width:720px;width:100%;background:#fff;border:1px solid #eee;border-radius:10px;padding:20px;text-align:center}
      button{padding:8px 12px;margin-top:12px;border-radius:6px}
    </style>
</head>
<body>
@extends('layouts.app')

@section('title', $ticket->event->name . ' — ' . ($ticket->name ?: 'Tiket'))

@section('content')
    <div class="bg-white border border-gray-200 rounded-lg p-6 text-center shadow-sm">
        <h1 class="text-2xl font-semibold">{{ $ticket->event->name }}</h1>

        <p class="mt-3 text-gray-700"><strong>Peserta:</strong> {{ $ticket->name }}</p>
        <p class="text-gray-700"><strong>Email:</strong> {{ $ticket->email }}</p>
        <p class="text-gray-700"><strong>Hadir:</strong>
            @if($ticket->checked_in_at)
                {{ $ticket->checked_in_at->format('j M Y H:i') }}
            @else
                Belum check-in
            @endif
        </p>

        <div class="mt-6">
            {!! \SimpleSoftwareIO\QrCode::size(220)->generate(url('/t/'.$ticket->qr_token)) !!}
        </div>

        <div class="mt-6">
            <button id="copyBtn" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Salin tautan tiket</button>
        </div>
    </div>
@endsection

    <script>
        (function(){
            const link = {!! json_encode(url('/t/'.$ticket->qr_token)) !!};
            const btn = document.getElementById('copyBtn');
            btn.addEventListener('click', function(){
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(link).then(function(){
                        btn.textContent = 'Link Copied';
                    }).catch(function(){
                        fallbackCopy(link);
                    });
                } else {
                    fallbackCopy(link);
                }
            });

            function fallbackCopy(text){
                const el = document.createElement('textarea');
                el.value = text;
                el.setAttribute('readonly','');
                el.style.position = 'absolute';
                el.style.left = '-9999px';
                document.body.appendChild(el);
                el.select();
                try { document.execCommand('copy'); btn.textContent = 'Link Copied'; } catch(e) { alert('Copy failed'); }
                document.body.removeChild(el);
            }
        })();
    </script>

</body>
</html>
