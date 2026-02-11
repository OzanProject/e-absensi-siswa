@extends('layouts.adminlte')

@section('title', 'Scan Kartu Guru')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
  <div>
    <h1 class="text-2xl font-bold text-gray-800 flex items-center">
      <i class="fas fa-camera text-indigo-600 mr-3"></i> Scan Kartu Guru
    </h1>
    <p class="text-sm text-gray-500 mt-1">Gunakan kamera untuk memindai QR Code pada Kartu Identitas Guru.</p>
  </div>
</div>
@stop

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  {{-- SCANNER SECTION --}}
  <div class="lg:col-span-2 space-y-6">
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
      <div class="p-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white flex justify-between items-center">
        <h3 class="font-bold text-lg"><i class="fas fa-qrcode mr-2"></i> Area Scan</h3>

        {{-- Camera Select --}}
        <div class="relative">
          <select id="camera-select"
            class="bg-white text-gray-800 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 w-48 font-bold cursor-pointer shadow-sm">
            <option value="" disabled selected>Memuat Kamera...</option>
          </select>
        </div>
      </div>

      <div class="p-6 bg-gray-900 flex justify-center items-center min-h-[400px] relative">
        <div id="reader" class="w-full max-w-lg border-4 border-indigo-500/50 rounded-xl overflow-hidden shadow-2xl">
        </div>

        {{-- Status Overlay --}}
        <div id="scan-status"
          class="absolute bottom-10 bg-black/70 text-white px-4 py-2 rounded-full text-sm backdrop-blur-sm hidden">
          <i class="fas fa-spinner fa-spin mr-2"></i> Memproses...
        </div>
      </div>

      <div class="p-4 bg-gray-50 text-center border-t border-gray-100">
        <p class="text-sm text-gray-500">Arahkan QR Code Kartu Guru ke kamera.</p>
      </div>
    </div>
  </div>

  {{-- LOG SECTION --}}
  <div class="space-y-6">
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden h-full flex flex-col">
      <div class="p-5 border-b border-gray-100 bg-gray-50">
        <h3 class="font-bold text-gray-800"><i class="fas fa-history mr-2 text-indigo-500"></i> Riwayat Scan Hari Ini
        </h3>
      </div>
      <div class="flex-1 overflow-y-auto p-0 min-h-[400px] max-h-[600px]" id="log-container">
        {{-- Log Items will be appended here --}}
        <div class="p-8 text-center text-gray-400" id="empty-log">
          <i class="fas fa-clipboard-list text-4xl mb-3 opacity-30"></i>
          <p class="text-sm">Belum ada data scan hari ini.</p>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Audio Elements --}}
<audio id="success-sound" src="{{ asset('audio/beep-success.mp3') }}" preload="auto"></audio>
<audio id="error-sound" src="{{ asset('audio/beep-error.mp3') }}" preload="auto"></audio>

@stop

