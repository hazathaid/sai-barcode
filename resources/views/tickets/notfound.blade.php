@extends('layouts.app')

@section('title','Tiket Tidak Ditemukan')

@section('content')
  <div class="bg-white border border-gray-200 rounded-lg p-6 text-center shadow-sm">
    <h1 class="text-2xl font-semibold mb-2">Tiket Tidak Ditemukan</h1>
    <p class="text-gray-600">Kami tidak menemukan tiket dengan kode/token berikut:</p>
    <p class="mt-3 font-mono text-sm text-gray-800 break-all">{{ $token }}</p>
    <p class="mt-4 text-gray-600">Periksa kembali tautan atau hubungi penyelenggara acara jika menurutmu ini salah.</p>
    <p class="mt-6"><a href="/" class="text-indigo-600 hover:underline">Kembali ke beranda</a></p>
  </div>
@endsection
