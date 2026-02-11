@extends('layouts.adminlte')

@section('title', 'Monitor QR Absensi')

@section('content_header')
<div class="flex justify-between items-center">
  <h1 class="text-2xl font-bold text-gray-800">
    <i class="fas fa-qrcode text-indigo-600 mr-2"></i> Monitor Absensi
  </h1>
  <div class="text-right">
    <h2 id="clock" class="text-3xl font-mono font-bold text-gray-700">00:00:00</h2>
    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
  </div>
</div>
@stop

@section('content')
<div class="relative">
  {{-- Fullscreen Button --}}
  <button onclick="toggleFullscreen()"
    class="absolute top-4 right-4 z-50 bg-white text-gray-600 hover:text-indigo-600 p-2 rounded-full shadow-lg border border-gray-200 transition-transform hover:scale-110">
    <i class="fas fa-expand text-xl"></i>
  </button>

  <div id="kiosk-container"
    class="flex flex-col items-center justify-center min-h-[70vh] bg-gradient-to-br from-indigo-50 to-purple-50 rounded-3xl shadow-inner border border-gray-200 p-8 transition-all duration-300">

    <div
      class="bg-white p-10 rounded-[3rem] shadow-2xl flex flex-col items-center relative overflow-hidden max-w-lg w-full transform hover:scale-[1.01] transition-transform duration-500">

      <div class="absolute top-0 w-full h-3 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 animate-pulse">
      </div>

      <h3 class="text-2xl font-black text-gray-800 mb-2 tracking-tight">SCAN DISINI</h3>
      <p class="text-gray-500 text-sm mb-6 font-medium">Arahkan kamera HP ke kode di bawah</p>

      {{-- QR CONTAINER --}}
      <div id="qr-container"
        class="mb-8 p-1 bg-white rounded-2xl shadow-[0_0_40px_rgba(99,102,241,0.15)] border-4 border-indigo-50 relative group">
        <div
          class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
        </div>
        <div class="flex items-center justify-center h-[320px] w-[320px] bg-white rounded-xl overflow-hidden relative">
          {{-- Loading State --}}
          <div id="qr-loading" class="absolute inset-0 flex flex-col items-center justify-center bg-white z-10">
            <div class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mb-4"></div>
            <span class="text-xs font-bold text-indigo-400 animate-pulse">Memuat Kode...</span>
          </div>
          {{-- QR Content --}}
          <div id="qr-content" class="opacity-0 transition-opacity duration-300"></div>
        </div>

        {{-- Corner Accents --}}
        <div class="absolute -top-2 -left-2 w-8 h-8 border-t-4 border-l-4 border-indigo-500 rounded-tl-xl"></div>
        <div class="absolute -top-2 -right-2 w-8 h-8 border-t-4 border-r-4 border-indigo-500 rounded-tr-xl"></div>
        <div class="absolute -bottom-2 -left-2 w-8 h-8 border-b-4 border-l-4 border-indigo-500 rounded-bl-xl"></div>
        <div class="absolute -bottom-2 -right-2 w-8 h-8 border-b-4 border-r-4 border-indigo-500 rounded-br-xl"></div>
      </div>

      {{-- TIMER / STATUS --}}
      <div class="w-full bg-gray-100 rounded-full h-3 mb-6 relative overflow-hidden shadow-inner">
        <div id="progress-bar"
          class="bg-gradient-to-r from-indigo-500 to-purple-600 h-3 rounded-full transition-all duration-100 ease-linear"
          style="width: 100%"></div>
      </div>

      <div
        class="flex items-center space-x-2 text-sm text-gray-500 bg-gray-50 px-4 py-2 rounded-full border border-gray-100">
        <i class="fas fa-sync-alt fa-spin text-indigo-400 text-xs"></i>
        <span>Refresh otomatis dalam <span id="timer-text"
            class="font-bold text-indigo-700 font-mono text-base">15</span> detik</span>
      </div>

    </div>

    <div class="mt-8 text-center text-gray-500 max-w-md">
      <div
        class="inline-flex items-center px-4 py-2 bg-white rounded-full shadow-sm border border-gray-200 text-xs font-bold text-indigo-600 mb-3">
        <i class="fas fa-wifi mr-2"></i> Pastikan terhubung ke WiFi Sekolah
      </div>
      <p class="text-xs opacity-70">Gunakan menu <strong>"Scan QR Sekolah"</strong> pada aplikasi Guru untuk melakukan
        absensi.</p>
    </div>

  </div>
