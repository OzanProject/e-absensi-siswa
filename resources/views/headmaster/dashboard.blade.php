@extends('layouts.adminlte')

@section('title', 'Dashboard Kepala Sekolah')

@section('content_header')
<div class="flex items-center justify-between pb-4">
    <div>
        <h1 class="m-0 text-gray-800 font-extrabold text-2xl tracking-tight flex items-center">
            <i class="fas fa-chart-line text-indigo-600 mr-3"></i> Dashboard Eksekutif
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Ringkasan aktivitas dan kinerja sekolah hari ini.</p>
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

{{-- 1. EARLY WARNING SYSTEM --}}
@if(count($lowAttendanceClasses) > 0)
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-500"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-bold text-red-800">Perhatian: Kehadiran Rendah Terdeteksi (< 75%)</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($lowAttendanceClasses as $alert)
                                    <li>
                                        Kelas <strong>{{ $alert['name'] }}</strong> hanya <strong>{{ $alert['rate'] }}%</strong>
                                        kehadiran.
                                        <span class="text-xs text-red-600">(Wali Kelas: {{ $alert['homeroom'] }})</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
            </div>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
    {{-- STAT 1: Total Kelas --}}
    <div
        class="bg-white rounded-3xl p-6 shadow-lg shadow-indigo-100 border border-indigo-50 relative overflow-hidden group hover:-translate-y-1 transition-transform">
        <div class="flex justify-between items-start z-10 relative">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Total Kelas</p>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $totalClasses }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fas fa-school"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-xs text-gray-400">
            <span class="text-indigo-600 font-bold mr-1">100%</span> Aktif
        </div>
    </div>

    {{-- STAT 2: Total Siswa --}}
    <div
        class="bg-white rounded-3xl p-6 shadow-lg shadow-purple-100 border border-purple-50 relative overflow-hidden group hover:-translate-y-1 transition-transform">
        <div class="flex justify-between items-start z-10 relative">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Total Siswa</p>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $totalStudents }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                <i class="fas fa-users mb-1"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-xs text-gray-400">
            <span class="bg-green-100 text-green-700 font-bold px-1.5 py-0.5 rounded mr-2">
                {{ $presentToday }} Hadir
            </span> Hari ini
        </div>
    </div>

    {{-- STAT 3: Total Guru --}}
    <div
        class="bg-white rounded-3xl p-6 shadow-lg shadow-amber-100 border border-amber-50 relative overflow-hidden group hover:-translate-y-1 transition-transform">
        <div class="flex justify-between items-start z-10 relative">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Total Guru</p>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $totalTeachers }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-xs text-gray-400">
            <span class="text-amber-600 font-bold mr-1">{{ $schedulesToday }}</span> Jadwal Hari Ini
        </div>
    </div>

    {{-- STAT 4: Kinerja Jurnal --}}
    <div
        class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden group hover:-translate-y-1 transition-transform">
        <div class="flex justify-between items-start z-10 relative">
            <div>
                <p class="text-indigo-200 text-xs font-bold uppercase tracking-widest mb-1">Kepatuhan Jurnal</p>
                <h3 class="text-3xl font-extrabold">{{ $journalPercentage }}%</h3>
            </div>
            <div
                class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white text-xl border border-white/10">
                <i class="fas fa-chart-pie"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-xs text-indigo-100">
            <div class="w-full bg-black/20 rounded-full h-1.5 mr-2">
                <div class="bg-white h-1.5 rounded-full" style="width: {{ $journalPercentage }}%"></div>
            </div>
            {{ $journalsToday }}/{{ $schedulesToday }}
        </div>

        <div class="absolute -right-6 -bottom-6 text-white opacity-10 transform -rotate-12">
            <i class="fas fa-tasks text-8xl"></i>
        </div>
    </div>
</div>

