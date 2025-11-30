@extends('layouts.guest') 

@section('title', 'Login Sistem Absensi')

@section('content')
{{-- Konten ini akan berada di dalam div class="login-box" (didefinisikan di guest layout) --}}
@php
    // Menggunakan fallback jika settings belum dimuat di layout guest
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
        <p class="text-gray-500 text-sm mt-1">Masuk ke Area Administrasi</p>
    </div>
    
    {{-- CARD BODY: Form --}}
    <div class="p-6">
        
        {{-- 🟢 PESAN SUCCESS/STATUS --}}
        @if (session('status') || session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded-lg text-sm mb-3" role="alert">
                {{ session('status') ?? session('success') }}
            </div>
        @endif
        
        {{-- ⚠️ PESAN WARNING DARI ROLE MIDDLEWARE (Belum Disetujui) --}}
        @if ($errors->has('email') && str_contains($errors->first('email'), 'disetujui')) 
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-2 rounded-lg text-sm mb-3" role="alert">
                {{ $errors->first('email') }}
            </div>
        @elseif ($errors->any())
            {{-- ERROR VALIDASI GLOBAL (Email/Password Salah) --}}
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-1.5 rounded-lg text-sm mb-3" role="alert">
                Email atau password yang Anda masukkan salah.
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            
            @php
                $inputClass = 'w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500';
                $errorInputClass = 'border-red-500 focus:ring-red-500 focus:border-red-500';
            @endphp
            
            {{-- Email Input --}}
            <div class="mb-3">
                <div class="relative">
                    <input type="email" name="email" 
                            class="{{ $inputClass }} pl-10 text-sm @error('email') {{ $errorInputClass }} @enderror" 
                            placeholder="Email Login" value="{{ old('email') }}" required autofocus autocomplete="username">
                    <span class="absolute left-0 top-0 mt-2 ml-3 text-gray-400"><span class="fas fa-envelope"></span></span>
                </div>
                {{-- Tampilkan error validasi email standar --}}
                @error('email') 
                    @if(!str_contains($message, 'disetujui'))
                        <span class="text-red-600 text-xs mt-1 d-block"> {{ $message }} </span> 
                    @endif
                @enderror
            </div>
            
            {{-- Password Input --}}
            <div class="mb-3">
                <div class="relative">
                    <input type="password" name="password" 
                            class="{{ $inputClass }} pl-10 text-sm @error('password') {{ $errorInputClass }} @enderror" 
                            placeholder="Password" required autocomplete="current-password">
                    <span class="absolute left-0 top-0 mt-2 ml-3 text-gray-400"><span class="fas fa-lock"></span></span>
                </div>
                @error('password') <span class="text-red-600 text-xs mt-1 d-block"> {{ $message }} </span> @enderror
            </div>


            <div class="flex flex-wrap items-center mt-4">
                <div class="w-7/12">
                    <div class="flex items-center">
                        {{-- Checkbox Remember Me (Perlu Icheck custom jika tanpa JS) --}}
                        <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="remember" class="ml-2 text-sm text-gray-500">
                            Ingat Saya
                        </label>
                    </div>
                </div>
                <div class="w-5/12">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-semibold rounded-lg shadow-md 
                            text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150" id="loginBtn">
                        <i class="fas fa-sign-in-alt mr-1"></i> Masuk
                    </button>
                </div>
            </div>
        </form>
        
        {{-- Link Lupa Password --}}
        <p class="mb-1 mt-4 text-sm text-center">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-gray-500 hover:text-indigo-600 transition duration-150">Lupa Password?</a>
            @endif
        </p>

        {{-- OPSI NAVIGASI TAMBAHAN --}}
        <div class="flex justify-center flex-wrap mt-3 text-sm">
            <a href="{{ route('landing') }}" class="text-indigo-600 hover:text-indigo-700 mr-3">
                <i class="fas fa-arrow-left mr-1"></i> Beranda
            </a> 
            @if (Route::has('register'))
                <span class="text-gray-400">|</span> 
                <a href="{{ route('register') }}" class="ml-3 text-gray-500 hover:text-indigo-600">Daftar Akun Baru</a>
            @endif
        </div>
        
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        // Loading State Saat Submit
        $('#loginForm').on('submit', function() {
            const btn = $('#loginBtn');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
        });
    });
</script>
@endpush