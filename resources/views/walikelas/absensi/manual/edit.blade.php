@extends('layouts.adminlte')

@section('title', 'Edit Absensi: ' . ($attendance->student->name ?? 'Siswa'))

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Amber/Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-user-edit text-amber-500 mr-2"></i>
        <span>Edit Absensi: {{ $attendance->student->name ?? 'Siswa' }}</span>
    </h1>
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('walikelas.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('walikelas.absensi.manual.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Absensi Manual</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Edit</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    {{-- Notifikasi Sukses/Error (Styling Tailwind) --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg relative mb-6 alert-dismissible" role="alert">
            <i class="icon fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-6 alert-dismissible" role="alert">
            <i class="icon fas fa-ban mr-2"></i> {{ session('error') }}
        </div>
    @endif
    
    <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-6">
        
        {{-- KOLOM KIRI: FORM EDIT UTAMA (2/3) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-clipboard-list mr-2 text-indigo-500"></i> Koreksi Data Kehadiran</h3>
                </div>

                <div class="p-6">
                    
                    @php
                        // Helper Class
                        $inputClass = 'w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition duration-150';
                        $errorBorder = 'border-red-500';
                        $defaultBorder = 'border-gray-300';
                    @endphp

                    {{-- Alert Validasi Error dari Controller --}}
                    @if ($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-6">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Harap periksa kembali input Anda.
                        </div>
                    @endif

                    {{-- Form di-submit ke route manual.update dengan method PUT --}}
                    <form action="{{ route('walikelas.absensi.manual.update', $attendance->id) }}" method="POST" id="editAbsenceForm" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Tampilkan Info Siswa --}}
                        <div class="border-b border-gray-200 pb-4 mb-4">
                            <h5 class="text-lg font-bold text-gray-800 flex items-center"><i class="fas fa-user mr-2 text-purple-600"></i> Siswa: {{ $attendance->student->name ?? 'N/A' }}</h5>
                            <p class="text-xs text-gray-500 mt-2 flex justify-between">
                                <span>Waktu Masuk Tercatat: <strong class="text-gray-800">{{ $attendance->attendance_time ? $attendance->attendance_time->format('d/m/Y H:i:s') : 'N/A' }}</strong></span>
                                <span>Waktu Pulang: 
                                    <strong class="text-gray-800">
                                        {{ $attendance->checkout_time ? $attendance->checkout_time->format('H:i:s') : 'BELUM PULANG' }}
                                    </strong>
                                </span>
                            </p>
                            
                            {{-- Hidden Input: NIS Siswa (LOGIKA AMAN) --}}
                            <input type="hidden" name="nis" value="{{ $attendance->student->nis ?? '' }}">
                        </div>
                        
                        {{-- Status Kehadiran --}}
                        <div>
                            <label for="editAttStatus" class="block text-sm font-semibold text-gray-700 mb-1">Ubah Status Kehadiran <span class="text-red-600">*</span></label>
                            <select class="{{ $inputClass }} @error('status') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" name="status" id="editAttStatus" required>
                                <option value="">Pilih Status</option>
                                @foreach(['Hadir', 'Terlambat', 'Sakit', 'Izin', 'Alpha'] as $status)
                                    <option value="{{ $status }}" {{ old('status', $attendance->status) == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                        
                        {{-- Keterangan --}}
                        <div>
                            <label for="editAttNotes" class="block text-sm font-semibold text-gray-700 mb-1">Keterangan Awal (Opsional)</label>
                            <textarea class="{{ $inputClass }} @error('notes') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" name="notes" id="editAttNotes" rows="3">{{ old('notes', $attendance->notes) }}</textarea>
                            @error('notes') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>

                        {{-- 💡 Alasan Koreksi (Wajib untuk Audit) --}}
                        <div class="mt-6 border border-amber-400 bg-amber-50 p-4 rounded-lg">
                            <label for="correction_reason" class="text-sm text-gray-700 mb-2 font-bold flex items-center">
                                <i class="fas fa-file-signature mr-2 text-amber-600"></i> Alasan Koreksi/Audit <span class="text-red-600 ml-1">*</span>
                            </label>
                            <textarea class="w-full px-3 py-2 border border-amber-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 @error('correction_reason') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" name="correction_reason" id="correction_reason" rows="2" required>{{ old('correction_reason', $attendance->correction_note) }}</textarea>
                            @error('correction_reason') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            <small class="text-xs text-gray-500 mt-1 block">Wajib diisi untuk tujuan audit (mis: Perubahan dari Izin menjadi Sakit karena surat dokter baru).</small>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="mt-6 border-t border-gray-100 pt-4 flex justify-between items-center">
                            <div class="flex space-x-3">
                                <a href="{{ route('walikelas.absensi.manual.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-base font-medium rounded-lg shadow-sm 
                                                text-gray-700 bg-white hover:bg-gray-100 transition duration-150 transform hover:scale-[1.02]">
                                    <i class="fas fa-arrow-left mr-2"></i> Batal
                                </a>
                                {{-- Tombol Simpan Perubahan (Amber) --}}
                                <button type="submit" class="inline-flex items-center px-5 py-2.5 border border-transparent text-base font-bold rounded-lg shadow-md 
                                        text-gray-800 bg-amber-400 hover:bg-amber-500 focus:ring-4 focus:ring-offset-2 focus:ring-amber-500/50 transition duration-150 transform hover:-translate-y-0.5" id="submitEditBtn">
                                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                                </button>
                            </div>
                            
                            {{-- Tombol Hapus --}}
                            <button type="button" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-base font-bold rounded-lg shadow-md 
                                    text-white bg-red-600 hover:bg-red-700 transition duration-150 transform hover:scale-[1.02]" 
                                    onclick="confirmDeleteAttendance('{{ $attendance->id }}', '{{ $attendance->student->name ?? 'Absensi' }}')">
                                <i class="fas fa-trash mr-2"></i> Hapus Absensi Ini
                            </button>
                        </div>
                    </form>
                    
                    {{-- Form Delete Tersembunyi (LOGIKA AMAN) --}}
                    <form id="delete-att-form-{{ $attendance->id }}" 
                          action="{{ route('walikelas.absensi.destroy', $attendance->id) }}" 
                          method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>

                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Informasi --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-lightbulb mr-2 text-indigo-500"></i> Tips Koreksi</h3>
                </div>
                <div class="p-6 text-sm">
                    {{-- Blok Audit Terakhir --}}
                    @if($attendance->is_manual_corrected)
                    <div class="bg-indigo-50 border-l-4 border-indigo-500 p-3 mb-4 rounded-lg">
                        <strong class="text-indigo-700 font-bold">Audit Terakhir:</strong>
                        <p class="text-xs text-indigo-600 mt-1">Dikoreksi oleh: {{ $attendance->corrected_by ?? 'N/A' }}</p>
                        <p class="text-xs text-indigo-600">Alasan: {{ $attendance->correction_note ?? 'Tidak ada catatan.' }}</p>
                    </div>
                    @endif
                    <ul class="list-disc ml-5 space-y-2 text-gray-600">
                        <li>Mengubah status menjadi **Sakit/Izin/Alpha** akan menyetel waktu pulang (*checkout_time*) menjadi NULL.</li>
                        <li>**Alasan Koreksi wajib** diisi untuk melacak siapa dan mengapa data ini diubah.</li>
                        <li>Gunakan tombol **Hapus** hanya jika data absensi ini benar-benar salah atau duplikat.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    
    $(document).ready(function() {
        // 🚨 FUNGSI HAPUS ABSENSI (SweetAlert2) - LOGIKA AMAN
        window.confirmDeleteAttendance = function(attendanceId, studentName) {
            Swal.fire({
                title: 'Hapus Absensi?',
                text: `Yakin ingin menghapus catatan absensi ${studentName}? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626', // Red
                cancelButtonColor: '#6c757d', // Gray
                confirmButtonText: 'Ya, Hapus!',
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = $(`#delete-att-form-${attendanceId}`);
                    
                    if (form.length) {
                        // Memaksa submit dari form tersembunyi
                        form.submit(); 
                    }
                }
            });
        };

        // 🚨 FUNGSI SUBMIT LOADING STATE (LOGIKA AMAN)
        $('#editAbsenceForm').on('submit', function() {
            const submitBtn = $('#submitEditBtn');
            // Cek validitas form HTML5
            if (this.checkValidity() === false) {
                 return;
            }
            // Tambahkan efek transform/hover ke loading state agar konsisten
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');
        });
        
        // Auto-dismiss alerts
        setTimeout(function() {
            $('.alert-dismissible').fadeOut(400);
        }, 5000);
    });
</script>
@endsection

@section('css')
<style>
/* --- MINIMAL CUSTOM CSS FOR TAILWIND --- */
.text-amber-500 { color: #f59e0b; }
.text-indigo-600 { color: #4f46e5; }
.text-indigo-500 { color: #6366f1; } 
.text-purple-600 { color: #9333ea; }

/* Warna Amber Button dan Focus */
.bg-amber-400 { background-color: #fbbf24; }
.hover\:bg-amber-500:hover { background-color: #f59e0b; }
.bg-amber-50 { background-color: #fff7ed; }
.border-amber-300 { border-color: #fcd34d; }
.border-amber-400 { border-color: #fbbf24; }
.text-amber-600 { color: #d97706; }

/* Warna Custom Blocks */
.bg-indigo-50 { background-color: #eef2ff; }
.border-indigo-500 { border-color: #6366f1; }
.text-indigo-700 { color: #4338ca; }

.bg-red-600 { background-color: #dc2626 !important; }
.hover\:bg-red-700:hover { background-color: #b91c1c !important; }

/* Default Border */
.border-gray-300 { border-color: #d1d5db; }

/* Menjamin form tersembunyi */
.hidden { display: none !important; }
</style>
@endsection