@extends('layouts.adminlte')

@section('title', 'ID Card Guru')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
  <div>
    <h1 class="text-2xl font-bold text-gray-800 flex items-center">
      <i class="fas fa-id-card text-indigo-600 mr-3"></i> Kartu Identitas Digital
    </h1>
    <p class="text-sm text-gray-500 mt-1">Cetak kartu ini untuk keperluan absensi.</p>
  </div>
</div>
@stop

@section('content')
<div class="flex flex-col items-center justify-center min-h-[400px]">

  {{-- ID CARD PREVIEW --}}
  <div id="id-card"
    class="bg-white w-[350px] h-[550px] rounded-3xl shadow-2xl border border-gray-200 overflow-hidden relative">

    {{-- Background Design --}}
    <div class="absolute top-0 w-full h-40 bg-gradient-to-br from-indigo-600 to-purple-700 rounded-b-[3rem] z-0"></div>

    {{-- Content --}}
    <div class="relative z-10 flex flex-col items-center pt-8 h-full">

      {{-- Logo Sekolah (Optional) --}}
      <div class="mb-4">
        @php
          $settings = \App\Models\Setting::pluck('value', 'key');
          $logoPath = !empty($settings['school_logo']) && file_exists(public_path('storage/' . $settings['school_logo']))
            ? asset('storage/' . $settings['school_logo'])
            : asset('images/default_logo.png');
        @endphp
        <img src="{{ $logoPath }}" class="h-12 w-auto bg-white rounded-lg p-1">
      </div>

      {{-- Photo Profile --}}
      <div class="w-28 h-28 rounded-full border-4 border-white shadow-lg overflow-hidden mb-4 bg-gray-200">
        <img
          src="{{ Auth::user()->photo ? asset('storage/photos/' . Auth::user()->photo) : asset('vendor/adminlte/dist/img/user2-160x160.jpg') }}"
          class="w-full h-full object-cover">
      </div>

      {{-- Name & Role --}}
      <div class="text-center px-4 mb-6">
        <h2 class="text-xl font-bold text-gray-800 leading-tight">{{ Auth::user()->name }}</h2>
        <p class="text-indigo-600 font-semibold text-sm mt-1">{{ Auth::user()->nip ?? 'NIP: -' }}</p>
        <span
          class="inline-block bg-indigo-50 text-indigo-700 text-xs px-3 py-1 rounded-full mt-2 font-bold uppercase tracking-wider">Guru
          / Staf</span>
      </div>

      {{-- QR Code --}}
      <div class="bg-white p-2 rounded-xl border border-gray-100 shadow-sm mb-4">
        {!! $qrCode !!}
      </div>

      <p class="text-xs text-gray-400 mt-auto mb-6">Scan QR ini untuk Absensi</p>

      {{-- Bottom Decor --}}
      <div class="w-full h-2 bg-indigo-500 absolute bottom-0"></div>
    </div>
  </div>

  {{-- ACTION BUTTONS --}}
  <div class="mt-8 flex gap-4 no-print">
    <button onclick="window.print()"
      class="px-6 py-2 bg-indigo-600 text-white rounded-xl shadow-lg hover:bg-indigo-700 transition flex items-center font-bold">
      <i class="fas fa-print mr-2"></i> Cetak Kartu
    </button>
  </div>

</div>
@stop

@section('css')
<style>
  @media print {
    body * {
      visibility: hidden;
    }

    #id-card,
    #id-card * {
      visibility: visible;
    }

    #id-card {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      box-shadow: none;
      border: 1px solid #ddd;
    }

    .main-footer,
    .no-print {
      display: none !important;
    }
  }
</style>
@stop