@extends('layouts.adminlte')

@section('title', 'Scan Absensi Mapel')

@section('content_header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="m-0 text-gray-800 font-bold text-2xl tracking-tight flex items-center">
            <i class="fas fa-qrcode text-indigo-500 mr-2"></i> Scan Absensi Siswa
        </h1>
        <p class="text-sm text-gray-500 mt-1">Pilih jadwal kelas hari ini untuk memulai scan QR Code.</p>
    </div>
    <div class="hidden sm:block">
        <a href="{{ route('teacher.dashboard') }}"
            class="group flex items-center px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-indigo-600 transition-all duration-200 shadow-sm">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Kembali
        </a>
    </div>
</div>
@stop

@section('content')
<div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-6">
    <div class="p-6 border-b border-gray-100 bg-gray-50/30 flex justify-between items-center">
        <h3 class="font-bold text-lg text-gray-800 flex items-center">
            <i class="far fa-calendar-alt text-indigo-500 mr-2"></i> Jadwal Hari Ini
        </h3>
        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold border border-indigo-200">
            {{ \Carbon\Carbon::now()->format('l, d M Y') }}
        </span>
    </div>

    @if($schedules->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6 animate-pulse">
                <i class="far fa-calendar-times text-4xl text-gray-300"></i>
            </div>
            <h4 class="font-bold text-xl text-gray-900">Tidak Ada Jadwal Hari Ini</h4>
            <p class="text-gray-500 mt-2 max-w-sm mx-auto">
                Anda tidak memiliki jadwal mengajar hari ini. Silakan kembali lagi besok atau cek jadwal lengkap.
            </p>
            <a href="{{ route('teacher.dashboard') }}"
                class="mt-8 px-6 py-3 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition shadow-sm">
                Ke Dashboard Utama
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            @foreach($schedules as $schedule)
                @php
                    // Random gradient for visual variety based on subject ID
                    $gradients = [
                        'from-indigo-500 to-purple-600',
                        'from-blue-500 to-cyan-600',
                        'from-emerald-500 to-teal-600',
                        'from-orange-500 to-amber-600',
                        'from-pink-500 to-rose-600'
                    ];
                    $gradIndex = $schedule->id % count($gradients);
                    $currentGradient = $gradients[$gradIndex];
                @endphp

                <div
                    class="group relative bg-white rounded-3xl shadow-sm border-2 border-dashed border-gray-200 hover:border-solid hover:border-transparent transition-all duration-300 overflow-hidden">
                    {{-- Gradient Overlay (Hidden by default, shown on hover/active) --}}
                    <div
                        class="absolute inset-0 bg-gradient-to-br {{ $currentGradient }} opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    </div>

                    <div class="relative z-10 p-6 flex flex-col h-full group-hover:text-white transition-colors duration-300">
                        {{-- Header: Time & Class --}}
                        <div class="flex justify-between items-start mb-4">
                            <span
                                class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold font-mono group-hover:bg-white/20 group-hover:text-white transition-colors">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                            </span>
                            <div
                                class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-bold group-hover:bg-white/20 group-hover:text-white transition-colors">
                                {{ substr($schedule->class->name, 0, 1) }}
                            </div>
                        </div>

                        {{-- Subject Info --}}
                        <h3 class="text-xl font-bold text-gray-900 mb-1 group-hover:text-white transition-colors">
                            {{ $schedule->class->name }}</h3>
                        <p class="text-sm text-gray-500 mb-6 group-hover:text-indigo-100 transition-colors line-clamp-1">
                            {{ $schedule->subject->name }}
                        </p>

                        {{-- Action Button --}}
                        <div class="mt-auto">
                            <a href="{{ route('teacher.scan.scanner', $schedule->id) }}"
                                class="flex items-center justify-center w-full py-3.5 bg-indigo-50 text-indigo-700 font-bold rounded-xl group-hover:bg-white group-hover:text-indigo-600 transition-colors shadow-sm transform group-hover:-translate-y-1">
                                <i class="fas fa-qrcode mr-2"></i> Mulai Scan
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@stop