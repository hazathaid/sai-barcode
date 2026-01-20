<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Ticket - {{ $ticket->event->name }} - {{ $ticket->name }}</title>
    <style>body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;padding:20px;max-width:820px;margin:auto}button{padding:8px 12px;margin-top:12px}</style>
</head>
<body>
    <h1>{{ $ticket->event->name }}</h1>

    <p><strong>Participant:</strong> {{ $ticket->name }}</p>
    <p><strong>Email:</strong> {{ $ticket->email }}</p>
    <p><strong>Attendance:</strong>
        @if($ticket->checked_in_at)
            {{ $ticket->checked_in_at->format('j M Y H:i') }}
        @else
            Not checked in
        @endif
    </p>

    <div>
        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(220)
            ->generate(url('/t/'.$ticket->qr_token)) !!}
    </div>

    <div>
        <button id="copyBtn">Copy Ticket Link</button>
    </div>

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
