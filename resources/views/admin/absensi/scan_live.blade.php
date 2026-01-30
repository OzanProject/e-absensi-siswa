@extends('layouts.adminlte')

@section('title', 'Scan Absensi Real-time')

@section('content_header')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-2 sm:mt-4 mb-2">
        <div
            class="flex flex-col md:flex-row justify-between items-center bg-white rounded-xl sm:rounded-3xl p-4 sm:p-6 shadow-lg border border-gray-100">
            {{-- Title Area --}}
            <div class="flex items-center space-x-4 w-full md:w-auto mb-4 md:mb-0">
                <div class="bg-indigo-600 p-3 rounded-xl shadow-lg flex-shrink-0 text-white">
                    <i class="fas fa-qrcode text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
                        Scan Absensi
                    </h1>
                    <p class="hidden sm:block text-sm font-medium text-gray-500">
                        <i class="fas fa-satellite-dish text-indigo-500 mr-1"></i> Mode Pemindaian Real-time
                    </p>
                </div>
            </div>

            {{-- Right Actions --}}
            <div class="flex items-center space-x-4 w-full md:w-auto justify-end">
                <div class="hidden sm:block text-right mr-2">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Waktu Server</span>
                    <span class="text-xl font-mono font-bold text-indigo-600 leading-none"
                        id="server-time-display">{{ date('H:i') }}</span>
                </div>

                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-full text-gray-500 hover:bg-gray-200 hover:text-indigo-600 transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- LEFT COLUMN: SCANNER AREA (7/12) --}}
            <div class="lg:col-span-7 space-y-6">
                {{-- Clean Camera Card --}}
                <div
                    class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 relative group text-center">

                    {{-- Camera Wrapper --}}
                    <div class="relative bg-black rounded-3xl overflow-hidden h-[400px] sm:h-[500px] group shadow-inner border border-gray-800">
                        {{-- 1. Scanner Video Element (Base Layer) --}}
                        <div id="scanner" class="w-full h-full absolute inset-0 z-10"></div>

                        {{-- 2. Top Bar Overlay (Z-Index 30) --}}
                         <div class="absolute top-0 left-0 w-full p-4 z-30 flex justify-between items-start pointer-events-none">
                            {{-- GPS Indicator --}}
                            <div id="gps-indicator-area" class="pointer-events-auto"></div>

                            {{-- Live Badge --}}
                            <div class="bg-red-500/20 backdrop-blur-md border border-red-500/50 text-white text-[10px] font-bold px-3 py-1 rounded-full flex items-center shadow-lg animate-pulse">
                                <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div> LIVE
                            </div>
                         </div>

                        {{-- 3. Camera Selector (Hidden by default, can be toggled) --}}
                         <div id="camera-selector-modal" class="hidden absolute top-14 right-4 z-40 bg-white p-2 rounded-lg shadow-xl">
                            <select id="camera-select" class="text-xs border-gray-200 rounded-md focus:ring-indigo-500"></select>
                         </div>


                        {{-- 4. Center Viewfinder Overlay (Z-Index 20) --}}
                        <div class="absolute inset-0 pointer-events-none z-20 flex flex-col items-center justify-center">
                            
                            {{-- Scanning Box --}}
                            <div class="w-64 h-64 sm:w-72 sm:h-72 border-2 border-white/50 rounded-3xl relative overflow-hidden backdrop-blur-[2px] shadow-[0_0_100px_rgba(0,0,0,0.3)]">
                                {{-- Corner Accents --}}
                                <div class="absolute top-0 left-0 w-10 h-10 border-t-4 border-l-4 border-indigo-400 rounded-tl-2xl"></div>
                                <div class="absolute top-0 right-0 w-10 h-10 border-t-4 border-r-4 border-indigo-400 rounded-tr-2xl"></div>
                                <div class="absolute bottom-0 left-0 w-10 h-10 border-b-4 border-l-4 border-indigo-400 rounded-bl-2xl"></div>
                                <div class="absolute bottom-0 right-0 w-10 h-10 border-b-4 border-r-4 border-indigo-400 rounded-br-2xl"></div>

                                {{-- Laser --}}
                                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-indigo-400 to-transparent opacity-80 animate-scan shadow-[0_0_20px_rgba(99,102,241,0.8)]"></div>
                            </div>
                            
                            {{-- Instruction Text --}}
                            <p class="mt-6 text-white/90 text-xs font-semibold bg-black/40 px-4 py-1.5 rounded-full backdrop-blur-md border border-white/10 tracking-wide">
                                <i class="fas fa-expand-arrows-alt mr-1"></i> Arahkan QR Code ke dalam kotak
                            </p>
                        </div>
                    </div>

                    {{-- Footer Status --}}
                    <div class="px-6 py-4 bg-white border-t border-gray-100 z-30 relative min-h-[5rem]">
                        <div id="scan-status">
                            {{-- Default State --}}
                            <div
                                class="flex items-center justify-center p-2 text-gray-500 animate__animated animate__fadeIn">
                                <i class="fas fa-camera mr-2"></i> Menunggu kamera siap...
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tips Card --}}
                <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 flex items-start space-x-4">
                    <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600 flex-shrink-0">
                        <i class="fas fa-info-circle text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-indigo-900 text-sm">Informasi Penggunaan</h4>
                        <p class="text-indigo-800/80 text-xs mt-1 leading-relaxed">
                            Sistem akan otomatis mencatat kehadiran saat QR Code valid terdeteksi.
                            Pastikan fitur lokasi (GPS) pada browser Anda diaktifkan untuk validasi jarak.
                        </p>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: RECENT ACTIVITY LOG (5/12) --}}
            <div class="lg:col-span-5 h-full">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 h-full flex flex-col overflow-hidden relative"
                    style="max-height: calc(100vh - 200px);">
                    {{-- Log Header --}}
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 flex items-center">
                            <i class="fas fa-history text-gray-400 mr-2"></i> Aktivitas Terbaru
                        </h3>
                        <button onclick="location.reload()"
                            class="text-xs bg-white border border-gray-200 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 px-3 py-1.5 rounded-lg transition duration-200 shadow-sm">
                            Refresh
                        </button>
                    </div>

                    {{-- Scrollable List Area --}}
                    <div class="flex-1 overflow-y-auto bg-gray-50/30 p-4" id="attendance-log-container">
                        <ul id="attendance-log" class="space-y-3">
                            @forelse($recentAbsences as $absence)
                                @php
                                    $status = $absence->status;
                                    $isOut = $absence->checkout_time != null;
                                    $isLate = $status == 'Terlambat';
                                    $time = $isOut ? $absence->checkout_time->format('H:i') : $absence->attendance_time->format('H:i');

                                    // Style Config
                                    if ($isOut) {
                                        $style = ['border' => 'border-purple-500', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'icon' => 'fa-door-open', 'label' => 'PULANG'];
                                    } elseif ($isLate) {
                                        $style = ['border' => 'border-amber-500', 'bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'fa-exclamation', 'label' => 'TERLAMBAT'];
                                    } else {
                                        $style = ['border' => 'border-emerald-500', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'fa-check', 'label' => 'MASUK'];
                                    }

                                    // Check distance if available
                                    $distance = $absence->distance ?? 0;
                                @endphp
                                <li
                                    class="bg-white p-3 rounded-xl border-l-4 {{ $style['border'] }} shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div class="flex space-x-3">
                                            <div
                                                class="w-10 h-10 rounded-full {{ $style['bg'] }} flex items-center justify-center {{ $style['text'] }}">
                                                <i class="fas {{ $style['icon'] }}"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800 text-sm">
                                                    {{ $absence->student->name ?? 'Siswa' }}</p>
                                                <p class="text-xs text-gray-500">{{ $absence->student->class->name ?? '-' }}</p>
                                                @if($distance > 0)
                                                    <p class="text-[10px] text-gray-400 mt-1"><i
                                                            class="fas fa-map-marker-alt mr-1"></i> {{ $distance }}m</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span
                                                class="text-xs font-bold {{ $style['text'] }} block">{{ $style['label'] }}</span>
                                            <span class="text-xs text-gray-400 font-mono">{{ $time }}</span>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="flex flex-col items-center justify-center py-10 text-gray-400">
                                    <i class="fas fa-clipboard text-4xl mb-2 opacity-20"></i>
                                    <span class="text-sm">Belum ada data hari ini</span>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    {{-- Libraries --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // --- PREVENT CONSOLE SPAM FROM EXTENSIONS ---
        window.addEventListener('error', function (e) {
            if (e.filename && e.filename.includes('content.js')) {
                e.preventDefault(); // Suppress extension errors
            }
        });

        // --- CONFIG ---
        const scanUrl = '{{ route("admin.absensi.record") }}';
        const csrfToken = '{{ csrf_token() }}';
        const RESCAN_DELAY = 2500;
        let isProcessing = false;
        let currentLat = null;
        let currentLng = null;

        // --- UI TEMPLATES ---
        const UI = {
            ready: `
                    <div class="flex items-center justify-center p-3 text-emerald-600 bg-emerald-50 rounded-lg border border-emerald-100 animate__animated animate__fadeIn">
                        <i class="fas fa-check-circle text-xl mr-2"></i>
                        <div>
                            <span class="font-bold text-sm block">Sistem Siap</span>
                            <span class="text-xs opacity-80">Silakan scan kartu siswa</span>
                        </div>
                    </div>`,
            processing: `
                    <div class="flex items-center justify-center p-3 text-indigo-600 bg-indigo-50 rounded-lg border border-indigo-100 animate__animated animate__fadeIn">
                        <i class="fas fa-circle-notch fa-spin text-xl mr-2"></i>
                        <div>
                            <span class="font-bold text-sm block">Memproses...</span>
                            <span class="text-xs opacity-80">Mohon tunggu sebentar</span>
                        </div>
                    </div>`,
            error: (msg) => `
                    <div class="flex items-center justify-center p-3 text-red-600 bg-red-50 rounded-lg border border-red-100 animate__animated animate__headShake">
                        <i class="fas fa-exclamation-triangle text-xl mr-2"></i>
                        <div>
                            <span class="font-bold text-sm block">Gagal</span>
                            <span class="text-xs opacity-80">${msg}</span>
                        </div>
                    </div>`
        };

        const scanStatus = $('#scan-status');

        // --- FUNCTIONS ---

        function showToast(type, message) {
            Swal.fire({
                icon: type === 'primary' ? 'success' : type, // 'primary' is used for OUT usually
                text: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }

        function playBeep(isSuccess) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;

            const ctx = new AudioContext();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.connect(gain);
            gain.connect(ctx.destination);

            if (isSuccess) {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(1000, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(1500, ctx.currentTime + 0.1);
            } else {
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(200, ctx.currentTime);
                osc.frequency.linearRampToValueAtTime(150, ctx.currentTime + 0.3);
            }

            gain.gain.setValueAtTime(0.1, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + (isSuccess ? 0.2 : 0.4));

            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + (isSuccess ? 0.2 : 0.4));
        }

        function logToSidebar(data) {
            let style = {};
            // Determine style based on response type/status
            if (data.type === 'OUT') {
                style = { border: 'border-purple-500', bg: 'bg-purple-100', text: 'text-purple-700', icon: 'fa-door-open', label: 'PULANG' };
            } else if (data.status === 'Terlambat') {
                style = { border: 'border-amber-500', bg: 'bg-amber-100', text: 'text-amber-700', icon: 'fa-exclamation', label: 'TERLAMBAT' };
            } else if (data.type === 'IN') {
                style = { border: 'border-emerald-500', bg: 'bg-emerald-100', text: 'text-emerald-700', icon: 'fa-check', label: 'MASUK' };
            } else {
                style = { border: 'border-red-500', bg: 'bg-red-100', text: 'text-red-700', icon: 'fa-times', label: 'GAGAL' };
            }

            const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            const distanceInfo = data.distance ? `<p class="text-[10px] text-gray-400 mt-1"><i class="fas fa-map-marker-alt mr-1"></i> ${data.distance}m</p>` : '';

            const html = `
                    <li class="bg-white p-3 rounded-xl border-l-4 ${style.border} shadow-sm animate__animated animate__fadeInLeft hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex space-x-3">
                                <div class="w-10 h-10 rounded-full ${style.bg} flex items-center justify-center ${style.text}">
                                    <i class="fas ${style.icon}"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">${data.student.name || 'Siswa'}</p>
                                    <p class="text-xs text-gray-500">${data.student.class || '-'}</p>
                                    ${distanceInfo}
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold ${style.text} block">${style.label}</span>
                                <span class="text-xs text-gray-400 font-mono">${time}</span>
                            </div>
                        </div>
                    </li>
                `;

            const list = $('#attendance-log');
            list.prepend(html);
            list.find('li:gt(15)').remove(); // Keep max 15 items
            // Remove "empty" placeholder if exists
            const emptyPlaceholder = list.find('li.text-center');
            if (emptyPlaceholder.length) emptyPlaceholder.remove();
        }

        function processBarcode(code) {
            if (isProcessing) return;
            isProcessing = true;
            scanStatus.html(UI.processing);

            $.ajax({
                url: scanUrl,
                method: 'POST',
                data: {
                    _token: csrfToken,
                    barcode: code,
                    latitude: currentLat,
                    longitude: currentLng
                },
                success: function (response) {
                    playBeep(true);
                    let type = 'success';
                    if (response.type === 'IN' && response.status === 'Terlambat') type = 'warning';
                    if (response.type === 'OUT') type = 'info'; // Use info/primary for OUT

                    let msg = response.message;
                    if (response.distance) msg += ` (${response.distance}m)`;

                    showToast(type, msg);

                    // Add to log
                    logToSidebar({
                        type: response.type,
                        status: response.status,
                        student: { name: response.student.name, class: response.student.class },
                        distance: response.distance
                    });

                    scanStatus.html(UI.ready);
                },
                error: function (xhr) {
                    playBeep(false);
                    let msg = 'Terjadi kesalahan sistem.';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;

                    showToast('error', msg);
                    scanStatus.html(UI.error(msg));
                },
                complete: function () {
                    setTimeout(() => {
                        isProcessing = false;
                        if (scanStatus.text().includes('Gagal')) scanStatus.html(UI.ready);
                    }, RESCAN_DELAY);
                }
            });
        }

        function onScanSuccess(decodedText) {
            if (!isProcessing && decodedText.length > 2) {
                processBarcode(decodedText);
            }
        }

        // --- INIT ---
        $(document).ready(function () {
            // Clock
            setInterval(() => {
                const now = new Date();
                $('#server-time-display').text(now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }));
            }, 1000);

            // GPS
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        currentLat = pos.coords.latitude;
                        currentLng = pos.coords.longitude;
                        $('#gps-indicator-area').html(`
                                <div class="bg-black/60 backdrop-blur-md text-white px-3 py-1.5 rounded-full text-xs flex items-center shadow-lg border border-white/10">
                                    <span class="relative flex h-2 w-2 mr-2">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                    </span>
                                    GPS Aktif
                                </div>`);
                    },
                    (err) => {
                        console.warn('GPS Error', err);
                        $('#gps-indicator-area').html(`
                                <div class="bg-red-600/80 backdrop-blur-md text-white px-3 py-1.5 rounded-full text-xs flex items-center shadow-lg border border-white/10">
                                    <i class="fas fa-exclamation-triangle mr-1.5"></i> GPS Off
                                </div>`);
                        Swal.fire({ icon: 'warning', title: 'GPS Mati', text: 'Lokasi tidak terdeteksi. Absensi mungkin ditolak jika validasi radius aktif.', toast: true, position: 'top-end', timer: 5000 });
                    },
                    { enableHighAccuracy: true }
                );
            }

            // Scanner
            const html5QrCode = new Html5Qrcode("scanner");
            const config = { fps: 15, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 };

            html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
                .then(() => scanStatus.html(UI.ready))
                .catch(err => {
                    scanStatus.html(UI.error('Kamera tidak dapat diakses/izin ditolak.'));
                    console.error(err);
                });

            // Cleanup
            window.addEventListener('beforeunload', () => {
                if (html5QrCode.isScanning) html5QrCode.stop().catch(err => { });
            });
        });
    </script>
@endsection

@section('css')
    <style>
        /* Gradient Animation for Scanner Laser */
        @keyframes scan {
            0% {
                top: 0;
                opacity: 0;
            }

            50% {
                opacity: 1;
            }

            100% {
                top: 100%;
                opacity: 0;
            }
        }

        .animate-scan {
            animation: scan 2s infinite ease-in-out;
        }

        /* Ensure video covers the div properly */
        #scanner video {
            object-fit: cover;
            border-radius: 12px;
        }
    </style>
@endsection