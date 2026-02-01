<style>
    .glass-card-modern {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.03) 0%, rgba(255, 255, 255, 0.01) 100%);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .glass-card-modern:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(99, 102, 241, 0.5);
        /* Indigo border on hover */
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
    }

    .icon-box-glow {
        position: relative;
        overflow: hidden;
    }

    .icon-box-glow::after {
        content: '';
        position: absolute;
        inset: 0;
        background: currentColor;
        opacity: 0.1;
        filter: blur(15px);
    }
</style>

<section id="features" class="py-24 bg-[#0f172a] relative overflow-hidden">
    {{-- Decorative Background Glow --}}
    <div
        class="absolute top-1/2 left-0 -translate-y-1/2 w-64 h-64 bg-indigo-600/10 rounded-full blur-[100px] pointer-events-none">
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {{-- Header --}}
        <div class="text-center max-w-3xl mx-auto mb-20">
            <div
                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-bold uppercase tracking-widest mb-6">
                <i class="fas fa-rocket"></i> Keunggulan Sistem
            </div>
            <h2 class="text-4xl md:text-5xl font-black text-white mb-6 tracking-tight leading-tight">
                Teknologi Modern <br> <span class="text-indigo-400">Pendidikan Digital</span>
            </h2>
            <p class="text-lg text-slate-400 font-light leading-relaxed">
                Kami menghadirkan ekosistem pintar untuk menyederhanakan manajemen kehadiran, memberikan transparansi
                maksimal bagi sekolah dan orang tua.
            </p>
        </div>

        {{-- Features Grid --}}
        <div class="grid md:grid-cols-3 gap-8">
            {{-- Feature 1: QR Code --}}
            <div class="glass-card-modern p-10 rounded-[2.5rem] group hover:-translate-y-3">
                <div
                    class="w-16 h-16 bg-blue-500/10 text-blue-400 rounded-2xl flex items-center justify-center text-3xl mb-8 icon-box-glow group-hover:bg-blue-500 group-hover:text-white transition-all duration-500">
                    <i class="fas fa-qrcode"></i>
                </div>
                <h4 class="text-2xl font-bold text-white mb-4 tracking-tight">QR Code Scanning</h4>
                <p class="text-slate-400 leading-relaxed font-light">
                    Presensi instan menggunakan kartu pelajar pintar. Data terverifikasi secara <span
                        class="text-white font-medium">real-time</span> ke server pusat tanpa risiko manipulasi.
                </p>
            </div>

            {{-- Feature 2: WA Gateway --}}
            <div class="glass-card-modern p-10 rounded-[2.5rem] group hover:-translate-y-3">
                <div
                    class="w-16 h-16 bg-emerald-500/10 text-emerald-400 rounded-2xl flex items-center justify-center text-3xl mb-8 icon-box-glow group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <h4 class="text-2xl font-bold text-white mb-4 tracking-tight">WhatsApp Gateway</h4>
                <p class="text-slate-400 leading-relaxed font-light">
                    Notifikasi otomatis langsung ke ponsel orang tua. Memberikan ketenangan pikiran dengan informasi jam
                    kedatangan dan kepulangan siswa yang <span class="text-white font-medium">akurat</span>.
                </p>
            </div>

            {{-- Feature 3: Monitoring --}}
            <div class="glass-card-modern p-10 rounded-[2.5rem] group hover:-translate-y-3">
                <div
                    class="w-16 h-16 bg-purple-500/10 text-purple-400 rounded-2xl flex items-center justify-center text-3xl mb-8 icon-box-glow group-hover:bg-purple-500 group-hover:text-white transition-all duration-500">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h4 class="text-2xl font-bold text-white mb-4 tracking-tight">Analisa Kehadiran</h4>
                <p class="text-slate-400 leading-relaxed font-light">
                    Dashboard khusus wali kelas untuk memantau statistik kehadiran. Memudahkan identifikasi dini
                    terhadap pola absensi siswa melalui <span class="text-white font-medium">grafik interaktif</span>.
                </p>
            </div>
        </div>
    </div>
</section>