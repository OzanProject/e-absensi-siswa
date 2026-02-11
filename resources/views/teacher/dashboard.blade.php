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

        {{-- Attendance Card (Moved to Top) --}}
        <div
            class="bg-white rounded-[2.5rem] p-6 shadow-xl shadow-gray-100/50 border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <i class="fas fa-clock text-6xl text-gray-800"></i>
            </div>

            <h4 class="font-bold text-gray-800 text-lg mb-4 flex items-center">
                <i class="fas fa-user-clock text-indigo-600 mr-2"></i> Presensi Hari Ini
            </h4>

            @if(!$attendance)
                {{-- Belum Absen Masuk --}}
                <div class="text-center py-4">
                    <p class="text-sm text-gray-500 mb-4">Anda belum melakukan absen masuk hari ini.</p>

                    {{-- Tombol Buka Kamera (Trigger Modal) --}}
                    <button type="button" onclick="openCamera('in')"
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold shadow-lg shadow-emerald-200 transform hover:-translate-y-1 transition-all">
                        <i class="fas fa-camera mr-2"></i> Absen Masuk (Selfie)
                    </button>
                </div>
            @elseif(is_null($attendance->clock_out))
                {{-- Sudah Masuk, Belum Pulang --}}
                <div class="space-y-4">
                    <div class="flex justify-between items-center bg-emerald-50 p-3 rounded-xl border border-emerald-100">
                        <span class="text-xs font-bold text-emerald-700 uppercase">Jam Masuk</span>
                        <span
                            class="font-mono font-bold text-emerald-800">{{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}</span>
                    </div>

                    <button type="button" onclick="openCamera('out')"
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-rose-500 to-red-600 text-white font-bold shadow-lg shadow-rose-200 transform hover:-translate-y-1 transition-all">
                        <i class="fas fa-sign-out-alt mr-2"></i> Absen Pulang (Selfie)
                    </button>
                </div>
            @else
                {{-- Selesai --}}
                <div class="space-y-3">
                    <div class="flex justify-between items-center bg-emerald-50 p-3 rounded-xl border border-emerald-100">
                        <span class="text-xs font-bold text-emerald-700 uppercase">Jam Masuk</span>
                        <span
                            class="font-mono font-bold text-emerald-800">{{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center bg-rose-50 p-3 rounded-xl border border-rose-100">
                        <span class="text-xs font-bold text-rose-700 uppercase">Jam Pulang</span>
                        <span
                            class="font-mono font-bold text-rose-800">{{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}</span>
                    </div>
                    <div class="text-center mt-4">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                            <i class="fas fa-check-circle mr-1 text-emerald-500"></i> Selesai
                        </span>
                    </div>
                </div>
            @endif
        </div>

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
                    <span class="font-bold text-gray-700 text-sm group-hover:text-indigo-700">Scan Siswa</span>
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

{{-- MODAL KAMERA --}}
<div id="cameraModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Ambil Foto Selfie
                        </h3>
                        <div class="mt-2 relative">
                            <video id="video" class="w-full rounded-lg shadow-inner bg-black" autoplay
                                playsinline></video>
                            <canvas id="canvas" class="hidden"></canvas>
                            <img id="photo-preview" class="w-full rounded-lg shadow-inner hidden" src="">

                            {{-- Loading & Error Message --}}
                            <div id="location-status" class="mt-2 text-sm text-gray-500">
                                <i class="fas fa-spinner fa-spin mr-1"></i> Mendeteksi Lokasi...
                            </div>
                            <div id="location-error"
                                class="hidden mt-2 p-2 bg-red-100 text-red-700 text-sm rounded border border-red-200">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <form action="{{ route('teacher.attendance.store') }}" method="POST" id="attendance-form-modal">
                    @csrf
                    <input type="hidden" name="latitude" id="lat">
                    <input type="hidden" name="longitude" id="long">
                    <input type="hidden" name="photo" id="photo-data">

                    <button type="button" id="capture-btn" onclick="takeSnapshot()" disabled
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Ambil Foto & Absen
                    </button>
                    <button type="button" id="retake-btn" onclick="retakePhoto()"
                        class="hidden w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Ambil Ulang
                    </button>
                </form>
                <button type="button" onclick="closeCamera()"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const photoPreview = document.getElementById('photo-preview');
    const captureBtn = document.getElementById('capture-btn');
    const retakeBtn = document.getElementById('retake-btn');
    const modal = document.getElementById('cameraModal');
    const formModal = document.getElementById('attendance-form-modal');
    const locationStatus = document.getElementById('location-status');
    const locationError = document.getElementById('location-error');

    let stream = null;
    let allowedToSubmit = false; // Flag untuk status lokasi

    // Setting Lokasi Sekolah dari Controller
    const schoolLat = {{ $schoolLocation['latitude'] ?? 0 }};
    const schoolLng = {{ $schoolLocation['longitude'] ?? 0 }};
    const schoolRadius = {{ $schoolLocation['radius'] ?? 100 }}; // Meter

    function openCamera(type) { // type: 'in' or 'out'
        modal.classList.remove('hidden');
        startCamera();
        checkLocation();
    }

    function closeCamera() {
        modal.classList.add('hidden');
        stopCamera();
        resetUI();
    }

    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" }, audio: false });
            video.srcObject = stream;
        } catch (err) {
            console.error("Error accessing camera: ", err);
            alert("Gagal mengakses kamera. Pastikan izin kamera diberikan.");
        }
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    }

    function takeSnapshot() {
        if (!allowedToSubmit) {
            alert("Anda berada di luar jangkauan lokasi sekolah!");
            return;
        }

        // Gambar ke canvas
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        // Convert to Base64
        const dataURL = canvas.toDataURL('image/jpeg', 0.8);

        // Set ke input hidden
        document.getElementById('photo-data').value = dataURL;

        // Preview
        video.classList.add('hidden');
        photoPreview.src = dataURL;
        photoPreview.classList.remove('hidden');

        // Tombol logic
        captureBtn.innerHTML = 'Kirim Absensi';
        captureBtn.onclick = submitForm; // Ganti action
        retakeBtn.classList.remove('hidden');
    }

    function submitForm() {
        const btn = document.getElementById('capture-btn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...';
        btn.disabled = true;
        formModal.submit();
    }

    function retakePhoto() {
        video.classList.remove('hidden');
        photoPreview.classList.add('hidden');
        captureBtn.innerHTML = 'Ambil Foto & Absen';
        captureBtn.onclick = takeSnapshot;
        retakeBtn.classList.add('hidden');
    }

    // Haversine Formula untuk Hitung Jarak (Meter)
    function getDistanceFromLatLonInM(lat1, lon1, lat2, lon2) {
        var R = 6371; // Radius bumi dalam km
        var dLat = deg2rad(lat2 - lat1);
        var dLon = deg2rad(lon2 - lon1);
        var a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2)
            ;
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var d = R * c; // Jarak dalam km
        return d * 1000; // Jarak dalam meter
    }

    function deg2rad(deg) {
        return deg * (Math.PI / 180)
    }

    function checkLocation() {
        if (navigator.geolocation) {
            locationStatus.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mendeteksi Lokasi...';
            locationError.classList.add('hidden');
            captureBtn.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    document.getElementById('lat').value = lat;
                    document.getElementById('long').value = lng;

                    // Cek jika Admin belum set lokasi (Lat/Lng 0 atau null)
                    if (schoolLat === 0 || schoolLng === 0) {
                        allowedToSubmit = true;
                        locationStatus.innerHTML = `<span class="text-amber-600 font-bold"><i class="fas fa-exclamation-circle"></i> Lokasi Sekolah Belum Diatur Admin</span>`;
                        locationError.innerHTML = `Sistem mengizinkan absensi sementara karena koordinat sekolah belum disetting oleh Admin.`;
                        locationError.classList.remove('hidden');
                        locationError.classList.replace('bg-red-100', 'bg-amber-100');
                        locationError.classList.replace('text-red-700', 'text-amber-800');
                        locationError.classList.replace('border-red-200', 'border-amber-200');

                        captureBtn.disabled = false;
                        captureBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        return;
                    }

                    // Hitung Jarak
                    const distance = getDistanceFromLatLonInM(lat, lng, schoolLat, schoolLng);

                    // Logic Peringatan
                    if (distance <= schoolRadius) {
                        allowedToSubmit = true;
                        locationStatus.innerHTML = `<span class="text-emerald-600 font-bold"><i class="fas fa-check-circle"></i> Dalam Jangkauan (${Math.round(distance)}m)</span>`;
                        captureBtn.disabled = false;
                        captureBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    } else {
                        allowedToSubmit = false; // Ubah ke true jika ingin bypass testing
                        locationStatus.innerHTML = `<span class="text-red-600 font-bold"><i class="fas fa-exclamation-triangle"></i> Di Luar Jangkauan</span>`;
                        locationError.innerHTML = `Jarak Anda: <b>${Math.round(distance)}m</b> dari sekolah. <br> Radius Maksimal: <b>${schoolRadius}m</b>. <br> Silakan mendekat ke sekolah.`;

                        // Reset style error ke merah
                        locationError.classList.remove('hidden');
                        locationError.classList.add('bg-red-100', 'text-red-700', 'border-red-200');
                        locationError.classList.remove('bg-amber-100', 'text-amber-800', 'border-amber-200');

                        captureBtn.disabled = true;
                    }
                },
                function (error) {
                    console.warn("Geolocation error:", error);
                    locationStatus.innerHTML = '<span class="text-red-500">Gagal mendeteksi lokasi atau GPS mati.</span>';
                    captureBtn.disabled = true;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            locationStatus.innerHTML = "Browser tidak mendukung Geolocation.";
        }
    }

    function resetUI() {
        video.classList.remove('hidden');
        photoPreview.classList.add('hidden');
        photoPreview.src = '';
        locationError.classList.add('hidden');
        locationStatus.innerHTML = '';
        retakeBtn.classList.add('hidden');
        captureBtn.innerHTML = 'Ambil Foto & Absen';
        captureBtn.onclick = takeSnapshot;

        // Reset Error Style
        locationError.classList.add('bg-red-100', 'text-red-700', 'border-red-200');
        locationError.classList.remove('bg-amber-100', 'text-amber-800', 'border-amber-200');
    }
</script>
@stop