@extends('layouts.adminlte')

@section('title', 'Hasil Laporan Kelas ' . $class->name)

@section('content_header')
{{-- HEADER: Menggunakan Flexbox Tailwind --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-chart-bar text-red-600 mr-2"></i>
        <span>Hasil Absensi Kelas {{ $class->name }}</span>
    </h1>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('walikelas.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('walikelas.report.index') }}" class="text-blue-600 hover:text-blue-800">Filter</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Hasil Laporan</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        
        {{-- CARD HEADER --}}
        <div class="p-4 border-b border-gray-100 flex flex-col lg:flex-row justify-between items-start lg:items-center">
            <h3 class="text-xl font-semibold text-gray-800 mb-3 lg:mb-0">
                Laporan dari **{{ $startDate->format('d/m/Y') }}** s/d **{{ $endDate->format('d/m/Y') }}**
            </h3>
            
            {{-- Tombol Aksi (Export) --}}
            <div class="flex space-x-3 flex-wrap">
                
                {{-- 🚨 FORM EXPORT EXCEL (Menggunakan method GET) --}}
                <form action="{{ route('report.export.excel') }}" method="GET" class="inline-flex">
                    <input type="hidden" name="class_id" value="{{ $class->id }}">
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 transition duration-150" title="Unduh ke Excel">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </button>
                </form>
                
                {{-- 🚨 FORM EXPORT PDF --}}
                <form action="{{ route('report.export.pdf') }}" method="GET" class="inline-flex" target="_blank">
                    <input type="hidden" name="class_id" value="{{ $class->id }}">
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm text-white bg-red-600 hover:bg-red-700 transition duration-150" title="Unduh ke PDF">
                        <i class="fas fa-file-pdf mr-1"></i> Export PDF
                    </button>
                </form>
                
                <a href="{{ route('walikelas.report.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-gray-200 hover:bg-gray-300 transition duration-150" title="Kembali ke Filter">
                    <i class="fas fa-undo mr-1"></i> Filter Baru
                </a>
            </div>
        </div>
        
        <div class="p-5">
            @if($absences->isEmpty())
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 text-center rounded-lg" role="alert">
                    <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                    Tidak ada data absensi yang tercatat untuk periode yang dipilih di Kelas {{ $class->name }}.
                </div>
            @else
                {{-- Wrapper Tabel dengan Scroll Horizontal --}}
                <div class="overflow-x-auto shadow-sm border border-gray-200 rounded-lg"> 
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider w-12">#</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider min-w-40">Nama Siswa</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider min-w-32">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider min-w-32">Waktu Masuk</th> 
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider min-w-32">Waktu Pulang</th> 
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider w-36">Status Masuk</th> 
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider min-w-32">Keterlambatan (Min)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($absences as $absence)
                            @php
                                $statusMap = [
                                    'Hadir' => 'bg-green-100 text-green-800',
                                    'Terlambat' => 'bg-amber-100 text-amber-800',
                                    'Absen' => 'bg-red-100 text-red-800',
                                    'Izin' => 'bg-blue-100 text-blue-800'
                                ];
                                $statusClass = $statusMap[$absence->status] ?? 'bg-gray-200 text-gray-700';
                            @endphp
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $absence->student->name ?? 'Siswa Dihapus' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $absence->attendance_time->format('d/m/Y') }}</td>
                                
                                {{-- WAKTU MASUK --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $absence->attendance_time->format('H:i:s') }}</td>
                                
                                {{-- WAKTU PULANG --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($absence->checkout_time)
                                        {{ $absence->checkout_time->format('H:i:s') }}
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-300 text-gray-700">Belum Pulang</span>
                                    @endif
                                </td>
                                
                                {{-- STATUS MASUK --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">{{ $absence->status }}</span>
                                </td>
                                
                                {{-- KETERLAMBATAN --}}
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
/* --- MINIMAL CUSTOM CSS FOR TAILWIND --- */
.text-red-600 { color: #dc3545; } 
.text-blue-600 { color: #2563eb; }
.text-amber-500 { color: #f59e0b; }
.text-green-600 { color: #059669; }
</style>
@stop