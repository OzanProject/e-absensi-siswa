@extends('layouts.adminlte')

@section('title', 'Tambah Siswa Baru')

@section('content_header')
{{-- CUSTOM HEADER (Menggunakan Indigo) --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        {{-- Mengganti green-600 ke indigo-600 untuk konsistensi branding --}}
        <i class="fas fa-user-plus text-indigo-600 mr-2"></i>
        <span>Tambah Siswa Baru</span>
    </h1>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('students.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Data Siswa</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Tambah Siswa</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <form action="{{ route('students.store') }}" method="POST" id="studentForm" enctype="multipart/form-data">
        @csrf
        
        {{-- Mengganti row dan col-md-X dengan Grid Tailwind (8/12 dan 4/12) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-6"> 
            
            {{-- KOLOM KIRI: DATA UTAMA & FORM (2/3 Kolom) --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg border border-gray-100"> 
                    <div class="p-5 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-indigo-500"></i> Data Utama Siswa
                        </h3>
                    </div>
                    
                    <div class="p-6 space-y-6"> {{-- Padding dan spacing lebih besar --}}
                        
                        {{-- 💡 Helper untuk menentukan class border error/default --}}
                        @php
                            // Base class: Styling umum (padding, shadow, rounded)
                            $baseInputClass = 'w-full px-3 py-2 rounded-lg shadow-sm focus:outline-none transition duration-150';
                            
                            // Kelas Normal (Border + Focus Indigo)
                            $normalClass = 'border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500';
                            
                            // Kelas Error (Border + Focus Red)
                            $errorClass = 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-red-500';
                        @endphp
                        
                        {{-- NISN & NIS (Grid 2 Kolom) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nisn" class="block text-sm font-semibold text-gray-700 mb-1">NISN <span class="text-red-600">*</span></label>
                                @php $nisnStatusClass = $errors->has('nisn') ? $errorClass : $normalClass; @endphp
                                <input type="text" name="nisn" id="nisn" 
                                        class="{{ $baseInputClass }} border {{ $nisnStatusClass }}" 
                                        value="{{ old('nisn') }}" 
                                        placeholder="Masukkan NISN" 
                                        required
                                        maxlength="20">
                                @error('nisn')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                                <small class="mt-1 text-xs text-gray-500">Nomor Induk Siswa Nasional</small>
                            </div>
                            
                            <div>
                                <label for="nis" class="block text-sm font-semibold text-gray-700 mb-1">NIS (Opsional)</label>
                                @php $nisStatusClass = $errors->has('nis') ? $errorClass : $normalClass; @endphp
                                <input type="text" name="nis" id="nis" 
                                        class="{{ $baseInputClass }} border {{ $nisStatusClass }}" 
                                        value="{{ old('nis') }}"
                                        placeholder="Masukkan NIS"
                                        maxlength="15">
                                @error('nis')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                                <small class="mt-1 text-xs text-gray-500">Nomor Induk Sekolah</small>
                            </div>
                        </div>

                        {{-- Nama Lengkap & Email --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap Siswa <span class="text-red-600">*</span></label>
                                @php $nameStatusClass = $errors->has('name') ? $errorClass : $normalClass; @endphp
                                <input type="text" name="name" id="name" 
                                        class="{{ $baseInputClass }} border {{ $nameStatusClass }}" 
                                        value="{{ old('name') }}" 
                                        placeholder="Masukkan nama lengkap siswa"
                                        required
                                        maxlength="100">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email (Opsional)</label>
                                @php $emailStatusClass = $errors->has('email') ? $errorClass : $normalClass; @endphp
                                <input type="email" name="email" id="email" 
                                        class="{{ $baseInputClass }} border {{ $emailStatusClass }}" 
                                        value="{{ old('email') }}"
                                        placeholder="Masukkan alamat email siswa"
                                        maxlength="255">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Kelas & Jenis Kelamin --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="class_id" class="block text-sm font-semibold text-gray-700 mb-1">Kelas <span class="text-red-600">*</span></label>
                                @php $classStatusClass = $errors->has('class_id') ? $errorClass : $normalClass; @endphp
                                <select name="class_id" id="class_id" 
                                        class="w-full select2-form-control border {{ $classStatusClass }}" 
                                        required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }} 
                                            @if(isset($class->grade))
                                                - Tingkat {{ $class->grade }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="gender" class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin <span class="text-red-600">*</span></label>
                                @php $genderStatusClass = $errors->has('gender') ? $errorClass : $normalClass; @endphp
                                <select name="gender" id="gender" 
                                        class="{{ $baseInputClass }} border {{ $genderStatusClass }}" 
                                        required>
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('gender')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Tanggal Lahir & Tempat Lahir --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="birth_place" class="block text-sm font-semibold text-gray-700 mb-1">Tempat Lahir (Opsional)</label>
                                @php $placeStatusClass = $errors->has('birth_place') ? $errorClass : $normalClass; @endphp
                                <input type="text" name="birth_place" id="birth_place" 
                                        class="{{ $baseInputClass }} border {{ $placeStatusClass }}" 
                                        value="{{ old('birth_place') }}" maxlength="100"
                                        placeholder="Masukkan tempat lahir">
                                @error('birth_place')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="birth_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Lahir (Opsional)</label>
                                @php $dateStatusClass = $errors->has('birth_date') ? $errorClass : $normalClass; @endphp
                                <input type="date" name="birth_date" id="birth_date" 
                                        class="{{ $baseInputClass }} border {{ $dateStatusClass }}" 
                                        value="{{ old('birth_date') }}">
                                @error('birth_date')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Nomor HP (Dikonversi ke Tailwind Group) --}}
                        <div>
                            <label for="phone_number" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon/HP (Opsional)</label>
                            <div class="flex rounded-lg shadow-sm">
                                {{-- Prefix Icon --}}
                                <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 text-gray-500 sm:text-sm bg-gray-50 border-gray-300">
                                    <i class="fas fa-phone"></i>
                                </span>
                                {{-- Input --}}
                                @php $phoneStatusClass = $errors->has('phone_number') ? $errorClass : $normalClass; @endphp
                                <input type="tel" name="phone_number" id="phone_number" 
                                        class="flex-1 block w-full rounded-none rounded-r-lg px-3 py-2 border border-gray-300 
                                                text-sm {{ $phoneStatusClass }}" 
                                        value="{{ old('phone_number') }}"
                                        placeholder="Contoh: 081234567890"
                                        maxlength="15">
                            </div>
                            @error('phone_number')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                            <small class="mt-1 text-xs text-gray-500">Nomor telepon siswa atau orang tua</small>
                        </div>
                        
                        {{-- Tombol Submit (Grid 2 Kolom) --}}
                        <div class="pt-4 border-t border-gray-100">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <a href="{{ route('students.index') }}" 
                                   class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-base font-medium rounded-lg shadow-sm
                                          text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 transform hover:scale-[1.02]">
                                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                                </a>
                                <button type="submit" 
                                        class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-base font-bold rounded-lg shadow-md 
                                               text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-green-500/50 transition duration-150 transform hover:-translate-y-0.5">
                                    <i class="fas fa-save mr-2"></i> Simpan Data Siswa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- KOLOM KANAN: FOTO DAN INFO TAMBAHAN (1/3 Kolom) --}}
            <div class="lg:col-span-1 mt-6 lg:mt-0 space-y-6">
                
                {{-- Card Foto --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-5 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-camera mr-2 text-indigo-500"></i> Foto Siswa</h3>
                    </div>
                    <div class="p-5 text-center">
                        {{-- Preview Foto --}}
                        <div class="mb-4 flex justify-center">
                            {{-- Border foto diubah ke Indigo --}}
                            <img id="photo-preview" 
                                    src="{{ asset('images/default_avatar.png') }}" 
                                    alt="Foto Siswa Preview"
                                    class="w-36 h-36 rounded-full border-4 border-indigo-500 object-cover shadow-xl">
                        </div>

                        {{-- Input File Foto (Dikonversi ke Tailwind) --}}
                        <div class="mb-4">
                            <label for="photo" class="block text-sm font-semibold text-gray-700 mb-1">Upload Foto Siswa (Opsional)</label>
                            <input type="file" 
                                    name="photo" 
                                    id="photo" 
                                    {{-- Mengganti styling file input ke Indigo --}}
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('photo') border-red-500 @enderror"
                                    accept="image/*">
                            @error('photo')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                            <small class="mt-1 text-xs text-gray-500 block">Max 1MB. Format: JPG/PNG. Rasio: 1:1.</small>
                        </div>
                    </div>
                </div>
                
                {{-- Card Informasi --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-5 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-lightbulb mr-2 text-indigo-500"></i> Informasi</h3>
                    </div>
                    <div class="p-5">
                        <p class="text-sm font-bold text-gray-700 mb-2"><strong>Field bertanda (<span class="text-red-600">*</span>) wajib diisi</strong></p>
                        <hr class="mb-4 mt-2 border-gray-200">
                        <h6 class="text-base font-bold text-gray-800 mb-2">Tips:</h6>
                        <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                            <li>Data barcode **digenerate otomatis oleh sistem** saat penyimpanan.</li>
                            <li>Pastikan **NISN unik** dan valid.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('js')
<script>
    // FUNGSI PREVIEW FOTO (Logika TIDAK BERUBAH)
    document.addEventListener('DOMContentLoaded', function() {
        const photoInput = document.getElementById('photo');
        const photoPreview = document.getElementById('photo-preview');

        photoInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                photoPreview.src = '{{ asset('images/default_avatar.png') }}';
            }
        });
    });

    $(document).ready(function() {
        // Initialize Select2 (Menggunakan class select2bs4/select2-form-control)
        $('#class_id').select2({
            theme: 'bootstrap4',
            placeholder: '-- Pilih Kelas --',
            allowClear: true,
            width: '100%'
        });

        // Form submission loading state (Logika TIDAK BERUBAH)
        $('#studentForm').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"]');
            // Menambahkan efek transform saat loading
            submitBtn.prop('disabled', true).addClass('transform transition duration-150 ease-in-out').html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');
        });

        // Client-side validation for number/phone (Logika TIDAK BERUBAH)
        $('#nisn, #nis').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $('#phone_number').on('input', function() {
            this.value = this.value.replace(/[^0-9+]/g, '');
        });
        
        // Show session success/error messages via SweetAlert2 Toast
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Error!', text: '{{ session('error') }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        @endif
    });
</script>
@stop