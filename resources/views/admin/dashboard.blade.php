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
    {{-- GRADIENT WELCOME HERO --}}
    <div class="relative bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl p-8 mb-8 shadow-2xl overflow-hidden relative" data-aos="fade-down">
        {{-- Abstract Pattern Overlay --}}
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
            </svg>
        </div>
        
        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between text-center sm:text-left">
            <div>
                <h2 class="text-3xl font-extrabold text-white mb-2 tracking-tight">Selamat Datang, {{ Auth::user()->name }}! 👋</h2>
                <p class="text-indigo-100 text-lg opacity-90 max-w-xl">
                    Pantau aktivitas sekolah secara <span class="font-bold text-white">Real-Time</span>. Sistem berjalan optimal.
                </p>
            </div>
            <div class="mt-6 sm:mt-0 bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 shadow-inner">
                <div class="flex items-center space-x-3 text-white">
                    <div class="text-right">
                        <p class="text-xs text-indigo-200 font-medium uppercase tracking-wider">Jam Server</p>
                        <p class="text-xl font-mono font-bold" id="dashboard-clock">{{ \Carbon\Carbon::now()->format('H:i') }}</p>
                    </div>
                    <i class="fas fa-clock text-3xl opacity-80"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @php
            $stats = [
                ['color' => 'indigo', 'icon' => 'fa-user-graduate', 'label' => 'Total Siswa', 'value' => $totalStudents, 'route' => route('students.index')],
                ['color' => 'cyan',   'icon' => 'fa-chalkboard',    'label' => 'Total Kelas', 'value' => $totalClasses,  'route' => route('classes.index')],
                ['color' => 'emerald','icon' => 'fa-check-circle',  'label' => 'Hadir Hari Ini', 'value' => $attendancePercentage . '%', 'route' => route('report.index')],
                ['color' => 'rose',   'icon' => 'fa-users',         'label' => 'Total User', 'value' => $totalUsers,    'route' => route('admin.users.index')],
            ];
            
            // Handle Pending Users Alert
            if($pendingUsers > 0) {
                 $stats[3] = ['color' => 'orange', 'icon' => 'fa-user-clock', 'label' => 'Menunggu Approval', 'value' => $pendingUsers, 'route' => route('admin.users.index', ['tab' => 'pending'])];
            }
        @endphp

        @foreach($stats as $index => $stat)
            <a href="{{ $stat['route'] }}" class="group relative" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 h-full transition-all duration-300 hover:shadow-xl hover:-translate-y-1 relative overflow-hidden">
                    {{-- Decorative Blur --}}
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-{{ $stat['color'] }}-50 rounded-full blur-2xl opacity-50 transition-all group-hover:scale-150"></div>
                    
                    <div class="relative z-10 flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">{{ $stat['label'] }}</p>
                            <h3 class="text-3xl font-black text-gray-800 tracking-tight group-hover:text-{{ $stat['color'] }}-600 transition-colors">
                                {{ $stat['value'] }}
                            </h3>
                        </div>
                        <div class="bg-{{ $stat['color'] }}-100 p-3 rounded-xl text-{{ $stat['color'] }}-600 group-hover:rotate-12 transition-transform duration-300 shadow-sm">
                            <i class="fas {{ $stat['icon'] }} text-xl"></i>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    {{-- MAIN CONTENT GRID (Timeline & Info) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- ACTIVITY TIMELINE (2 Columns) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 text-lg flex items-center">
                        <span class="bg-indigo-100 p-2 rounded-lg mr-3 text-indigo-600"><i class="fas fa-stream"></i></span>
                        Live Absensi Timeline
                    </h3>
                    <a href="{{ route('report.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-1.5 rounded-full transition">
                        View All
                    </a>
                </div>
                
                <div class="p-6 max-h-[500px] overflow-y-auto custom-scrollbar">
                    @forelse($recentAbsences as $absence)
                        @php
                            $isOut = $absence->checkout_time;
                            $status = $isOut ? 'PULANG' : $absence->status;
                            $time = $isOut ? $absence->checkout_time->format('H:i') : $absence->attendance_time->format('H:i');
                            
                            $colorMap = [
                                'Hadir' => 'teal',
                                'Terlambat' => 'amber',
                                'Alpa' => 'red',
                                'Izin' => 'blue',
                                'Sakit' => 'purple',
                                'PULANG' => 'indigo'
                            ];
                            $color = $colorMap[$status] ?? 'gray';
                        @endphp
                        
                        <div class="relative pl-8 pb-8 last:pb-0 border-l-2 border-gray-100 last:border-l-0">
                            {{-- Timeline Dot --}}
                            <div class="absolute -left-[9px] top-0 bg-white border-4 border-{{ $color }}-100 h-5 w-5 rounded-full z-10 flex items-center justify-center">
                                <div class="bg-{{ $color }}-500 h-2.5 w-2.5 rounded-full"></div>
                            </div>
                            
                            {{-- Content --}}
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between group">
                                <div class="flex-1">
                                    <div class="flex items-center mb-1">
                                        <h4 class="font-bold text-gray-800 text-sm group-hover:text-indigo-600 transition">{{ $absence->student->name ?? 'Siswa Dihapus' }}</h4>
                                        <span class="ml-2 px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-500">{{ $absence->student->class->name ?? '-' }}</span>
                                    </div>
                                    <p class="text-xs text-gray-400">
                                        Scan via <span class="font-medium text-gray-500">QR Code</span>
                                    </p>
                                </div>
                                
                                <div class="mt-2 sm:mt-0 text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
                                        {{ $status }}
                                    </span>
                                    <p class="text-xs font-mono font-bold text-gray-400 mt-1">{{ $time }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="bg-gray-50 rounded-full h-16 w-16 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-coffee text-gray-300 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium">Belum ada aktivitas absensi hari ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- SIDEBAR: SYSTEM & QUICK ACTIONS --}}
        <div class="space-y-6">
            
            {{-- System Status Card --}}
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-server text-gray-400 mr-2"></i> System Health
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="h-2 w-2 bg-green-500 rounded-full animate-pulse mr-3"></div>
                            <span class="text-sm font-medium text-gray-600">Status Database</span>
                        </div>
                        <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded">Connected</span>
                    </div>

                    <div class="p-3 bg-gray-50 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">Environment</p>
                        <div class="flex justify-between items-end">
                            <span class="text-sm font-bold text-gray-700">Laravel v{{ app()->version() }}</span>
                            <span class="text-xs text-gray-400">PHP {{ $phpVersion }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-3xl shadow-lg p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                
                <h3 class="font-bold text-lg mb-4 relative z-10">Quick Actions</h3>
                
                <div class="grid grid-cols-2 gap-3 relative z-10">
                    <a href="{{ route('admin.absensi.scan') }}" class="bg-white/10 hover:bg-white/20 p-3 rounded-xl text-center transition backdrop-blur-sm border border-white/5">
                        <i class="fas fa-qrcode text-xl mb-2 text-indigo-400"></i>
                        <p class="text-xs font-semibold">Scan Live</p>
                    </a>
                    <a href="{{ route('report.index') }}" class="bg-white/10 hover:bg-white/20 p-3 rounded-xl text-center transition backdrop-blur-sm border border-white/5">
                        <i class="fas fa-file-export text-xl mb-2 text-emerald-400"></i>
                        <p class="text-xs font-semibold">Laporan</p>
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
                text: {!! json_encode(session('success')) !!}, 
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
                text: {!! json_encode(session('error')) !!}, 
                toast: true, 
                position: 'top-end', 
                showConfirmButton: false, 
                timer: 3000 
            });
        @endif
    });
</script>
@stop