@php
    use Illuminate\Support\Facades\Storage;
    $settings = $settings ?? \App\Models\Setting::pluck('value', 'key')->toArray();
    $schoolName = $settings['school_name'] ?? 'E-Absensi Siswa';
    $schoolLogoPath = $settings['school_logo'] ?? null;
    $defaultLogo = asset('images/default_logo.png');
    $finalLogo = ($schoolLogoPath && Storage::disk('public')->exists($schoolLogoPath)) ? asset('storage/' . $schoolLogoPath) : $defaultLogo;
@endphp

<nav x-data="{ mobileMenu: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)"
    :class="{ 'bg-[#0f172a]/80 backdrop-blur-lg border-b border-white/10 py-3 shadow-2xl': scrolled, 'bg-transparent py-5': !scrolled }"
    class="fixed top-0 w-full z-[100] transition-all duration-500 font-sans">

    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">

            {{-- Logo Section --}}
            <a href="#" class="flex items-center gap-3 group relative z-[110]">
                <div class="relative">
                    <div
                        class="absolute -inset-1 bg-indigo-500 rounded-xl blur opacity-20 group-hover:opacity-40 transition duration-500">
                    </div>
                    <img src="{{ $finalLogo }}" alt="Logo"
                        class="relative w-10 h-10 object-contain bg-white rounded-xl p-1.5 shadow-lg transition-transform duration-300 group-hover:scale-110">
                </div>
                <div class="flex flex-col">
                    <span
                        class="text-white font-black text-lg md:text-xl leading-none tracking-tight group-hover:text-indigo-300 transition-colors">
                        {{ $schoolName }}
                    </span>
                    <span class="text-[10px] text-indigo-400 uppercase tracking-[0.2em] font-bold mt-1">
                        Digital Platform
                    </span>
                </div>
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex items-center bg-white/5 border border-white/10 rounded-2xl p-1 backdrop-blur-sm">
                <a href="#features"
                    class="px-5 py-2 text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-300">
                    Fitur
                </a>
                <a href="#how"
                    class="px-5 py-2 text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-300">
                    Cara Kerja
                </a>
            </div>

            {{-- Auth Section --}}
            <div class="hidden md:flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-500 shadow-[0_10px_20px_-5px_rgba(79,70,229,0.4)] hover:-translate-y-0.5 transition-all duration-300">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-6 py-2.5 text-sm font-bold text-white bg-white/10 border border-white/10 rounded-xl hover:bg-white/20 hover:border-white/20 transition-all duration-300">
                        Masuk
                    </a>
                @endauth
            </div>

            {{-- Mobile Toggle --}}
            <button @click="mobileMenu = !mobileMenu"
                class="md:hidden relative z-[110] p-2.5 text-slate-300 hover:text-white bg-white/5 rounded-xl border border-white/10 transition-all">
                <i :class="mobileMenu ? 'fas fa-times' : 'fas fa-bars'" class="text-xl w-6 text-center"></i>
            </button>
        </div>
    </div>

    {{-- Mobile Menu Overlay --}}
    <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-full"
        class="absolute top-0 left-0 w-full bg-[#0f172a] border-b border-white/10 pt-24 pb-10 px-6 shadow-2xl md:hidden z-[100]">

        <div class="space-y-4">
            <a href="#features" @click="mobileMenu = false"
                class="block px-4 py-4 rounded-2xl text-slate-300 hover:bg-white/5 hover:text-white font-bold transition-all border border-transparent hover:border-white/5">
                <i class="fas fa-layer-group mr-3 text-indigo-500"></i> Fitur Utama
            </a>
            <a href="#how" @click="mobileMenu = false"
                class="block px-4 py-4 rounded-2xl text-slate-300 hover:bg-white/5 hover:text-white font-bold transition-all border border-transparent hover:border-white/5">
                <i class="fas fa-project-diagram mr-3 text-indigo-500"></i> Cara Kerja
            </a>

            <div class="h-px bg-white/10 my-4"></div>

            @auth
                <a href="{{ route('dashboard') }}"
                    class="block w-full py-4 rounded-2xl text-center font-black text-white bg-indigo-600 shadow-lg shadow-indigo-900/40">
                    Buka Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="block w-full py-4 rounded-2xl text-center font-black text-white bg-white/10 border border-white/10">
                    Masuk ke Sistem
                </a>
            @endauth
        </div>
    </div>
</nav>