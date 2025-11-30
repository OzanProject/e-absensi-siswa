@extends('layouts.adminlte') 

@section('title', 'Scan Absensi Kelas ' . ($class->name ?? 'Anda'))

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-qrcode text-blue-600 mr-2"></i>
        <span>Absensi Scan Kelas: {{ $class->name ?? 'N/A' }}</span>
    </h1>
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('walikelas.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Scan Absensi</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- KOLOM KIRI: SCANNER (7/12) --}}
        <div class="lg:col-span-7">
            <div class="bg-white rounded-xl shadow-lg border border-blue-500/50"> 
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center"><i class="fas fa-camera mr-2 text-gray-500"></i> Live Scan QR/Barcode</h3>
                </div>
                <div class="p-5 text-center">
                    
                    {{-- AREA KAMERA html5-qrcode --}}
                    <div id="scanner" class="scanner-container">
                        {{-- Video stream akan dimasukkan di sini --}}
                    </div>

                    {{-- Hasil Scan Status --}}
                    <div id="scan-status" class="alert mt-3 w-full" style="display:none;"></div>
                    <p class="text-gray-500 text-sm mt-2">
                        Arahkan QR Code siswa di depan kamera.
                    </p>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: LOG ABSENSI TERBARU (5/12) --}}
        <div class="lg:col-span-5">
            <div class="bg-white rounded-xl shadow-lg border border-green-500/50"> 
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center"><i class="fas fa-list-alt mr-2 text-gray-500"></i> Log Kehadiran Kelas {{ $class->name ?? 'N/A' }}</h3>
                </div>
                <div class="custom-log-area p-0" id="attendance-log-container">
                    <ul class="divide-y divide-gray-100" id="attendance-log">
                        @forelse($recentAbsences as $absence)
                             <li class="p-3 text-sm hover:bg-gray-50 transition duration-150">
                                <div class="flex justify-between items-center">
                                    <div>
                                        @php
                                            // Tentukan ikon dan warna berdasarkan status
                                            $status = $absence->status;
                                            $statusMessage = $absence->checkout_time ? 'Pulang' : ($status == 'Terlambat' ? 'Terlambat' : 'Masuk');
                                            $iconClass = $statusMessage == 'Pulang' ? 'fas fa-door-open text-blue-600' : ($status == 'Terlambat' ? 'fas fa-exclamation-triangle text-amber-500' : 'fas fa-sign-in-alt text-green-600');
                                            $badgeClass = $statusMessage == 'Pulang' ? 'bg-blue-100 text-blue-800' : ($status == 'Terlambat' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800');
                                        @endphp
                                        <i class="{{ $iconClass }} mr-2"></i>
                                        <strong class="text-gray-900">{{ $absence->student->name ?? 'N/A' }}</strong>
                                    </div>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                        {{ $statusMessage }} {{ $absence->checkout_time ? $absence->checkout_time->format('H:i') : $absence->attendance_time->format('H:i') }}
                                    </span>
                                </div>
                            </li>
                        @empty
                            <li class="p-3 text-center text-gray-500 text-sm">Belum ada aktivitas absensi di kelas ini hari ini.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Audio untuk feedback scan --}}
    <audio id="scanSuccessAudio" src="{{ asset('assets/audio/scan-beep.mp3') }}" preload="auto"></audio>
@stop

@section('js')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
    <script>
        // --- Variabel Global & Konfigurasi ---
        // 💡 PASTIKAN ROUTE INI ADA DI routes/web.php UNTUK WALIKELAS
        const scanUrl = '{{ route("walikelas.absensi.record") }}'; 
        const csrfToken = '{{ csrf_token() }}';
        const scanDelay = 3000; // 3 detik jeda antar scan
        let lastScanTime = 0;
        
        const html5QrCode = new Html5Qrcode("scanner"); 
        const scanStatus = $('#scan-status');
        const scanSuccessAudio = document.getElementById('scanSuccessAudio'); 
        const attendanceLog = $('#attendance-log');


        // --- FUNGSI UTILITY SWEETALERT (TOAST) ---
        function showToast(type, message, title = 'Notifikasi') {
            let icon = 'info';
            if (type === 'success' || type === 'primary') icon = 'success';
            if (type === 'warning') icon = 'warning';
            if (type === 'danger') icon = 'error';
            
            Swal.fire({
                icon: icon,
                title: title,
                text: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        }

        // --- LOG LOGIC ---
        function logToSidebar(message, studentName, className, type, time) {
            // Mapping tipe ke kelas Tailwind
            const iconMap = {
                'primary': { icon: 'fas fa-door-open', color: 'text-blue-600', badge: 'bg-blue-100 text-blue-800', badgeText: 'PULANG' }, 
                'success': { icon: 'fas fa-sign-in-alt', color: 'text-green-600', badge: 'bg-green-100 text-green-800', badgeText: 'MASUK' }, 
                'warning': { icon: 'fas fa-exclamation-triangle', color: 'text-amber-500', badge: 'bg-amber-100 text-amber-800', badgeText: 'TERLAMBAT' }, 
                'danger': { icon: 'fas fa-times-circle', color: 'text-red-600', badge: 'bg-red-100 text-red-800', badgeText: 'GAGAL' } 
            };
            const map = iconMap[type] || iconMap['danger'];
            
            const logEntry = `
                <li class="p-3 text-sm hover:bg-gray-50 transition duration-150 border-b border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <i class="${map.icon} ${map.color} mr-2"></i>
                            <strong class="text-gray-900">${studentName}</strong> 
                        </div>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full ${map.badge}">
                            ${map.badgeText} ${time}
                        </span>
                    </div>
                </li>`;
            
            // Tambahkan di awal list (prepend)
            attendanceLog.prepend(logEntry);
            
            // Hapus pesan "Belum ada aktivitas..." jika ada
            attendanceLog.find('li').filter(function() { 
                return $(this).text().includes('Belum ada aktivitas absensi'); 
            }).remove();
        }

        // --- AJAX CONTROLLER ---
        function processBarcode(code) {
            scanStatus.removeClass().addClass('alert alert-info bg-blue-100 border-blue-400 text-blue-700 p-3 rounded-lg').html('<i class="fas fa-sync fa-spin mr-1"></i> Memproses: ' + code).show();

            $.ajax({
                url: scanUrl,
                method: 'POST',
                data: { _token: csrfToken, barcode: code },
                success: function(response) {
                    let type = 'danger'; // Default error
                    let time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    
                    if (response.type === 'IN') {
                        type = response.status === 'Terlambat' ? 'warning' : 'success';
                    } else if (response.type === 'OUT') {
                        type = 'primary'; // Pulang
                    } 
                    
                    // Fitur Suara (Beep)
                    if (scanSuccessAudio) {
                        scanSuccessAudio.currentTime = 0; 
                        scanSuccessAudio.play().catch(e => console.error("Error playing audio:", e));
                    }
                    
                    showToast(type, response.message, 'Scan Berhasil');
                    
                    logToSidebar(
                        response.message, 
                        response.student.name, 
                        response.student.class, // Class name harusnya ada di response dari controller
                        type, 
                        time
                    );
                    
                    scanStatus.removeClass().addClass(`alert bg-${type}-100 border-${type}-400 text-${type}-700 p-3 rounded-lg`).html('✅ ' + response.message);
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Kesalahan Server (500).';
                    const alertType = xhr.status === 409 ? 'warning' : 'danger'; // 409 biasanya konflik (sudah masuk/belum waktunya pulang)
                    showToast('danger', errorMsg, 'Scan Gagal');
                    scanStatus.removeClass().addClass(`alert bg-${alertType}-100 border-${alertType}-400 text-${alertType}-700 p-3 rounded-lg`).html('❌ ' + errorMsg);
                },
                complete: function() {
                    // Restart scanner setelah delay
                    setTimeout(() => { 
                        if (html5QrCode.isScanning()) {
                           html5QrCode.resume(); 
                        }
                    }, scanDelay);
                }
            });
        }
        
        // FUNGSI onScanSuccess (Handler saat QR Code terdeteksi)
        function onScanSuccess(decodedText, decodedResult) {
            let currentTime = new Date().getTime();
            
            if(currentTime - lastScanTime > scanDelay) {
                lastScanTime = currentTime;
                
                html5QrCode.pause(); 
                
                if(decodedText) {
                    processBarcode(decodedText);
                }
            }
        }

        // --- INIT LISTENER ---
        $(document).ready(function() {
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 }, 
                videoConstraints: {
                    facingMode: "environment" // Prioritaskan kamera belakang
                },
                formats: [Html5QrcodeSupportedFormats.QR_CODE] 
            };
            
            html5QrCode.start(
                { facingMode: "environment" },
                config,
                onScanSuccess, 
                (errorMessage) => { 
                    // Silent
                }
            )
            .then(() => {
                scanStatus.addClass('alert alert-success bg-green-100 border-green-400 text-green-700').html('✅ Kamera berhasil dimuat. Siap scan.').show();
            })
            .catch((err) => {
                console.error("HTML5-QRCODE INIT ERROR:", err);
                scanStatus.addClass('alert alert-danger bg-red-100 border-red-400 text-red-700').text('❌ Gagal mengakses kamera: ' + (err.message || 'Perlu HTTPS/Izin.').replace('NotFoundError', 'Tidak ada kamera terdeteksi')).show();
            });
        });

        // Listener untuk menghentikan kamera saat user meninggalkan halaman
        $(window).on('beforeunload', function(){
            if (html5QrCode && html5QrCode.isScanning()) {
                html5QrCode.stop().catch(e => console.log('Kamera gagal dihentikan saat meninggalkan halaman:', e));
            }
        });
        
    </script>
@endsection

@push('css')
<style>
/* Area Kamera */
.scanner-container {
    width: 100%;
    max-width: 450px; 
    height: 350px; 
    position: relative;
    overflow: hidden;
    margin: 0 auto;
    border: 3px solid #3b82f6; /* Blue Tailwind Primary */
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(59, 130, 246, 0.5); 
}
#scanner > div {
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}
#scanner video {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover;
}

/* Log Style */
.custom-log-area {
    max-height: 400px;
    overflow-y: auto;
    padding: 0;
}

/* Overrides Alert Styling for consistency */
.alert {
    padding: 0.75rem 1.25rem;
    border: 1px solid transparent;
    margin-top: 1rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
}
</style>
@endpush