@extends('layouts.guest') 

@section('title', 'Daftar Akun Baru')

@section('content')
{{-- Wrapper Konten ini akan berada di dalam div class="login-box" (didefinisikan di guest layout) --}}
@php
    // Logika PHP untuk setting
    $settings = $settings ?? \App\Models\Setting::pluck('value', 'key')->toArray(); 
    $schoolName = $settings['school_name'] ?? 'E-ABSENSI SISWA';
    $schoolLogoPath = $settings['school_logo'] ?? null;
    $logoSrc = (isset($schoolLogoPath) && $schoolLogoPath && file_exists(public_path('storage/' . $schoolLogoPath)))
                ? asset('storage/' . $schoolLogoPath)
                : null;
@endphp

<div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-100">
    
    {{-- CARD HEADER: Branding --}}
    <div class="bg-gray-200 text-center p-6 border-b-2 border-gray-300">
        {{-- LOGO BRANDING --}}
        @if($logoSrc)
            <img src="{{ $logoSrc }}" alt="Logo Sekolah" class="max-h-14 w-auto mx-auto mb-3 object-contain">
        @else
            <i class="fas fa-clipboard-check text-indigo-600 text-4xl mb-3"></i>
        @endif
        
        <div class="text-2xl font-bold text-indigo-600">
            {{ $schoolName }}
        </div>
        <p class="text-gray-500 text-sm mt-1">Daftar Akun Pengguna Baru</p>
    </div>
    
    {{-- CARD BODY: Form --}}
    <div class="p-6">
        
        {{-- ✅ PESAN STATUS/SUCCESS DARI SESSION --}}
        @if (session('status') || session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded-lg text-sm mb-3" role="alert">
                {{ session('status') ?? session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-1.5 rounded-lg text-sm mb-3" role="alert">
                Harap periksa kembali isian Anda.
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" id="registerForm">
            @csrf
            
            @php
                $inputGroupBaseClass = 'relative flex items-center mb-4';
                $inputClass = 'w-full px-3 py-2 border rounded-lg pl-10 shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm';
                $errorInputClass = 'border-red-500';
            @endphp
            
            {{-- Name Input --}}
            <div class="{{ $inputGroupBaseClass }}">
                <input type="text" name="name" 
                        class="{{ $inputClass }} @error('name') {{ $errorInputClass }} @enderror" 
                        placeholder="Nama Lengkap" value="{{ old('name') }}" required autofocus autocomplete="name">
                <span class="absolute left-0 mt-0 ml-3 text-gray-400"><span class="fas fa-user"></span></span>
            </div>
            @error('name') <span class="text-red-600 text-xs mt-1 d-block"> {{ $message }} </span> @enderror

            {{-- Email Input --}}
            <div class="{{ $inputGroupBaseClass }}">
                <input type="email" name="email" 
                        class="{{ $inputClass }} @error('email') {{ $errorInputClass }} @enderror" 
                        placeholder="Email Login" value="{{ old('email') }}" required autocomplete="username">
                <span class="absolute left-0 mt-0 ml-3 text-gray-400"><span class="fas fa-envelope"></span></span>
            </div>
            @error('email') <span class="text-red-600 text-xs mt-1 d-block"> {{ $message }} </span> @enderror

            {{-- ROLE SELECT INPUT --}}
            <div class="{{ $inputGroupBaseClass }}">
                <select name="role" class="w-full px-3 py-2 border rounded-lg pl-10 shadow-sm text-gray-500 focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('role') {{ $errorInputClass }} @enderror" required>
                    <option value="" disabled selected>Daftar sebagai...</option>
                    <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="wali_kelas" {{ old('role') == 'wali_kelas' ? 'selected' : '' }}>Guru/Wali Kelas</option>
                    <option value="orang_tua" {{ old('role') == 'orang_tua' ? 'selected' : '' }}>Orang Tua</option>
                </select>
                <span class="absolute left-0 mt-0 ml-3 text-gray-400"><span class="fas fa-id-badge"></span></span>
            </div>
            @error('role') <span class="text-red-600 text-xs mt-1 d-block"> {{ $message }} </span> @enderror


            {{-- Password Input --}}
            <div class="{{ $inputGroupBaseClass }}">
                <input type="password" name="password" 
                        class="{{ $inputClass }} @error('password') {{ $errorInputClass }} @enderror" 
                        placeholder="Password" required autocomplete="new-password">
                <span class="absolute left-0 mt-0 ml-3 text-gray-400"><span class="fas fa-lock"></span></span>
            </div>
            @error('password') <span class="text-red-600 text-xs mt-1 d-block"> {{ $message }} </span> @enderror

            {{-- Confirm Password Input --}}
            <div class="relative flex items-center mb-4">
                <input type="password" name="password_confirmation" 
                        class="{{ $inputClass }} @error('password_confirmation') {{ $errorInputClass }} @enderror" 
                        placeholder="Ulangi Password" required autocomplete="new-password">
                <span class="absolute left-0 mt-0 ml-3 text-gray-400"><span class="fas fa-lock"></span></span>
            </div>
            @error('password_confirmation') <span class="text-red-600 text-xs mt-1 d-block"> {{ $message }} </span> @enderror


            <div class="mt-5">
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 text-base font-semibold rounded-lg shadow-md 
                        text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150" id="registerBtn">
                    Daftar Akun
                </button>
            </div>
        </form>
        
        {{-- NAVIGASI PUBLIK --}}
        <div class="flex justify-center flex-wrap mt-3 text-sm space-x-3">
            <a href="{{ route('landing') }}" class="text-indigo-600 hover:text-indigo-700">
                <i class="fas fa-arrow-left mr-1"></i> Beranda
            </a> 
            <span class="text-gray-400">|</span> 
            <a href="{{ route('login') }}" class="text-gray-500 hover:text-indigo-600">Sudah punya akun? Masuk</a>
        </div>
        
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        // Loading State Saat Submit
        $('#registerForm').on('submit', function() {
            const btn = $('#registerBtn');
            // Cek validitas HTML5
            if (this.checkValidity() === false) {
                 return;
            }
            // Tampilkan loading state
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
        });
    });
</script>
@endpush