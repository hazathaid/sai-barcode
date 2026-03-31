<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Barcode</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>body{font-family:system-ui,Segoe UI,Helvetica,Arial;background:#f7f7fb;color:#111;padding:24px} .card{max-width:520px;margin:24px auto;padding:18px;background:#fff;border:1px solid #eee;border-radius:8px;text-align:center} img{max-width:100%;height:auto;background:#fff;padding:8px;border-radius:6px} .copy-btn{margin-top:8px;padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;background:#fff;cursor:pointer} .copy-btn:hover{background:#f3f4f6} .copy-note{font-size:12px;color:#6b7280;margin-top:6px;display:none}</style>
</head>
<body>
  <div class="card">
    <h2>Tiket untuk {{ $ticket->name ?? '—' }}</h2>
    <p class="muted">Event: {{ $ticket->event->name ?? ($ticket->event_id ?? '—') }}</p>
    <div style="margin:18px 0">
      <img src="{{ url('/barcode/'.$ticket->id) }}" alt="barcode">
    </div>
    <div style="text-align:left;background:#f8fafc;border:1px solid #e5e7eb;border-radius:8px;padding:12px;margin-bottom:14px;word-break:break-all">
      <div style="font-size:13px;color:#4b5563;margin-bottom:6px">Token URL (cadangan jika barcode tidak terbaca):</div>
      <a href="{{ url('/t/'.$ticket->qr_token) }}" style="color:#4f46e5;text-decoration:underline">{{ url('/t/'.$ticket->qr_token) }}</a>
      <div>
        <button type="button" class="copy-btn" data-copy="{{ url('/t/'.$ticket->qr_token) }}">Copy Token URL</button>
        <div class="copy-note" id="copyNote">Tersalin.</div>
      </div>
    </div>
    <p><a href="{{ route('barcode.index') }}">Cari lagi</a></p>
  </div>
</body>
<script>
  async function copyText(value){
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(value);
      return;
    }

    const ta = document.createElement('textarea');
    ta.value = value;
    ta.setAttribute('readonly', '');
    ta.style.position = 'absolute';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    document.execCommand('copy');
    document.body.removeChild(ta);
  }

  const btn = document.querySelector('.copy-btn');
  const note = document.getElementById('copyNote');
  btn?.addEventListener('click', async function(){
    const val = this.getAttribute('data-copy') || '';
    if (!val) return;
    try {
      await copyText(val);
      if (note) {
        note.style.display = 'block';
        note.textContent = 'Tersalin.';
        setTimeout(()=>{ note.style.display = 'none'; }, 1400);
      }
    } catch (e) {
      if (note) {
        note.style.display = 'block';
        note.textContent = 'Gagal copy. Silakan copy manual.';
      }
    }
  });
</script>
</html>
