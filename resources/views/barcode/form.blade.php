@extends('layouts.app')

@section('title','Cari Barcode')

@section('content')
  <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
    <h1 class="text-center text-2xl font-semibold">Cari Barcode Anda</h1>
    <p class="text-center text-sm text-gray-600">Masukkan email dan/atau nomor telepon yang kamu gunakan saat mendaftar.</p>

    <form method="get" action="{{ route('barcode.index') }}" class="mt-4 space-y-4">
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email (boleh kosong jika pakai phone)</label>
        <input id="email" name="email" type="email" value="{{ old('email', request('email')) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200">
      </div>

      <div>
        <label for="phone" class="block text-sm font-medium text-gray-700">Phone (boleh kosong jika pakai email)</label>
        <input id="phone" name="phone" type="text" value="{{ old('phone', request('phone')) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200">
      </div>

      <div class="text-center">
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg">Cari</button>
      </div>
    </form>

    @if($errors->any())
      <div class="mt-4 text-red-700 text-sm">{{ $errors->first() }}</div>
    @endif
  </div>
@endsection
