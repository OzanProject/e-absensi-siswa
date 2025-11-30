@extends('layouts.adminlte')

@section('title', 'Edit Siswa: ' . ($student->name ?? 'N/A'))

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Amber/Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-user-edit text-amber-500 mr-2"></i>
        <span>Edit Siswa: {{ $student->name ?? 'N/A' }}</span>
    </h1>
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <li><a href="{{ route('walikelas.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <li><a href="{{ route('walikelas.students.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Data Siswa</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Edit</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
<div class="bg-white rounded-xl shadow-lg border border-gray-100">
    <div class="p-5 border-b border-gray-100">
        <h3 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-file-alt mr-2 text-amber-500"></i> Form Edit Data Siswa
        </h3>
    </div>

    <div class="p-6"> {{-- Padding disesuaikan --}}
        
        @if ($errors->any())
            {{-- Styling Error Umum (Tailwind) --}}
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-6">
                <i class="fas fa-exclamation-triangle mr-2"></i> Harap periksa kembali input Anda.
            </div>
        @endif

        {{-- Form di-submit ke route walikelas.students.update dengan method PUT --}}
        <form action="{{ route('walikelas.students.update', $student->id) }}" method="POST" enctype="multipart/form-data" id="studentForm" class="space-y-6">
            @csrf
            @method('PUT')
            
            @php
                // Helper Class untuk Input Styling (Fokus Amber)
                $inputClass = 'w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition duration-150';
                $errorBorder = 'border-red-500';
                $defaultBorder = 'border-gray-300';
                
                // Foto saat ini (LOGIKA AMAN)
                $currentPhoto = $student->photo && $student->photo != 'default_avatar.png' 
                                 ? asset('storage/' . $student->photo) 
                                 : asset('images/default_avatar.png');
            @endphp
            
            <input type="hidden" name="class_id" value="{{ $student->class_id }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- Kolom 1: Foto Siswa (1/3) --}}
                <div class="md:col-span-1 pr-0 md:pr-6 md:border-r border-gray-100">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Foto Siswa</h4>
                    
                    <div class="mb-6 text-center">
                        <img id="photo-preview" src="{{ $currentPhoto }}" 
                             alt="Preview Foto" class="w-36 h-36 rounded-full mx-auto object-cover shadow-xl border-4 border-amber-500/70">
                    </div>
                    
                    <label for="photo" class="block text-sm font-semibold text-gray-700 mb-1">Ganti Foto</label>
                    <input type="file" name="photo" id="photo" accept="image/png, image/jpeg, image/jpg"
                            {{-- Styling File Input: Mengganti blue-50/700 ke Amber --}}
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 @error('photo') {{ $errorBorder }} @enderror">
                    <small class="text-xs text-gray-500 mt-1 block">Kosongkan jika tidak ingin mengubah foto.</small>
                    @error('photo') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                </div>

                {{-- Kolom 2 & 3: Informasi Pokok, Kontak, TTL (2/3) --}}
                <div class="md:col-span-2 space-y-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-3 border-b pb-2">Informasi Pokok</h4>
                    
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <strong class="text-sm text-gray-700">Kelas Saat Ini:</strong> 
                        <span class="font-bold text-indigo-600">{{ $student->class->name ?? 'N/A' }}</span>
                        <small class="text-xs text-red-500 block mt-1">Wali Kelas tidak diizinkan mengubah kelas siswa.</small>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $student->name) }}" required class="{{ $inputClass }} @error('name') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror">
                            @error('name') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="gender" class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin <span class="text-red-600">*</span></label>
                            <select name="gender" id="gender" required class="{{ $inputClass }} @error('gender') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki" {{ old('gender', $student->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('gender', $student->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('gender') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="nisn" class="block text-sm font-semibold text-gray-700 mb-1">NISN <span class="text-red-600">*</span></label>
                            <input type="text" name="nisn" id="nisn" value="{{ old('nisn', $student->nisn) }}" required class="{{ $inputClass }} @error('nisn') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror">
                            @error('nisn') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="nis" class="block text-sm font-semibold text-gray-700 mb-1">NIS</label>
                            <input type="text" name="nis" id="nis" value="{{ old('nis', $student->nis) }}" class="{{ $inputClass }} @error('nis') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror">
                            @error('nis') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <h4 class="text-lg font-bold text-gray-800 mb-3 border-b pb-2 pt-2">Informasi Kontak & TTL</h4>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="birth_place" class="block text-sm font-semibold text-gray-700 mb-1">Tempat Lahir</label>
                            <input type="text" name="birth_place" id="birth_place" value="{{ old('birth_place', $student->birth_place) }}" class="{{ $inputClass }} @error('birth_place') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror">
                            @error('birth_place') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="birth_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Lahir</label>
                            <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('Y-m-d') : null) }}" class="{{ $inputClass }} @error('birth_date') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror">
                            @error('birth_date') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="phone_number" class="block text-sm font-semibold text-gray-700 mb-1">No. HP Wali</label>
                            <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $student->phone_number) }}" class="{{ $inputClass }} @error('phone_number') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror">
                            @error('phone_number') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-1">Alamat</label>
                        <textarea name="address" id="address" rows="2" class="{{ $inputClass }} @error('address') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror">{{ old('address', $student->address) }}</textarea>
                        @error('address') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="mt-6 border-t border-gray-100 pt-4 flex justify-end space-x-3">
                        <a href="{{ route('walikelas.students.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-base font-medium rounded-lg shadow-sm 
                                    text-gray-700 bg-white hover:bg-gray-100 transition duration-150 transform hover:scale-[1.02]">
                            <i class="fas fa-arrow-left mr-2"></i> Batal
                        </a>
                        {{-- Tombol Simpan Perubahan (Amber) --}}
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 border border-transparent text-base font-bold rounded-lg shadow-md 
                                    text-gray-800 bg-amber-400 hover:bg-amber-500 focus:ring-4 focus:ring-offset-2 focus:ring-amber-500/50 transition duration-150 transform hover:-translate-y-0.5" id="submitBtn">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Preview Foto (LOGIKA AMAN)
        document.getElementById('photo').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photo-preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Form submission loading state (LOGIKA AMAN)
        $('#studentForm').on('submit', function() {
            const submitBtn = $('#submitBtn');
            if (this.checkValidity() === false) {
                 return;
            }
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');
        });
        
        // Auto-hide alerts
        setTimeout(function() {
            $('.alert-dismissible').fadeOut(400);
        }, 5000);
    });
</script>
@endsection

@section('css')
<style>
/* --- MINIMAL CUSTOM CSS FOR TAILWIND --- */
.text-indigo-600 { color: #4f46e5; }
.text-amber-500 { color: #f59e0b; }
.bg-amber-400 { background-color: #fbbf24; }
.hover\:bg-amber-500:hover { background-color: #f59e0b; }

/* Styling File Input */
.file\:bg-indigo-50 { background-color: #e0e7ff; }
.file\:text-indigo-700 { color: #4338ca; }
.file\:hover\:bg-indigo-100:hover { background-color: #c7d2fe; }

/* FIXES */
.alert { border-radius: 0.5rem; }
.text-red-600 { color: #dc3545; }
</style>
@endsection