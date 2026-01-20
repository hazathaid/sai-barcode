<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Scanner - {{ $event->name }}</title>
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;padding:20px}
        #reader{width:100%;max-width:720px;margin:12px auto}
        .status{padding:12px;margin-top:12px;border-radius:6px;color:#fff}
        .status.ok{background:#2ecc71}
        .status.already{background:#f1c40f;color:#111}
        .status.invalid{background:#e74c3c}
        button{padding:8px 12px;margin-right:8px}
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.7/minified/html5-qrcode.min.js"></script>
</head>
<body>
    <h1>Scanner: {{ $event->name }}</h1>

    <div>
        <button id="startBtn">Start Scan</button>
        <button id="stopBtn" disabled>Stop Scan</button>
    </div>

    <div id="reader"></div>

    <div id="output"></div>

    <script>
        const eventId = {{ (int) $event->id }};
        const startBtn = document.getElementById('startBtn');
        const stopBtn = document.getElementById('stopBtn');
        const output = document.getElementById('output');
        let html5QrCode = null;
        let running = false;

        function loadScript(url){
            return new Promise((resolve, reject) => {
                const s = document.createElement('script');
                s.src = url;
                s.onload = resolve;
                s.onerror = reject;
                document.head.appendChild(s);
            });
        }

        async function ensureLibrary(){
            if (typeof Html5Qrcode !== 'undefined') return;

            // Try local first (public/js/html5-qrcode.min.js), then CDN
            const local = '/js/html5-qrcode.min.js';
            try {
                await loadScript(local);
                return;
            } catch (e) {
                console.warn('Local html5-qrcode not found, falling back to CDN', e);
            }

            try {
                await loadScript('https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.7/minified/html5-qrcode.min.js');
                return;
            } catch(e) {
                console.error('Failed to load html5-qrcode library from CDN', e);
                throw e;
            }
        }

        function showStatus(type, message, ticket){
            output.innerHTML = '';
            const el = document.createElement('div');
            el.className = 'status ' + type;
            el.textContent = message;
            output.appendChild(el);
            if (ticket) {
                const info = document.createElement('div');
                info.style.marginTop = '8px';
                info.textContent = ticket.name + ' — ' + ticket.email;
                output.appendChild(info);
            }
        }

        async function handleToken(raw){
            // raw may be a URL like https://.../t/<token> or just the token
            let token = raw;
            try {
                const u = new URL(raw);
                const parts = u.pathname.split('/').filter(Boolean);
                if (parts.length && parts[0] === 't') token = parts[1] || token;
            } catch(e) {
                // not a URL, keep raw
            }

            // POST to API
            const res = await fetch('/api/admin/events/' + eventId + '/checkin', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ qr_token: token, device_info: navigator.userAgent })
            });

            if (res.status === 404) {
                showStatus('invalid', 'INVALID: QR tidak valid');
                return;
            }

            const json = await res.json();
            if (json.status === 'OK') {
                showStatus('ok', json.message, json.ticket);
            } else if (json.status === 'ALREADY') {
                showStatus('already', json.message, json.ticket);
            } else {
                showStatus('invalid', json.message || 'QR tidak valid');
            }
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) console.warn('getUserMedia not available in this browser');

        startBtn.addEventListener('click', async function(){
            if (running) return;
            console.log('Start scan pressed');

            try {
                await ensureLibrary();
            } catch (e) {
                console.error('Failed to load html5-qrcode library', e);
                alert('Failed to load scanner library. Check network or CDN access.');
                return;
            }

            if (!html5QrCode) html5QrCode = new Html5Qrcode('reader');

            try {
                const cameras = await Html5Qrcode.getCameras();
                console.log('Cameras found:', cameras);
                const cameraId = (cameras && cameras[0]) ? cameras[0].id : null;
                const cameraConfig = cameraId ? { deviceId: { exact: cameraId } } : { facingMode: 'environment' };

                await html5QrCode.start(
                    cameraConfig,
                    { fps: 10, qrbox: 250 },
                    (decodedText) => { console.log('Decoded:', decodedText); handleToken(decodedText); },
                    (errorMessage) => { console.debug('QR parse error', errorMessage); }
                );

                running = true; startBtn.disabled = true; stopBtn.disabled = false;
            } catch (err) {
                console.error('Camera start failed', err);
                if (err && err.name === 'NotAllowedError') {
                    alert('Camera access was denied. Please allow camera permission and try again.');
                } else {
                    alert('Camera start failed: ' + err);
                }
            }
        });

        stopBtn.addEventListener('click', function(){
            if (!running) return;
            html5QrCode.stop().then(() => {
                running = false; startBtn.disabled = false; stopBtn.disabled = true;
            }).catch(err => { alert('Stop failed: ' + err); });
        });
    </script>

</body>
</html>
