@extends('layouts.app')

@section('title','Halaman tidak ditemukan')

@section('content')
  <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm max-w-3xl mx-auto text-center">
      <div class="mx-auto mb-6 flex h-40 w-40 items-center justify-center">
        <img src="{{ asset('images/sai-barcode.png') }}" alt="sai barcode" class="h-40 w-40 object-contain rounded-md">
      </div>

    <h1 class="text-3xl font-semibold text-gray-900">404 — Halaman tidak ditemukan</h1>
    <p class="mt-2 text-gray-600">Maaf, halaman yang kamu cari tidak tersedia atau sudah dipindahkan.</p>

    <div class="mt-6">
      <a href="{{ url('/') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-white font-medium hover:bg-indigo-700">Kembali ke Beranda</a>
    </div>
  </div>
@endsection