</div>

<style>
  /* Fullscreen Mode Styles */
  body.fullscreen-mode .main-header,
  body.fullscreen-mode .main-sidebar,
  body.fullscreen-mode .content-header,
  body.fullscreen-mode .main-footer {
    display: none !important;
  }

  body.fullscreen-mode .content-wrapper {
    margin-left: 0 !important;
    padding: 0 !important;
    background: #f3f4f6;
    /* gray-100 */
    height: 100vh;
  }

  body.fullscreen-mode .content {
    padding: 0 !important;
    height: 100%;
  }

  body.fullscreen-mode #kiosk-container {
    height: 100vh;
    border-radius: 0;
    border: none;
  }
</style>
@stop

@section('js')
<script>
  $(document).ready(function () {

    // Clock
    setInterval(() => {
      const now = new Date();
      $('#clock').text(now.toLocaleTimeString('id-ID', { hour12: false }));
    }, 1000);

    // QR Logic
    const REFRESH_INTERVAL = 15000; // 15 Seconds
    let timeLeft = REFRESH_INTERVAL;

    function loadQR() {
      // Show loading
      $('#qr-loading').fadeIn(200);
      $('#qr-content').css('opacity', 0);

      $.ajax({
        url: "{{ route('admin.qrcode.generate') }}",
        method: "GET",
        success: function (response) {
          // Render QR
          $('#qr-content').html(response.qr_code);

          // Fade In
          $('#qr-loading').fadeOut(200, function () {
            $('#qr-content').css('opacity', 1);
          });

          resetTimer();
        },
        error: function (err) {
          console.error("Gagal memuat QR:", err);
          $('#qr-content').html('<div class="flex flex-col items-center justify-center h-full text-red-500 font-bold p-4 text-center"><i class="fas fa-exclamation-triangle text-3xl mb-2"></i><span class="text-xs">Gagal memuat QR.<br>Periksa koneksi internet.</span></div>');
          $('#qr-loading').hide();
          $('#qr-content').css('opacity', 1);
        }
      });
    }

    function resetTimer() {
      timeLeft = REFRESH_INTERVAL;
      $('#progress-bar').css('width', '100%');
      $('#progress-bar').removeClass('bg-red-500').addClass('from-indigo-500 to-purple-600');
    }

    // Timer countdown animation
    setInterval(() => {
      timeLeft -= 100;
      const percent = (timeLeft / REFRESH_INTERVAL) * 100;
      $('#progress-bar').css('width', percent + '%');

      // Update text seconds
      $('#timer-text').text(Math.ceil(timeLeft / 1000));

      if (percent < 20) {
        $('#progress-bar').removeClass('from-indigo-500 to-purple-600').addClass('bg-red-500');
      }

      if (timeLeft <= 0) {
        loadQR();
      }
    }, 100);

    // Initial Load
    loadQR();

    // Fullscreen Toggle
    window.toggleFullscreen = function () {
      if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch((e) => {
          console.log("Fullscreen denied", e);
        });
        $('body').addClass('fullscreen-mode');
        $('button i.fa-expand').removeClass('fa-expand').addClass('fa-compress');
      } else {
        if (document.exitFullscreen) {
          document.exitFullscreen();
        }
        $('body').removeClass('fullscreen-mode');
        $('button i.fa-compress').removeClass('fa-compress').addClass('fa-expand');
      }
    };

    // Listen to escape key or other exit methods
    document.addEventListener('fullscreenchange', (event) => {
      if (!document.fullscreenElement) {
        $('body').removeClass('fullscreen-mode');
        $('button i.fa-compress').removeClass('fa-compress').addClass('fa-expand');
      } else {
        $('body').addClass('fullscreen-mode');
      }
    });

  });
</script>
@stop