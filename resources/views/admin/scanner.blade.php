@extends('layouts.admin')

@section('page-title','Scanner')

@section('content')
<div class="max-w-3xl mx-auto">
    <x-admin.card>
        <div class="flex flex-col gap-4">
            <div class="text-center">
                <h3 class="text-xl font-semibold">Scanner — {{ $event->name }}</h3>
                <p class="text-sm text-gray-500">Arahkan kamera ke QR code peserta. Scanner bekerja dengan URL /t/{token} atau token mentah.</p>
            </div>

            <div class="flex justify-center">
                <div id="reader" class="bg-gray-100 rounded-xl overflow-hidden w-full max-w-md min-w-[280px] aspect-square flex items-center justify-center"></div>
            </div>

            <div class="flex items-center justify-center gap-3">
                <x-admin.button id="startBtn" class="bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500">Start Scan</x-admin.button>
                <x-admin.button id="stopBtn" class="bg-gray-200 text-gray-800 hover:bg-gray-300" disabled>Stop Scan</x-admin.button>
            </div>

            <div class="mt-3 flex items-center justify-center gap-2">
                <input id="manualToken" placeholder="Paste token or /t/{token} here" class="px-3 py-2 border border-gray-200 rounded-lg w-3/4" />
                <button id="manualScanBtn" class="px-3 py-2 bg-indigo-600 text-white rounded-lg">Test</button>
            </div>

            <div class="mt-3 flex items-center justify-center gap-2">
                <label for="cameraSelect" class="sr-only">Camera</label>
                <select id="cameraSelect" class="px-3 py-2 border border-gray-200 rounded-lg w-3/4"></select>
            </div>

            <div id="status" class="mt-4"></div>
            <div id="toastContainer" class="fixed top-6 right-6 z-50 flex flex-col gap-2"></div>
        </div>
    </x-admin.card>
    <div class="mt-4 text-center text-sm text-gray-500">Ensure your browser allows camera access and that you're logged in.</div>
</div>

