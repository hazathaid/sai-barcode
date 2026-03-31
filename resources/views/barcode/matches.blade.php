@extends('layouts.app')

@section('title','Beberapa tiket ditemukan')

@section('content')
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm max-w-3xl mx-auto">
        <h1 class="text-2xl font-semibold text-center">Ditemukan beberapa tiket</h1>
        <p class="text-gray-600 text-center">Pilih salah satu untuk melihat barcode</p>

        <ul class="mt-4 space-y-4">
            @foreach($tickets as $t)
                <li class="flex items-center gap-4 bg-white border border-gray-100 rounded-lg p-4">
                    <img src="{{ url('/barcode/'.$t->id) }}" alt="barcode" class="h-28 w-28 object-contain bg-white p-2 rounded">
                    <div class="flex-1">
                        <div class="font-semibold">{{ $t->name ?? '—' }}</div>
                        <div class="text-sm text-gray-600">Event: {{ $t->event->name ?? ($t->event_id ?? '—') }} · Kode: {{ $t->code ?? $t->id }}</div>
                        <div class="mt-1 text-sm text-gray-700 break-all">
                            Token URL: <a href="{{ url('/t/'.$t->qr_token) }}" class="text-indigo-600 hover:underline">{{ url('/t/'.$t->qr_token) }}</a>
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <button
                                type="button"
                                class="copy-btn px-3 py-1.5 text-xs bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                data-copy="{{ url('/t/'.$t->qr_token) }}"
                            >
                                Copy Token URL
                            </button>
                            <span class="copy-note text-xs text-gray-500 hidden">Tersalin.</span>
                        </div>
                        <div class="mt-2"><a href="{{ route('barcode.show', ['ticket' => $t->id]) }}" class="text-indigo-600 hover:underline">Lihat detail</a></div>
                    </div>
                </li>
            @endforeach
        </ul>

        <p class="mt-6 text-center"><a href="{{ route('barcode.index') }}" class="text-indigo-600 hover:underline">Cari lagi</a></p>
    </div>
@endsection

@push('scripts')
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

    document.querySelectorAll('.copy-btn').forEach(function(btn){
        btn.addEventListener('click', async function(){
            const val = this.getAttribute('data-copy') || '';
            if (!val) return;
            const note = this.parentElement?.querySelector('.copy-note');
            try {
                await copyText(val);
                if (note) {
                    note.classList.remove('hidden');
                    note.textContent = 'Tersalin.';
                    setTimeout(()=>{ note.classList.add('hidden'); }, 1400);
                }
            } catch (e) {
                if (note) {
                    note.classList.remove('hidden');
                    note.textContent = 'Gagal copy. Silakan copy manual.';
                }
            }
        });
    });
</script>
@endpush
