@extends('layouts.adminlte')

@section('title', 'Scan QR Absensi')

@section('content_header')
<h1 class="text-2xl font-bold text-gray-800">
  <i class="fas fa-camera text-indigo-600 mr-2"></i> Scan QR Sekolah
</h1>
@stop

@section('content')
<div class="max-w-md mx-auto">

  <div class="bg-white rounded-3xl shadow-lg overflow-hidden relative">
    {{-- Scanner Container --}}
    <div id="reader" class="w-full bg-black"></div>

    <div class="p-6 text-center">
      <h3 class="text-lg font-bold text-gray-800 mb-2">Arahkan Kamera ke Layar Admin</h3>
      <p class="text-xs text-gray-500 mb-4">Pastikan QR Code berada di dalam kotak area scan.</p>

      {{-- Status Messages --}}
      <div id="scan-status" class="hidden mb-4 p-3 rounded-xl text-sm font-bold"></div>

      {{-- Location Info --}}
      <div class="text-xs text-left bg-gray-50 p-3 rounded-lg border border-gray-100 space-y-1">
        <div class="flex justify-between">
          <span class="text-gray-500">Status GPS:</span>
          <span id="gps-status" class="text-amber-500 font-bold"><i class="fas fa-spinner fa-spin"></i>
            Checking...</span>
        </div>
        <div class="flex justify-between hidden" id="dist-info">
          <span class="text-gray-500">Jarak:</span>
          <span id="dist-val" class="font-bold text-gray-800">-</span>
        </div>
      </div>

      <a href="{{ route('teacher.dashboard') }}"
        class="mt-6 block w-full py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition">
        Kembali ke Dashboard
      </a>
    </div>
  </div>

</div>
@stop

@section('js')
{{-- Load Html5-Qrcode Library --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  // Config from Server
  const schoolLat = {{ $schoolLat }};
  const schoolLng = {{ $schoolLng }};
  const schoolRadius = {{ $schoolRadius }};

  let currentLat = 0;
  let currentLng = 0;
  let isScanning = true;

  function onScanSuccess(decodedText, decodedResult) {
    if (!isScanning) return; // Prevent multiple scans

    console.log(`Code matched = ${decodedText}`, decodedResult);

    // Stop scanning to process
    isScanning = false;
    // html5QrcodeScanner.clear(); 

    $('#scan-status').removeClass('hidden bg-red-100 text-red-600 bg-green-100 text-green-600')
      .addClass('bg-indigo-100 text-indigo-600')
      .html('<i class="fas fa-circle-notch fa-spin mr-2"></i> Memproses Absensi...');

    // Send to Server
    $.ajax({
      url: "{{ route('teacher.attendance.store_qr') }}",
      method: "POST",
      data: {
        _token: "{{ csrf_token() }}",
        qr_token: decodedText,
        latitude: currentLat,
        longitude: currentLng
      },
      success: function (response) {
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: response.message,
          timer: 2000,
          showConfirmButton: false
        }).then(() => {
          window.location.href = "{{ route('teacher.dashboard') }}";
        });
      },
      error: function (xhr) {
        isScanning = true; // Allow rescan
        let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi Kesalahan';

        Swal.fire({
          icon: 'error',
          title: 'Gagal Absen',
          text: msg
        });

        $('#scan-status').removeClass('bg-indigo-100 text-indigo-600')
          .addClass('bg-red-100 text-red-600')
          .html('<i class="fas fa-times-circle mr-2"></i> ' + msg);
      }
    });
  }

  function onScanFailure(error) {
    // handle scan failure, usually better to ignore and keep scanning.
    // console.warn(`Code scan error = ${error}`);
  }

  // Initialize Scanner with Html5Qrcode (Core Library) - Better Control
  const html5QrCode = new Html5Qrcode("reader");
  const config = { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 };
  
  // Start Camera Automatically
  html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
    .catch(err => {
        $('#scan-status').removeClass('hidden').addClass('bg-red-100 text-red-600')
        .html('<i class="fas fa-exclamation-triangle mr-1"></i> Gagal akses kamera: ' + err);
        console.error("Camera start error:", err);
    });

  // GPS Logic (Reused)
  function checkLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.watchPosition(
        (position) => {
          currentLat = position.coords.latitude;
          currentLng = position.coords.longitude;

          const dist = getDistanceFromLatLonInM(currentLat, currentLng, schoolLat, schoolLng);

          $('#dist-info').removeClass('hidden');
          $('#dist-val').text(Math.round(dist) + ' meter');

          // Update GPS Status UI
          if (dist <= schoolRadius) {
            $('#gps-status').html('<span class="text-green-600"><i class="fas fa-check-circle"></i> Dalam Jangkauan (' + Math.round(dist) + 'm)</span>');
          } else {
            $('#gps-status').html('<span class="text-red-500"><i class="fas fa-exclamation-triangle"></i> Di Luar Jangkauan (' + Math.round(dist) + 'm)</span>');
          }
        },
        (error) => {
          $('#gps-status').html('<span class="text-red-500">GPS Error/Disabled</span>');
        },
        { enableHighAccuracy: true }
      );
    } else {
      $('#gps-status').text("Browser tidak support GPS");
    }
  }

  // Haversine Algo
  function getDistanceFromLatLonInM(lat1, lon1, lat2, lon2) {
    var R = 6371; // km
    var dLat = deg2rad(lat2 - lat1);
    var dLon = deg2rad(lon2 - lon1);
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
      Math.sin(dLon / 2) * Math.sin(dLon / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return (R * c) * 1000;
  }
  function deg2rad(deg) { return deg * (Math.PI / 180); }

  // Cleanup on leave
  window.addEventListener('beforeunload', () => {
        if (html5QrCode.isScanning) {
            html5QrCode.stop().catch(err => console.log("Stop failed", err));
        }
    });

  // Start
  checkLocation();

</script>
@stop

@section('css')
<style>
  #reader__scan_region img {
    display: none;
  }

  /* Hide decoration image */
</style>
@stop