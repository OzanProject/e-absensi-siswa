@extends('layouts.guest') 

@section('title', 'Lupa Password - ' . ($globalSettings['school_name'] ?? 'Sistem Absensi'))

@section('content')
<div class="min-h-screen flex text-gray-900">

    {{-- LEFT COLUMN: Branding --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-5/12 bg-indigo-900 relative flex-col justify-between p-12 overflow-hidden">
        {{-- Background --}}
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-800 to-purple-900 opacity-90"></div>
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 rounded-full bg-indigo-500 opacity-20 blur-3xl"></div>
             <div class="absolute inset-0" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px; opacity: 0.05;"></div>
        </div>

        {{-- Branding --}}
        <div class="relative z-10 flex items-center space-x-3">
             @if($globalSettings['logo_url'])
                <img src="{{ $globalSettings['logo_url'] }}" alt="Logo" class="h-10 w-auto bg-white/10 p-1.5 rounded-lg backdrop-blur-sm">
            @else
                <div class="h-10 w-10 bg-white/10 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-school text-white"></i>
                </div>
            @endif
            <span class="text-white font-bold text-lg tracking-wide uppercase opacity-90">{{ $globalSettings['school_name'] ?? 'E-Absensi' }}</span>
        </div>

        {{-- Hero Text --}}
        <div class="relative z-10 mt-10">
            <h1 class="text-4xl font-extrabold text-white leading-tight mb-6">
                Pemulihan Akun
            </h1>
            <p class="text-indigo-200 text-lg max-w-md leading-relaxed">
                Lupa password bukan masalah besar. Kami akan membantu Anda mendapatkan kembali akses ke akun Anda dengan aman.
            </p>
        </div>

        {{-- Footer --}}
        <div class="relative z-10 text-indigo-300 text-sm font-medium">
             &copy; {{ date('Y') }} {{ $globalSettings['school_name'] ?? 'Sekolah' }}. All rights reserved.
        </div>
    </div>

    {{-- RIGHT COLUMN: Form --}}
    <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24 bg-white relative">
        
        {{-- Mobile Header --}}
        <div class="lg:hidden text-center mb-8">
             @if($globalSettings['logo_url'])
                <img src="{{ $globalSettings['logo_url'] }}" alt="Logo" class="h-14 w-auto mx-auto mb-4">
            @endif
            <h2 class="text-2xl font-bold text-gray-900">Reset Password</h2>
        </div>

        <div class="mx-auto w-full max-w-sm lg:max-w-md">
            
            <div class="text-left mb-8 hidden lg:block">
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Reset Password</h2>
                <p class="mt-2 text-sm text-gray-500">
                    Masukkan email Anda untuk menerima link reset.
                </p>
            </div>

            @if (session('status'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('status') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                 <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ $errors->first() }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-6" id="forgotForm">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required autofocus
                        class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-xl placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition @error('email') border-red-500 @enderror" 
                        placeholder="nama@email.com" value="{{ old('email') }}">
                </div>

                <div>
                    <button type="submit" id="forgotBtn" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        <i class="fas fa-paper-plane mt-0.5 mr-2"></i> Kirim Link Reset
                    </button>
                    
                    <p class="mt-4 text-center text-sm text-gray-600">
                        Ingat password Anda? 
                        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 transition">Kembali Login</a>
                    </p>
                </div>
            </form>
            
            <div class="mt-8 border-t border-gray-100 pt-6 text-center">
                 <a href="{{ url('/') }}" class="text-xs text-gray-400 hover:text-gray-600 transition">
                    &larr; Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.getElementById('forgotForm').addEventListener('submit', function() {
        const btn = document.getElementById('forgotBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Memproses...';
        btn.classList.add('opacity-75', 'cursor-not-allowed');
    });
</script>
@endsection
