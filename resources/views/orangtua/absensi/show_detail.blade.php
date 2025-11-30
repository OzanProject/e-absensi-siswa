@extends('layouts.adminlte')

@section('title', 'Detail Absensi Anak')

@section('content_header')
<div class="custom-header-container">
    <h1 class="custom-header-title">
        <i class="fas fa-clipboard-list custom-header-icon" style="color: #007bff;"></i>
        <span>Detail Absensi - {{ $absence->student->name ?? 'Anak' }}</span>
    </h1>
</div>
@stop

@section('content')
    <div class="card shadow-lg border-0">
        <div class="card-header bg-white">
            <h3 class="card-title">Riwayat Tanggal {{ $absence->attendance_time->format('d M Y') }}</h3>
        </div>
        <div class="card-body">
            
            {{-- INFORMASI UTAMA --}}
            <div class="row mb-5">
                <div class="col-md-6">
                    <p><strong>Nama Anak:</strong> {{ $absence->student->name ?? '-' }}</p>
                    <p><strong>Kelas:</strong> {{ $absence->student->class->name ?? '-' }}</p>
                    <p><strong>Status Tercatat:</strong> <span class="badge badge-lg badge-{{ $absence->status == 'Alpha' ? 'danger' : ($absence->status == 'Terlambat' ? 'warning' : 'success') }}">{{ $absence->status }}</span></p>
                    <p><strong>Keterangan:</strong> {{ $absence->notes ?? 'Tidak ada' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Waktu Masuk:</strong> {{ $absence->attendance_time->format('H:i:s') }}</p>
                    <p><strong>Waktu Pulang:</strong> {{ $absence->checkout_time?->format('H:i:s') ?? '-' }}</p>
                    @if($absence->late_duration)
                        <p><strong>Durasi Terlambat:</strong> {{ $absence->late_duration }} menit</p>
                    @endif
                </div>
            </div>
            
            {{-- LOG AUDIT KOREKSI --}}
            <h5 class="mb-3 border-top pt-3">Log Koreksi Manual</h5>
            @if($absence->is_manual_corrected)
                <div class="alert alert-info">
                    <p>Record ini telah **dikoreksi** oleh staf sekolah.</p>
                    <p><strong>Pengoreksi Terakhir:</strong> {{ $absence->corrected_by ?? 'N/A' }}</p>
                    <p><strong>Alasan Koreksi:</strong> <em>{{ $absence->correction_note ?? 'Tidak ada catatan audit.' }}</em></p>
                </div>
            @else
                <div class="alert alert-success">
                    Data ini tercatat secara **otomatis** melalui sistem scan dan belum pernah diubah.
                </div>
            @endif

            <a href="{{ route('orangtua.dashboard') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Kembali</a>

        </div>
    </div>
@stop