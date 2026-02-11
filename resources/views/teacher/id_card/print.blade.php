<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cetak ID Card - {{ $user->name }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    body {
      background-color: #f3f4f6;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      padding: 20px;
    }

    /* ID Card Container */
    #id-card {
      width: 53.98mm;
      height: 85.60mm;
      background: white;
      position: relative;
      overflow: hidden;
      border-radius: 1.5rem;
      /* rounded-3xl approx */
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
      border: 1px dashed #d1d5db;
      /* Visual Guide */
    }

    .no-print {
      position: fixed;
      bottom: 20px;
      right: 20px;
    }

    @media print {
      body {
        background: white;
        margin: 0;
        padding: 0;
      }

      #id-card {
        border: none;
        /* Remove border on actual print if desired, or keep as cut guide */
        box-shadow: none;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border: 1px dashed #ccc;
        /* Cut guide */
      }

      .no-print {
        display: none;
      }

      /* Force Graphics */
      * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
    }
  </style>
</head>

<body>

  <div id="id-card" class="bg-white overflow-hidden relative">
    {{-- Background --}}
    <div class="absolute top-0 w-full h-24 bg-gradient-to-br from-indigo-600 to-purple-700 rounded-b-[2rem] z-0"></div>

    {{-- Content --}}
    <div class="relative z-10 flex flex-col items-center pt-6 h-full">

      {{-- Logo --}}
      <div class="mb-2">
        @php
          $settings = \App\Models\Setting::pluck('value', 'key');
          $logoPath = !empty($settings['school_logo']) && file_exists(public_path('storage/' . $settings['school_logo']))
            ? asset('storage/' . $settings['school_logo'])
            : asset('images/default_logo.png');
        @endphp
        <img src="{{ $logoPath }}" class="h-10 w-auto bg-white rounded-lg p-0.5 shadow-sm">
      </div>

      {{-- Photo --}}
      <div class="w-24 h-24 rounded-full border-4 border-white shadow-md overflow-hidden mb-2 bg-gray-200">
        <img
          src="{{ $user->photo ? asset('storage/photos/' . $user->photo) : asset('vendor/adminlte/dist/img/user2-160x160.jpg') }}"
          class="w-full h-full object-cover">
      </div>

      {{-- Info --}}
      <div class="text-center px-2 mb-2 w-full">
        <h2 class="text-lg font-bold text-gray-800 leading-tight truncate px-4">{{ $user->name }}</h2>
        <p class="text-indigo-600 font-semibold text-xs mt-0.5">{{ $user->nip ?? '-' }}</p>
        <span
          class="inline-block bg-indigo-50 text-indigo-700 text-[10px] px-2 py-0.5 rounded-full mt-1 font-bold uppercase">Guru
          / Staf</span>
      </div>

      {{-- QR --}}
      <div class="bg-white p-1 rounded-lg border border-gray-100 shadow-sm mt-auto mb-4">
        {{-- Resize QR for PDF/Print --}}
        <div class="w-24 h-24 flex items-center justify-center">
          {{-- Note: Re-generating QR here with smaller size to fit --}}
          {!! QrCode::size(90)->generate($user->nip && $user->nip != '-' ? $user->nip : (string) $user->id) !!}
        </div>
      </div>

      <p class="text-[8px] text-gray-400 absolute bottom-1">Scan untuk Absensi</p>

      {{-- Bottom Decor --}}
      <div class="w-full h-1.5 bg-indigo-500 absolute bottom-0"></div>
    </div>
  </div>

  <div class="no-print space-x-2">
    <button onclick="window.print()"
      class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow font-bold hover:bg-indigo-700">Cetak</button>
    <button onclick="window.close()"
      class="bg-gray-500 text-white px-4 py-2 rounded-lg shadow font-bold hover:bg-gray-600">Tutup</button>
  </div>

  <script>
    window.onload = function () {
      setTimeout(function () {
        window.print();
      }, 500);
    }
  </script>
</body>

</html>