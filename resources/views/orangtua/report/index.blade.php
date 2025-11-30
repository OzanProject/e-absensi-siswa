@extends('layouts.adminlte')

@section('title', 'Riwayat Absensi Anak')

@section('content_header')
{{-- HEADER: Menggunakan Flexbox Tailwind --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-list-alt text-blue-600 mr-2"></i>
        <span>Riwayat Absensi Anak (30 Hari Terakhir)</span>
    </h1>
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('orangtua.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Riwayat Absensi</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="card shadow-lg border-0 glassmorphism-card">
        
        {{-- CARD HEADER DENGAN TOMBOL EXPORT --}}
        <div class="card-header bg-white border-0 p-4">
            <div class="flex justify-between items-center flex-wrap">
                
                {{-- Kiri: Judul dan Keterangan --}}
                <div class="mb-2 sm:mb-0">
                    <h3 class="card-title custom-card-title mb-1"><i class="fas fa-table mr-1"></i> Data Kehadiran 30 Hari</h3>
                    <p class="small text-muted">Riwayat absensi semua anak yang terhubung dengan akun Anda.</p>
                </div>
                
                {{-- Kanan: Tombol Export (Dibuat lebih robust) --}}
                <div class="btn-group flex flex-wrap gap-2 sm:gap-1 flex-shrink-0">
                    <a href="{{ route('orangtua.report.export', ['format' => 'excel']) }}" 
                       class="btn btn-success btn-sm text-white shadow-sm">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                    <a href="{{ route('orangtua.report.export', ['format' => 'pdf']) }}" 
                       class="btn btn-danger btn-sm text-white shadow-sm">
                        <i class="fas fa-file-pdf mr-1"></i> Export PDF
                    </a>
                </div>
                
            </div>
        </div>
        
        <div class="card-body p-0">
            
            @if($parentRecord->students->isEmpty())
                <div class="alert alert-warning m-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Akun Anda terhubung, tetapi belum ada siswa yang terdaftar.
                </div>
            @else
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
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($absences as $absence)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $absence->student->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $absence->student->class?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $absence->attendance_time->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $absence->attendance_time->format('H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $absence->checkout_time?->format('H:i') ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @php
                                        // Logika badge status
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
                                {{-- LINK KE DETAIL ABSENSI --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    <a href="{{ route('orangtua.absensi.show_detail', $absence->id) }}" class="text-blue-600 hover:text-blue-800" title="Lihat Detail & Log Koreksi">
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
            @endif
        </div>
        
        <div class="card-footer clearfix">
            {{ $absences->links('pagination::bootstrap-4') }} 
        </div>
    </div>
@stop

@section('js')
<script>
    // Auto-dismiss alerts (jika ada)
    setTimeout(function() {
         $('.alert').fadeOut(400);
    }, 5000);
</script>
@endsection

@push('css')
<style>
/* --- CUSTOM CSS TAILWIND MAPPING --- */
.text-blue-600 { color: #2563eb; }
.bg-blue-100 { background-color: #dbeafe; }
.text-blue-800 { color: #1e40af; }
.bg-indigo-100 { background-color: #e0e7ff; }
.text-indigo-800 { color: #3730a3; }
.bg-gray-800 { background-color: #1f2937 !important; }

/* Warna Badge Khusus */
.bg-green-100 { background-color: #d1fae5; }
.text-green-800 { color: #065f46; }
.bg-amber-100 { background-color: #fef3c7; }
.text-amber-800 { color: #b45309; }
.bg-cyan-100 { background-color: #cffafe; }
.text-cyan-800 { color: #0e7490; }
.bg-red-100 { background-color: #fee2e2; }
.text-red-800 { color: #991b1b; }

/* Glassmorphism Card Style (dari dashboard) */
.glassmorphism-card {
    background: rgba(255, 255, 255, 0.9) !important;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    border-radius: 1rem !important;
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
}
.custom-card-title { font-size: 1.25rem; font-weight: 600; }

/* Tombol Export */
.btn-success { background-color: #198754 !important; border-color: #198754; color: #fff !important; }
.btn-danger { background-color: #dc3545 !important; border-color: #dc3545; color: #fff !important; }

/* Perbaikan AdminLTE */
.btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; }

/* FIXES */
.btn-group { display: inline-flex; } /* Memastikan btn-group tetap flex */

</style>
@endpush