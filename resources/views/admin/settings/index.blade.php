@extends('layouts.adminlte')

@section('title', 'Pengaturan Umum Sistem')

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        {{-- Ikon Cogs diubah ke warna Indigo --}}
        <i class="fas fa-cogs text-indigo-600 mr-2"></i> 
        <span>Pengaturan Umum Sistem</span>
    </h1>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Pengaturan</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-5 border-b border-gray-100">
            <h3 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-tools mr-2 text-indigo-500"></i> Konfigurasi Sistem E-Absensi</h3>
        </div>
        
        <div class="p-6"> {{-- Padding disesuaikan --}}
            
            {{-- Notifikasi Error Umum (Styling Tailwind) --}}
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-6">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Harap periksa kembali input Anda. Ditemukan kesalahan.
                </div>
            @endif
            
            {{-- Notifikasi Sukses/Gagal dari session (Styling Tailwind) --}}
            @if(session('success')) 
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg relative mb-6">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error')) 
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-6">
                    <i class="fas fa-ban mr-2"></i> {{ session('error') }}
                </div>
            @endif

            {{-- FORM (Logika PHP/Blade & Input Values AMAN) --}}
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
                @csrf
                @method('PUT')
                
                @php
                    // Helper Class untuk Input Styling (Fokus Indigo)
                    // Mengganti fokus blue-500 ke indigo-500
                    $inputClass = 'w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150';
                    $errorBorder = 'border-red-500';
                    $defaultBorder = 'border-gray-300';
                @endphp

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8"> {{-- Gap disesuaikan --}}
                    
                    {{-- KOLOM KIRI: INFO SEKOLAH & ABSENSI (1/2) --}}
                    <div>
                        {{-- Judul: Mengganti blue-600 ke indigo-600 --}}
                        <h5 class="text-xl font-bold text-gray-800 flex items-center mb-4 border-b border-gray-100 pb-2"><i class="fas fa-school mr-2 text-indigo-500"></i> Informasi Sekolah</h5>
                        
                        <div class="space-y-6">
                            
                            {{-- Nama Sekolah --}}
                            <div>
                                <label for="school_name" class="block text-sm font-semibold text-gray-700 mb-1">{{ $keys['school_name'] ?? 'Nama Sekolah' }} <span class="text-red-600">*</span></label>
                                <input type="text" name="school_name" id="school_name" 
                                        class="{{ $inputClass }} @error('school_name') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" 
                                        value="{{ old('school_name', $settings['school_name'] ?? '') }}" required>
                                @error('school_name') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                            
                            {{-- INPUT LOGO UPLOAD (Logika PHP AMAN) --}}
                            <div>
                                <label for="school_logo_file" class="block text-sm font-semibold text-gray-700 mb-1">Upload Logo Sekolah</label>
                                
                                {{-- Tampilkan Logo Saat Ini --}}
                                @php
                                    $currentLogoPath = $settings['school_logo'] ?? '';
                                    $logoExists = !empty($currentLogoPath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($currentLogoPath);
                                    $logoPath = $logoExists
                                                    ? asset('storage/' . $currentLogoPath)
                                                    : asset('images/default_logo.png'); // Asumsi path default
                                @endphp
                                <div class="mb-3 text-center border-2 border-gray-200 p-3 rounded-xl bg-gray-50/50">
                                    <img src="{{ $logoPath }}" alt="Logo Sekolah Saat Ini" id="logo-preview" 
                                            style="max-height: 100px; max-width: 100%; object-fit: contain;" class="mx-auto">
                                </div>

                                {{-- File Input (Styling Tailwind File Input) --}}
                                {{-- Mengganti blue-50/blue-700 ke indigo-50/indigo-700 --}}
                                <input type="file" name="school_logo_file" id="school_logo_file" 
                                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('school_logo_file') border-red-500 @enderror" 
                                        accept="image/png, image/jpeg, image/jpg">
                                <small class="mt-1 text-xs text-gray-500 block">Max 2MB (JPG, PNG). Kosongkan jika tidak ingin diubah.</small>
                                @error('school_logo_file') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                            
                            {{-- Judul Pengaturan Absensi --}}
                            <h5 class="text-xl font-bold text-gray-800 flex items-center pt-4 mb-4 border-b border-gray-100 pb-2"><i class="fas fa-clock mr-2 text-indigo-500"></i> Pengaturan Absensi</h5>

                            {{-- Jam Mulai Absensi (IN) --}}
                            <div>
                                <label for="attendance_start_time" class="block text-sm font-semibold text-gray-700 mb-1">{{ $keys['attendance_start_time'] ?? 'Jam Mulai Absensi' }} <span class="text-red-600">*</span></label>
                                @php
                                    $startTime = old('attendance_start_time', $settings['attendance_start_time'] ?? '07:00');
                                    $timeValue = substr($startTime, 0, 5); // Ambil HH:MM
                                @endphp
                                <input type="time" name="attendance_start_time" id="attendance_start_time" 
                                        class="{{ $inputClass }} @error('attendance_start_time') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" 
                                        value="{{ $timeValue }}" required>
                                <small class="mt-1 text-xs text-gray-500 block">Waktu mulai absensi masuk (HH:MM).</small>
                                @error('attendance_start_time') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                            
                            {{-- Jam Mulai Pulang (OUT) --}}
                            <div>
                                <label for="attendance_end_time" class="block text-sm font-semibold text-gray-700 mb-1">{{ $keys['attendance_end_time'] ?? 'Jam Mulai Pulang' }} <span class="text-red-600">*</span></label>
                                @php
                                    $endTime = old('attendance_end_time', $settings['attendance_end_time'] ?? '15:00');
                                    $endTimeValue = substr($endTime, 0, 5); // Ambil HH:MM
                                @endphp
                                <input type="time" name="attendance_end_time" id="attendance_end_time" 
                                        class="{{ $inputClass }} @error('attendance_end_time') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" 
                                        value="{{ $endTimeValue }}" required>
                                <small class="mt-1 text-xs text-gray-500 block">Siswa **hanya** dapat absen pulang setelah waktu ini (HH:MM).</small>
                                @error('attendance_end_time') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            {{-- Toleransi Keterlambatan --}}
                            <div>
                                <label for="late_tolerance_minutes" class="block text-sm font-semibold text-gray-700 mb-1">{{ $keys['late_tolerance_minutes'] ?? 'Toleransi Keterlambatan' }} (Menit) <span class="text-red-600">*</span></label>
                                <input type="number" name="late_tolerance_minutes" id="late_tolerance_minutes" 
                                        class="{{ $inputClass }} @error('late_tolerance_minutes') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" 
                                        value="{{ old('late_tolerance_minutes', $settings['late_tolerance_minutes'] ?? 10) }}" required min="0">
                                <small class="mt-1 text-xs text-gray-500 block">Siswa dianggap terlambat jika absen melebihi jam mulai + toleransi.</small>
                                @error('late_tolerance_minutes') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- KOLOM KANAN: PENGATURAN WHATSAPP (1/2) --}}
                    <div>
                        {{-- Judul: Mengganti blue-600 ke indigo-600 --}}
                        <h5 class="text-xl font-bold text-gray-800 flex items-center mb-4 border-b border-gray-100 pb-2"><i class="fab fa-whatsapp mr-2 text-indigo-500"></i> Pengaturan Notifikasi WhatsApp</h5>
                        
                        <div class="space-y-6">
                            
                            {{-- Endpoint API WhatsApp --}}
                            <div>
                                <label for="wa_api_endpoint" class="block text-sm font-semibold text-gray-700 mb-1">{{ $keys['wa_api_endpoint'] ?? 'Endpoint API WhatsApp' }}</label>
                                <input type="url" name="wa_api_endpoint" id="wa_api_endpoint" 
                                        class="{{ $inputClass }} @error('wa_api_endpoint') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" 
                                        value="{{ old('wa_api_endpoint', $settings['wa_api_endpoint'] ?? '') }}">
                                <small class="mt-1 text-xs text-gray-500 block">URL layanan API WhatsApp Anda (cth: https://api.service.com/send).</small>
                                @error('wa_api_endpoint') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>

                            {{-- Kunci API WhatsApp --}}
                            <div>
                                <label for="wa_api_key" class="block text-sm font-semibold text-gray-700 mb-1">{{ $keys['wa_api_key'] ?? 'Kunci API WhatsApp' }}</label>
                                <input type="text" name="wa_api_key" id="wa_api_key" 
                                        class="{{ $inputClass }} @error('wa_api_key') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" 
                                        value="{{ old('wa_api_key', $settings['wa_api_key'] ?? '') }}">
                                @error('wa_api_key') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                            
                            {{-- Button Simpan --}}
                            <div class="pt-6 border-t border-gray-100 mt-6">
                                <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-2.5 border border-transparent text-base font-bold rounded-lg shadow-md
                                         text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-offset-2 focus:ring-green-500/50 transition duration-150 transform hover:-translate-y-0.5" id="submitSettingsBtn">
                                    <i class="fas fa-save mr-2"></i> Simpan Pengaturan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    
    $(document).ready(function() {
        // 1. Preview Logo Saat Upload (Vanilla JS) - LOGIKA AMAN
        const logoInput = document.getElementById('school_logo_file');
        const logoPreview = document.getElementById('logo-preview');
        
        if (logoInput) {
            logoInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        logoPreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // 2. Form submission loading state - LOGIKA AMAN
        $('#settingsForm').on('submit', function() {
            const submitBtn = $('#submitSettingsBtn');
            // Cek validitas form HTML5
            if (this.checkValidity() === false) {
                 return;
            }
            // Tampilkan loading state
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        });

        // 3. Auto-show session success/error messages using SweetAlert2 Toast
        // LOGIKA AMAN
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Gagal!', text: '{{ session('error') }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        @endif
    });
</script>
@stop

@section('css')
<style>
/* CSS KUSTOM MINIMAL UNTUK KOMPATIBILITAS */
.text-indigo-600 { color: #4f46e5; }
.bg-indigo-50 { background-color: #eef2ff; }
.text-indigo-700 { color: #4338ca; }
.text-teal-500 { color: #20c997; } 
.bg-teal-600 { background-color: #0d9488 !important; }
.hover\:bg-teal-700:hover { background-color: #0f766e !important; }

/* FIXES */
.text-red-600 { color: #dc3545; }
.bg-green-600 { background-color: #10b981 !important; }
.hover\:bg-green-700:hover { background-color: #059669 !important; }
</style>
@stop