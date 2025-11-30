@extends('layouts.adminlte')

@section('title', 'Dashboard Orang Tua')

@section('content_header')
{{-- HEADER: Menggunakan Flexbox Tailwind --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-home text-indigo-600 mr-2"></i>
        <span>Dashboard Orang Tua</span>
    </h1>
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('orangtua.dashboard') }}" class="text-blue-600 hover:text-blue-800">Home</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Dashboard</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="space-y-6">
        
        @if(!$parentRecord)
            {{-- KASUS 1: AKUN BELUM TERHUBUNG --}}
            <div class="bg-white rounded-xl shadow-lg border border-red-500/50">
                <div class="p-8 text-center">
                    <i class="fas fa-times-circle fa-3x text-red-600 mb-4"></i>
                    <h3 class="text-2xl font-bold text-red-600 mb-3">Akun Belum Terhubung</h3>
                    <p class="lead text-gray-600 mb-0">
                        Mohon hubungi **Super Admin** untuk menghubungkan akun Anda dengan data anak.
                    </p>
                </div>
            </div>
        @else
            
            {{-- Alert Welcome --}}
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg relative alert-dismissible" role="alert">
                <i class="fas fa-hand-wave mr-2"></i> Selamat datang, **{{ $parentRecord->name }}**. Berikut adalah ringkasan absensi anak-anak Anda.
            </div>

            {{-- 💡 BAGIAN 1: STATISTIK AKUMULATIF (Responsif Grid) --}}
            <h4 class="text-xl font-bold text-gray-800 mt-6 mb-3">Statistik Absensi Akumulatif</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                
                @php
                    // Data Card
                    $statCards = [
                        ['label' => 'Total Alpha', 'count' => $totalSIA['Alpha'] ?? 0, 'icon' => 'fas fa-times-circle', 'color' => 'bg-red-500', 'border' => 'border-red-500'],
                        ['label' => 'Total Sakit', 'count' => $totalSIA['Sakit'] ?? 0, 'icon' => 'fas fa-hospital', 'color' => 'bg-blue-500', 'border' => 'border-blue-500'],
                        ['label' => 'Total Izin', 'count' => $totalSIA['Izin'] ?? 0, 'icon' => 'fas fa-sticky-note', 'color' => 'bg-yellow-500', 'border' => 'border-yellow-500'],
                        ['label' => 'Total Terlambat', 'count' => $totalSIA['Terlambat'] ?? 0, 'icon' => 'fas fa-clock', 'color' => 'bg-purple-500', 'border' => 'border-purple-500'],
                    ];
                @endphp
                
                @foreach($statCards as $card)
                    <div class="bg-white p-5 rounded-xl shadow-md border-t-4 {{ $card['border'] }} hover:shadow-lg transition duration-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-3xl font-bold text-gray-800">{{ $card['count'] }}</div>
                                <p class="text-sm text-gray-500 mt-1">{{ $card['label'] }}</p>
                            </div>
                            <div class="text-3xl {{ $card['color'] }} opacity-70">
                                <i class="{{ $card['icon'] }}"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- 💡 BAGIAN 2: RIWAYAT ABSENSI TERAKHIR --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 mt-6">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-history mr-2 text-gray-500"></i> Riwayat Absensi Anak (30 Hari Terakhir)
                    </h3>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Anak</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Kelas</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Masuk</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Pulang</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($absences as $absence)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $absence->student->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $absence->student->class?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $absence->attendance_time->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $absence->attendance_time->format('H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $absence->checkout_time?->format('H:i') ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $statusText = $absence->status;
                                            $badgeClass = [
                                                'Hadir' => 'bg-green-100 text-green-800', 
                                                'Terlambat' => 'bg-amber-100 text-amber-800', 
                                                'Izin' => 'bg-blue-100 text-blue-800',
                                                'Sakit' => 'bg-cyan-100 text-cyan-800', 
                                                'Alpha' => 'bg-red-100 text-red-800'
                                            ][$statusText] ?? 'bg-gray-100 text-gray-600';
                                            
                                            if ($absence->checkout_time) {
                                                $badgeClass = 'bg-indigo-100 text-indigo-800'; 
                                                $statusText .= ' / Pulang';
                                            }
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        <a href="{{ route('orangtua.absensi.show_detail', $absence->id) }}" class="text-blue-600 hover:text-blue-800" title="Lihat Detail & Koreksi Log">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-muted">Belum ada riwayat absensi tercatat dalam 30 hari terakhir.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            {{-- Daftar Anak yang Diampu --}}
            <div class="mt-4 border-t pt-3">
                <p class="small mb-1 text-gray-600">Anak yang Anda ampu:</p>
                @foreach($parentRecord->students as $student)
                    <span class="badge bg-indigo-500 text-white mr-1 mb-1">{{ $student->name }} ({{ $student->class->name ?? 'N/A' }})</span>
                @endforeach
            </div>
            
        @endif
    </div>
@stop

@section('css')
<style>
/* --- TAILWIND COLORS MAPPING --- */
.text-indigo-600 { color: #4f46e5; }
.bg-indigo-500 { background-color: #6366f1 !important; }
.border-red-500 { border-color: #ef4444; }
.bg-red-500 { background-color: #ef4444 !important; }
.bg-blue-500 { background-color: #3b82f6 !important; }
.bg-yellow-500 { background-color: #f59e0b !important; }
.bg-purple-500 { background-color: #8b5cf6 !important; }

/* Badge Colors */
.bg-green-100 { background-color: #d1fae5; }
.text-green-800 { color: #065f46; }
.bg-red-100 { background-color: #fee2e2; }
.text-red-800 { color: #991b1b; }
.bg-indigo-100 { background-color: #e0e7ff; }
.text-indigo-800 { color: #3730a3; }
.bg-amber-100 { background-color: #fef3c7; }
.text-amber-800 { color: #b45309; }
.bg-cyan-100 { background-color: #cffafe; }
.text-cyan-800 { color: #0e7490; }

/* Table and General Styling */
.bg-gray-800 { background-color: #1f2937 !important; }
.alert-dismissible { border-radius: 0.5rem; }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Auto-dismiss alerts (Tailwind alerts)
        setTimeout(function() {
            $('.alert-dismissible').fadeOut(400);
        }, 5000);
    });
</script>
@endsection