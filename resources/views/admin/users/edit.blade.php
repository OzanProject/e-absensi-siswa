@extends('layouts.adminlte')

@section('title', 'Edit Pengguna: ' . $user->name)

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Amber/Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-user-edit text-amber-500 mr-2"></i> 
        <span>Edit Pengguna: {{ $user->name }}</span>
    </h1>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <li><a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Pengguna</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Edit</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    {{-- Mengganti row justify-content-center dan col-md-7 dengan Flex/Grid terpusat --}}
    <div class="flex justify-center">
        <div class="w-full max-w-2xl"> {{-- Batasi lebar maksimum --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-edit mr-2 text-amber-500"></i> Perbarui Detail Akun
                    </h3>
                </div>
                
                <div class="p-6"> {{-- Padding disesuaikan --}}
                    
                    {{-- Notifikasi Error Umum (Styling Tailwind) --}}
                    @if($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-6">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Harap periksa kembali input Anda.
                        </div>
                    @endif
                    
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" id="userForm" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        @php
                            // Helper Class untuk Input Styling (Fokus Amber)
                            $inputClass = 'w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition duration-150';
                            $errorBorder = 'border-red-500';
                            $defaultBorder = 'border-gray-300';
                        @endphp

                        {{-- Nama Lengkap --}}
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
                            <input type="text" name="name" id="name" 
                                    class="{{ $inputClass }} @error('name') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" 
                                    value="{{ old('name', $user->name) }}" required>
                            @error('name') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                        
                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email (Login) <span class="text-red-600">*</span></label>
                            <input type="email" name="email" id="email" 
                                    class="{{ $inputClass }} @error('email') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" 
                                    value="{{ old('email', $user->email) }}" required>
                            @error('email') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>

                        {{-- Password Baru (Opsional) --}}
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password Baru (Kosongkan jika tidak diubah)</label>
                            <input type="password" name="password" id="password" 
                                    class="{{ $inputClass }} @error('password') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror"
                                    placeholder="Minimal 8 karakter">
                            @error('password') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                        
                        {{-- Peran (Role) --}}
                        <div>
                            <label for="role" class="block text-sm font-semibold text-gray-700 mb-1">Peran (Role) <span class="text-red-600">*</span></label>
                            <select name="role" id="role" 
                                    class="{{ $inputClass }} @error('role') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" required>
                                <option value="">-- Pilih Peran --</option>
                                @foreach($roles as $key => $label)
                                    <option value="{{ $key }}" {{ old('role', $user->role) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('role') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>

                        {{-- Status Akun (is_approved) --}}
                        <div>
                            <label for="is_approved" class="block text-sm font-semibold text-gray-700 mb-1">Status Akun</label>
                            <select name="is_approved" id="is_approved" 
                                    class="{{ $inputClass }} @error('is_approved') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" required>
                                <option value="1" {{ old('is_approved', $user->is_approved) == 1 ? 'selected' : '' }}>Disetujui (Aktif)</option>
                                <option value="0" {{ old('is_approved', $user->is_approved) == 0 ? 'selected' : '' }}>Menunggu/Ditolak (Non-Aktif)</option>
                            </select>
                            @error('is_approved') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="mt-6 border-t border-gray-100 pt-4 flex space-x-3 justify-end">
                            <a href="{{ route('admin.users.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-base font-medium rounded-lg shadow-sm 
                                     text-gray-700 bg-white hover:bg-gray-100 transition duration-150 transform hover:scale-[1.02]">
                                <i class="fas fa-arrow-left mr-2"></i> Batal
                            </a>
                            {{-- Tombol Perbarui (Styling Tailwind Amber) --}}
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-bold rounded-lg shadow-md 
                                   text-gray-800 bg-amber-400 hover:bg-amber-500 focus:ring-4 focus:ring-offset-2 focus:ring-amber-500/50 transition duration-150 transform hover:-translate-y-0.5" id="submitBtn">
                                <i class="fas fa-save mr-2"></i> Perbarui Akun
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // --- LOGIKA SUBMIT DAN LOADING STATE (LOGIKA AMAN) ---
        $('#userForm').on('submit', function() {
            const btn = $('#submitBtn');
            // Cek validitas HTML5
            if (this.checkValidity() === false) {
                 return;
            }
            // Tampilkan loading state
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memperbarui...');
        });
        
        // --- Tampilkan notifikasi SweetAlert Toast untuk pesan sesi (LOGIKA AMAN) ---
        @if(session('success'))
             Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        @endif
        @if(session('error'))
             Swal.fire({ icon: 'error', title: 'Gagal!', text: '{{ session('error') }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        @endif

        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').fadeOut(400);
        }, 5000);
    });
</script>
@endsection