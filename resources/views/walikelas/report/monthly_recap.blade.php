@extends('layouts.adminlte')

@section('title', 'Rekap Absensi Bulanan Kelas ' . ($class->name ?? ''))

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-calendar-check text-blue-600 mr-2"></i>
        <span>Rekap Bulanan Kelas {{ $class->name ?? 'N/A' }}</span>
    </h1>
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('walikelas.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
            <li>/</li>
            <li class="text-gray-600">Rekap Bulanan</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
<div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden p-5">

    {{-- Form Filter Bulan/Tahun --}}
    <div class="mb-5 border-b pb-4">
        <form action="{{ route('walikelas.report.monthly_recap') }}" method="GET" class="flex flex-wrap gap-4 items-center">
            <h5 class="text-md font-semibold text-gray-700">Filter Bulan:</h5>
            <div>
                <select name="month" class="form-control border-gray-300 rounded-lg shadow-sm text-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $currentMonthNum == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <select name="year" class="form-control border-gray-300 rounded-lg shadow-sm text-sm">
                    @for($y = \Carbon\Carbon::now()->year - 2; $y <= \Carbon\Carbon::now()->year; $y++)
                        <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="btn btn-primary bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-700 transition">
                Tampilkan
            </button>
            <a href="{{ route('walikelas.report.monthly_recap.export', ['month' => $currentMonthNum, 'year' => $currentYear]) }}" 
              class="btn btn-success bg-green-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-700 transition"
              target="_blank">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
        </form>
        <h4 class="text-2xl font-bold text-gray-900 mt-4">{{ $currentMonth }}</h4>
    </div>

    {{-- Tabel Rekap Absensi --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 text-sm">
            <thead class="bg-gray-800 text-white sticky top-0">
                <tr>
                    <th class="px-3 py-3 text-left font-semibold uppercase tracking-wider min-w-40 sticky left-0 bg-gray-800 border-r border-gray-700">Nama Siswa</th>
                    @for($i = 1; $i <= $daysInMonth; $i++)
                        <th class="px-1 py-3 text-center font-semibold uppercase tracking-wider w-10">
                            {{ $i }}
                        </th>
                    @endfor
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                // Definisikan Mapping Status untuk View
                $statusMap = [
                    'Hadir'     => ['char' => 'H', 'color' => 'bg-green-100 text-green-800'],
                    'Terlambat' => ['char' => 'T', 'color' => 'bg-amber-100 text-amber-800'],
                    'Sakit'     => ['char' => 'S', 'color' => 'bg-cyan-100 text-cyan-800'],
                    'Izin'      => ['char' => 'I', 'color' => 'bg-blue-100 text-blue-800'],
                    'Alpha'     => ['char' => 'A', 'color' => 'bg-red-100 text-red-800'],
                    'Pulang'    => ['char' => 'H', 'color' => 'bg-green-100 text-green-800'], // Jika Pulang, tampilkan sebagai Hadir (H)
                    'N/A'       => ['char' => '-', 'color' => 'bg-gray-100 text-gray-500'], // Status Masa Depan
                ];
                @endphp
                
                @forelse($recapData as $studentId => $data)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 whitespace-nowrap text-gray-900 font-semibold sticky left-0 bg-white border-r border-gray-200">{{ $data['name'] }}</td>
                        @for($i = 1; $i <= $daysInMonth; $i++)
                            @php
                                $status = $data['status_by_day'][$i];
                                
                                // Dapatkan mapping status
                                $display = $statusMap[$status] ?? $statusMap['Alpha']; 
                                $colorClass = $display['color'];
                                $displayStatus = $display['char'];
                            @endphp
                            <td class="px-1 py-2 text-center whitespace-nowrap">
                                <span class="inline-block w-6 h-6 leading-6 rounded-full text-xs font-semibold {{ $colorClass }}">
                                    {{ $displayStatus }}
                                </span>
                            </td>
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $daysInMonth + 1 }}" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data siswa di kelas ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@stop

@push('css')
<style>
/* --- CUSTOM CSS untuk tampilan tabel rekap bulanan --- */

/* Sticky Column Fix (Nama Siswa) */
.sticky-left-0 {
    position: sticky;
    left: 0;
    z-index: 5;
    background-color: #fff !important; 
}
/* Sticky Header */
.bg-gray-800 th {
    position: sticky;
    top: 0;
    z-index: 10;
}

/* Overwrite Backgrounds */
.bg-gray-800 { background-color: #1f2937 !important; }
.btn-primary { background-color: #2563eb !important; border-color: #2563eb; }
.hover\:bg-blue-700:hover { background-color: #1d4ed8 !important; }

/* Warna Status Absensi */
.text-green-800 { color: #065f46; } .bg-green-100 { background-color: #d1fae5; }
.text-amber-800 { color: #92400e; } .bg-amber-100 { background-color: #fef3c7; }
.text-cyan-800 { color: #0e7490; } .bg-cyan-100 { background-color: #cffafe; }
.text-blue-800 { color: #1e40af; } .bg-blue-100 { background-color: #dbeafe; }
.text-red-800 { color: #991b1b; } .bg-red-100 { background-color: #fee2e2; }
.text-gray-700 { color: #4b5563; } .bg-gray-300 { background-color: #d1d5db; }

/* Styling untuk sel status (lingkaran) */
td .inline-block {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush