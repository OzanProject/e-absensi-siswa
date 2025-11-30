@extends('layouts.adminlte')

@section('title', 'Absensi QR Scan Terpusat')

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        {{-- Ikon QR Scan menggunakan Indigo untuk konsistensi branding --}}
        <i class="fas fa-qrcode text-indigo-600 mr-2"></i> 
        <span>Absensi QR Scan Terpusat</span>
    </h1>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Scan Kelas</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- KOLOM KIRI: SCANNER (7/12) --}}
        <div class="lg:col-span-7">
            {{-- Border disesuaikan ke Indigo --}}
            <div class="bg-white rounded-xl shadow-xl border border-indigo-500/50"> 
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-camera mr-2 text-indigo-500"></i> Live Scan QR/Barcode</h3>
                </div>
                <div class="p-6 text-center"> {{-- Padding disesuaikan --}}
                    
                    {{-- AREA KAMERA html5-qrcode --}}
                    <div id="scanner" class="scanner-container">
                        {{-- Video stream akan dimasukkan di sini --}}
                    </div>

                    {{-- Hasil Scan Status --}}
                    <div id="scan-status" class="alert mt-3 w-full" style="display:none;"></div>
                    <p class="text-red-600 text-sm mt-2">
                        Pastikan QR Code kartu pelajar terlihat jelas di kamera.
                    </p>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: LOG ABSENSI TERBARU (5/12) --}}
        <div class="lg:col-span-5">
            {{-- Border disesuaikan ke Green untuk log success --}}
            <div class="bg-white rounded-xl shadow-xl border border-green-500/50"> 
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-list-alt mr-2 text-indigo-500"></i> Log Kehadiran Hari Ini</h3>
                </div>
                <div class="custom-log-area p-0" id="attendance-log-container">
                    <ul class="divide-y divide-gray-100" id="attendance-log">
                        @forelse($recentAbsences as $absence)
                            <li class="p-3 text-sm hover:bg-gray-50 transition duration-150">
                                <div class="flex justify-between items-center">
                                    <div>
                                        @php
                                            // Tentukan ikon dan warna berdasarkan status (Mapping Tailwind)
                                            $status = $absence->status;
                                            $statusMessage = $absence->checkout_time ? 'Pulang' : ($status == 'Terlambat' ? 'Terlambat' : 'Masuk');
                                            $iconClass = $statusMessage == 'Pulang' ? 'fas fa-door-open text-indigo-600' : ($status == 'Terlambat' ? 'fas fa-exclamation-triangle text-amber-500' : 'fas fa-sign-in-alt text-green-600');
                                            $badgeClass = $statusMessage == 'Pulang' ? 'bg-indigo-100 text-indigo-800' : ($status == 'Terlambat' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800');
                                        @endphp
                                        <i class="{{ $iconClass }} mr-2"></i>
                                        <strong class="text-gray-900">{{ $absence->student->name ?? 'N/A' }}</strong> ({{ $absence->student->class->name ?? 'N/A' }})
                                    </div>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                        {{ $statusMessage }} {{ $absence->checkout_time ? $absence->checkout_time->format('H:i') : $absence->attendance_time->format('H:i') }}
                                    </span>
                                </div>
                            </li>
                        @empty
                            <li class="p-3 text-center text-gray-500 text-sm">Belum ada aktivitas absensi hari ini.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    {{-- 💡 Load html5-qrcode dan library pendukung --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script>
        // --- Variabel Global & Konfigurasi ---
        // LOGIKA AMAN
        const scanUrl = '{{ route("admin.absensi.record") }}';
        const csrfToken = '{{ csrf_token() }}';
        const scanDelay = 500; 
        let lastScanTime = 0;
        
        // 💡 Inisialisasi html5-qrcode
        const html5QrCode = new Html5Qrcode("scanner"); 
        const scanStatus = $('#scan-status');

        // --- Konstanta Pesan Status Awal (Dikonversi ke Tailwind) ---
        const READY_MESSAGE = '✅ Kamera berhasil dimuat. Siap scan.';
        const READY_CLASS = 'alert alert-success bg-green-100 border-green-400 text-green-700';

        // --- FUNGSI UTILITY SWEETALERT (TOAST) - LOGIKA AMAN ---
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
                timer: 3000,
                timerProgressBar: true
            });
        }

        // --- LOG LOGIC (Mapping Dikonversi ke Tailwind) ---
        function logToSidebar(message, studentName, className, type, time) {
            // Mapping tipe ke kelas Tailwind
            const iconMap = {
                'primary': { icon: 'fas fa-door-open', color: 'text-indigo-600', badge: 'bg-indigo-100 text-indigo-800', badgeText: 'PULANG' },
                'success': { icon: 'fas fa-sign-in-alt', color: 'text-green-600', badge: 'bg-green-100 text-green-800', badgeText: 'MASUK' },
                'warning': { icon: 'fas fa-exclamation-triangle', color: 'text-amber-500', badge: 'bg-amber-100 text-amber-800', badgeText: 'TERLAMBAT' },
                'danger': { icon: 'fas fa-times-circle', color: 'text-red-600', badge: 'bg-red-100 text-red-800', badgeText: 'GAGAL' }
            };
            const map = iconMap[type] || iconMap['info'];
            
            const logType = (type === 'primary' ? 'PULANG' : (type === 'success' ? 'MASUK' : (type === 'warning' ? 'TERLAMBAT' : map.badgeText)));

            const logEntry = `
                <li class="p-3 text-sm hover:bg-gray-50 transition duration-150 border-b border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <i class="${map.icon} ${map.color} mr-2"></i>
                            <strong class="text-gray-900">${studentName}</strong> (${className})
                        </div>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full ${map.badge}">
                            ${logType} ${time}
                        </span>
                    </div>
                </li>`;
            
            $('#attendance-log').prepend(logEntry);
            
            // Hapus pesan "Belum ada aktivitas absensi hari ini." jika ada
            $('#attendance-log').find('li').filter(function() { 
                return $(this).text().includes('Belum ada aktivitas absensi hari ini.'); 
            }).remove();

            // Opsional: Batasi log hanya 10 item terbaru
            while ($('#attendance-log').children().length > 10) {
                 $('#attendance-log').find('li').last().remove();
            }
        }

        // --- AJAX CONTROLLER - LOGIKA AMAN ---
        function processBarcode(code) {
            // Mengubah styling alert Bootstrap ke Tailwind
            scanStatus.removeClass().addClass('alert alert-info bg-blue-100 border-blue-400 text-blue-700 p-3 rounded-lg').html('<i class="fas fa-sync fa-spin mr-1"></i> Memproses: ' + code).show();

            $.ajax({
                url: scanUrl,
                method: 'POST',
                data: { _token: csrfToken, barcode: code },
                success: function(response) {
                    let type = 'danger'; 
                    let time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    
                    if (response.type === 'IN') {
                        type = response.status === 'Terlambat' ? 'warning' : 'success';
                    } else if (response.type === 'OUT') {
                        type = 'primary'; 
                    } 
                    
                    showToast(type, response.message, 'Scan Berhasil');
                    
                    logToSidebar(
                        response.message, 
                        response.student.name, 
                        response.student.class, 
                        type, 
                        time
                    );
                    
                    // KUNCI: Reset Status ke READY_MESSAGE setelah success
                    scanStatus.removeClass().addClass(READY_CLASS).html(READY_MESSAGE);
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Kesalahan Server (500).';
                    const alertType = xhr.status === 409 ? 'warning' : 'danger'; 
                    showToast('danger', errorMsg, 'Scan Gagal');
                    
                    // Mengubah styling alert Bootstrap ke Tailwind
                    scanStatus.removeClass().addClass(`alert bg-${alertType}-100 border-${alertType}-400 text-${alertType}-700 p-3 rounded-lg`).html('❌ ' + errorMsg);
                },
                complete: function() {
                    // PENTING: Segera lanjutkan scan setelah AJAX selesai, baik sukses maupun gagal.
                    if (html5QrCode.isScanning()) {
                        html5QrCode.resume(); 
                    }
                }
            });
        }
        
        /**
         * FUNGSI onScanSuccess (Handler saat QR Code terdeteksi) - LOGIKA AMAN
         */
        function onScanSuccess(decodedText, decodedResult) {
            let currentTime = new Date().getTime();
            
            // Mempertahankan jeda minimum untuk menghindari pemindaian berkali-kali
            if(currentTime - lastScanTime > scanDelay) {
                lastScanTime = currentTime;
                
                html5QrCode.pause(); 
                
                if(decodedText) {
                    processBarcode(decodedText);
                } else {
                    if (html5QrCode.isScanning()) {
                         html5QrCode.resume(); 
                    }
                    return;
                }
            }
        }

        // --- INIT LISTENER BARU - LOGIKA AMAN ---
        $(document).ready(function() {
            // Konfigurasi html5-qrcode
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 }, 
                formats: [Html5QrcodeSupportedFormats.QR_CODE] 
            };
            
            html5QrCode.start(
                { facingMode: "environment" }, // Prioritaskan kamera belakang
                config,
                onScanSuccess, 
                (errorMessage) => {}
            )
            .then(() => {
                // Tampilkan pesan READY saat scanner berhasil dimuat
                scanStatus.addClass(READY_CLASS).html(READY_MESSAGE).show();
            })
            .catch((err) => {
                console.error("HTML5-QRCODE INIT ERROR:", err);
                // Mengubah styling error alert Bootstrap ke Tailwind
                scanStatus.addClass('alert bg-red-100 border-red-400 text-red-700 p-3 rounded-lg').text('❌ Gagal mengakses kamera: ' + (err.message || 'Perlu HTTPS/Izin.').replace('NotFoundError', 'Tidak ada kamera terdeteksi')).show();
            });
        });

        // 💡 Listener untuk menghentikan kamera saat user meninggalkan halaman - LOGIKA AMAN
        $(window).on('beforeunload', function(){
            if (html5QrCode.isScanning()) {
                html5QrCode.stop().catch(e => console.log('Kamera gagal dihentikan saat meninggalkan halaman:', e));
            }
        });
        
    </script>
