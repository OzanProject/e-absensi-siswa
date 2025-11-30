@extends('layouts.adminlte')

@section('title', 'Tambah Wali Kelas Baru')

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Purple/Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div class="mb-2 sm:mb-0">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-user-plus text-purple-600 mr-2"></i>
            <span>Tambah Wali Kelas Baru</span>
        </h1>
    </div>
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('teachers.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Data Wali Kelas</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Tambah Baru</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    {{-- Notifikasi Error/Success Session (Dikonversi ke Tailwind) --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg relative mb-4" role="alert">
            <i class="icon fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-4" role="alert">
            <i class="icon fas fa-ban mr-2"></i> {{ session('error') }}
        </div>
    @endif
    
    {{-- Mengganti row dan col-md-X dengan Grid Tailwind (8/12 dan 4/12) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-6">
        
        {{-- KOLOM KIRI: FORM UTAMA (2/3 Kolom) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100"> 
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-id-card mr-2 text-purple-500"></i> Data Akun & Penugasan</h3>
                </div>
                <div class="p-6">
                    
                    {{-- Validasi Error Global --}}
                    @if($errors->any() && !session('error'))
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-6">
                            <i class="icon fas fa-exclamation-triangle mr-2"></i> **Harap periksa kembali input Anda.** Ditemukan kesalahan validasi.
                        </div>
                    @endif
                    
                    <form action="{{ route('teachers.store') }}" method="POST" id="teacherForm" class="space-y-6">
                        @csrf
                        
                        @php
                            // Helper Class untuk Input Styling (Fokus Purple/Ungu)
                            $baseInputClass = 'w-full px-3 py-2 rounded-lg shadow-sm focus:outline-none transition duration-150';
                            $normalClass = 'border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500';
                            $errorClass = 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-red-500';
                        @endphp

                        {{-- Nama Guru --}}
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
                            @php $nameStatusClass = $errors->has('name') ? $errorClass : $normalClass; @endphp
                            <input type="text" name="name" id="name" 
                                    class="{{ $baseInputClass }} border {{ $nameStatusClass }}" 
                                    value="{{ old('name') }}" 
                                    placeholder="Nama lengkap guru"
                                    required>
                            @error('name') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                        
                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email (Digunakan untuk Login) <span class="text-red-600">*</span></label>
                            @php $emailStatusClass = $errors->has('email') ? $errorClass : $normalClass; @endphp
                            <input type="email" name="email" id="email" 
                                    class="{{ $baseInputClass }} border {{ $emailStatusClass }}" 
                                    value="{{ old('email') }}" 
                                    placeholder="Email unik (Contoh: budi@sekolah.sch.id)"
                                    required>
                            @error('email') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password (Minimal 8 Karakter) <span class="text-red-600">*</span></label>
                            @php $passStatusClass = $errors->has('password') ? $errorClass : $normalClass; @endphp
                            <input type="password" name="password" id="password" 
                                    class="{{ $baseInputClass }} border {{ $passStatusClass }}" 
                                    required>
                            @error('password') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>

                        {{-- Kelas yang Diampu --}}
                        <div>
                            <label for="class_id" class="block text-sm font-semibold text-gray-700 mb-1">Kelas yang Diampu (Opsional)</label>
                            @php $classStatusClass = $errors->has('class_id') ? $errorClass : $normalClass; @endphp
                            <select name="class_id" id="class_id" 
                                    class="w-full select2-form-control border {{ $classStatusClass }}">
                                <option value="">-- Pilih Kelas (Kosongkan jika belum mengampu) --</option>
                                @foreach($availableClasses as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }} (Tingkat {{ $class->grade }})
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            <small class="mt-1 text-xs text-gray-500 block">Hanya kelas yang belum memiliki Wali Kelas yang akan tampil.</small>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="pt-4 border-t border-gray-100 flex space-x-3">
                            <a href="{{ route('teachers.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-base font-medium rounded-lg shadow-sm
                                      text-gray-700 bg-white hover:bg-gray-100 transition duration-150 transform hover:scale-[1.02]">
                                <i class="fas fa-arrow-left mr-2"></i> Batal
                            </a>
                            {{-- Tombol Submit (Green) --}}
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-base font-bold rounded-lg shadow-md 
                                           text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-offset-2 focus:ring-green-500/50 transition duration-150 transform hover:-translate-y-0.5" 
                                    id="submitBtn">
                                <i class="fas fa-save mr-2"></i> Simpan Akun Wali Kelas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        {{-- KOLOM KANAN: INFO & TIPS (1/3 Kolom) --}}
        <div class="lg:col-span-1 mt-6 lg:mt-0">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-lightbulb mr-2 text-purple-500"></i> Aturan Akun</h3>
                </div>
                <div class="p-6 text-sm space-y-4">
                    {{-- Info Box --}}
                    <div class="bg-purple-50 border-l-4 border-purple-500 p-4 rounded-lg">
                        <p class="text-purple-800 font-semibold mb-2">Akun yang dibuat akan otomatis memiliki peran **`wali_kelas`** dan dapat login menggunakan Email dan Password yang didaftarkan.</p>
                    </div>
                    
                    <h6 class="font-bold text-gray-800">Tips Penting:</h6>
                    <ul class="list-disc ml-5 space-y-2 text-gray-600">
                        <li>**Email** harus unik di tabel `users`.</li>
                        <li>**Password** harus minimal 8 karakter untuk keamanan.</li>
                        <li>Jika Wali Kelas belum mengampu, biarkan kolom Kelas kosong. Penugasan dapat dilakukan melalui halaman edit.</li>
                        <li>**Kelas yang sudah diampu oleh guru lain tidak akan terlihat di sini.**</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

{{-- HAPUS @section('css') yang lama --}}

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Pastikan JQuery tersedia di master layout Anda
    
    $(document).ready(function() {
        // Initialize Select2 (Pencarian Kelas)
        $('#class_id').select2({
            theme: 'bootstrap4',
            placeholder: '-- Pilih Kelas (Opsional) --',
            allowClear: true
        });

        // Form submission loading state (Logika tidak berubah)
        $('#teacherForm').on('submit', function() {
            const submitBtn = $('#submitBtn');
            
            // Perbaikan: Cek status validitas form HTML5/Bootstrap sebelum submit
            if (this.checkValidity() === false) {
                 return; 
            }

            // Tampilkan loading state dan nonaktifkan tombol
            submitBtn.prop('disabled', true)
                     .addClass('transform transition duration-150 ease-in-out')
                     .html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');
        });
        
        // --- Tampilkan notifikasi SweetAlert Toast untuk pesan sesi (Logika tidak berubah) ---
        @if(session('success'))
             Swal.fire({ 
                 icon: 'success', 
                 title: 'Berhasil!', 
                 text: '{{ session('success') }}', 
                 toast: true, 
                 position: 'top-end', 
                 showConfirmButton: false, 
                 timer: 3000 
             });
        @endif
        @if(session('error'))
             Swal.fire({ 
                 icon: 'error', 
                 title: 'Gagal!', 
                 text: '{{ session('error') }}', 
                 toast: true, 
                 position: 'top-end', 
                 showConfirmButton: false, 
                 timer: 3000 
             });
        @endif
    });
</script>
@stop