@section('js')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function () {
    const html5QrCode = new Html5Qrcode("reader");
    let isScanning = true;
    let devicesList = [];

    // Success Callback
    const onScanSuccess = (decodedText, decodedResult) => {
      if (!isScanning) return;

      let nip = decodedText.trim();
      if (!nip) return;

      // Prevent spam scanning
      isScanning = false;

      // Show Processing Toast
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      });
      Toast.fire({
        icon: 'info',
        title: 'Sedang memproses QRCode...'
      });

      // Send to Server
      $.ajax({
        url: "{{ route('admin.scan.teacher.store') }}",
        method: "POST",
        data: {
          _token: "{{ csrf_token() }}",
          nip: nip
        },
        success: function (response) {
          playAudio('success');

          Swal.fire({
            icon: 'success',
            title: response.type == 'IN' ? 'Selamat Datang!' : 'Hati-hati di Jalan!',
            text: response.message,
            timer: 2000,
            showConfirmButton: false
          });

          addLogItem(response.teacher, response.time, response.type, 'Hadir'); // Log success
        },
        error: function (xhr) {
          playAudio('error');
          let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem (' + xhr.status + ')';

          Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: msg,
            // timer: 3000,
            showConfirmButton: true
          });
        },
        complete: function () {
          setTimeout(() => { isScanning = true; }, 2500); // 2.5s delay before next scan
        }
      });
    };

    // Start Scanner with specific Camera ID
    function startScanner(cameraId) {
      const config = { fps: 10, qrbox: { width: 250, height: 250 } };

      // If already scanning, stop first
      if (html5QrCode.isScanning) {
        html5QrCode.stop().then(() => {
          runCamera(cameraId, config);
        }).catch(err => console.error("Failed to stop", err));
      } else {
        runCamera(cameraId, config);
      }
    }

    function runCamera(cameraId, config) {
      html5QrCode.start(
        cameraId,
        config,
        onScanSuccess,
        (errorMessage) => {
          // parse error, ignore 
        }
      ).catch(err => {
        console.error("Error starting scanner", err);
        Swal.fire('Error', 'Gagal mengakses kamera: ' + err, 'error');
      });
    }

    // 1. Get Cameras
    Html5Qrcode.getCameras().then(devices => {
      if (devices && devices.length) {
        devicesList = devices;
        const select = $('#camera-select');
        select.empty();

        devices.forEach(device => {
          select.append(`<option value="${device.id}">${device.label || 'Kamera ' + (select.children().length + 1)}</option>`);
        });

        // Auto-select last camera (usually back camera on mobile)
        // or just select the first one if only one exists
        let initialCameraId = devices[0].id;

        // Try to find "back" or "environment" in label to default to rear camera
        const backCamera = devices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('belakang') || d.label.toLowerCase().includes('environment'));
        if (backCamera) {
          initialCameraId = backCamera.id;
          select.val(initialCameraId);
        }

        // Start Scanner
        startScanner(initialCameraId);
      } else {
        $('#camera-select').html('<option>Tidak ada kamera</option>');
        Swal.fire('Error', 'Tidak ada kamera yang terdeteksi.', 'error');
      }
    }).catch(err => {
      $('#camera-select').html('<option>Error Kamera</option>');
      console.error("Error getting cameras", err);
      Swal.fire('Error', 'Gagal mendeteksi kamera. Pastikan izin diberikan.', 'error');
    });

    // 2. Camera Switch Listener
    $('#camera-select').change(function () {
      const cameraId = $(this).val();
      if (cameraId) {
        startScanner(cameraId);
      }
    });

    // Helper: Play Audio
    function playAudio(type) {
      const audio = document.getElementById(type + '-sound');
      if (audio) audio.play().catch(e => console.log('Audio error', e));
    }

    // Helper: Add Log Item UI
    function addLogItem(name, time, type, status) {
      $('#empty-log').remove();

      const colorClass = type === 'IN' ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : 'text-rose-600 bg-rose-50 border-rose-100';
      const iconClass = type === 'IN' ? 'fa-sign-in-alt' : 'fa-sign-out-alt';

      const html = `
                <div class="flex items-center p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors animate-fade-in-down">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center ${colorClass} mr-4">
                        <i class="fas ${iconClass}"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">${name}</p>
                        <p class="text-xs text-gray-500">${time} • ${status}</p>
                    </div>
                    <div class="ml-auto">
                        <span class="text-xs font-bold px-2 py-1 rounded-md ${colorClass}">${type}</span>
                    </div>
                </div>
            `;

      $('#log-container').prepend(html);
    }


    // Load existing logs (handled by blade loop if passed, but here we start fresh or fetch via ajax)
    // For V1 we just show live logs from current session interactions to keep it simple, 
    // or we could iterate passed variable $todaysAttendances

    @if(isset($todaysAttendances) && count($todaysAttendances) > 0)
      $('#empty-log').remove();
      @foreach($todaysAttendances as $att)
        addLogItem(
          "{{ $att->user->name }}",
          "{{ $att->updated_at->format('H:i:s') }}",
          "{{ $att->clock_out && $att->updated_at->format('H:i') == \Carbon\Carbon::parse($att->clock_out)->format('H:i') ? 'OUT' : 'IN' }}",
          "{{ ucfirst($att->status) }}"
        );
      @endforeach
    @endif
    });
</script>

<style>
  /* Custom Animation for logs */
  @keyframes fadeInDown {
    from {
      opacity: 0;
      transform: translate3d(0, -20px, 0);
    }

    to {
      opacity: 1;
      transform: translate3d(0, 0, 0);
    }
  }

  .animate-fade-in-down {
    animation-name: fadeInDown;
    animation-duration: 0.5s;
  }
</style>
@stop