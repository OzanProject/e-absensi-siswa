@extends('layouts.adminlte')

@section('title', 'Edit Siswa: ' . $student->name)

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Amber --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-user-edit text-amber-500 mr-2"></i> 
        <span>Edit Siswa: {{ $student->name }}</span>
    </h1>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('students.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Data Siswa</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Edit Siswa</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <form action="{{ route('students.update', $student->id) }}" method="POST" id="studentForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-6"> 
            
            {{-- KOLOM KIRI: DATA UTAMA & FORM (2/3 Kolom) --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg border border-gray-100"> 
                    <div class="p-5 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-edit mr-2 text-amber-500"></i> Edit Data Siswa
                        </h3>
                    </div>
                    
                    <div class="p-6 space-y-6"> {{-- Padding dan spacing lebih besar --}}
                        
                        @php
                            // Helper Class untuk Input Styling (Fokus Amber/Warning)
                            $baseInputClass = 'w-full px-3 py-2 rounded-lg shadow-sm focus:outline-none transition duration-150';
                            $normalClass = 'border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500';
                            $errorClass = 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-red-500';
                            
                            // Untuk mendapatkan nilai tanggal yang diformat YYYY-MM-DD
                            $birthDateValue = $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('Y-m-d') : '';
                        @endphp
                        
                        {{-- NISN & NIS --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nisn" class="block text-sm font-semibold text-gray-700 mb-1">NISN <span class="text-red-600">*</span></label>
                                @php $nisnStatusClass = $errors->has('nisn') ? $errorClass : $normalClass; @endphp
                                <input type="text" name="nisn" id="nisn" 
                                        class="{{ $baseInputClass }} border {{ $nisnStatusClass }}" 
                                        value="{{ old('nisn', $student->nisn) }}" required maxlength="20">
                                @error('nisn') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                                <small class="mt-1 text-xs text-gray-500">Nomor Induk Siswa Nasional</small>
                            </div>
                            
                            <div>
                                <label for="nis" class="block text-sm font-semibold text-gray-700 mb-1">NIS (Opsional)</label>
                                @php $nisStatusClass = $errors->has('nis') ? $errorClass : $normalClass; @endphp
                                <input type="text" name="nis" id="nis" 
                                        class="{{ $baseInputClass }} border {{ $nisStatusClass }}" 
                                        value="{{ old('nis', $student->nis) }}" maxlength="15">
                                @error('nis') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
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
                                        value="{{ old('name', $student->name) }}" required maxlength="100">
                                @error('name') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email (Opsional)</label>
                                @php $emailStatusClass = $errors->has('email') ? $errorClass : $normalClass; @endphp
                                <input type="email" name="email" id="email" 
                                        class="{{ $baseInputClass }} border {{ $emailStatusClass }}" 
                                        value="{{ old('email', $student->email) }}" maxlength="255">
                                @error('email') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
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
                                        <option value="{{ $class->id }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }} @if(isset($class->grade)) - Tingkat {{ $class->grade }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="gender" class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin <span class="text-red-600">*</span></label>
                                @php $genderStatusClass = $errors->has('gender') ? $errorClass : $normalClass; @endphp
                                <select name="gender" id="gender" 
                                        class="{{ $baseInputClass }} border {{ $genderStatusClass }}" 
                                        required>
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="Laki-laki" {{ old('gender', $student->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('gender', $student->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('gender') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Tanggal Lahir & Tempat Lahir --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="birth_place" class="block text-sm font-semibold text-gray-700 mb-1">Tempat Lahir (Opsional)</label>
                                @php $placeStatusClass = $errors->has('birth_place') ? $errorClass : $normalClass; @endphp
                                <input type="text" name="birth_place" id="birth_place" 
                                        class="{{ $baseInputClass }} border {{ $placeStatusClass }}" 
                                        value="{{ old('birth_place', $student->birth_place) }}" maxlength="100">
                                @error('birth_place') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="birth_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Lahir (Opsional)</label>
                                @php $dateStatusClass = $errors->has('birth_date') ? $errorClass : $normalClass; @endphp
                                <input type="date" name="birth_date" id="birth_date" 
                                        class="{{ $baseInputClass }} border {{ $dateStatusClass }}" 
                                        value="{{ old('birth_date', $birthDateValue) }}">
                                @error('birth_date') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Nomor HP --}}
                        <div>
                            <label for="phone_number" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon/HP (Opsional)</label>
                            <div class="flex rounded-lg shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 text-gray-500 sm:text-sm bg-gray-50 border-gray-300">
                                    <i class="fas fa-phone"></i>
                                </span>
                                @php $phoneStatusClass = $errors->has('phone_number') ? $errorClass : $normalClass; @endphp
                                <input type="tel" name="phone_number" id="phone_number" 
                                        class="flex-1 block w-full rounded-none rounded-r-lg px-3 py-2 border border-gray-300 
                                                text-sm {{ $phoneStatusClass }}" 
                                        value="{{ old('phone_number', $student->phone_number) }}" maxlength="15">
                            </div>
                            @error('phone_number') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            <small class="mt-1 text-xs text-gray-500">Nomor telepon siswa atau orang tua</small>
                        </div>
                        
                        {{-- Status Siswa --}}
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Status Siswa <span class="text-red-600">*</span></label>
                            @php $statusStatusClass = $errors->has('status') ? $errorClass : $normalClass; @endphp
                            <select name="status" id="status" class="{{ $baseInputClass }} border {{ $statusStatusClass }}" required>
                                <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                            @error('status') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            <small class="mt-1 text-xs text-gray-500">Kelas non-aktif tidak akan muncul dalam pemilihan absensi.</small>
                        </div>

                        {{-- Barcode Data (Readonly + Regenerate Button) --}}
                        <div>
                            <label for="barcode_data" class="block text-sm font-semibold text-gray-700 mb-1">Data Barcode (Identifier Unik)</label>
                            <div class="flex rounded-lg shadow-sm">
                                <input type="text" name="barcode_data" id="barcode_data" 
                                        class="flex-1 block w-full rounded-l-lg px-3 py-2 border border-gray-300 bg-gray-50 text-gray-600" 
                                        value="{{ old('barcode_data', $student->barcode_data) }}"
                                        readonly>
                                {{-- Tombol Regenerate (Dikonversi ke Tailwind) --}}
                                <button type="button" 
                                        class="px-4 py-2 text-sm font-medium rounded-r-lg text-white bg-red-600 hover:bg-red-700 transition duration-150 relative z-10" 
                                        onclick="confirmRegenerate()">
                                    <i class="fas fa-sync-alt mr-1"></i> Regenerate
                                </button>
                            </div>
                            @error('barcode_data') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            <small class="mt-1 text-xs text-red-500">⚠️ Meregenerate akan membatalkan kartu pelajar lama. Gunakan hanya jika QR Code tidak berfungsi.</small>
                        </div>

                        {{-- Tombol Submit --}}
                        <div class="pt-4 border-t border-gray-100">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Tombol Kembali --}}
                                <a href="{{ route('students.index') }}" 
                                   class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-base font-medium rounded-lg shadow-sm
                                          text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 transform hover:scale-[1.02]">
                                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                                </a>
                                {{-- Tombol Perbarui Data (Amber) --}}
                                <button type="submit" 
                                        class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-base font-bold rounded-lg shadow-md 
                                               text-gray-800 bg-amber-400 hover:bg-amber-500 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-amber-500/50 transition duration-150 transform hover:-translate-y-0.5">
                                    <i class="fas fa-save mr-2"></i> Perbarui Data Siswa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- KOLOM KANAN: FOTO SISWA & BARCODE (1/3 Kolom) --}}
            <div class="lg:col-span-1 mt-6 lg:mt-0 space-y-6">
                
                {{-- Card Foto --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-5 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-camera mr-2 text-indigo-500"></i> Foto Siswa</h3>
                    </div>
                    <div class="p-5 text-center">
                        @php
                            // Mengganti 'images/default_avatar.png' ke 'img/default_avatar.png' jika diperlukan, tergantung struktur asset Anda
                            $photoPath = ($student->photo && $student->photo != 'default_avatar.png') 
                                        ? asset('storage/' . $student->photo) 
                                        : asset('img/default_avatar.png')
                        @endphp

                        <div class="mb-4 flex justify-center flex-col items-center">
                            <h5 class="text-gray-600 text-sm mb-2 font-semibold">Foto Siswa Saat Ini</h5>
                            {{-- Border foto diubah ke Indigo --}}
                            <img id="photo-preview" 
                                    src="{{ $photoPath }}" 
                                    alt="Foto Siswa Saat Ini"
                                    class="w-36 h-36 rounded-full border-4 border-indigo-500 object-cover shadow-xl">
                        </div>

                        <div class="mb-4">
                            <label for="photo" class="block text-sm font-semibold text-gray-700 mb-1">Ganti Foto Siswa (Opsional)</label>
                            <input type="file" 
                                    name="photo" 
                                    id="photo" 
                                    {{-- Mengganti styling file input ke Indigo --}}
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('photo') border-red-500 @enderror"
                                    accept="image/*">
                            @error('photo')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                            <small class="mt-1 text-xs text-gray-500 block">Max 1MB. Kosongkan jika tidak ingin diganti.</small>
                        </div>
                    </div>
                </div>
                
                {{-- Card Barcode --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-5 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-qrcode mr-2 text-indigo-500"></i> Barcode Saat Ini</h3>
                    </div>
                    <div class="p-5 text-center">
                        <small class="text-gray-500 text-sm block mb-3 font-semibold">QR Code:</small>
                        <div class="mb-4 flex justify-center p-3 border border-gray-100 rounded-lg">
                            {{-- LOGIKA TAMPIL QR CODE TETAP SAMA --}}
                            {!! QrCode::size(150)->generate($student->barcode_data) !!}
                        </div>
                        
                        <small class="text-gray-500 text-sm block mb-2 font-semibold">Barcode 1D (Code 128):</small>
                        <div class="mb-3 overflow-x-auto p-2 border border-gray-100 rounded-lg"> 
                            {{-- LOGIKA TAMPIL BARCODE 1D TETAP SAMA --}}
                            {!! $student->barcode_1d !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('js')
<script>
    // FUNGSI GENERATE UUID (LOGIKA TIDAK BERUBAH)
    if (typeof window.generateUUID === 'undefined') {
        window.generateUUID = function() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        };
    }

    // 🔥 FUNGSI CONFIRM REGENERATE (LOGIKA TIDAK BERUBAH, HANYA WARNA SWAL)
    function confirmRegenerate() {
        Swal.fire({
            title: 'Regenerate Barcode?',
            text: 'Ini akan membuat data barcode unik baru. Kartu pelajar lama siswa ini TIDAK akan berfungsi lagi. Lanjutkan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626', // red-600
            cancelButtonColor: '#4f46e5', // indigo-600
            confirmButtonText: 'Ya, Regenerate!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const barcodeInput = $('#barcode_data');
                
                // --- LOGIKA UTAMA REGENERATE TETAP SAMA ---
                barcodeInput.prop('readonly', false); 
                barcodeInput.removeClass('bg-gray-50').addClass('bg-amber-50 border-amber-500'); 
                barcodeInput.val(window.generateUUID()); 
                // ------------------------------------------
                
                Swal.fire({
                    title: 'Berhasil!',
                    html: 'Data barcode baru berhasil digenerate di form. Jangan lupa **perbarui data** untuk menyimpannya!',
                    icon: 'success'
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const photoInput = document.getElementById('photo');
        const photoPreview = document.getElementById('photo-preview');
        // Pastikan path default di ambil dari elemen foto yang sudah di-render Blade
        const initialPhotoPath = photoPreview ? photoPreview.src : '{{ asset('img/default_avatar.png') }}';

        if (photoInput) {
            photoInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) { photoPreview.src = e.target.result; };
                    reader.readAsDataURL(file);
                } else {
                    photoPreview.src = initialPhotoPath;
                }
            });
        }
    });

    // --- JQUERY READY FUNCTION ---
    $(document).ready(function() {
        // Initialize Select2
        $('#class_id').select2({ theme: 'bootstrap4', placeholder: '-- Pilih Kelas --', allowClear: true, width: '100%' });

        // 🔥 ATTACH EVENT LISTENER TOMBOL REGENERATE
        $('#regenerateBarcodeBtn').on('click', function() {
            confirmRegenerate(); // Panggil fungsi SweetAlert
        });

        // Form submission loading state
        $('#studentForm').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"]');
            // Menambahkan efek transform saat loading
            submitBtn.prop('disabled', true).addClass('transform transition duration-150 ease-in-out').html('<i class="fas fa-spinner fa-spin mr-2"></i> Memperbarui...');
        });

        // Validation - only numbers
        $('#nisn, #nis').on('input', function() { this.value = this.value.replace(/[^0-9]/g, ''); });
        
        // Phone number validation
        $('#phone_number').on('input', function() { this.value = this.value.replace(/[^0-9+]/g, ''); });

        // Load session messages (SweetAlert2 Toast)
        @if(session('success')) Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 }); @endif
        @if(session('error')) Swal.fire({ icon: 'error', title: 'Error!', text: '{{ session('error') }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 }); @endif
    });
</script>
@stop