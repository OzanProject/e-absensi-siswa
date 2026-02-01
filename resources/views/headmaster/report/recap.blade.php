@extends('layouts.adminlte')

@section('title', 'Laporan Kehadiran Kelas')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-4">
    <div>
        <h1 class="m-0 text-gray-800 font-extrabold text-2xl tracking-tight flex items-center">
            <i class="fas fa-clipboard-list text-indigo-600 mr-3"></i> Laporan Kehadiran
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Rekapitulasi kehadiran siswa per kelas bulan ini.</p>
    </div>

    <div class="flex items-center space-x-3 mt-4 sm:mt-0">
        {{-- Back Button --}}
        <a href="{{ route('headmaster.report.index') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-bold rounded-xl shadow-sm transition-all hover:-translate-y-0.5"
            title="Kembali ke Filter Detail">
            <i class="fas fa-arrow-left mr-2"></i> Laporan Detail
        </a>

        {{-- Filter Bulan --}}
        <form method="GET" action="{{ route('headmaster.report.recap') }}"
            class="flex items-center bg-white rounded-xl shadow-sm border border-gray-200 p-1">
            <input type="month" name="month" value="{{ $selectedMonth }}" onchange="this.form.submit()"
                class="border-0 focus:ring-0 text-sm font-bold text-gray-600 bg-transparent rounded-lg cursor-pointer">
        </form>

        <a href="{{ route('headmaster.report.export.excel', ['month' => $selectedMonth]) }}"
            class="hidden inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-sm transition-all hover:-translate-y-0.5"
            title="Export Excel (Segera Hadir)">
            <i class="fas fa-file-excel mr-2"></i> Export
        </a>
    </div>
</div>
@stop

@section('content')
<div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
    <div class="p-0 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead
                class="bg-gray-50/50 text-xs uppercase tracking-wider text-gray-500 font-extrabold border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 w-16 text-center">#</th>
                    <th class="px-6 py-4">Nama Kelas</th>
                    <th class="px-6 py-4">Wali Kelas</th>
                    <th class="px-6 py-4 text-center">Jurnal Guru</th>
                    <th class="px-6 py-4 w-1/3">Statistik Kehadiran</th>
                    <th class="px-6 py-4 text-center">Rate</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($reportData as $index => $data)
                    <tr class="hover:bg-indigo-50/10 transition-colors group">
                        <td class="px-6 py-4 text-center text-gray-400 font-mono text-sm">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800 text-sm group-hover:text-indigo-700 transition">
                                {{ $data['class']->name }}
                            </div>
                            <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $data['class']->major ?? 'Umum' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($data['class']->homeroomTeacher && $data['class']->homeroomTeacher->user)
                                <div class="flex items-center">
                                    <div
                                        class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-[10px] font-bold mr-2">
                                        {{ substr($data['class']->homeroomTeacher->user->name, 0, 1) }}
                                    </div>
                                    <span
                                        class="text-sm font-medium text-gray-600">{{ $data['class']->homeroomTeacher->user->name }}</span>
                                </div>
                            @else
                                <span class="text-xs text-red-500 italic bg-red-50 px-2 py-1 rounded">Belum ada Wali
                                    Kelas</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-bold text-gray-700">{{ $data['total_journals'] }}</span>
                            <span class="text-xs text-gray-400 ml-1">Sesi</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-1 h-2 rounded-full overflow-hidden bg-gray-100 mb-2">
                                @php $total = $data['hadir'] + $data['sakit'] + $data['izin'] + $data['alpha']; @endphp
                                @if($total > 0)
                                    <div class="h-full bg-emerald-500" style="width: {{ ($data['hadir'] / $total) * 100 }}%"
                                        title="Hadir: {{ $data['hadir'] }}"></div>
                                    <div class="h-full bg-blue-400" style="width: {{ ($data['sakit'] / $total) * 100 }}%"
                                        title="Sakit: {{ $data['sakit'] }}"></div>
                                    <div class="h-full bg-amber-400" style="width: {{ ($data['izin'] / $total) * 100 }}%"
                                        title="Izin: {{ $data['izin'] }}"></div>
                                    <div class="h-full bg-red-500" style="width: {{ ($data['alpha'] / $total) * 100 }}%"
                                        title="Alpha: {{ $data['alpha'] }}"></div>
                                @endif
                            </div>
                            <div class="flex justify-between text-[10px] text-gray-500 px-1">
                                <span><i class="fas fa-check text-emerald-500 mr-1"></i>{{ $data['hadir'] }}</span>
                                <span><i class="fas fa-procedures text-blue-400 mr-1"></i>{{ $data['sakit'] }}</span>
                                <span><i class="fas fa-envelope text-amber-400 mr-1"></i>{{ $data['izin'] }}</span>
                                <span><i class="fas fa-times text-red-500 mr-1"></i>{{ $data['alpha'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $rate = $data['rate'];
                                $color = $rate >= 90 ? 'text-emerald-600 bg-emerald-50 border-emerald-100' :
                                    ($rate >= 75 ? 'text-amber-600 bg-amber-50 border-amber-100' : 'text-red-600 bg-red-50 border-red-100');
                            @endphp
                            <span class="px-3 py-1 rounded-lg text-sm font-extrabold border {{ $color }}">
                                {{ $rate }}%
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-database text-gray-300 text-2xl"></i>
                            </div>
                            <p class="font-medium">Belum ada data untuk periode ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop