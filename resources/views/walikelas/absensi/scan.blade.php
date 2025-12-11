@extends('layouts.adminlte')

@section('title', 'Scan Absensi Kelas')

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Scan Absensi Harian</h2>
            <p class="text-sm text-gray-500 mt-1">Kelas: <span class="font-bold text-indigo-600">{{ $class->name ?? 'N/A' }}</span></p>
        </div>
        <nav class="flex text-sm font-medium text-gray-500 space-x-2" aria-label="Breadcrumb">
            <a href="{{ route('walikelas.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition">Dashboard</a>
            <span class="text-gray-400">/</span>
            <span class="text-gray-600">Scan Absensi</span>
        </nav>
    </div>

    {{-- MAIN CONTENT GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- KOLOM KIRI: SCANNER (7/12) --}}
        <div class="lg:col-span-7 space-y-6">
            {{-- Instructions Card --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                <div class="flex items-start md:items-center">
                    <div class="bg-white/20 p-3 rounded-xl mr-4 backdrop-blur-sm">
                        <i class="fas fa-qrcode text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Mode Pindai Aktif</h3>
                        <p class="text-blue-100 text-sm opacity-90">
                            Arahkan kartu pelajar siswa ke kamera. Sistem akan mencatat Masuk/Pulang secara otomatis.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Scanner Card --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden relative">
                <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap sm:flex-nowrap justify-between items-center bg-gray-50/50">
                    {{-- Left: Title --}}
                    <div class="flex items-center">
                        <h3 class="font-bold text-gray-800 flex items-center text-lg">
                            <span class="w-1.5 h-6 bg-indigo-500 rounded-full mr-3"></span>
                            Kamera Scanner
                        </h3>
                    </div>

                    {{-- Right: Status --}}
                    <div id="camera-status-indicator" class="flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 text-gray-400 rounded-lg text-xs font-bold shadow-sm transition-all duration-300">
                         <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                         <span>Offline</span>
                    </div>
                </div>
                
                <div class="p-6">
                    {{-- SCANNER CONTAINER --}}
                    <div class="relative bg-black rounded-2xl overflow-hidden shadow-inner mx-auto max-w-[500px] aspect-square sm:aspect-[4/3]">
                        <div id="scanner" class="w-full h-full object-cover"></div>
                        
                        {{-- Overlay Guide --}}
                        <div class="absolute inset-0 border-2 border-white/30 rounded-2xl pointer-events-none"></div>
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-64 h-64 border-2 border-dashed border-indigo-400/70 rounded-xl relative">
                                <div class="absolute top-0 left-0 w-4 h-4 border-t-4 border-l-4 border-indigo-500 -mt-1 -ml-1"></div>
                                <div class="absolute top-0 right-0 w-4 h-4 border-t-4 border-r-4 border-indigo-500 -mt-1 -mr-1"></div>
                                <div class="absolute bottom-0 left-0 w-4 h-4 border-b-4 border-l-4 border-indigo-500 -mb-1 -ml-1"></div>
                                <div class="absolute bottom-0 right-0 w-4 h-4 border-b-4 border-r-4 border-indigo-500 -mb-1 -mr-1"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Status Message --}}
                    <div id="scan-status" class="mt-4 hidden transform transition-all duration-300 ease-in-out">
                        {{-- Content injected by JS --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: LIVE LOG (5/12) --}}
        <div class="lg:col-span-5">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 h-full flex flex-col">
                <div class="p-6 border-b border-gray-100 bg-gray-50/30">
                    <h3 class="font-bold text-gray-800 flex items-center">
                        <i class="fas fa-history text-indigo-500 mr-2"></i> Riwayat Scan Hari Ini
                    </h3>
                </div>
                
                {{-- SCROLLABLE LOG AREA --}}
                <div class="flex-1 overflow-y-auto p-4 custom-scrollbar" style="max-height: 600px;">
                    <ul class="space-y-3" id="attendance-log">
                        @forelse($recentAbsences as $absence)
                            @php
                                $status = $absence->status;
                                $isCheckout = !is_null($absence->checkout_time);
                                $displayStatus = $isCheckout ? 'PULANG' : ($status == 'Terlambat' ? 'TERLAMBAT' : 'MASUK');
                                
                                $cardColor = match($displayStatus) {
                                    'PULANG' => 'bg-blue-50 border-blue-100',
                                    'TERLAMBAT' => 'bg-amber-50 border-amber-100',
                                    'MASUK' => 'bg-green-50 border-green-100',
                                    default => 'bg-gray-50 border-gray-100'
                                };
                                $iconColor = match($displayStatus) {
                                    'PULANG' => 'text-blue-600 bg-blue-100',
                                    'TERLAMBAT' => 'text-amber-600 bg-amber-100',
                                    'MASUK' => 'text-green-600 bg-green-100',
                                    default => 'text-gray-600 bg-gray-100'
                                };
                                $icon = match($displayStatus) {
                                    'PULANG' => 'fa-door-open',
                                    'TERLAMBAT' => 'fa-exclamation-triangle',
                                    default => 'fa-check'
                                };
                                $time = $isCheckout ? $absence->checkout_time->format('H:i') : $absence->attendance_time->format('H:i');
                            @endphp
                            
                            <li class="p-3 rounded-xl border {{ $cardColor }} hover:shadow-md transition-all duration-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full {{ $iconColor }} flex items-center justify-center flex-shrink-0">
                                        <i class="fas {{ $icon }}"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate">{{ $absence->student->name }}</p>
                                        <div class="flex items-center text-xs space-x-2 mt-0.5">
                                            <span class="font-bold opacity-80">{{ $displayStatus }}</span>
                                            <span class="text-gray-400">•</span>
                                            <span class="text-gray-500 font-mono">{{ $time }}</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-center py-10" id="empty-log-msg">
                                <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-clipboard-list text-gray-300 text-2xl"></i>
                                </div>
                                <p class="text-gray-400 text-sm">Belum ada aktivitas scan.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    {{-- AUDIO --}}
    <audio id="scanSuccessAudio" src="{{ asset('assets/audio/scan-beep.mp3') }}" preload="auto"></audio>
