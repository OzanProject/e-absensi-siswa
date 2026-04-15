@extends('layouts.adminlte')

@section('title', 'Detail Absensi - ' . ($absence->student->name ?? 'Anak'))

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div class="mb-3 sm:mb-0">
        <h1 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 flex items-center">
            <i class="fas fa-clipboard-check text-blue-600 mr-3"></i>
            Detail Absensi
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Informasi lengkap kehadiran putra/putri Anda.</p>
    </div>
    <nav class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('orangtua.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150"><i class="fas fa-home"></i></a></li>
            <li class="text-gray-300">/</li>
            <li><a href="{{ route('orangtua.report.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Riwayat</a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-bold">Detail</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
@php
    $statusConfig = [
        'Hadir'     => ['bg' => 'from-emerald-500 to-green-600',   'badge' => 'bg-emerald-100 text-emerald-700 border-emerald-200', 'icon' => 'fa-check-circle',    'iconColor' => 'text-emerald-500', 'label' => 'HADIR'],
        'Terlambat' => ['bg' => 'from-amber-500 to-orange-500',    'badge' => 'bg-amber-100 text-amber-700 border-amber-200',       'icon' => 'fa-clock',           'iconColor' => 'text-amber-500',   'label' => 'TERLAMBAT'],
        'Sakit'     => ['bg' => 'from-cyan-500 to-blue-500',       'badge' => 'bg-cyan-100 text-cyan-700 border-cyan-200',          'icon' => 'fa-heartbeat',       'iconColor' => 'text-cyan-500',    'label' => 'SAKIT'],
        'Izin'      => ['bg' => 'from-blue-500 to-indigo-500',     'badge' => 'bg-blue-100 text-blue-700 border-blue-200',          'icon' => 'fa-envelope-open-text','iconColor' => 'text-blue-500',   'label' => 'IZIN'],
        'Alpha'     => ['bg' => 'from-red-500 to-rose-600',        'badge' => 'bg-red-100 text-red-700 border-red-200',             'icon' => 'fa-times-circle',    'iconColor' => 'text-red-500',     'label' => 'ALPHA'],
    ];
    $cfg = $statusConfig[$absence->status] ?? ['bg' => 'from-gray-400 to-gray-600', 'badge' => 'bg-gray-100 text-gray-700 border-gray-200', 'icon' => 'fa-question-circle', 'iconColor' => 'text-gray-400', 'label' => strtoupper($absence->status)];
    $tanggal = $absence->attendance_time->isoFormat('dddd, D MMMM YYYY');
    $namaSiswa = $absence->student->name ?? '-';
    $namaKelas = $absence->student->class->name ?? '-';
    $jamMasuk = $absence->attendance_time->format('H:i');
    $jamPulang = $absence->checkout_time?->format('H:i') ?? null;
    $lateDuration = $absence->late_duration ? (int) $absence->late_duration : null;
@endphp

<div class="space-y-6 max-w-2xl mx-auto">

    {{-- HERO CARD STATUS --}}
    <div class="bg-gradient-to-br {{ $cfg['bg'] }} rounded-3xl shadow-xl overflow-hidden relative">
        {{-- Background decorative circles --}}
        <div class="absolute top-0 right-0 w-48 h-48 bg-white opacity-10 rounded-full -translate-y-12 translate-x-12"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white opacity-10 rounded-full translate-y-8 -translate-x-8"></div>

        <div class="relative z-10 p-8 text-white">
            <div class="flex items-center justify-between mb-6">
                <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-bold">
                    <i class="fas fa-calendar-day mr-2"></i>{{ $tanggal }}
                </div>
                <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-bold">
                    <i class="fas fa-shield-alt mr-2"></i>Terverifikasi
                </div>
            </div>

            <div class="flex items-center space-x-5">
                <div class="bg-white/20 backdrop-blur-sm w-20 h-20 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <i class="fas {{ $cfg['icon'] }} text-4xl text-white"></i>
                </div>
                <div>
                    <p class="text-white/80 text-sm font-medium uppercase tracking-widest mb-1">Status Kehadiran</p>
                    <h2 class="text-4xl font-black tracking-tight">{{ $cfg['label'] }}</h2>
                    <p class="text-white/80 mt-1 text-sm">
                        @if($lateDuration)
                            Terlambat <span class="font-bold text-white">{{ $lateDuration }} menit</span> dari jam masuk
                        @elseif($absence->status === 'Hadir')
                            Hadir tepat waktu
                        @else
                            {{ $absence->notes ?? 'Tidak ada keterangan' }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- INFO CARD --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        {{-- Card Header --}}
        <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-100 flex items-center space-x-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                <i class="fas fa-user-graduate text-indigo-600 text-sm"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800 text-sm">Informasi Siswa</h3>
            </div>
        </div>

        <div class="p-6 space-y-4">
            {{-- Nama Siswa --}}
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-purple-600 text-sm"></i>
                    </div>
                    <span class="text-sm text-gray-500 font-medium">Nama Anak</span>
                </div>
                <span class="text-sm font-bold text-gray-900">{{ $namaSiswa }}</span>
            </div>

            {{-- Kelas --}}
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-school text-blue-600 text-sm"></i>
                    </div>
                    <span class="text-sm text-gray-500 font-medium">Kelas</span>
                </div>
                <span class="text-sm font-bold text-gray-900">{{ $namaKelas }}</span>
            </div>

            {{-- Status --}}
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-tag text-emerald-600 text-sm"></i>
                    </div>
                    <span class="text-sm text-gray-500 font-medium">Status</span>
                </div>
                <span class="px-3 py-1 text-xs font-bold rounded-lg border {{ $cfg['badge'] }}">
                    {{ $cfg['label'] }}
                </span>
            </div>

            {{-- Jam Masuk --}}
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-teal-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-sign-in-alt text-teal-600 text-sm"></i>
                    </div>
                    <span class="text-sm text-gray-500 font-medium">Jam Masuk</span>
                </div>
                <span class="text-sm font-bold text-gray-900 font-mono">{{ $jamMasuk }} WIB</span>
            </div>

            {{-- Jam Pulang --}}
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-sign-out-alt text-orange-600 text-sm"></i>
                    </div>
                    <span class="text-sm text-gray-500 font-medium">Jam Pulang</span>
                </div>
                @if($jamPulang)
                    <span class="text-sm font-bold text-gray-900 font-mono">{{ $jamPulang }} WIB</span>
                @else
                    <span class="text-xs text-gray-400 italic bg-gray-50 px-3 py-1 rounded-lg">Belum tercatat</span>
                @endif
            </div>

            {{-- Keterlambatan --}}
            @if($lateDuration)
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-hourglass-half text-amber-600 text-sm"></i>
                    </div>
                    <span class="text-sm text-gray-500 font-medium">Durasi Terlambat</span>
                </div>
                <span class="text-sm font-bold text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1 rounded-lg">+{{ $lateDuration }} menit</span>
            </div>
            @endif

            {{-- Keterangan --}}
            <div class="flex items-center justify-between py-3">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-sticky-note text-gray-600 text-sm"></i>
                    </div>
                    <span class="text-sm text-gray-500 font-medium">Keterangan</span>
                </div>
                <span class="text-sm font-medium text-gray-700 max-w-xs text-right">{{ $absence->notes ?? 'Tidak ada keterangan' }}</span>
            </div>
        </div>
    </div>

    {{-- AUDIT LOG CARD --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-100 flex items-center space-x-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                <i class="fas fa-history text-indigo-600 text-sm"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800 text-sm">Riwayat & Audit Log</h3>
            </div>
        </div>

        <div class="p-6">
            @if($absence->is_manual_corrected)
                <div class="flex items-start space-x-4 p-4 bg-amber-50 rounded-2xl border border-amber-200">
                    <div class="flex-shrink-0 w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-edit text-amber-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-amber-800 mb-1">Data Telah Dikoreksi oleh Staf Sekolah</p>
                        <div class="space-y-1">
                            <p class="text-xs text-amber-700">
                                <span class="font-semibold">Dikoreksi oleh:</span> {{ $absence->corrected_by ?? 'N/A' }}
                            </p>
                            <p class="text-xs text-amber-700">
                                <span class="font-semibold">Alasan:</span> {{ $absence->correction_note ?? 'Tidak ada catatan.' }}
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex items-start space-x-4 p-4 bg-emerald-50 rounded-2xl border border-emerald-200">
                    <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-double text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-emerald-800 mb-1">Data Asli — Belum Pernah Dikoreksi</p>
                        <p class="text-xs text-emerald-700">Data ini tercatat secara otomatis melalui sistem scan absensi dan belum pernah diubah oleh staf sekolah. Data terjamin validitasnya.</p>
                    </div>
                </div>
            @endif

            {{-- Dicatat oleh --}}
            @if($absence->recorded_by)
            <div class="mt-4 flex items-center space-x-3 text-xs text-gray-500 bg-gray-50 rounded-xl px-4 py-3">
                <i class="fas fa-user-check text-gray-400"></i>
                <span>Dicatat oleh: <span class="font-bold text-gray-700">{{ $absence->recorded_by }}</span></span>
            </div>
            @endif
        </div>
    </div>

    {{-- BACK BUTTON --}}
    <div class="flex justify-between items-center pb-4">
        <a href="{{ url()->previous() }}" 
           class="inline-flex items-center px-6 py-3 bg-white border border-gray-200 text-sm font-bold text-gray-700 rounded-xl shadow-sm hover:bg-gray-50 hover:shadow-md transition-all duration-200">
            <i class="fas fa-arrow-left mr-2 text-gray-500"></i> Kembali
        </a>
        <a href="{{ route('orangtua.report.index') }}"
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-sm font-bold text-white rounded-xl shadow-md hover:from-indigo-700 hover:to-blue-700 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
            <i class="fas fa-list mr-2"></i> Lihat Semua Riwayat
        </a>
    </div>

</div>
@stop

@section('css')
<style>
    /* Font mono untuk jam agar lebih rapi */
    .font-mono { font-family: 'Courier New', Courier, monospace; letter-spacing: 0.05em; }
</style>
@endsection