{{-- 2. CHARTS SECTION --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    {{-- Chart Tren Kehadiran --}}
    <div class="lg:col-span-2 bg-white rounded-3xl shadow-xl border border-gray-100 p-6 min-w-0">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-gray-800 text-lg flex items-center">
                <i class="fas fa-chart-line text-indigo-500 mr-2"></i> Tren Kehadiran Siswa
            </h3>
            <span class="text-xs font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded">Tahun Ini</span>
        </div>
        <div class="relative h-72">
            <canvas id="attendanceTrendChart"></canvas>
        </div>
    </div>

    {{-- Kinerja Guru --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6 flex flex-col">
        <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center">
            <i class="fas fa-medal text-amber-500 mr-2"></i> Top 5 Guru Rajin
        </h3>
        <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
            <ul class="space-y-4">
                @foreach($topTeachers as $index => $guru)
                    <li
                        class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100 hover:bg-amber-50 transition-colors">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 rounded-full {{ $index == 0 ? 'bg-amber-100 text-amber-600' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center font-bold text-sm mr-3">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800 line-clamp-1">{{ $guru['name'] }}</p>
                                <p class="text-xs text-gray-500">
                                    <span class="font-bold text-indigo-600">{{ $guru['count'] }}</span> Jurnal
                                    <span class="text-gray-300 mx-1">|</span>
                                    <span class="text-gray-400" title="Beban Mengajar">{{ $guru['schedules_count'] }}
                                        Jam/Minggu</span>
                                </p>
                            </div>
                        </div>
                        @if($index == 0)
                            <i class="fas fa-crown text-amber-400"></i>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        @if(count($bottomTeachers) > 0)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <h4 class="text-xs font-bold text-red-400 uppercase tracking-wider mb-3">Perlu Perhatian (Bottom 5)</h4>
                <div class="space-y-2">
                    @foreach($bottomTeachers as $guru)
                        <div class="flex justify-between items-center text-xs p-2 rounded hover:bg-red-50 transition-colors">
                            <div>
                                <span class="font-semibold text-gray-700 block">{{ $guru['name'] }}</span>
                                <span class="text-gray-400 text-[10px]">{{ $guru['schedules_count'] }} Jam/Minggu</span>
                            </div>
                            <span class="text-red-500 font-bold bg-red-100 px-2 py-0.5 rounded-full">{{ $guru['count'] }}
                                Jurnal</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

{{-- 3. SISWA BERMASALAH (DISCIPLINE ANALYTICS) --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    {{-- Siswa Sering Terlambat --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6 min-w-0">
        <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center">
            <i class="fas fa-user-clock text-amber-500 mr-2"></i> Top 5 Sering Terlambat
            <span class="text-xs font-normal text-gray-400 ml-auto">Bulan Ini</span>
        </h3>
        <div class="overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg">Siswa</th>
                        <th class="px-4 py-3 text-right rounded-r-lg">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($topLateStudents as $index => $s)
                        <tr class="hover:bg-amber-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-800">{{ $s->name }}</div>
                                <div class="text-xs text-gray-500">{{ $s->class_name }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="bg-amber-100 text-amber-700 py-1 px-2.5 rounded-full font-bold text-xs">
                                    {{ $s->total }}x
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-gray-400">
                                <i class="fas fa-check-circle text-green-400 text-2xl mb-2"></i>
                                <p>Tidak ada keterlambatan bulan ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Siswa Sering Alpha (Bolos) --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6 min-w-0">
        <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center">
            <i class="fas fa-user-times text-red-500 mr-2"></i> Top 5 Sering Alpha (Bolos)
            <span class="text-xs font-normal text-gray-400 ml-auto">Bulan Ini</span>
        </h3>
        <div class="overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg">Siswa</th>
                        <th class="px-4 py-3 text-right rounded-r-lg">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($topAlphaStudents as $index => $s)
                        <tr class="hover:bg-red-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-800">{{ $s->name }}</div>
                                <div class="text-xs text-gray-500">{{ $s->class_name }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="bg-red-100 text-red-700 py-1 px-2.5 rounded-full font-bold text-xs">
                                    {{ $s->total }}x
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-gray-400">
                                <i class="fas fa-check-circle text-green-400 text-2xl mb-2"></i>
                                <p>Tidak ada yang bolos bulan ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    {{-- MENU AKSES CEPAT --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 min-w-0">
        <h3 class="font-bold text-gray-800 text-lg mb-6 flex items-center">
            <i class="fas fa-rocket text-indigo-500 mr-2"></i> Menu Cepat
        </h3>

        <div class="grid grid-cols-2 gap-4">
            {{-- MENU 1: LAPORAN ABSENSI (DETAIL) --}}
            <a href="{{ route('headmaster.report.index') }}"
                class="block group bg-gray-50 p-6 rounded-2xl border border-gray-100 hover:bg-indigo-50 hover:border-indigo-100 transition-all text-center">
                <div
                    class="w-14 h-14 mx-auto bg-white rounded-xl shadow-sm text-indigo-600 flex items-center justify-center text-2xl mb-3 group-hover:scale-110 transition-transform">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h4 class="font-bold text-gray-800 group-hover:text-indigo-700">Laporan Absensi</h4>
                <p class="text-xs text-gray-500 mt-1">Filter & Export Log Absensi</p>
            </a>

            {{-- MENU 2: REKAP KELAS (YANG LAMA) --}}
            <a href="{{ route('headmaster.report.recap') }}"
                class="block group bg-gray-50 p-6 rounded-2xl border border-gray-100 hover:bg-purple-50 hover:border-purple-100 transition-all text-center">
                <div
                    class="w-14 h-14 mx-auto bg-white rounded-xl shadow-sm text-purple-600 flex items-center justify-center text-2xl mb-3 group-hover:scale-110 transition-transform">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <h4 class="font-bold text-gray-800 group-hover:text-purple-700">Rekap Kelas</h4>
                <p class="text-xs text-gray-500 mt-1">Ringkasan per Kelas</p>
            </a>
        </div>
    </div>
    {{-- INFO KEPALA SEKOLAH --}}
    <div
        class="relative bg-gradient-to-r from-gray-900 to-gray-800 rounded-3xl shadow-xl p-8 text-white overflow-hidden">
        <div class="relative z-10">
            <span
                class="px-3 py-1 rounded-full bg-white/20 text-xs font-bold uppercase tracking-wider mb-4 inline-block border border-white/10">
                Kepala Sekolah Area
            </span>
            <h2 class="text-2xl font-bold mb-2">Selamat Datang, {{ Auth::user()->name }}</h2>
            <p class="text-gray-400 text-sm leading-relaxed mb-6">
                Ini adalah pusat kontrol Anda untuk memantau aktivitas akademik sekolah. Data kehadiran dan jurnal
                mengajar diperbarui secara real-time.
            </p>

            <div class="flex items-center space-x-4">
                <div class="text-center">
                    <span class="block text-2xl font-bold">{{ date('H:i') }}</span>
                    <span class="text-xs text-gray-500">WIB</span>
                </div>
                <div class="h-8 w-px bg-gray-700"></div>
                <div class="text-xs text-gray-400">
                    Gunakan menu Laporan untuk melihat detail per kelas.
                </div>
            </div>
        </div>

        <div class="absolute right-0 bottom-0 opacity-10">
            <i class="fas fa-building text-9xl transform translate-x-4 translate-y-4"></i>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('attendanceTrendChart').getContext('2d');

        // Data dari Controller
        const monthlyData = @json($monthlyAttendance);
        const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        // Gradient Background
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)'); // Indigo
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Persentase Kehadiran (%)',
                    data: monthlyData,
                    borderColor: '#4f46e5', // Indigo 600
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function (context) {
                                return context.parsed.y + '% Kehadiran';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: '#f3f4f6',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            font: { size: 11, family: "'Inter', sans-serif" },
                            color: '#9ca3af'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11, family: "'Inter', sans-serif" },
                            color: '#9ca3af'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });
    });
</script>
@stop