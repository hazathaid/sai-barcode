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

            <div id="status" class="mt-4"></div>
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
        if (typeof Html5Qrcode !== 'undefined') return;
        try { await loadScript('/js/html5-qrcode.min.js'); return; } catch(e){ /* fall through */ }
        await loadScript('https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.7/minified/html5-qrcode.min.js');
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

        const res = await fetch('/api/admin/events/' + eventId + '/checkin', {
            method: 'POST', credentials: 'same-origin',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ qr_token: token, device_info: navigator.userAgent })
        });

        if (res.status === 404) { renderAlert('invalid','QR tidak valid'); return; }
        const json = await res.json();
        if (json.status === 'OK') { renderAlert('ok', json.message, json.ticket); }
        else if (json.status === 'ALREADY') { renderAlert('already', json.message, json.ticket); }
        else { renderAlert('invalid', json.message || 'QR tidak valid'); }
    }

    startBtn.addEventListener('click', async function(){
        if (running) return; try{ await ensureLibrary(); }catch(e){ alert('Scanner library failed to load.'); return; }
        if (!html5QrCode) html5QrCode = new Html5Qrcode('reader');
        try{
            const cameras = await Html5Qrcode.getCameras(); const cameraId = (cameras && cameras[0]) ? cameras[0].id : null;
            const cameraConfig = cameraId ? { deviceId: { exact: cameraId } } : { facingMode: 'environment' };
            await html5QrCode.start(cameraConfig, { fps:10, qrbox: {width:320, height:320} }, (decoded)=>{ handleToken(decoded); }, ()=>{} );
            running = true; startBtn.disabled = true; stopBtn.disabled = false;
        } catch(err){ alert('Camera start failed: ' + err); }
    });

    stopBtn.addEventListener('click', function(){ if (!running) return; html5QrCode.stop().then(()=>{ running=false; startBtn.disabled=false; stopBtn.disabled=true; }).catch(()=>{}); });
</script>
@endpush

@endsection
