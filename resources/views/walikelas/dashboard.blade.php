@extends('layouts.adminlte')

@section('title', 'Dashboard Wali Kelas')

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-home text-indigo-600 mr-2"></i>
        <span>Dashboard Kelas: {{ $class->name ?? 'Belum Ditugaskan' }}</span>
    </h1>
</div>
@stop

@section('content')
    
    {{-- KASUS 1: KELAS BELUM DIATUR (Styling Tailwind) --}}
    @if(!$class)
        <div class="bg-white rounded-xl shadow-xl border border-red-500/50">
            <div class="p-10 text-center">
                <i class="fas fa-exclamation-triangle fa-4x text-red-600 mb-4 animate-pulse"></i>
                <h3 class="text-2xl font-extrabold text-red-700 mb-3">Anda Belum Mengampu Kelas</h3>
                <p class="text-lg text-gray-600 mb-8">
                    Mohon hubungi **Super Admin** untuk menetapkan kelas yang Anda ampu di Modul Manajemen Wali Kelas.
                </p>
                {{-- Mengganti blue-600 ke indigo-600 --}}
                <a href="{{ route('teachers.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-bold rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 transform hover:-translate-y-0.5">
                    <i class="fas fa-arrow-right mr-2"></i> Lihat Daftar Wali Kelas
                </a>
            </div>
        </div>
    @else
        {{-- KASUS 2: KELAS SUDAH ADA (Tampilkan Statistik) --}}
        
        {{-- Alert Welcome (Styling Tailwind) --}}
        <div class="bg-indigo-50 border-l-4 border-indigo-500 text-indigo-700 p-4 rounded-xl relative mb-6 shadow-md alert-dismissible" role="alert">
            <i class="fas fa-hand-peace mr-2 text-indigo-600"></i> Selamat datang, Wali Kelas **{{ $user->name }}**. Anda mengampu **Kelas {{ $class->name }}** (Tingkat {{ $class->grade }}).
        </div>
        
        {{-- BLOK PERINGATAN TINGKAT ABSENSI --}}
        @if($warningStudents->isNotEmpty())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl shadow-md mb-6 alert-dismissible">
            <h3 class="text-xl font-bold mb-3 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2 text-red-600"></i> Peringatan Batas Absensi Terlampaui
            </h3>
            <p class="mb-4 text-gray-700">Perhatian: Siswa di bawah ini telah mencapai atau melampaui batas toleransi ketidakhadiran:</p>

            <ul class="list-disc ml-6 space-y-2 text-sm">
                @foreach($warningStudents as $warning)
                <li class="font-semibold text-gray-800">
                    {{ $warning['name'] }}
                    <span class="text-xs text-red-700 ml-2 font-normal">
                        ({{ $warning['warning_status'] }}: {{ $warning['count'] }}x / Batas: {{ $warning['max_limit'] }}x)
                    </span>
                    <a href="{{ route('walikelas.students.show', $warning['student_id']) }}" class="text-indigo-600 hover:underline ml-2 text-xs">
                        [Lihat Detail]
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
        
        {{-- KOTAK STATISTIK UTAMA (Grid 5 Kolom Responsif) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6"> 
            
            @php
                // Helper untuk Styling Box
                $boxClass = 'bg-white p-5 rounded-xl shadow-md transition duration-300 transform hover:shadow-xl hover:-translate-y-1 group';
                $iconBase = 'text-4xl opacity-70 group-hover:opacity-90 transition duration-300';
                $linkBase = 'mt-4 text-xs font-bold flex items-center group-hover:translate-x-1 transition duration-200';
            @endphp

            {{-- 1. Total Siswa (Primary: Indigo) --}}
            <a href="{{ route('walikelas.students.index') }}" class="block">
                <div class="{{ $boxClass }} border-t-4 border-indigo-600">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-3xl font-extrabold text-gray-900">{{ $totalStudents }}</h3>
                            <p class="text-sm font-medium text-gray-500 mt-1">Total Siswa di Kelas</p>
                        </div>
                        <div class="text-indigo-600 {{ $iconBase }}"><i class="fas fa-users"></i></div>
                    </div>
                    <div class="{{ $linkBase }} text-indigo-600">
                        Lihat Siswa <i class="fas fa-arrow-right ml-2 text-sm transition duration-200 group-hover:translate-x-1"></i>
                    </div>
                </div>
            </a>
            
            {{-- 2. Hadir (Hadir + Terlambat) Hari Ini (Success: Green) --}}
            <a href="{{ route('walikelas.absensi.scan') }}" class="block">
                <div class="{{ $boxClass }} border-t-4 border-green-600">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-3xl font-extrabold text-gray-900">{{ $presentToday }}</h3>
                            <p class="text-sm font-medium text-gray-500 mt-1">Siswa Hadir/Terlambat</p>
                        </div>
                        <div class="text-green-600 {{ $iconBase }}"><i class="fas fa-user-check"></i></div>
                    </div>
                    <div class="{{ $linkBase }} text-green-600">
                        Scan Absensi <i class="fas fa-arrow-right ml-2 text-sm transition duration-200 group-hover:translate-x-1"></i>
                    </div>
                </div>
            </a>
            
            {{-- 3. Belum Absen Hari Ini (Danger: Red) --}}
            <a href="{{ route('walikelas.absensi.manual.index') }}" class="block">
                <div class="{{ $boxClass }} border-t-4 border-red-600">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-3xl font-extrabold text-gray-900">{{ $absentToday }}</h3>
                            <p class="text-sm font-medium text-gray-500 mt-1">Siswa Belum Ada Record</p>
                        </div>
                        <div class="text-red-600 {{ $iconBase }}"><i class="fas fa-user-times"></i></div>
                    </div>
                    <div class="{{ $linkBase }} text-red-600">
                        Catat Absensi Manual <i class="fas fa-arrow-circle-right ml-2 text-sm transition duration-200 group-hover:translate-x-1"></i>
                    </div>
                </div>
            </a>
            
            {{-- 4. Alpha / Sakit / Izin Hari Ini (Warning: Amber) --}}
            <a href="{{ route('walikelas.absensi.manual.index') }}" class="block">
                <div class="{{ $boxClass }} border-t-4 border-amber-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-3xl font-extrabold text-gray-900">
                                {{ ($dailyStats['Alpha'] ?? 0) + ($dailyStats['Sakit'] ?? 0) + ($dailyStats['Izin'] ?? 0) }}
                            </h3>
                            <p class="text-sm font-medium text-gray-500 mt-1">Alpha/Sakit/Izin Hari Ini</p>
                        </div>
                        <div class="text-amber-500 {{ $iconBase }}"><i class="fas fa-exclamation-triangle"></i></div>
                    </div>
                    <div class="{{ $linkBase }} text-amber-600">
                        Lihat Status & Koreksi <i class="fas fa-arrow-circle-right ml-2 text-sm transition duration-200 group-hover:translate-x-1"></i>
                    </div>
                </div>
            </a>
            
            {{-- 💡 5. PERMINTAAN IZIN PENDING (Info: Purple) --}}
            <a href="{{ route('walikelas.izin.index') }}" class="block">
                <div class="{{ $boxClass }} border-t-4 border-purple-600">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-3xl font-extrabold text-gray-900">{{ $pendingRequestsCount ?? 0 }}</h3>
                            <p class="text-sm font-medium text-gray-500 mt-1">Permintaan Izin Pending</p>
                        </div>
                        <div class="text-purple-600 {{ $iconBase }}"><i class="fas fa-envelope-open-text"></i></div>
                    </div>
                    <div class="{{ $linkBase }} text-purple-600">
                        Proses Sekarang <i class="fas fa-arrow-circle-right ml-2 text-sm transition duration-200 group-hover:translate-x-1"></i>
                    </div>
                </div>
            </a>
            
        </div>
        
        {{-- Log Absensi Terbaru (Menggunakan Full Width di bawah grid utama) --}}
        <div class="mt-8 lg:col-span-full">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-history mr-2 text-indigo-500"></i> Log Absensi Terbaru (Hari Ini)</h3>
                </div>
                <div class="p-0">
                    <ul class="divide-y divide-gray-100">
                        @forelse($recentAbsences as $absence)
                        <li class="p-3 hover:bg-gray-50 transition duration-150">
                            <div class="flex justify-between items-center text-sm">
                                <div>
                                    @php
                                        // LOGIKA AMAN: Mapping Status
                                        $isCheckout = $absence->checkout_time;
                                        $statusType = $isCheckout ? 'PULANG' : $absence->status;
                                        
                                        if ($isCheckout) {
                                            $icon = 'fas fa-door-open text-indigo-600';
                                            $badgeClass = 'bg-indigo-100 text-indigo-800';
                                        } elseif ($absence->status == 'Terlambat') {
                                            $icon = 'fas fa-exclamation-triangle text-amber-500';
                                            $badgeClass = 'bg-amber-100 text-amber-800';
                                        } elseif (in_array($absence->status, ['Sakit', 'Izin', 'Alpha'])) {
                                            $icon = 'fas fa-user-times text-red-600';
                                            $badgeClass = 'bg-red-100 text-red-800';
                                        } else {
                                            $icon = 'fas fa-user-check text-green-600';
                                            $badgeClass = 'bg-green-100 text-green-800';
                                        }
                                        $time = $isCheckout ? $absence->checkout_time : $absence->attendance_time;
                                    @endphp

                                    <i class="{{ $icon }} mr-2"></i>
                                    <strong class="text-gray-900">{{ $absence->student->name ?? 'N/A' }}</strong>
                                </div>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                    {{ $statusType }} pukul {{ $time->format('H:i:s') }}
                                </span>
                            </div>
                        </li>
                        @empty
                        <li class="p-3 text-center text-gray-500 text-sm">Belum ada absensi tercatat hari ini.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    @endif
@stop

@section('css')
<style>
/* --- MINIMAL CUSTOM CSS FOR TAILWIND --- */
.text-indigo-600 { color: #4f46e5; }
.border-indigo-500 { border-color: #6366f1; }
.bg-indigo-50 { background-color: #eef2ff; }
.text-indigo-700 { color: #4338ca; }
.bg-indigo-100 { background-color: #e0e7ff; }
.text-indigo-800 { color: #3730a3; }

.text-amber-500 { color: #f59e0b; }
.border-amber-500 { border-color: #f59e0b; }
.text-red-600 { color: #dc3545; }
.border-red-500 { border-color: #ef4444; }
.border-red-600 { border-color: #dc3545; }

.text-green-600 { color: #10b981; }
.border-green-600 { border-color: #059669; }
.bg-green-600 { background-color: #10b981 !important; }

.text-purple-600 { color: #9333ea; }
.border-purple-600 { border-color: #9333ea; }
.bg-purple-600 { background-color: #9333ea !important; }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Pastikan JQuery tersedia di master layout
    
    $(document).ready(function() {
        // Tampilkan notifikasi SweetAlert Toast untuk pesan sesi
        @if(session('error'))
             Swal.fire({ 
                 icon: 'error', 
                 title: 'Gagal!', 
                 text: '{{ session('error') }}', 
                 toast: true, 
                 position: 'top-end', 
                 showConfirmButton: false, 
                 timer: 5000 
             });
        @endif
        
        // Auto-dismiss alerts (Tailwind alerts)
        setTimeout(function() {
            $('.alert-dismissible').fadeOut(400, function() { $(this).remove(); });
        }, 5000);
    });
</script>
@stop