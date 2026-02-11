@extends('layouts.adminlte')

@section('title', 'Scan Absensi Kelas')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-camera text-indigo-600 mr-3"></i> Scan Absensi Kelas
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Kelas: <span class="font-bold text-indigo-600">{{ $class->name ?? 'N/A' }}</span> |
            Gunakan kamera untuk memindai kartu siswa.
        </p>
    </div>
    <div class="mt-2 sm:mt-0">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block text-right">Waktu Server</span>
        <span class="text-xl font-mono font-bold text-indigo-600 leading-none block text-right"
            id="server-time-display">{{ date('H:i') }}</span>
    </div>
</div>
@stop

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- SCANNER SECTION --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div
                class="p-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white flex justify-between items-center">
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
                <div id="reader"
                    class="w-full max-w-lg border-4 border-indigo-500/50 rounded-xl overflow-hidden shadow-2xl"></div>

                {{-- GPS Status Overlay --}}
                <div id="gps-indicator-area" class="absolute top-4 left-4 z-20"></div>

                {{-- Status Overlay (Processing) --}}
                <div id="scan-status"
                    class="absolute bottom-10 bg-black/70 text-white px-4 py-2 rounded-full text-sm backdrop-blur-sm hidden">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Memproses...
                </div>
            </div>

            <div class="p-4 bg-gray-50 flex justify-between items-center border-t border-gray-100">
                <p class="text-sm text-gray-500"><i class="fas fa-info-circle mr-1"></i> Pastikan GPS aktif untuk
                    validasi jarak.</p>
                <a href="{{ route('walikelas.dashboard') }}"
                    class="text-indigo-600 hover:text-indigo-800 font-bold text-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    {{-- LOG SECTION --}}
    <div class="space-y-6">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden h-full flex flex-col"
            style="max-height: 600px;">
            <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800"><i class="fas fa-history mr-2 text-indigo-500"></i> Aktivitas
                    Terbaru</h3>
                <button onclick="location.reload()"
                    class="text-xs text-indigo-600 hover:text-indigo-800 font-bold underline">Refresh</button>
            </div>
            <div class="flex-1 overflow-y-auto p-0" id="log-container">
                {{-- Log Items will be appended here --}}
                @forelse($recentAbsences as $absence)
                    @php
                        $status = $absence->status;
                        $isOut = $absence->checkout_time != null;
                        $isLate = $status == 'Terlambat';
                        $time = $isOut ? $absence->checkout_time->format('H:i') : $absence->attendance_time->format('H:i');
                        $type = $isOut ? 'OUT' : 'IN';

                        // Style properties to match JS addLogItem
                        $colorClass = $type === 'IN' ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : 'text-purple-600 bg-purple-50 border-purple-100';
                        if ($isLate)
                            $colorClass = 'text-amber-600 bg-amber-50 border-amber-100';

                        $iconClass = $type === 'IN' ? 'fa-sign-in-alt' : 'fa-sign-out-alt';
                    @endphp
                    <div
                        class="flex items-center p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors animate-fade-in-down">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $colorClass }} mr-4">
                            <i class="fas {{ $iconClass }}"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm">{{ $absence->student->name ?? 'Siswa' }}</p>
                            <p class="text-xs text-gray-500">{{ $time }} • {{ $status }}</p>
                        </div>
                        <div class="ml-auto">
                            <span class="text-xs font-bold px-2 py-1 rounded-md {{ $colorClass }}">{{ $type }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400" id="empty-log">
                        <i class="fas fa-clipboard-list text-4xl mb-3 opacity-30"></i>
                        <p class="text-sm">Belum ada data scan hari ini.</p>
                    </div>
                @endforelse
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
        // --- VARIABLES ---
        const html5QrCode = new Html5Qrcode("reader");
        let isScanning = true;
        let devicesList = [];
        let currentLat = null;
        let currentLng = null;

        // --- SERVER SETTINGS ---
        const scanUrl = '{{ route("walikelas.absensi.record") }}';
        const csrfToken = '{{ csrf_token() }}';
        const schoolLat = parseFloat("{{ $settings['school_latitude'] ?? 0 }}");
        const schoolLng = parseFloat("{{ $settings['school_longitude'] ?? 0 }}");
        const schoolRadius = parseInt("{{ $settings['school_radius'] ?? 100 }}");
        const enableLocationCheck = "{{ $settings['enable_location_check'] ?? 'false' }}" === 'true';

        // --- CLOCK ---
        setInterval(() => {
            const now = new Date();
            $('#server-time-display').text(now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }));
        }, 1000);

        // --- GPS LOGIC ---
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3;
            const φ1 = lat1 * Math.PI / 180;
            const φ2 = lat2 * Math.PI / 180;
            const Δφ = (lat2 - lat1) * Math.PI / 180;
            const Δλ = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function updateGpsUI() {
            if (!currentLat || !currentLng) return;
            if (schoolLat && schoolLng) {
                const distance = Math.round(calculateDistance(currentLat, currentLng, schoolLat, schoolLng));
                const inRange = distance <= schoolRadius;

                let gpsHtml = '';
                if (enableLocationCheck) {
                    if (inRange) {
                        gpsHtml = `<div class="bg-emerald-600/90 text-white px-3 py-1 rounded-full text-xs shadow-md backdrop-blur-md">
                                    <i class="fas fa-map-marker-alt mr-1"></i> ${distance}m (OK)
                                   </div>`;
                    } else {
                        gpsHtml = `<div class="bg-red-600/90 text-white px-3 py-1 rounded-full text-xs shadow-md backdrop-blur-md animate-pulse">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> ${distance}m (Jauh)
                                   </div>`;
                    }
                } else {
                    gpsHtml = `<div class="bg-blue-600/90 text-white px-3 py-1 rounded-full text-xs shadow-md backdrop-blur-md">
                                <i class="fas fa-info-circle mr-1"></i> ${distance}m
                               </div>`;
                }
                $('#gps-indicator-area').html(gpsHtml);
            }
        }

        // Init GPS
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                (pos) => {
                    currentLat = pos.coords.latitude;
                    currentLng = pos.coords.longitude;
                    updateGpsUI();
                },
                (err) => console.warn('GPS Error', err),
                { enableHighAccuracy: true, maximumAge: 10000, timeout: 5000 }
            );
        }

        // --- SCANNER LOGIC ---
        const onScanSuccess = (decodedText, decodedResult) => {
            if (!isScanning) return;

            // Basic Validation
            if (decodedText.length < 3) return;

            // GPS Validation (Client Side)
            if (enableLocationCheck && schoolLat && schoolLng) {
                if (!currentLat) {
                    Swal.fire({ icon: 'error', title: 'GPS Belum Siap', text: 'Tunggu posisi GPS terdeteksi.', timer: 2000, showConfirmButton: false });
                    return;
                }
                const dist = calculateDistance(currentLat, currentLng, schoolLat, schoolLng);
                if (dist > schoolRadius) {
                    playAudio('error');
                    Swal.fire({
                        icon: 'error',
                        title: 'Diluar Jangkauan',
                        text: `Jarak: ${Math.round(dist)}m. Max: ${schoolRadius}m`,
                        timer: 3000,
                        showConfirmButton: false
                    });
                    // Pause briefly then resume
                    isScanning = false;
                    setTimeout(() => { isScanning = true; }, 3000);
                    return;
                }
            }

            isScanning = false;
            $('#scan-status').removeClass('hidden').addClass('flex');

            // Send to Server
            $.ajax({
                url: scanUrl,
                method: "POST",
                data: {
                    _token: csrfToken,
                    barcode: decodedText,
                    latitude: currentLat,
                    longitude: currentLng
                },
                success: function (response) {
                    playAudio('success');
                    $('#scan-status').addClass('hidden').removeClass('flex');

                    let type = response.type; // IN or OUT
                    let title = type === 'IN' ? 'Berhasil Masuk' : 'Berhasil Pulang';
                    let icon = 'success';
                    if (response.status === 'Terlambat') {
                        title = 'Terlambat!';
                        icon = 'warning';
                    }

                    Swal.fire({
                        icon: icon,
                        title: title,
                        text: `${response.student.name} (${response.message})`,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    addLogItem(response.student.name, new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }), type, response.status);
                },
                error: function (xhr) {
                    playAudio('error');
                    $('#scan-status').addClass('hidden').removeClass('flex');
                    let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem';
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg, timer: 3000, showConfirmButton: false });
                },
                complete: function () {
                    setTimeout(() => { isScanning = true; }, 2500);
                }
            });
        };

        // --- CAMERA CONTROLS ---
        function startScanner(cameraId) {
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };
            if (html5QrCode.isScanning) {
                html5QrCode.stop().then(() => { runCamera(cameraId, config); }).catch(err => console.error("Failed to stop", err));
            } else {
                runCamera(cameraId, config);
            }
        }

        function runCamera(cameraId, config) {
            html5QrCode.start(cameraId, config, onScanSuccess, (err) => { /* ignore parse error */ })
                .catch(err => {
                    console.error("Error starting scanner", err);
                    Swal.fire('Error', 'Gagal mengakses kamera.', 'error');
                });
        }

        // Get Cameras and Start
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                const select = $('#camera-select');
                select.empty();
                devices.forEach(device => {
                    select.append(`<option value="${device.id}">${device.label || 'Kamera ' + (select.children().length + 1)}</option>`);
                });

                // Auto-select Back Camera
                let initialCameraId = devices[0].id;
                const backCamera = devices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('belakang') || d.label.toLowerCase().includes('environment'));
                if (backCamera) {
                    initialCameraId = backCamera.id;
                    select.val(initialCameraId);
                }

                startScanner(initialCameraId);
            } else {
                $('#camera-select').html('<option>Tidak ada kamera</option>');
                Swal.fire('Error', 'Tidak ada kamera terdeteksi.', 'error');
            }
        }).catch(err => {
            console.error("Error enumerating cameras", err);
            $('#camera-select').html('<option>Error Kamera</option>');
        });

        $('#camera-select').change(function () {
            const cameraId = $(this).val();
            if (cameraId) startScanner(cameraId);
        });

        // --- HELPER UI ---
        function playAudio(type) {
            const audio = document.getElementById(type + '-sound');
            if (audio) audio.play().catch(e => console.log('Audio error', e));
        }

        function addLogItem(name, time, type, status) {
            $('#empty-log').remove();
            let colorClass = type === 'IN' ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : 'text-purple-600 bg-purple-50 border-purple-100';
            if (status === 'Terlambat') colorClass = 'text-amber-600 bg-amber-50 border-amber-100';
            let iconClass = type === 'IN' ? 'fa-sign-in-alt' : 'fa-sign-out-alt';

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
                </div>`;

            $('#log-container').prepend(html);
        }
    });
</script>

<style>
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

    /* Fix video container rounded corners */
    #reader video {
        border-radius: 0.75rem;
        object-fit: cover;
    }
</style>
@stop