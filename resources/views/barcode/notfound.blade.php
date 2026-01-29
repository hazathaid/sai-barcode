<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Barcode Tidak Ditemukan</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>body{font-family:system-ui,Segoe UI,Helvetica,Arial;background:#fff;color:#111;padding:48px;text-align:center}a{color:#2563eb}</style>
</head>
<body>
@extends('layouts.app')

@section('title','Tidak Ditemukan')

@section('content')
  <div class="bg-white border border-gray-200 rounded-lg p-6 text-center shadow-sm">
    <h1 class="text-2xl font-semibold mb-2">Tidak Ditemukan</h1>
    <p class="text-gray-600">Kami tidak menemukan tiket untuk:</p>
    <p class="mt-3 font-mono text-sm text-gray-800">{{ $email }} &middot; {{ $phone }}</p>
    <p class="mt-4 text-gray-600">Periksa kembali data yang kamu masukkan atau hubungi penyelenggara acara.</p>
    <p class="mt-6"><a href="{{ route('barcode.index') }}" class="text-indigo-600 hover:underline">Kembali ke pencarian</a></p>
  </div>
@endsection
</body>
</html>