@push('scripts')
<script>
    const eventId = {{ (int) $event->id }};
    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');
    const statusEl = document.getElementById('status');
    const readerEl = document.getElementById('reader');
    let html5QrCode = null; let running = false; let cooldown = false;

    function loadScript(url){
        return new Promise((resolve,reject)=>{
            const s = document.createElement('script'); s.src = url; s.onload = resolve; s.onerror = reject; document.head.appendChild(s);
        });
    }

    async function ensureLibrary(){
        if (typeof Html5Qrcode !== 'undefined') { console.debug('Html5Qrcode already present'); return; }
        try { await loadScript('/js/html5-qrcode.min.js'); console.debug('Loaded local html5-qrcode'); return; } catch(e){ console.debug('Local html5-qrcode load failed', e); }
        try { await loadScript('https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.7/minified/html5-qrcode.min.js'); console.debug('Loaded CDN html5-qrcode'); } catch(e){ console.error('Failed to load html5-qrcode library', e); throw e; }
    }

    function renderAlert(type, message, ticket){
        const variants = {
            ok: ['bg-emerald-50 text-emerald-800'],
            already: ['bg-amber-50 text-amber-800'],
            invalid: ['bg-rose-50 text-rose-800']
        };
        const cls = variants[type] ? variants[type].join(' ') : 'bg-gray-50 text-gray-800';
        let inner = `<div class="p-4 rounded-xl ${cls}">` + `<div class="font-semibold">${message}</div>`;
        if (ticket) inner += `<div class="text-sm text-gray-600 mt-1">${ticket.name} — ${ticket.email}</div>`;
        inner += `</div>`;
        statusEl.innerHTML = inner;
    }

    async function handleToken(raw){
        if (cooldown) return;
        cooldown = true;
        setTimeout(()=>{ cooldown = false; }, 1200);

        let token = raw;
        try { const u = new URL(raw); const parts = u.pathname.split('/').filter(Boolean); if (parts[0] === 't') token = parts[1] || token; } catch(e) {}

        console.log('handleToken: sending token', token);
        let res;
        try {
            res = await fetch('/api/admin/events/' + eventId + '/checkin', {
                method: 'POST', credentials: 'same-origin',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ qr_token: token, device_info: navigator.userAgent })
            });
        } catch (err) {
            console.error('Network/fetch error', err);
            renderAlert('invalid', 'Network error: ' + err.message);
            return;
        }

        console.log('handleToken: response status', res.status);
        if (res.status === 404) { renderAlert('invalid','QR tidak valid'); return; }
        let json;
        try { json = await res.json(); console.log('handleToken: response json', json); } catch(e){ console.error('Invalid JSON response', e); renderAlert('invalid','Invalid server response'); return; }
        if (json.status === 'OK') {
            console.log('Checkin OK:', json.message, json.ticket);
            renderAlert('ok', json.message, json.ticket);
            try { showToast('success', json.message); } catch(e) { console.log('Toast failed', e); }
            // stop scanner after successful check-in
            if (html5QrCode && running) {
                html5QrCode.stop().then(()=>{ running=false; startBtn.disabled=false; stopBtn.disabled=true; }).catch(()=>{});
            }
        }
        else if (json.status === 'ALREADY') {
            console.log('Already checked-in:', json.message, json.ticket);
            renderAlert('already', json.message, json.ticket);
            try { showToast('warning', json.message); } catch(e) { console.log('Toast failed', e); }
        }
        else { renderAlert('invalid', json.message || 'QR tidak valid'); }
    }

    startBtn.addEventListener('click', async function(){
        if (running) return;
        try{ await ensureLibrary(); }catch(e){ alert('Scanner library failed to load.'); return; }
        if (!html5QrCode) html5QrCode = new Html5Qrcode('reader');
        try{
            const cameras = await Html5Qrcode.getCameras();
            const cameraSelect = document.getElementById('cameraSelect');
            // populate select if empty
            if (cameraSelect && cameraSelect.options.length === 0) {
                const placeholder = document.createElement('option'); placeholder.value = ''; placeholder.text = cameras && cameras.length ? 'Choose camera (or use default)' : 'No camera detected';
                cameraSelect.appendChild(placeholder);
                if (cameras && cameras.length) {
                    cameras.forEach(c => {
                        const opt = document.createElement('option'); opt.value = c.id; opt.text = c.label || c.id; cameraSelect.appendChild(opt);
                    });
                }
            }

            // choose camera: user-selected if any, otherwise prefer rear-like or last camera
            let selectedId = cameraSelect?.value;
            let cameraId = selectedId || (cameras && cameras.length ? (cameras.find(c => /back|rear|environment/i.test(c.label))?.id || cameras[cameras.length-1].id) : null);

            const cameraConfig = cameraId ? { deviceId: { exact: cameraId } } : { facingMode: 'environment' };

            const qrOptions = {
                fps: 10,
                qrbox: { width: 480, height: 480 },
                experimentalFeatures: { useBarCodeDetectorIfSupported: true },
                disableFlip: false
            };

            await html5QrCode.start(cameraConfig, qrOptions,
                (decoded)=>{ console.log('decoded', decoded); handleToken(decoded); },
                (err)=>{ /* frequent parse-errors expected; keep minimal logging */ }
            );

            running = true; startBtn.disabled = true; stopBtn.disabled = false;

            // react to camera selection changes while running
            cameraSelect?.addEventListener('change', async function(){
                const newId = this.value;
                if (!newId) return;
                if (!running) return;
                try { await html5QrCode.stop(); running = false; } catch(e) {}
                try {
                    await html5QrCode.start({ deviceId: { exact: newId } }, qrOptions, (decoded)=>{ console.log('decoded', decoded); handleToken(decoded); }, ()=>{} );
                    running = true; startBtn.disabled = true; stopBtn.disabled = false;
                } catch(e) { alert('Failed to switch camera: ' + e); }
            });

        } catch(err){ alert('Camera start failed: ' + err); }
    });

    stopBtn.addEventListener('click', function(){ if (!running) return; html5QrCode.stop().then(()=>{ running=false; startBtn.disabled=false; stopBtn.disabled=true; }).catch(()=>{}); });

    document.getElementById('manualScanBtn').addEventListener('click', function(){
        const v = document.getElementById('manualToken').value.trim(); if (!v) return; handleToken(v);
    });

    // Simple toast helper
    function showToast(type, message, timeout = 3500) {
        const container = document.getElementById('toastContainer');
        if (!container) return;
        const id = 'toast-' + Date.now();
        const colors = {
            success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
            warning: 'bg-amber-50 border-amber-200 text-amber-800',
            error: 'bg-rose-50 border-rose-200 text-rose-800',
            info: 'bg-indigo-50 border-indigo-200 text-indigo-800'
        };
        const cls = colors[type] || colors.info;
        const el = document.createElement('div');
        el.id = id;
        el.className = `max-w-sm w-full border ${cls} px-4 py-3 rounded-lg shadow-sm transform transition-all duration-200`;
        el.style.opacity = '0';
        el.innerHTML = `<div class="font-medium">${message}</div>`;
        container.appendChild(el);
        // fade in
        requestAnimationFrame(()=>{ el.style.opacity = '1'; el.style.transform = 'translateY(0)'; });
        // remove after timeout
        setTimeout(()=>{
            el.style.opacity = '0'; el.style.transform = 'translateY(-8px)';
            setTimeout(()=>{ el.remove(); }, 220);
        }, timeout);
    }
</script>
@endpush

@endsection
