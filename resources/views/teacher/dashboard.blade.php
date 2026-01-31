@extends('layouts.adminlte')

@section('title', 'Dashboard Guru')

@section('content_header')
<div class="flex items-center justify-between pb-4">
    <div>
        <h1 class="m-0 text-gray-800 font-extrabold text-2xl tracking-tight flex items-center">
            <i class="fas fa-home text-indigo-600 mr-3"></i> Dashboard
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Selamat datang kembali, <span
                class="text-indigo-600 font-bold">{{ Auth::user()->name }}</span>! 👋</p>
    </div>
    <div class="hidden sm:block">
        <div
            class="flex items-center space-x-3 text-sm text-gray-800 bg-white px-5 py-2.5 rounded-2xl shadow-sm border border-gray-100 font-semibold">
            <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                <i class="far fa-calendar-alt"></i>
            </div>
            <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- LEFT COLUMN: STATS & TIMELINE (2/3) --}}
    <div class="lg:col-span-2 space-y-8">

        {{-- Hero Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            {{-- Card 1: Total Jadwal --}}
            <div
                class="bg-gradient-to-br from-indigo-600 to-violet-700 rounded-[2rem] p-6 text-white shadow-xl shadow-indigo-200 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="relative z-10">
                    <div
                        class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-4 text-2xl border border-white/10">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                    <p class="text-indigo-100 text-sm font-semibold tracking-wide mb-1">Jadwal Hari Ini</p>
                    <h3 class="text-4xl font-extrabold tracking-tight">{{ $stats['total_classes'] }} <span
                            class="text-lg font-medium opacity-80">Kelas</span></h3>
                </div>
                <div
                    class="absolute -right-6 -bottom-6 opacity-10 transform rotate-12 group-hover:scale-110 transition-transform duration-500">
                    <i class="fas fa-chalkboard-teacher text-9xl"></i>
                </div>
                <div class="absolute top-0 right-0 p-4 opacity-30">
                    <i class="fas fa-arrow-right text-xl group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>

            {{-- Card 2: Sudah Diisi --}}
            <div
                class="bg-white rounded-[2rem] p-6 shadow-lg shadow-gray-100 border border-gray-50 relative overflow-hidden group hover:border-emerald-200 transition-colors duration-300">
                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 text-xl">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <span class="text-xs font-bold bg-emerald-50 text-emerald-600 px-2 py-1 rounded-lg">Done</span>
                    </div>
                    <p class="text-gray-400 text-sm font-bold uppercase tracking-wider mb-1">Jurnal Terisi</p>
                    <h3 class="text-3xl font-extrabold text-gray-800">{{ $stats['filled'] }} <span
                            class="text-sm font-semibold text-gray-400">Sesi</span></h3>
                </div>
                <div
                    class="absolute bottom-0 left-0 w-full h-1 bg-emerald-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left">
                </div>
            </div>

            {{-- Card 3: Belum Diisi --}}
            <div
                class="bg-white rounded-[2rem] p-6 shadow-lg shadow-gray-100 border border-gray-50 relative overflow-hidden group hover:border-amber-200 transition-colors duration-300">
                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600 text-xl animate-pulse">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <span class="text-xs font-bold bg-amber-50 text-amber-600 px-2 py-1 rounded-lg">Pending</span>
                    </div>
                    <p class="text-gray-400 text-sm font-bold uppercase tracking-wider mb-1">Belum Diisi</p>
                    <h3 class="text-3xl font-extrabold text-gray-800">{{ $stats['pending'] }} <span
                            class="text-sm font-semibold text-gray-400">Sesi</span></h3>
                </div>
                <div
                    class="absolute bottom-0 left-0 w-full h-1 bg-amber-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left">
                </div>
            </div>
        </div>

        {{-- Smart Timeline --}}
        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden">
            <div
                class="px-8 py-6 border-b border-gray-50 flex flex-col sm:flex-row justify-between sm:items-center gap-4 bg-gray-50/30 backdrop-blur-xl">
                <div>
                    <h3 class="font-extrabold text-gray-800 flex items-center text-xl">
                        <i class="fas fa-stream text-indigo-500 mr-3"></i> Timeline Mengajar
                    </h3>
                    <p class="text-sm text-gray-400 font-medium mt-1">Jadwal pelajaran Anda hari ini.</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span
                        class="px-4 py-1.5 bg-indigo-50 text-indigo-700 rounded-full text-xs font-bold uppercase tracking-wide border border-indigo-100 shadow-sm">
                        {{ $todayDay }}
                    </span>
                </div>
            </div>

            <div class="p-8">
                @if($schedules->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div
                            class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6 animate-bounce-slow">
                            <i class="fas fa-mug-hot text-4xl text-gray-300"></i>
                        </div>
                        <h4 class="text-gray-900 font-extrabold text-xl mb-2">Hari ini Libur Mengajar!</h4>
                        <p class="text-gray-500 text-base max-w-md mx-auto leading-relaxed">Anda tidak memiliki jadwal kelas
                            hari ini. Gunakan waktu ini untuk istirahat atau mempersiapkan materi esok hari.</p>
                    </div>
                @else
                    <div class="relative">
                        {{-- Connecting Line --}}
                        <div class="absolute left-[88px] top-6 bottom-6 w-0.5 bg-gray-100 hidden sm:block"></div>

                        <div class="space-y-8">
                            @foreach($schedules as $schedule)
                                @php
                                    $isFilled = $schedule->journal_status == 'filled';
                                    $now = \Carbon\Carbon::now();
                                    $start = \Carbon\Carbon::parse($schedule->start_time);
                                    $end = \Carbon\Carbon::parse($schedule->end_time);

                                    // Logic Status
                                    if ($isFilled) {
                                        $status = 'selesai';
                                        $color = 'emerald';
                                        $icon = 'fa-check-circle';
                                    } elseif ($now->between($start, $end)) {
                                        $status = 'berlangsung';
                                        $color = 'indigo';
                                        $icon = 'fa-play-circle';
                                    } elseif ($now->gt($end)) {
                                        $status = 'terlewat';
                                        $color = 'red';
                                        $icon = 'fa-exclamation-circle';
                                    } else {
                                        $status = 'mendatang';
                                        $color = 'gray';
                                        $icon = 'fa-clock';
                                    }
                                @endphp

                                <div class="relative flex flex-col sm:flex-row gap-6 group">
                                    {{-- Time Stamp --}}
                                    <div
                                        class="flex sm:flex-col items-center sm:items-end sm:w-16 flex-shrink-0 z-10 pt-4 sm:pt-0">
                                        <span
                                            class="text-lg font-extrabold text-gray-800 font-mono tracking-tight">{{ $start->format('H:i') }}</span>
                                        <span
                                            class="text-xs font-bold text-gray-400 font-mono hidden sm:block">{{ $end->format('H:i') }}</span>
                                    </div>

                                    {{-- Timeline Dot --}}
                                    <div class="hidden sm:flex flex-shrink-0 w-8 flex-col items-center relative z-10 pt-1.5">
                                        <div
                                            class="w-4 h-4 rounded-full border-[3px] border-{{ $color }}-500 bg-white shadow-sm {{ $status == 'berlangsung' ? 'animate-ping' : '' }}">
                                        </div>
                                        @if($status == 'berlangsung')
                                            <div class="w-4 h-4 rounded-full bg-{{ $color }}-500 absolute top-1.5"></div>
                                        @endif
                                    </div>

                                    {{-- Card --}}
                                    <div class="flex-1">
                                        <div
                                            class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-{{ $color }}-200 transition-all duration-300 relative overflow-hidden group-hover:bg-{{ $color }}-50/10">

                                            {{-- Status Flag --}}
                                            <div
                                                class="absolute top-0 right-0 px-4 py-1.5 bg-{{ $color }}-50 rounded-bl-2xl border-b border-l border-{{ $color }}-100">
                                                <span
                                                    class="text-xs font-bold text-{{ $color }}-700 uppercase tracking-wider flex items-center">
                                                    <i class="fas {{ $icon }} mr-1.5"></i> {{ ucfirst($status) }}
                                                </span>
                                            </div>

                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-2">
                                                <div>
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <span
                                                            class="px-3 py-1 rounded-lg text-xs font-extrabold uppercase tracking-wider bg-gray-100 text-gray-600 border border-gray-200">
                                                            {{ $schedule->class->name }}
                                                        </span>
                                                        <span class="text-xs font-bold text-gray-400">
                                                            Ruang {{ $schedule->class->major ?? '-' }}
                                                        </span>
                                                    </div>
                                                    <h4
                                                        class="font-bold text-gray-800 text-lg mb-1 leading-tight group-hover:text-{{ $color }}-700 transition-colors">
                                                        {{ $schedule->subject->name }}
                                                    </h4>
                                                    <p class="text-sm text-gray-500 font-medium">
                                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} WIB
                                                    </p>
                                                </div>

                                                {{-- Action Buttons --}}
                                                <div class="flex items-center gap-3 pt-4 sm:pt-0">
                                                    @if($isFilled)
                                                        <a href="{{ route('teacher.journals.edit', $schedule->journal_id) }}"
                                                            class="flex-1 sm:flex-none inline-flex justify-center items-center px-5 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 hover:text-indigo-600 transition-all shadow-sm">
                                                            <i class="fas fa-eye mr-2"></i> Lihat
                                                        </a>
                                                    @else
                                                        <a href="{{ route('teacher.scan.scanner', $schedule->id) }}"
                                                            class="flex-1 sm:flex-none inline-flex justify-center items-center w-12 h-11 rounded-xl bg-indigo-50 text-indigo-600 font-bold hover:bg-indigo-600 hover:text-white transition-all shadow-sm border border-indigo-100"
                                                            title="Scan QR Code">
                                                            <i class="fas fa-qrcode"></i>
                                                        </a>
                                                        <a href="{{ route('teacher.journals.create', $schedule->id) }}"
                                                            class="flex-1 sm:flex-none inline-flex justify-center items-center px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold text-sm shadow-md hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 transition-all">
                                                            <i class="fas fa-pen mr-2"></i> Isi Jurnal
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- RIGHT COLUMN: PROFILE & QUICK MENU (1/3) --}}
    <div class="lg:col-span-1 space-y-8">

        {{-- Profile Widget --}}
        <div
            class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-100/50 border border-gray-100 p-8 text-center relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-br from-indigo-600 to-purple-700"></div>
            <div
                class="absolute top-0 left-0 w-full h-32 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-20">
            </div>

            <div class="relative z-10 mt-12">
                <div
                    class="w-24 h-24 rounded-3xl bg-white p-1.5 mx-auto shadow-xl rotate-3 group-hover:rotate-0 transition-transform duration-300">
                    <div
                        class="w-full h-full rounded-2xl bg-indigo-50 flex items-center justify-center text-4xl font-extrabold text-indigo-600 border border-indigo-100">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
                <h3 class="mt-4 font-extrabold text-gray-900 text-xl">{{ Auth::user()->name }}</h3>
                <p class="text-sm font-medium text-gray-500 mb-4">{{ Auth::user()->email }}</p>

                <div
                    class="inline-flex items-center px-4 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-full border border-indigo-100 uppercase tracking-wide">
                    <i class="fas fa-id-badge mr-2"></i> Guru Pengajar
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div>
            <h4 class="font-bold text-gray-800 text-lg mb-4 flex items-center px-2">
                <i class="fas fa-rocket text-purple-600 mr-2"></i> Akses Cepat
            </h4>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('teacher.scan.index') }}"
                    class="group bg-white p-5 rounded-3xl shadow-lg shadow-gray-100 border border-gray-100 hover:border-indigo-200 hover:shadow-indigo-100 transition-all duration-300 flex flex-col items-center text-center">
                    <div
                        class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-2xl mb-3 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <span class="font-bold text-gray-700 text-sm group-hover:text-indigo-700">Scan QR</span>
                </a>

                <a href="{{ route('teacher.journals.index') }}"
                    class="group bg-white p-5 rounded-3xl shadow-lg shadow-gray-100 border border-gray-100 hover:border-purple-200 hover:shadow-purple-100 transition-all duration-300 flex flex-col items-center text-center">
                    <div
                        class="w-14 h-14 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600 text-2xl mb-3 group-hover:bg-purple-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                        <i class="fas fa-history"></i>
                    </div>
                    <span class="font-bold text-gray-700 text-sm group-hover:text-purple-700">Riwayat</span>
                </a>

                <a href="{{ route('teacher.report.index') }}"
                    class="group bg-white p-5 rounded-3xl shadow-lg shadow-gray-100 border border-gray-100 hover:border-amber-200 hover:shadow-amber-100 transition-all duration-300 flex flex-col items-center text-center col-span-2">
                    <div class="flex items-center justify-center w-full mb-2">
                        <div
                            class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 text-xl group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300 shadow-sm">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <span class="font-bold text-gray-700 text-sm group-hover:text-amber-700 block">Laporan
                            Absensi</span>
                        <span class="text-xs text-gray-400 mt-1 block group-hover:text-amber-600/70">Rekap kehadiran
                            siswa</span>
                    </div>
                </a>
            </div>
        </div>

        {{-- Mini Calendar / Info --}}
        <div
            class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-[2rem] p-6 text-white shadow-xl relative overflow-hidden">
            <div class="relative z-10">
                <h4 class="font-bold text-lg mb-2">Tips Hari Ini</h4>
                <p class="text-gray-300 text-sm leading-relaxed">Jangan lupa untuk menutup jurnal mengajar setiap
                    selesai sesi kelas agar data presensi tersimpan rapi.</p>
            </div>
            <i
                class="fas fa-lightbulb absolute bottom-4 right-4 text-gray-700 text-6xl opacity-20 transform rotate-12"></i>
        </div>

    </div>
</div>
@stop