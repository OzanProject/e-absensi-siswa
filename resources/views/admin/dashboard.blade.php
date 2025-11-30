@extends('layouts.adminlte')

@section('title', 'Dashboard Super Admin')

@section('content_header')
{{-- CUSTOM HEADER: Sudah menggunakan Indigo, hanya penyesuaian sedikit --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    {{-- Judul Halaman --}}
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-tachometer-alt text-indigo-600 mr-2"></i> 
        <span>Dashboard Super Admin</span>
    </h1>
    
    {{-- Breadcrumb --}}
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li> 
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Dashboard</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    {{-- ROW 1: Welcome Card (Menggunakan shadow dan border yang lebih halus) --}}
    <div class="mb-6">
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex-grow-1">
                    <h5 class="text-xl font-bold text-gray-900 mb-1">Selamat Datang, {{ Auth::user()->name }}! 👏</h5> {{-- Mengganti emoji --}}
                    <p class="text-sm text-gray-500 mt-0">
                        Sistem <strong class="text-indigo-600">E-Absensi</strong> siap digunakan. 
                        Anda mengelola data seluruh sekolah.
                    </p>
                </div>
                <div class="ml-4 flex-shrink-0 hidden sm:block">
                    <i class="fas fa-clipboard-check text-indigo-600 opacity-20 text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2 & 3: KOTAK STATISTIK UTAMA (Responsive Grid 4 Kolom) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        @php
            // --- LOGIKA DATA (TIDAK BERUBAH) ---
            $stats = [
                // [warna, icon, judul, nilai, route]
                ['indigo', 'fa-user-graduate', 'Total Siswa Aktif', $totalStudents, route('students.index')], // Mengganti green ke indigo
                ['cyan', 'fa-school', 'Total Kelas Aktif', $totalClasses, route('classes.index')], // Mengganti blue ke cyan
                ['orange', 'fa-chart-pie', 'Kehadiran Hari Ini', $attendancePercentage . '%', route('report.index')], // Mengganti yellow ke orange, icon chart-line ke chart-pie
                ['red', 'fa-users-cog', ($pendingUsers > 0 ? 'Akun Menunggu Persetujuan' : 'Total Pengguna'), 
                 ($pendingUsers > 0 ? $pendingUsers : $totalUsers), 
                 route('admin.users.index', ['tab' => $pendingUsers > 0 ? 'pending' : 'all'])],
            ];
            $teachers = ['green', 'fa-chalkboard-teacher', 'Total Guru/Wali Kelas', $totalTeachers, route('teachers.index')]; // Mengganti gray ke green
            
            // Gabungkan stats dan teachers ke dalam satu array untuk loop
            $allStats = array_merge($stats, [$teachers]);
        @endphp

        @foreach($allStats as $stat)
        
            @if ($loop->index == 4)
                {{-- Spacer untuk item ke-5 --}}
                <div class="hidden lg:block lg:col-span-3"></div>
            @endif
        
            <a href="{{ $stat[4] }}" class="block group"> {{-- Tambahkan class group untuk efek hover --}}
                {{-- PERUBAHAN UTAMA: Design Card Box Lebih Clean --}}
                <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 h-full 
                             hover:shadow-xl transition duration-300 transform group-hover:scale-[1.03]
                             ring-2 ring-transparent group-hover:ring-{{ $stat[0] }}-300/50">
                    
                    <div class="flex justify-between items-start">
                        <div>
                            {{-- Judul dan Icon Kecil di atas --}}
                            <div class="text-{{ $stat[0] }}-600 mb-2 flex items-center">
                                <i class="fas {{ $stat[1] }} mr-2 text-lg"></i>
                                <p class="text-sm font-semibold uppercase text-{{ $stat[0] }}-600">{{ $stat[2] }}</p>
                            </div>
                            
                            {{-- Nilai Statistik --}}
                            <h3 class="text-4xl font-extrabold text-gray-900 mt-1">
                                @if($stat[2] == 'Akun Menunggu Persetujuan' && $stat[3] > 0)
                                    <span class="bg-red-500 text-white text-xl px-3 py-1 rounded-full shadow-lg animate-pulse">{{ $stat[3] }}</span>
                                @else
                                    {{ $stat[3] }}
                                @endif
                            </h3>
                        </div>
                        
                        {{-- Icon Besar Dihilangkan untuk Clean Design --}}
                    </div>
                    
                    {{-- Tombol Lihat Detail/Action (Diposisikan di Bawah) --}}
                    <div class="mt-4 pt-3 border-t border-gray-100 text-sm font-semibold tracking-wide text-{{ $stat[0] }}-600 group-hover:text-{{ $stat[0] }}-700 flex items-center">
                        {{ $stat[2] == 'Akun Menunggu Persetujuan' ? 'Kelola Pengguna' : 'Lihat Detail' }} 
                        <i class="fas fa-arrow-right ml-2 text-sm transition duration-150 group-hover:translate-x-1"></i>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    
    {{-- ROW 4: Log Absensi dan Info Sistem (Responsive Grid 2 Kolom) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        
        {{-- LOG ABSENSI TERBARU (List Bersih) --}}
        <div>
            <div class="bg-white rounded-xl shadow-lg h-full border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-history mr-2 text-indigo-500"></i> {{-- Menggunakan Indigo --}}
                        Aktivitas Absensi Terbaru
                    </h3>
                </div>
                
                <ul class="divide-y divide-gray-100">
                    @forelse($recentAbsences as $absence)
                    <li class="flex justify-between items-center px-4 py-3 hover:bg-indigo-50/20 transition duration-150">
                        <div class="text-sm flex items-center flex-1 min-w-0">
                            @php
                                $isOut = $absence->checkout_time;
                                $status = $isOut ? 'PULANG' : $absence->status;
                                
                                // Penyesuaian Warna Badge untuk tampilan yang lebih modern
                                // Menggunakan kelas warna Tailwind dengan konsisten
                                if ($isOut) { $badgeColor = 'bg-indigo-500 text-white'; } 
                                elseif ($absence->status == 'Terlambat') { $badgeColor = 'bg-yellow-400 text-gray-800'; } // Teks gelap pada kuning terang
                                elseif (in_array($absence->status, ['Hadir'])) { $badgeColor = 'bg-green-500 text-white'; } 
                                else { $badgeColor = 'bg-red-500 text-white'; }
                            @endphp
                            
                            {{-- Nama Siswa & Kelas --}}
                            <i class="fas fa-user-circle text-gray-400 mr-3 text-xl flex-shrink-0"></i>
                            <div class="min-w-0 flex-1 overflow-hidden">
                                <strong class="text-gray-800 font-semibold truncate block">{{ $absence->student->name ?? 'N/A' }}</strong> 
                                <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full mt-1 inline-block whitespace-nowrap">
                                    {{ $absence->student->class?->name ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                        {{-- Badge Status --}}
                        <span class="text-xs font-bold {{ $badgeColor }} px-3 py-1.5 rounded-full whitespace-nowrap shadow-sm ml-4 flex-shrink-0">
                            {{ $status }} | {{ $isOut ? $absence->checkout_time->format('H:i') : $absence->attendance_time->format('H:i') }}
                        </span>
                    </li>
                    @empty
                    <li class="p-6 text-center text-gray-500 text-sm">Belum ada aktivitas absensi hari ini.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- INFO SISTEM & PENGATURAN --}}
        <div>
            <div class="bg-white rounded-xl shadow-lg h-full border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-indigo-500"></i>
                        System Info & Konfigurasi
                    </h3>
                </div>
                
                <div class="p-6"> {{-- Padding lebih besar --}}
                    <div class="grid grid-cols-2 gap-y-6 gap-x-4">
                        
                        {{-- Data Info (Menggunakan box sederhana) --}}
                        @foreach([
                            'Total Semua Pengguna' => $totalUsers,
                            'Zona Waktu Server' => 'WIB (Asia/Jakarta)',
                            'PHP Version' => $phpVersion,
                            'Laravel Version' => app()->version(),
                        ] as $label => $value)
                            <div class="border-l-4 border-indigo-500 pl-3 bg-indigo-50/30 p-2 rounded-lg">
                                <small class="text-xs font-semibold text-gray-500 block uppercase tracking-wider">{{ $label }}</small>
                                <p class="mt-0.5 text-xl font-extrabold text-gray-900">{{ $value }}</p>
                            </div>
                        @endforeach
                    </div>
                    
                    <a href="{{ route('settings.index') }}" 
                       class="mt-8 inline-flex items-center px-6 py-3 border border-transparent text-sm font-bold 
                              rounded-xl shadow-lg text-white bg-indigo-600 hover:bg-indigo-700 
                              focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-indigo-500/50 
                              transition duration-150 transform hover:-translate-y-0.5">
                        <i class="fas fa-cogs mr-2"></i> Kelola Pengaturan Sistem
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
{{-- SweetAlert JS dan Logika Session Flash (TIDAK DIUBAH) --}}
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

<script>
    // Logika SweetAlert tetap sama
    $(document).ready(function() {
        @if(session('success')) 
            Swal.fire({ 
                icon: 'success', 
                title: 'Berhasil!', 
                text: '{{ session('success') }}', 
                toast: true, 
                position: 'top-end', 
                showConfirmButton: false, 
                timer: 3000 
            });
        @endif
        @if(session('error')) 
            Swal.fire({ 
                icon: 'error', 
                title: 'Gagal!', 
                text: '{{ session('error') }}', 
                toast: true, 
                position: 'top-end', 
                showConfirmButton: false, 
                timer: 3000 
            });
        @endif
    });
</script>
@stop