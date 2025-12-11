@extends('layouts.adminlte')

@section('title', 'Dashboard Wali Kelas')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-end">
    <div>
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">
            Dashboard Kelas
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Ringkasan aktivitas dan status absensi siswa hari ini.</p>
    </div>
    
    @if($class)
    <div class="mt-4 sm:mt-0 flex items-center bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100">
        <div class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></div>
        <span class="text-sm font-bold text-gray-700">Kelas {{ $class->grade }} {{ $class->name }}</span>
    </div>
    @endif
</div>
@stop

@section('content')
    
    {{-- KASUS 1: KELAS BELUM DIATUR --}}
    @if(!$class)
        <div class="flex flex-col items-center justify-center p-12 bg-white rounded-3xl shadow-xl border border-gray-100 text-center">
            <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-chalkboard-teacher text-4xl text-red-500"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Kelas Belum Ditugaskan</h3>
            <p class="text-gray-500 max-w-md mx-auto mb-8">
                Halo <strong>{{ $user->name }}</strong>, sepertinya Anda belum ditugaskan sebagai Wali Kelas. Mohon hubungi Administrator untuk pengaturan lebih lanjut.
            </p>
            <a href="#" class="px-8 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition duration-200 cursor-not-allowed opacity-75">
                <i class="fas fa-lock mr-2"></i> Akses Terbatas
            </a>
        </div>
    @else
        {{-- KASUS 2: KELAS SUDAH ADA --}}

        {{-- Sapaan Personal --}}
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-3xl p-8 mb-8 text-white relative overflow-hidden shadow-lg transform transition hover:scale-[1.01] duration-300">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
            <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0">
                    <h2 class="text-3xl font-bold mb-2">Selamat Datang, {{ $user->name }}! 👋</h2>
                    <p class="text-indigo-100 text-lg">Kelola absensi dan pantau aktivitas siswa <strong>Kelas {{ $class->name }}</strong> dengan mudah.</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('walikelas.students.index') }}" class="px-6 py-2.5 bg-white/20 backdrop-blur-md border border-white/30 rounded-xl font-bold hover:bg-white/30 transition">
                        <i class="fas fa-users mr-2"></i> Data Siswa
                    </a>
                    <a href="{{ route('walikelas.absensi.scan') }}" class="px-6 py-2.5 bg-white text-indigo-600 rounded-xl font-bold shadow-lg hover:bg-gray-50 transition">
                        <i class="fas fa-qrcode mr-2"></i> Scan Absen
                    </a>
                </div>
            </div>
        </div>

        {{-- ALERT: WARNING SISWA --}}
        @if($warningStudents->isNotEmpty())
        <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-2xl shadow-sm mb-8 animate-fade-in-up">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mt-1"></i>
                </div>
                <div class="ml-4 w-full">
                    <h3 class="text-lg font-bold text-red-800 mb-2">Perhatian Diperlukan!</h3>
                    <p class="text-sm text-red-700 mb-4">Beberapa siswa telah mencapai batas toleransi ketidakhadiran:</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($warningStudents as $warning)
                        <div class="flex items-center justify-between bg-white p-3 rounded-xl border border-red-100 shadow-sm">
                            <div>
                                <span class="font-bold text-gray-800 block">{{ $warning['name'] }}</span>
                                <span class="text-xs text-red-500 font-semibold">{{ $warning['warning_status'] }}: {{ $warning['count'] }}/{{ $warning['max_limit'] }}</span>
                            </div>
                            <a href="{{ route('walikelas.students.show', $warning['student_id']) }}" class="text-xs bg-red-100 text-red-600 px-3 py-1 rounded-lg hover:bg-red-200 font-bold transition">
                                Detail
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        {{-- STATS GRID --}}
        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-pie text-indigo-500 mr-2"></i> Statistik Hari Ini
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6 mb-8"> 
            
            {{-- 1. Total Siswa --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg transition duration-300 group">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-blue-50 rounded-2xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
                <h4 class="text-3xl font-extrabold text-gray-800 mb-1">{{ $totalStudents }}</h4>
                <p class="text-sm text-gray-500 font-medium">Total Siswa</p>
            </div>

            {{-- 2. Hadir --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg transition duration-300 group">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-emerald-50 rounded-2xl text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition duration-300">
                        <i class="fas fa-user-check text-xl"></i>
                    </div>
                </div>
                <h4 class="text-3xl font-extrabold text-gray-800 mb-1">{{ $presentToday }}</h4>
                <p class="text-sm text-gray-500 font-medium">Hadir Hari Ini</p>
            </div>

            {{-- 3. Belum Absen --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg transition duration-300 group">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-rose-50 rounded-2xl text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition duration-300">
                        <i class="fas fa-user-clock text-xl"></i>
                    </div>
                    @if($absentToday > 0)
                    <a href="{{ route('walikelas.absensi.manual.index') }}" class="text-xs font-bold text-rose-500 hover:text-rose-700 bg-rose-50 px-2 py-1 rounded-lg">
                        Cek
                    </a>
                    @endif
                </div>
                <h4 class="text-3xl font-extrabold text-gray-800 mb-1">{{ $absentToday }}</h4>
                <p class="text-sm text-gray-500 font-medium">Belum Absen</p>
            </div>

            {{-- 4. Izin/Sakit/Alpha --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg transition duration-300 group">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-amber-50 rounded-2xl text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition duration-300">
                        <i class="fas fa-procedures text-xl"></i>
                    </div>
                </div>
                <h4 class="text-3xl font-extrabold text-gray-800 mb-1">
                    {{ ($dailyStats['Alpha'] ?? 0) + ($dailyStats['Sakit'] ?? 0) + ($dailyStats['Izin'] ?? 0) }}
                </h4>
                <p class="text-sm text-gray-500 font-medium">Tidak Hadir</p>
            </div>

             {{-- 5. Izin Pending --}}
             <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg transition duration-300 group">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-purple-50 rounded-2xl text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition duration-300">
                        <i class="fas fa-envelope-open-text text-xl"></i>
                    </div>
                </div>
                <h4 class="text-3xl font-extrabold text-gray-800 mb-1">{{ $pendingRequestsCount ?? 0 }}</h4>
                <p class="text-sm text-gray-500 font-medium">Permintaan Izin</p>
            </div>
            
        </div>
        
        {{-- BOTTOM SECTION: GRID 2 KOLOM --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- KOLOM KIRI: Log Absensi Terbaru (Lebih Lebar) --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 h-full">
                    <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-history text-indigo-500 mr-2"></i> Log Absensi Terbaru
                        </h3>
                        <a href="{{ route('walikelas.report.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition">
                            Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-0">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-500 font-semibold">
                                    <tr>
                                        <th class="px-6 py-4">Waktu</th>
                                        <th class="px-6 py-4">Siswa</th>
                                        <th class="px-6 py-4">Status</th>
                                        <th class="px-6 py-4">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($recentAbsences as $absence)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-4 font-mono text-gray-600">
                                            {{ \Carbon\Carbon::parse($absence->attendance_time)->format('H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold mr-3 text-xs">
                                                    {{ substr($absence->student->name, 0, 1) }}
                                                </div>
                                                <span class="font-bold text-gray-800">{{ $absence->student->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $statusClass = match($absence->status) {
                                                    'Hadir' => 'bg-green-100 text-green-700',
                                                    'Terlambat' => 'bg-amber-100 text-amber-700',
                                                    'Sakit' => 'bg-blue-100 text-blue-700',
                                                    'Izin' => 'bg-purple-100 text-purple-700',
                                                    default => 'bg-red-100 text-red-700'
                                                };
                                            @endphp
                                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusClass }}">
                                                {{ $absence->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-500 truncate max-w-xs">
                                            {{ $absence->notes ?? '-' }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <i class="far fa-clipboard text-3xl mb-3 opacity-30"></i>
                                                <p>Belum ada data absensi hari ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: Menu Cepat --}}
            <div class="lg:col-span-1">
                <div class="bg-indigo-900 rounded-3xl shadow-xl overflow-hidden text-white h-full relative">
                    {{-- Dekorasi Background --}}
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-5 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-purple-500 opacity-20 rounded-full blur-3xl"></div>
                    
                    <div class="p-8 relative z-10">
                        <h3 class="text-xl font-bold mb-6 flex items-center">
                            <i class="fas fa-rocket mr-3 text-yellow-300"></i> Aksi Cepat
                        </h3>
                        
                        <div class="space-y-4">
                            <a href="{{ route('walikelas.absensi.manual.index') }}" class="block bg-white/10 hover:bg-white/20 border border-white/10 p-4 rounded-2xl transition duration-200 group">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center mr-4 group-hover:scale-110 transition">
                                        <i class="fas fa-edit"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-sm">Input Manual</h4>
                                        <p class="text-xs text-indigo-200">Catat absensi tanpa scan</p>
                                    </div>
                                    <i class="fas fa-chevron-right ml-auto text-white/50 group-hover:text-white transition"></i>
                                </div>
                            </a>

                            <a href="{{ route('walikelas.izin.index') }}" class="block bg-white/10 hover:bg-white/20 border border-white/10 p-4 rounded-2xl transition duration-200 group">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-purple-500 flex items-center justify-center mr-4 group-hover:scale-110 transition">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-sm">Cek Surat Izin</h4>
                                        <p class="text-xs text-indigo-200">Verifikasi pengajuan ortu</p>
                                    </div>
                                    <i class="fas fa-chevron-right ml-auto text-white/50 group-hover:text-white transition"></i>
                                </div>
                            </a>

                            <a href="#" class="block bg-white/10 hover:bg-white/20 border border-white/10 p-4 rounded-2xl transition duration-200 group">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center mr-4 group-hover:scale-110 transition">
                                        <i class="fas fa-file-excel"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-sm">Download Rekap</h4>
                                        <p class="text-xs text-indigo-200">Unduh laporan bulanan</p>
                                    </div>
                                    <i class="fas fa-chevron-right ml-auto text-white/50 group-hover:text-white transition"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('js')
<script>
    // SweetAlert Toast Notification Logics
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                customClass: { popup: 'rounded-xl' }
            });
        @endif
    });
</script>
@stop