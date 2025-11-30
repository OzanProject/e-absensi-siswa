@extends('layouts.adminlte')

@section('title', 'Import Data Siswa')

@section('content_header')
{{-- HEADER: Menggunakan Flexbox Tailwind (Struktur yang Rapi) --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <div class="mb-2 sm:mb-0">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-file-import text-gray-600 mr-2"></i> 
            <span>Import Data Siswa</span>
        </h1>
    </div>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">Home</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('students.index') }}" class="text-blue-600 hover:text-blue-800">Data Siswa</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Import</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    
    {{-- 💡 1. PEMBERSIHAN ERROR: Blok Notifikasi Sukses dipisahkan --}}
    @if(session('success'))
        <div id="session-success-alert" data-message="{{ session('success') }}" style="display:none;"></div>
    @endif
    
    {{-- Alert Error Sesi (Ditampilkan di sini jika terjadi Error non-validasi) --}}
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4 alert-dismissible" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Mengganti row dan col-md-X dengan Grid Tailwind (2/3 dan 1/3) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-6">
        
        {{-- KOLOM KIRI: FORM UTAMA (2/3) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200"> 
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center"><i class="fas fa-upload mr-2 text-gray-500"></i> Unggah File Excel</h3>
                </div>
                <div class="p-5">
                    
                    {{-- Alert Validasi Maatwebsite --}}
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4 alert-validation-errors">
                            <h5 class="font-bold text-lg"><i class="icon fas fa-times-circle mr-2"></i> Ditemukan {{ $errors->count() }} Kesalahan Validasi:</h5>
                            <p class="text-sm mt-1">Mohon periksa baris dan kolom berikut di file Excel Anda:</p>
                            <ul class="list-disc ml-6 mt-2 text-sm space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h5 class="font-bold mb-3 text-blue-600">1. Unduh Template</h5>
                    <p class="text-gray-600 text-sm">Unduh template ini untuk memastikan format kolom (**NISN, Nama Siswa, Nama Kelas**, dll.) sudah benar sebelum diunggah.</p>
                    <a href="{{ route('students.export') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm text-white bg-cyan-600 hover:bg-cyan-700 transition duration-150 mb-4" target="_blank">
                        <i class="fas fa-file-download mr-1"></i> Unduh Template Excel
                    </a>
                    
                    <h5 class="font-bold mt-4 mb-3 text-blue-600">2. Unggah File</h5>
                    <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf
                        <div class="mb-4">
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Pilih File Excel (.xlsx atau .xls)</label>
                            <input type="file" name="file" id="file" 
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 
                                            @error('file') border-red-500 @enderror" 
                                    required>
                            @error('file')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <small class="mt-1 text-xs text-gray-500 block">Maksimal ukuran file: 2MB.</small>
                        </div>

                        <div class="mt-4 flex space-x-3">
                            <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg 
                                            shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition duration-150"><i class="fas fa-arrow-left mr-1"></i> Batal</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm 
                                            text-white bg-green-600 hover:bg-green-700 transition duration-150" id="submitImportBtn"><i class="fas fa-share-square mr-1"></i> Proses Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        {{-- KOLOM KANAN: INFORMASI (1/3) --}}
        <div class="lg:col-span-1 mt-6 lg:mt-0">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center"><i class="fas fa-info-circle mr-2 text-gray-500"></i> Catatan Penting</h3>
                </div>
                <div class="p-5">
                    <ul class="list-disc ml-5 text-sm text-gray-600 space-y-1">
                        <li>Pastikan **Nama Kelas** di file Excel sudah terdaftar di sistem.</li>
                        <li>**NISN** dan **Nama** adalah kolom wajib. NISN harus unik.</li>
                        <li>Sistem akan mengabaikan baris yang datanya tidak valid atau duplikat.</li>
                        <li>Format file yang diterima hanya **.xlsx** dan **.xls**.</li>
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
        // 💡 1. LOGIKA SUBMIT DAN LOADING STATE
        $('#importForm').on('submit', function() {
            const submitBtn = $('#submitImportBtn');
            
            // Cek validasi dasar 
            if (!document.getElementById('file').files.length) {
                return; 
            }
            
            // Tampilkan loading state dan nonaktifkan tombol
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');
            
            // Tampilkan SweetAlert untuk proses panjang
            Swal.fire({
                title: 'Sedang Memproses Import Data',
                text: 'Proses ini mungkin memakan waktu beberapa saat. Jangan tutup halaman ini.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });

        // 💡 2. TAMPILKAN NOTIFIKASI SUKSES/ERROR DARI SESSION
        const successMessageElement = $('#session-success-alert');
        
        if (successMessageElement.length) {
            Swal.close(); // Tutup loading SweetAlert jika ada
            
            Swal.fire({
                icon: 'success', 
                title: 'Import Berhasil!', 
                text: successMessageElement.data('message'), 
                toast: true, 
                position: 'top-end', 
                showConfirmButton: false, 
                timer: 5000 
            });
        }
        
        // 3. Hapus alert validasi saat user mulai memilih file baru
        $('#file').on('change', function() {
            // Hapus alert validasi yang dibuat oleh Blade/Controller
            $('.alert-validation-errors').slideUp(200, function() { $(this).remove(); });
            // Hapus alert sesi
            $('.alert-dismissible').slideUp(200, function() { $(this).remove(); });
        });
    });
</script>
@stop

@section('css')
<style>
/* CSS Tambahan */
.text-cyan-600 { color: #0891b2; } 
.text-red-600 { color: #dc3545; }
.bg-cyan-600 { background-color: #0891b2 !important; }
.hover\:bg-cyan-700:hover { background-color: #0e7490 !important; }
.bg-green-600 { background-color: #10b981 !important; }
.hover\:bg-green-700:hover { background-color: #059669 !important; }

/* Styling untuk file input agar responsive */
input[type="file"]::-webkit-file-upload-button {
    visibility: hidden;
}
input[type="file"]::before {
    content: 'Pilih File';
    display: inline-block;
    background: #e5e7eb; /* gray-200 */
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    outline: none;
    white-space: nowrap;
    -webkit-user-select: none;
    cursor: pointer;
    font-weight: 500;
    font-size: 0.875rem;
    color: #374151; /* gray-700 */
}
input[type="file"]:hover::before {
    background: #d1d5db; /* gray-300 */
}
</style>
@endsection