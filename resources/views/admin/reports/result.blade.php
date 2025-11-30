@extends('layouts.adminlte')

@section('title', 'Hasil Laporan Absensi')

@section('content_header')
{{-- HEADER: Menggunakan Flexbox Tailwind --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-chart-bar text-teal-500 mr-2"></i>
        <span>Hasil Laporan Absensi</span>
    </h1>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">Home</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('report.index') }}" class="text-blue-600 hover:text-blue-800">Filter Laporan</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Hasil</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        
        {{-- CARD HEADER --}}
        <div class="p-4 border-b border-gray-100 flex flex-col lg:flex-row justify-between items-start lg:items-center">
            <h3 class="text-xl font-semibold text-gray-800 mb-3 lg:mb-0">
                Laporan Absensi 
                @if($class)
                    Kelas {{ $class->name }} 
                @endif
                (Tanggal {{ $startDate->format('d/m/Y') }} s/d {{ $endDate->format('d/m/Y') }})
            </h3>
            
            {{-- Tombol Aksi (Export) --}}
            <div class="flex space-x-3 flex-wrap">
                
                {{-- 🚨 FORM EXPORT EXCEL --}}
                <form action="{{ route('report.export.excel') }}" method="GET" class="inline-flex">
                    <input type="hidden" name="class_id" value="{{ request('class_id') }}">
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 transition duration-150" title="Unduh ke Excel">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </button>
                </form>
                
                {{-- 🚨 FORM EXPORT PDF --}}
                <form action="{{ route('report.export.pdf') }}" method="GET" class="inline-flex" target="_blank">
                    <input type="hidden" name="class_id" value="{{ request('class_id') }}">
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm text-white bg-red-600 hover:bg-red-700 transition duration-150" title="Unduh ke PDF">
                        <i class="fas fa-file-pdf mr-1"></i> Export PDF
                    </button>
                </form>
                
                <a href="{{ route('report.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-gray-200 hover:bg-gray-300 transition duration-150" title="Kembali ke Filter">
                    <i class="fas fa-undo mr-1"></i> Filter Baru
                </a>
            </div>
        </div>
        
        <div class="p-5">
            @if($absences->isEmpty())
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 text-center rounded-lg" role="alert">
                    <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                    Tidak ada data absensi yang tercatat untuk periode yang dipilih.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider w-12">#</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider min-w-40">Nama Siswa</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider min-w-24">Kelas</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider min-w-40">Waktu Absen</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider w-24">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider min-w-32">Keterlambatan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($absences as $absence)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $absence->student->name ?? 'Siswa Dihapus' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $absence->student->class->name ?? 'Kelas N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $absence->attendance_time->format('d/m/Y H:i:s') }}
                                    <small class="text-gray-500 text-xs block">{{ $absence->attendance_time->diffForHumans() }}</small>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusMap = [
                                            'Hadir' => 'bg-green-100 text-green-800',
                                            'Terlambat' => 'bg-amber-100 text-amber-800',
                                            'Absen' => 'bg-red-100 text-red-800',
                                            'Izin' => 'bg-blue-100 text-blue-800'
                                        ];
                                        $statusClass = $statusMap[$absence->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ $absence->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if ($absence->status == 'Terlambat')
                                        {{ $absence->late_duration ?? 'N/A' }} menit
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@stop

@section('css')
<style>
/* Minmal CSS untuk warna Tailwind yang belum terdefinisi secara utility */
.text-teal-500 { color: #20c997; } 
</style>
@stop