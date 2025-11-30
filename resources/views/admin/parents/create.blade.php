@extends('layouts.adminlte')

@section('title', 'Tambah Orang Tua Baru')

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Orange/Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <div class="mb-2 sm:mb-0">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-user-plus text-orange-500 mr-2"></i>
            <span>Tambah Orang Tua Baru</span>
        </h1>
    </div>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li>
            <li class="text-gray-400">/</li>
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <li><a href="{{ route('parents.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Data Orang Tua</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Tambah Baru</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-6"> {{-- Padding disesuaikan --}}
            
            {{-- Notifikasi Error/Success Session (Styling Tailwind) --}}
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-6" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('parents.store') }}" method="POST" id="parentForm">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    {{-- KOLOM KIRI: DATA PERSONAL & LOGIN (1/2) --}}
                    <div>
                        {{-- Mengganti blue-600 ke orange-500/gray-800 dan size --}}
                        <h5 class="text-xl font-bold text-gray-800 flex items-center mb-4 border-b border-gray-100 pb-2">
                            <i class="fas fa-id-card mr-2 text-orange-500"></i> Data Personal & Kontak
                        </h5>
                        <div class="space-y-6">
                            
                            @php
                                // Mengganti fokus ring blue-500 menjadi orange-500
                                $inputClass = 'w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500';
                                $errorBorder = 'border-red-500';
                                $defaultBorder = 'border-gray-300';
                            @endphp

                            {{-- Nama Lengkap Orang Tua --}}
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap Orang Tua <span class="text-red-600">*</span></label>
                                <input type="text" name="name" id="name" 
                                        class="{{ $inputClass }} @error('name') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" 
                                        value="{{ old('name') }}" 
                                        placeholder="Nama lengkap Ayah/Ibu/Wali"
                                        required>
                                @error('name') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                            
                            {{-- Status Hubungan --}}
                            <div>
                                <label for="relation_status" class="block text-sm font-semibold text-gray-700 mb-1">Status Hubungan (Ayah/Ibu/Wali)</label>
                                <input type="text" name="relation_status" id="relation_status" 
                                        class="{{ $inputClass }} @error('relation_status') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" 
                                        value="{{ old('relation_status') }}"
                                        placeholder="Contoh: Ibu Kandung">
                                @error('relation_status') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            {{-- Nomor HP (WhatsApp) --}}
                            <div>
                                <label for="phone_number" class="block text-sm font-semibold text-gray-700 mb-1">Nomor HP (WhatsApp) <span class="text-red-600">*</span></label>
                                <div class="flex rounded-lg shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 text-gray-500 sm:text-sm bg-gray-50 border-gray-300">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="text" name="phone_number" id="phone_number" 
                                            class="flex-1 block w-full rounded-none rounded-r-lg px-3 py-2 border border-gray-300 
                                                     text-sm focus:ring-orange-500 focus:border-orange-500 
                                                     @error('phone_number') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" 
                                            value="{{ old('phone_number') }}" 
                                            placeholder="Contoh: 081234567890"
                                            required>
                                </div>
                                @error('phone_number') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                                <small class="mt-1 text-xs text-gray-500">Nomor ini harus unik dan digunakan untuk notifikasi.</small>
                            </div>

                            {{-- Judul Data Login Akun --}}
                            <h5 class="text-xl font-bold text-gray-800 flex items-center pt-4 mb-4 border-b border-gray-100 pb-2">
                                <i class="fas fa-lock mr-2 text-orange-500"></i> Data Login Akun
                            </h5>

                            {{-- Email Login --}}
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Login <span class="text-red-600">*</span></label>
                                <input type="email" name="email" id="email" 
                                        class="{{ $inputClass }} @error('email') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" 
                                        value="{{ old('email') }}" 
                                        placeholder="Email unik untuk login ke sistem"
                                        required>
                                @error('email') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            {{-- Password --}}
                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password <span class="text-red-600">*</span></label>
                                <input type="password" name="password" id="password" 
                                        class="{{ $inputClass }} @error('password') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" 
                                        placeholder="Minimal 8 karakter"
                                        required>
                                @error('password') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                    
                    {{-- KOLOM KANAN: RELASI SISWA (1/2) --}}
                    <div>
                        {{-- Mengganti blue-600 ke orange-500/gray-800 dan size --}}
                        <h5 class="text-xl font-bold text-gray-800 flex items-center mb-4 border-b border-gray-100 pb-2">
                            <i class="fas fa-users mr-2 text-orange-500"></i> Relasi Anak Siswa
                        </h5>
                        <div class="space-y-6">
                            
                            {{-- Pilih Siswa (Select2 Multi-Select) --}}
                            <div>
                                <label for="student_ids" class="block text-sm font-semibold text-gray-700 mb-1">Pilih Siswa (Anak) yang Diampu <span class="text-red-600">*</span></label>
                                {{-- Select2 membutuhkan kelas border di elemen <select> untuk error state --}}
                                @php $studentIdsStatusClass = $errors->has('student_ids') || $errors->has('student_ids.*') ? $errorBorder : $defaultBorder; @endphp
                                <select name="student_ids[]" id="student_ids" 
                                        class="w-full select2-form-control border {{ $studentIdsStatusClass }}" 
                                        multiple="multiple" required>
                                    @foreach($students as $student) 
                                        <option value="{{ $student->id }}" 
                                                {{ in_array($student->id, old('student_ids', [])) ? 'selected' : '' }}>
                                            {{ $student->name }} ({{ $student->class->name ?? 'Kelas N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="mt-1 text-xs text-gray-500 block">Wajib memilih minimal satu siswa.</small>
                                @error('student_ids') 
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> Pilih minimal satu siswa yang valid.</p>
                                @enderror
                                @error('student_ids.*') 
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> Pilih minimal satu siswa yang valid.</p>
                                @enderror
                            </div>

                            {{-- Info & Tips --}}
                            <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-lg mt-4">
                                <h6 class="font-bold text-lg text-orange-700 flex items-center mb-2"><i class="fas fa-info-circle mr-2"></i> Penting:</h6>
                                <ul class="list-disc ml-5 text-sm text-gray-700 space-y-1">
                                    <li>Pastikan setiap Orang Tua terhubung dengan **minimal satu siswa**.</li>
                                    <li>Data **Nomor HP** harus unik di sistem (digunakan sebagai identifikasi).</li>
                                    <li>Sistem akan menggunakan relasi ini untuk menampilkan data absensi anak di akun Orang Tua.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 p-6 border-t border-gray-100 flex justify-end space-x-3">
                    {{-- Tombol Batal --}}
                    <a href="{{ route('parents.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-base font-medium rounded-lg shadow-sm 
                            text-gray-700 bg-white hover:bg-gray-100 transition duration-150 transform hover:scale-[1.02]">
                        <i class="fas fa-arrow-left mr-2"></i> Batal
                    </a>
                    {{-- Tombol Submit (Green) --}}
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 border border-transparent text-base font-bold rounded-lg shadow-md 
                            text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-offset-2 focus:ring-green-500/50 transition duration-150 transform hover:-translate-y-0.5" id="submitBtn">
                        <i class="fas fa-save mr-2"></i> Simpan Akun Orang Tua
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script src="{{ asset('template/adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
    // Pastikan JQuery tersedia di master layout Anda
    
    $(document).ready(function() {
        // 🚨 Initialize Select2 (Multi-Select Siswa) - LOGIKA AMAN
        $('#student_ids').select2({
            theme: 'bootstrap4',
            placeholder: 'Cari dan Pilih Siswa...',
            allowClear: true,
            closeOnSelect: false, // Biarkan tetap terbuka setelah memilih satu siswa
            width: '100%'
        });
        
        // 🚨 Form submission loading state - LOGIKA AMAN
        $('#parentForm').on('submit', function() {
            const submitBtn = $('#submitBtn');
            
            // Cek validitas form
            if (this.checkValidity() === false) {
                 return; 
            }
            
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');
        });

        // 🚨 Validasi Input Nomor HP (hanya angka) - LOGIKA AMAN
        $('#phone_number').on('input', function() {
            // Hanya izinkan angka 0-9
            this.value = this.value.replace(/[^0-9]/g, ''); 
        });
        
        // Auto-hide alerts (Menggunakan JS/JQuery untuk alerts HTML biasa) - LOGIKA AMAN
        setTimeout(function() {
            $('.alert').fadeOut(400, function() { $(this).remove(); });
        }, 5000);
    });
</script>
@stop