@endsection

@section('css')
<style>
/* --- TAILWIND & CUSTOM LAYOUT --- */
.text-teal-500 { color: #20c997; } 
.text-red-600 { color: #dc3545; }
.text-indigo-600 { color: #4f46e5; }
.text-indigo-500 { color: #6366f1; } /* Warna icon */
.bg-indigo-600 { background-color: #4f46e5 !important; }

/* Area Kamera */
.scanner-container {
    width: 100%;
    max-width: 450px; 
    height: 350px; 
    position: relative;
    overflow: hidden;
    margin: 0 auto;
    /* Menggunakan warna Indigo untuk border kamera */
    border: 3px solid #6366f1; 
    border-radius: 12px; /* Dibuat lebih bulat */
    box-shadow: 0 0 15px rgba(99, 102, 241, 0.6); 
}

/* Log Style */
.custom-log-area {
    max-height: 400px;
    overflow-y: auto;
    /* Dihilangkan padding p-0 di Blade, tapi di sini kita jaga max-height */
}

/* Overrides Alert Styling for consistency (Digunakan oleh JS) */
.alert {
    padding: 0.75rem 1.25rem;
    border: 1px solid transparent;
    margin-top: 1rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
}
/* Memastikan video mengisi penuh container */
#scanner video {
    width: 100% !important; 
    height: 100% !important; 
    object-fit: cover;
}
</style>
@endsection