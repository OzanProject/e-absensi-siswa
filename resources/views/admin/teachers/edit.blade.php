@extends('layouts.adminlte')

@section('title', 'Edit Akun: ' . $teacher->name)

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Amber/Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        {{-- Menggunakan warna Amber untuk Edit --}}
        <i class="fas fa-user-edit text-amber-500 mr-2"></i> 
        <span>Edit Akun Wali Kelas: {{ $teacher->name }}</span>
    </h1>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('teachers.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Data Wali Kelas</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Edit Akun</li>
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
    
    <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-6">
        
        {{-- KOLOM KIRI: FORM EDIT UTAMA (2/3 Kolom) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100"> 
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-edit mr-2 text-amber-500"></i> Data Akun & Penugasan</h3>
                </div>
                <div class="p-6">
                    
                    {{-- Validasi Error Global --}}
                    @if($errors->any() && !session('error'))
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-6">
                            <i class="icon fas fa-exclamation-triangle mr-2"></i> **Harap periksa kembali input Anda.** Ditemukan kesalahan validasi.
                        </div>
                    @endif
                    
                    <form action="{{ route('teachers.update', $teacher->id) }}" method="POST" id="teacherForm" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        @php
                            // Helper Class untuk Input Styling (Fokus Purple/Ungu)
                            $baseInputClass = 'w-full px-3 py-2 rounded-lg shadow-sm focus:outline-none transition duration-150';
                            $normalClass = 'border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500';
                            $errorClass = 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-red-500';
                            $currentClassId = $teacher->homeroomTeacher->class_id ?? null; // Ambil ID kelas yang diampu saat ini
                        @endphp

                        {{-- Nama Guru --}}
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
                            @php $nameStatusClass = $errors->has('name') ? $errorClass : $normalClass; @endphp
                            <input type="text" name="name" id="name" 
                                    class="{{ $baseInputClass }} border {{ $nameStatusClass }}" 
                                    value="{{ old('name', $teacher->name) }}" 
                                    placeholder="Nama lengkap guru"
                                    required>
                            @error('name') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                        
                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email (Login) <span class="text-red-600">*</span></label>
                            @php $emailStatusClass = $errors->has('email') ? $errorClass : $normalClass; @endphp
                            <input type="email" name="email" id="email" 
                                    class="{{ $baseInputClass }} border {{ $emailStatusClass }}" 
                                    value="{{ old('email', $teacher->email) }}" 
                                    placeholder="Email unik untuk login"
                                    required>
                            @error('email') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>

                        {{-- Password (Opsional) --}}
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password Baru (Kosongkan jika tidak diubah)</label>
                            @php $passStatusClass = $errors->has('password') ? $errorClass : $normalClass; @endphp
                            <input type="password" name="password" id="password" 
                                    class="{{ $baseInputClass }} border {{ $passStatusClass }}"
                                    placeholder="Isi hanya jika ingin mengganti password">
                            @error('password') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            <small class="mt-1 text-xs text-gray-500">Minimal 8 karakter.</small>
                        </div>

                        {{-- Kelas yang Diampu --}}
                        <div>
                            <label for="class_id" class="block text-sm font-semibold text-gray-700 mb-1">Kelas yang Diampu</label>
                            @php $classStatusClass = $errors->has('class_id') ? $errorClass : $normalClass; @endphp
                            <select name="class_id" id="class_id" 
                                    class="w-full select2-form-control border {{ $classStatusClass }}">
                                <option value="">-- Hapus Kelas yang Diampu / Kosongkan --</option>
                                
                                @foreach($availableClasses as $class)
                                    @php
                                        // LOGIKA INI AMAN, TIDAK DIUBAH
                                        $isDisabled = $class->homeroomTeacher && $class->homeroomTeacher->user_id !== $teacher->id;
                                        $isSelected = old('class_id') == $class->id || $currentClassId == $class->id;
                                        $isCurrent = $class->id == $currentClassId;
                                    @endphp
                                    
                                    <option value="{{ $class->id }}" 
                                            {{ $isSelected ? 'selected' : '' }}
                                            {{ $isDisabled ? 'disabled' : '' }}>
                                        {{ $class->name }} (Tingkat {{ $class->grade }})
                                        @if ($isCurrent) (Kelas Saat Ini) @endif
                                        @if ($isDisabled) (Sudah Diampu Guru Lain) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            <small class="mt-1 text-xs text-gray-500">Hanya kelas yang belum diampu atau kelas guru ini sendiri yang bisa dipilih.</small>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="pt-4 border-t border-gray-100 flex space-x-3">
                            <a href="{{ route('teachers.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-base font-medium rounded-lg shadow-sm 
                                      text-gray-700 bg-white hover:bg-gray-100 transition duration-150 transform hover:scale-[1.02]">
                                <i class="fas fa-arrow-left mr-2"></i> Batal
                            </a>
                            {{-- Tombol Perbarui Data (Amber) --}}
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-base font-bold rounded-lg shadow-md 
                                           text-gray-800 bg-amber-400 hover:bg-amber-500 focus:ring-4 focus:ring-offset-2 focus:ring-amber-500/50 transition duration-150 transform hover:-translate-y-0.5" 
                                    id="submitBtn">
                                <i class="fas fa-save mr-2"></i> Perbarui Akun
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
                    <h3 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-user-tag mr-2 text-purple-500"></i> Status & Penugasan</h3>
                </div>
                <div class="p-6 text-sm space-y-4">
                    <p><strong>Nama Akun:</strong> {{ $teacher->name }}</p>
                    <p><strong>Peran Sistem:</strong> <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-200 text-gray-700">Wali Kelas</span></p>
                    
                    <hr class="border-gray-200">
                    
                    <h6 class="font-bold text-gray-800">Kelas Saat Ini:</h6>
                    @if($teacher->homeroomTeacher && $teacher->homeroomTeacher->class)
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-purple-600 text-white shadow-md">
                            {{ $teacher->homeroomTeacher->class->name }}
                        </span>
                        <p class="text-xs mt-2 text-gray-500">Kelas ini akan di-update jika Anda memilih kelas baru di form utama.</p>
                    @else
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-gray-400 text-white">
                            Belum Mengampu Kelas
                        </span>
                        <p class="text-xs mt-2 text-gray-500">Pilih kelas di form utama untuk menugaskan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="{{ asset('template/adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Pastikan JQuery dimuat di master layout
    
    $(document).ready(function() {
        // Initialize Select2
        $('#class_id').select2({
            theme: 'bootstrap4',
            placeholder: '-- Hapus Kelas yang Diampu / Kosongkan --',
            allowClear: true,
            width: '100%'
        });

        // Form submission loading state
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
        
        // --- Tampilkan notifikasi SweetAlert Toast untuk pesan sesi ---
        // (Logika ini akan bekerja jika SweetAlert2 dimuat di master layout)
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