</div>
@stop

@section('js')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<script>
    // --- KONFIGURASI ---
    const scanUrl = '{{ route("walikelas.absensi.record") }}'; 
    const csrfToken = '{{ csrf_token() }}';
    const scanDelay = 2500; // 2.5 detik
    let lastScanTime = 0;
    let isProcessing = false;
    
    // --- ELEMENTS ---
    const html5QrCode = new Html5Qrcode("scanner"); 
    const scanStatus = $('#scan-status');
    const audio = document.getElementById('scanSuccessAudio'); 
    const attendanceLog = $('#attendance-log');
    const cameraIndicator = $('#camera-status-indicator');

    // --- HELPER: UPDATE CAMERA STATUS ---
    function updateCameraStatus(status) {
        let color = 'bg-gray-300';
        let text = 'Offline';
        if(status === 'active') { color = 'bg-green-500 animate-pulse'; text = 'Live'; }
        if(status === 'error') { color = 'bg-red-500'; text = 'Error'; }
        
        cameraIndicator.html(`
            <span class="w-2 h-2 rounded-full ${color}"></span>
            <span class="${status === 'active' ? 'text-green-600' : 'text-gray-400'}">${text}</span>
        `);
    }

    // --- HELPER: ADD LOG ITEM (ANIMATED) ---
    function addLogItem(data) {
        // Hapus empty state jika ada
        $('#empty-log-msg').remove();

        const typeMap = {
            'IN': { 
                'Hadir': { status: 'MASUK', subClass: 'bg-green-50 border-green-100', iconClass: 'bg-green-100 text-green-600', icon: 'fa-check' },
                'Terlambat': { status: 'TERLAMBAT', subClass: 'bg-amber-50 border-amber-100', iconClass: 'bg-amber-100 text-amber-600', icon: 'fa-exclamation-triangle' }
            },
            'OUT': { 
                'default': { status: 'PULANG', subClass: 'bg-blue-50 border-blue-100', iconClass: 'bg-blue-100 text-blue-600', icon: 'fa-door-open' }
            }
        };

        const config = (data.type === 'IN') ? typeMap['IN'][data.status] : typeMap['OUT']['default'];
        const time = new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});

        const itemHtml = `
            <li class="p-3 rounded-xl border ${config.subClass} hover:shadow-md transition-all duration-500 transform translate-y-[-10px] opacity-0" id="new-log-item">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full ${config.iconClass} flex items-center justify-center flex-shrink-0">
                        <i class="fas ${config.icon}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 truncate">${data.student.name}</p>
                        <div class="flex items-center text-xs space-x-2 mt-0.5">
                            <span class="font-bold opacity-80">${config.status}</span>
                            <span class="text-gray-400">•</span>
                            <span class="text-gray-500 font-mono">${time}</span>
                        </div>
                    </div>
                </div>
            </li>
        `;
        
        attendanceLog.prepend(itemHtml);
        
        // Trigger Animation
        setTimeout(() => {
            $('#new-log-item').removeClass('translate-y-[-10px] opacity-0').removeAttr('id');
        }, 50);
    }

    // --- MAIN LOGIC: PROCESS BARCODE ---
    function processBarcode(code) {
        if(isProcessing) return;
        isProcessing = true;
        
        // UI Feedback: Processing
        scanStatus.removeClass('hidden bg-green-50 bg-red-50 bg-amber-50 text-green-700 text-red-700 text-amber-700')
                  .addClass('block bg-blue-50 text-blue-700 p-4 rounded-xl border border-blue-100 shadow-sm flex items-center justify-center')
                  .html('<i class="fas fa-spinner fa-spin mr-2"></i> Memproses Data...');

        $.ajax({
            url: scanUrl,
            method: 'POST',
            data: { _token: csrfToken, barcode: code },
            success: function(res) {
                // Audio Feedback
                if(audio) { audio.currentTime = 0; audio.play().catch(()=>{}); }

                // Success UI
                let colorClass = res.status === 'Terlambat' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-green-50 text-green-700 border-green-200';
                let icon = res.status === 'Terlambat' ? 'fa-exclamation-triangle' : 'fa-check-circle';
                
                scanStatus.removeClass('bg-blue-50 text-blue-700 border-blue-100')
                          .addClass(colorClass)
                          .html(`<i class="fas ${icon} mr-2"></i> <span class="font-bold">${res.message}</span>`);

                // Toast
                const toastType = res.status === 'Terlambat' ? 'warning' : 'success';
                Swal.fire({
                    icon: toastType,
                    title: 'Berhasil',
                    text: res.message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });

                // Update Log
                addLogItem(res);
            },
            error: function(xhr) {
                let msg = xhr.responseJSON?.message || 'Gagal memproses data';
                let isWarning = xhr.status === 409; // 409 usually means "Already Scanned" or "Too Early"
                
                let colorClass = isWarning ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-red-50 text-red-700 border-red-200';
                let icon = isWarning ? 'fa-exclamation-circle' : 'fa-times-circle';

                scanStatus.removeClass('bg-blue-50 text-blue-700 border-blue-100')
                          .addClass(colorClass)
                          .html(`<i class="fas ${icon} mr-2"></i> <span class="font-bold">${msg}</span>`);
                
                Swal.fire({
                    icon: isWarning ? 'warning' : 'error',
                    title: isWarning ? 'Perhatian' : 'Gagal',
                    text: msg,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            },
            complete: function() {
                // Resume Scanning after delay
                setTimeout(() => {
                    isProcessing = false;
                    scanStatus.addClass('hidden'); // Hide status to clean UI
                    
                    if(html5QrCode.isScanning()) {
                        html5QrCode.resume();
                    }
                }, scanDelay);
            }
        });
    }

    // --- QR CODE CALLBACK ---
    function onScanSuccess(decodedText, decodedResult) {
        let currentTime = new Date().getTime();
        if(currentTime - lastScanTime > scanDelay) {
            lastScanTime = currentTime;
            html5QrCode.pause(); // Pause camera visually
            processBarcode(decodedText);
        }
    }

    // --- INITIALIZATION (ROBUST SIMPLE VERSION) ---
    $(document).ready(function() {
        const config = { 
            fps: 10, 
            qrbox: { width: 250, height: 250 }, 
            aspectRatio: 1.0 
        };

        // Langsung start dengan konfigurasi environment (Kamera Belakang)
        // Ini lebih stabil di berbagai browser/perangkat dibanding getCameras()
        html5QrCode.start(
            { facingMode: "environment" }, 
            config, 
            onScanSuccess
        ).then(() => {
            updateCameraStatus('active');
        }).catch(err => {
            console.error("Camera Error: ", err);
            updateCameraStatus('error');
            Swal.fire({
                icon: 'error',
                title: 'Kamera Gagal Akses',
                text: 'Pastikan izin kamera diberikan dan akses via HTTPS atau Localhost.',
                footer: err.message
            });
        });
    });

    // Cleanup
    $(window).on('beforeunload', function(){
        if(html5QrCode.isScanning()){
            html5QrCode.stop().catch(()=>{});
        }
    });

</script>
@endsection

@section('css')
<style>
    /* Custom Scrollbar for Log */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    
    /* Animation Utility */
    @keyframes slideDownFade {
        from { transform: translateY(-10px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>
@endsection