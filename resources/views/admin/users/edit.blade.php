@extends('layouts.adminlte')

@section('title', 'Edit Pengguna: ' . $user->name)

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div class="mb-3 sm:mb-0">
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-user-edit text-purple-600 mr-3"></i>
            Edit Pengguna
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Perbarui profil dan hak akses pengguna.</p>
    </div>
    <nav class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150"><i class="fas fa-home"></i></a></li>
            <li class="text-gray-300">/</li>
            <li><a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Pengguna</a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-bold">Edit</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-8">
        
        {{-- KOLOM KIRI: FORM (2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" id="userForm">
                @csrf
                @method('PUT')
                
                {{-- CARD 1: INFORMASI AKUN --}}
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-6"> 
                    <div class="p-6 border-b border-gray-100 bg-indigo-50/50">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-id-badge mr-3 text-indigo-500 bg-white p-2 rounded-lg shadow-sm"></i> 
                            Informasi Login
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 gap-6">
                        @php
                            $labelClass = 'block text-sm font-bold text-gray-700 mb-2';
                            $inputClass = 'w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition duration-200 bg-gray-50 focus:bg-white text-gray-800 font-medium';
                            $inputErrorClass = 'w-full px-4 py-3 rounded-xl border border-red-300 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition duration-200 bg-red-50 text-red-900';
                        @endphp
                        
                        {{-- Nama & Email --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="{{ $labelClass }}">Nama Lengkap <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500 text-gray-400">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <input type="text" name="name" id="name" 
                                            class="pl-11 @error('name') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                            value="{{ old('name', $user->name) }}" required>
                                </div>
                                @error('name') <p class="mt-2 text-xs text-red-600 font-bold"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="email" class="{{ $labelClass }}">Email <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500 text-gray-400">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <input type="email" name="email" id="email" 
                                            class="pl-11 @error('email') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                            value="{{ old('email', $user->email) }}" required>
                                </div>
                                @error('email') <p class="mt-2 text-xs text-red-600 font-bold"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>

                         {{-- Password Change --}}
                         <div>
                            <label for="password" class="{{ $labelClass }}">Password Baru (Opsional)</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500 text-gray-400">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input type="password" name="password" id="password" 
                                        class="pl-11 @error('password') {{ $inputErrorClass }} @else {{ $inputClass }} @enderror" 
                                        placeholder="Kosongkan jika tidak ingin mengubah">
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1 ml-1">Min. 8 karakter jika diisi.</p>
                            @error('password') <p class="mt-2 text-xs text-red-600 font-bold"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- CARD 2: PILIH PERAN & STATUS --}}
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-6">
                    <div class="p-6 border-b border-gray-100 bg-purple-50/50">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-user-cog mr-3 text-purple-500 bg-white p-2 rounded-lg shadow-sm"></i> 
                            Konfigurasi Peran & Akses
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        
                        {{-- Role Selection --}}
                        <div>
                            <label class="{{ $labelClass }}">Peran Pengguna</label>
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
                                        $isChecked = old('role', $user->role) == $key;
                                    @endphp
                                    <label class="relative flex flex-col p-4 bg-white border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-{{ $color }}-500 hover:bg-{{ $color }}-50/30 transition-all duration-200 group">
                                        <input type="radio" name="role" value="{{ $key }}" class="peer sr-only" {{ $isChecked ? 'checked' : '' }} required>
                                        
                                        <div class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 text-{{ $color }}-600 transition-opacity">
                                            <i class="fas fa-check-circle text-xl"></i>
                                        </div>
                                        <div class="absolute inset-0 border-2 border-transparent peer-checked:border-{{ $color }}-500 rounded-2xl pointer-events-none transition-all"></div>

                                        <div class="flex items-center mb-1">
                                            <div class="w-8 h-8 rounded-full bg-{{ $color }}-100 text-{{ $color }}-600 flex items-center justify-center text-sm mr-3">
                                                <i class="fas {{ $icon }}"></i>
                                            </div>
                                            <span class="font-bold text-gray-700 peer-checked:text-{{ $color }}-800 text-sm">{{ $label }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('role') <p class="mt-2 text-xs text-red-600 font-bold"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>

                        <hr class="border-gray-100">

                        {{-- Status Selection --}}
                        <div>
                            <label for="is_approved" class="{{ $labelClass }}">Status Akun</label>
                            <div class="flex items-center space-x-4 bg-gray-50 p-3 rounded-xl border border-gray-200">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="radio" name="is_approved" value="1" class="text-indigo-600 focus:ring-indigo-500" {{ old('is_approved', $user->is_approved) == 1 ? 'checked' : '' }}>
                                    <span class="text-sm font-bold text-emerald-600 bg-emerald-100 px-3 py-1 rounded-lg border border-emerald-200">
                                        <i class="fas fa-check-circle mr-1"></i> Aktif (Disetujui)
                                    </span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="radio" name="is_approved" value="0" class="text-indigo-600 focus:ring-indigo-500" {{ old('is_approved', $user->is_approved) == 0 ? 'checked' : '' }}>
                                    <span class="text-sm font-bold text-red-600 bg-red-100 px-3 py-1 rounded-lg border border-red-200">
                                        <i class="fas fa-ban mr-1"></i> Nonaktif / Pending
                                    </span>
                                </label>
                            </div>
                        </div>

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
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
        
        {{-- KOLOM KANAN: INFO (1/3) --}}
        <div class="lg:col-span-1">
             <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-3xl shadow-xl text-white p-6 overflow-hidden relative mb-6">
                <i class="fas fa-exclamation-triangle absolute top-4 right-4 text-white/20 text-6xl transform rotate-12"></i>
                <h3 class="text-lg font-bold mb-3 relative z-10">Perhatian Penting</h3>
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/20 relative z-10">
                    <p class="text-sm font-semibold mb-2 text-white">⚠️ Mengubah Peran</p>
                    <p class="text-xs text-white/90 leading-relaxed text-justify">
                        Mengubah peran pengguna (misal: dari Wali Kelas ke Orang Tua) akan menyebabkan <strong>penghapusan data relasi lama</strong> (seperti data kelas yang diampu atau siswa yang ditautkan). Pastikan ini tindakan yang disengaja.
                    </p>
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
                         .html('<i class="fas fa-spinner fa-spin mr-2"></i> Memperbarui...');
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