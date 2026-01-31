@extends('layouts.adminlte')

@section('title', 'Tambah Pengguna Baru')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div class="mb-3 sm:mb-0">
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-user-plus text-purple-600 mr-3"></i>
            Tambah Pengguna
        </h1>
    </div>
    <nav class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150"><i class="fas fa-home"></i></a></li>
            <li class="text-gray-300">/</li>
            <li><a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Pengguna</a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-bold">Baru</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-8">
        
        {{-- KOLOM KIRI: FORM (2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('admin.users.store') }}" method="POST" id="userForm">
                @csrf
                
                {{-- CARD 1: INFORMASI AKUN --}}
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-6"> 
                    <div class="p-6 border-b border-gray-100 bg-indigo-50/50">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-id-badge mr-3 text-indigo-500 bg-white p-2 rounded-lg shadow-sm"></i> 
                            Informasi Login
                        </h3>
                        <p class="text-xs text-gray-500 mt-1 ml-11">Data ini akan digunakan untuk login aplikasi.</p>
                    </div>
                    <div class="p-6 grid grid-cols-1 gap-6">
                        @php
                            $labelClass = 'block text-sm font-bold text-gray-700 mb-2';
                            $inputClass = 'w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition duration-200 bg-gray-50 focus:bg-white text-gray-800 font-medium';
                            $inputErrorClass = 'w-full px-4 py-3 rounded-xl border border-red-300 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition duration-200 bg-red-50 text-red-900';
                        @endphp
                        
                        {{-- Nama & Email Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nama Lengkap --}}
                            <div>
                                <label for="name" class="{{ $labelClass }}">Nama Lengkap <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500 text-gray-400">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <input type="text" name="name" id="name" 
                                            class="pl-11 @error('name') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                            value="{{ old('name') }}" 
                                            placeholder="Nama Lengkap" required>
                                </div>
                                @error('name') <p class="mt-2 text-xs text-red-600 font-bold"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="{{ $labelClass }}">Email <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500 text-gray-400">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <input type="email" name="email" id="email" 
                                            class="pl-11 @error('email') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                            value="{{ old('email') }}" 
                                            placeholder="email@sekolah.sch.id" required>
                                </div>
                                @error('email') <p class="mt-2 text-xs text-red-600 font-bold"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="{{ $labelClass }}">Password <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500 text-gray-400">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input type="password" name="password" id="password" 
                                        class="pl-11 @error('password') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                        placeholder="Minimal 8 karakter" required>
                            </div>
                            @error('password') <p class="mt-2 text-xs text-red-600 font-bold"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- CARD 2: PILIH PERAN (Premium Radio Selection) --}}
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-6">
                    <div class="p-6 border-b border-gray-100 bg-purple-50/50">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-user-tag mr-3 text-purple-500 bg-white p-2 rounded-lg shadow-sm"></i> 
                            Pilih Peran Pengguna
                        </h3>
                        <p class="text-xs text-gray-500 mt-1 ml-11">Tentukan hak akses pengguna ini.</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($roles as $key => $label)
                                @php
                                    $icon = match($key) {
                                        'super_admin' => 'fa-crown',
                                        'guru' => 'fa-chalkboard-teacher',
                                        'wali_kelas' => 'fa-user-graduate',
                                        'orang_tua' => 'fa-users',
                                        default => 'fa-user'
                                    };
                                    $color = match($key) {
                                        'super_admin' => 'indigo',
                                        'guru' => 'emerald',
                                        'wali_kelas' => 'purple',
                                        'orang_tua' => 'amber',
                                        default => 'gray'
                                    };
                                @endphp
                                <label class="relative flex flex-col p-4 bg-white border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-{{ $color }}-500 hover:bg-{{ $color }}-50/30 transition-all duration-200 group">
                                    <input type="radio" name="role" value="{{ $key }}" class="peer sr-only" {{ old('role') == $key ? 'checked' : '' }} required>
                                    
                                    {{-- Active Border & Indicator --}}
                                    <div class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 text-{{ $color }}-600 transition-opacity">
                                        <i class="fas fa-check-circle text-xl"></i>
                                    </div>
                                    <div class="absolute inset-0 border-2 border-transparent peer-checked:border-{{ $color }}-500 rounded-2xl pointer-events-none transition-all"></div>

                                    {{-- Content --}}
                                    <div class="flex items-center mb-2">
                                        <div class="w-10 h-10 rounded-full bg-{{ $color }}-100 text-{{ $color }}-600 flex items-center justify-center text-lg mr-3 shadow-sm group-hover:scale-110 transition-transform">
                                            <i class="fas {{ $icon }}"></i>
                                        </div>
                                        <span class="font-bold text-gray-700 peer-checked:text-{{ $color }}-800">{{ $label }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 leading-relaxed ml-13">
                                        @if($key === 'super_admin') Akses penuh ke seluruh sistem tanpa batasan.
                                        @elseif($key === 'guru') Akses manajemen absen mapel & jurnal mengajar.
                                        @elseif($key === 'wali_kelas') Akses manajemen kelas & laporan presensi lengkap.
                                        @elseif($key === 'orang_tua') Akses monitoring kehadiran siswa (read-only).
                                        @endif
                                    </p>
                                </label>
                            @endforeach
                        </div>
                        @error('role') <p class="mt-4 text-xs text-red-600 font-bold block"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('admin.users.index') }}" 
                    class="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 hover:text-gray-800 transition-colors">
                        Batal
                    </a>
                    <button type="submit" id="submitBtn"
                            class="px-8 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold shadow-lg hover:shadow-indigo-500/30 hover:scale-[1.02] active:scale-95 transition-all duration-200 flex items-center">
                        <i class="fas fa-save mr-2"></i> Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
        
        {{-- KOLOM KANAN: INFO (1/3) --}}
        <div class="lg:col-span-1">
            <div class="sticky top-6">
                <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl shadow-xl text-white p-6 overflow-hidden relative mb-6">
                    <i class="fas fa-info-circle absolute top-4 right-4 text-white/20 text-6xl transform rotate-12"></i>
                    <h3 class="text-lg font-bold mb-3 relative z-10">Panduan Admin</h3>
                    <p class="text-indigo-100 text-sm leading-relaxed relative z-10 mb-4">
                        Pilih peran yang sesuai untuk pengguna baru. Sistem akan otomatis membuat profile tambahan berdasarkan peran yang dipilih:
                    </p>
                    <ul class="space-y-3 text-sm text-indigo-50 relative z-10">
                        <li class="flex items-start bg-white/10 p-2 rounded-lg">
                            <i class="fas fa-check-circle mt-1 mr-2 text-emerald-300"></i>
                            <span><strong>Wali Kelas</strong> akan dibuatkan data di <em>Table Guru</em> (perlu assign kelas nanti).</span>
                        </li>
                        <li class="flex items-start bg-white/10 p-2 rounded-lg">
                            <i class="fas fa-check-circle mt-1 mr-2 text-emerald-300"></i>
                            <span><strong>Orang Tua</strong> akan dibuatkan data profil (perlu tautkan siswa nanti).</span>
                        </li>
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
            // Form submission loading state
            $('#userForm').on('submit', function() {
                if (this.checkValidity() === false) return; 
                
                const submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true)
                         .html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');
            });
            
            // Alert Error Toast 
            @if($errors->any())
                 Swal.fire({ 
                     icon: 'error', 
                     title: 'Kesalahan Validasi', 
                     text: 'Mohon periksa inputan yang bertanda merah.', 
                     toast: true, 
                     position: 'top-end', 
                     showConfirmButton: false, 
                     timer: 5000,
                     timerProgressBar: true
                 });
            @endif
        });
    </script>
@stop