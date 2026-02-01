<style>
    .bg-hero-gradient {
        background: radial-gradient(circle at top, #1e1b4b 0%, #0f172a 100%);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .text-gradient {
        background: linear-gradient(to right, #818cf8, #c084fc, #e879f9);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Memastikan tidak ada overlap pada container utama */
    .hero-wrapper {
        padding-top: 140px;
        /* Jarak aman dari Navbar */
        padding-bottom: 80px;
    }

    @media (max-width: 768px) {
        .hero-wrapper {
            padding-top: 100px;
        }
    }
</style>

<header class="relative bg-hero-gradient overflow-hidden">
    {{-- Background Blobs --}}
    <div
        class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[600px] bg-indigo-600/20 rounded-full blur-[120px] z-0 opacity-50 pointer-events-none">
    </div>

    <div class="container mx-auto px-4 relative z-10 hero-wrapper">
        <div class="text-center max-w-4xl mx-auto">

            {{-- 1. Badge Status --}}
            <div
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 backdrop-blur-md text-indigo-300 text-xs font-bold uppercase tracking-widest mb-12">
                <span class="relative flex h-2 w-2">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                SISTEM ABSENSI ONLINE {{ $settings['app_version'] ?? 'V1.2.0' }}
            </div>

            {{-- 2. Judul Utama --}}
            <h1 class="text-5xl lg:text-7xl font-extrabold text-white mb-8 leading-tight tracking-tight">
                Absensi Sekolah <br>
                <span class="text-gradient">Cepat & Terintegrasi</span>
            </h1>

            {{-- 3. Deskripsi --}}
            <p class="text-lg lg:text-xl text-slate-400 mb-12 leading-relaxed max-w-2xl mx-auto font-light">
                {{ $settings['site_description'] ?? 'Platform manajemen kehadiran siswa digital dengan notifikasi WhatsApp Real-time untuk transparansi sekolah dan orang tua.' }}
            </p>

            {{-- 4. Tombol Aksi --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-5 mb-24">
                <a href="{{ route('login') }}"
                    class="w-full sm:w-auto px-10 py-4 text-white bg-indigo-600 rounded-2xl font-bold text-lg shadow-xl hover:bg-indigo-500 transition-all active:scale-95">
                    Mulai Sekarang
                </a>
                <a href="#features"
                    class="w-full sm:w-auto px-10 py-4 text-slate-200 bg-white/5 border border-white/10 rounded-2xl font-bold text-lg hover:bg-white/10 backdrop-blur-sm flex items-center justify-center transition-all">
                    <i class="fas fa-play-circle mr-3 text-indigo-400"></i> Pelajari Fitur
                </a>
            </div>

            {{-- 5. Dashboard Preview (Diberi margin top besar agar tidak menabrak tombol) --}}
            <div class="relative mx-auto max-w-4xl mt-16 px-4">
                <div
                    class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-[2.5rem] blur opacity-20">
                </div>

                <div
                    class="relative p-2 rounded-[2.5rem] bg-white/5 border border-white/10 backdrop-blur-md shadow-2xl">
                    {{-- Kita ganti aspect-video menjadi height yang diatur (h-64 atau h-80) --}}
                    <div
                        class="bg-[#0f172a] rounded-[2rem] overflow-hidden h-64 md:h-80 flex flex-col items-center justify-center border border-white/5 relative group">

                        {{-- Elemen Dekoratif Dashboard (Garis-garis halus) --}}
                        <div
                            class="absolute inset-x-0 top-0 h-12 bg-white/5 border-b border-white/5 flex items-center px-6 gap-3">
                            <div class="flex gap-1.5">
                                <div class="w-2.5 h-2.5 rounded-full bg-red-500/50"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-yellow-500/50"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-green-500/50"></div>
                            </div>
                            <div class="w-32 h-2 bg-white/10 rounded-full"></div>
                        </div>

                        {{-- Konten Utama --}}
                        <div class="text-center z-10">
                            <div
                                class="w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-3xl md:text-4xl text-white shadow-xl mb-4 mx-auto transition-transform duration-500 group-hover:scale-110">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <h3 class="text-xl md:text-2xl font-bold text-white mb-1 tracking-tight">Dashboard Overview
                            </h3>
                            <p class="text-slate-500 text-sm font-medium">Monitoring kehadiran siswa secara real-time
                            </p>
                        </div>

                        {{-- Grid Background Pattern --}}
                        <div class="absolute inset-0 opacity-10 pointer-events-none"
                            style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;">
                        </div>

                        {{-- Dark Vignette --}}
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-[#0f172a] via-transparent to-transparent opacity-80">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>