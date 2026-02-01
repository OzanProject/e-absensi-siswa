<style>
    .step-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.01) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.4s ease;
    }

    .step-card:hover {
        border-color: rgba(99, 102, 241, 0.4);
        transform: translateY(-10px);
        background: rgba(255, 255, 255, 0.07);
    }

    /* Garis penghubung antar langkah */
    .step-line {
        height: 2px;
        background: linear-gradient(to right, #6366f1, #a855f7);
        flex-grow: 1;
        max-width: 100px;
        opacity: 0.3;
    }
</style>

<section id="how" class="py-32 bg-[#0f172a] relative border-t border-white/5 overflow-hidden">
    {{-- Decorative light behind title --}}
    <div
        class="absolute top-0 left-1/2 -translate-x-1/2 w-1/2 h-1/2 bg-indigo-500/5 rounded-full blur-[120px] pointer-events-none">
    </div>

    <div class="container mx-auto px-6 relative z-10">
        {{-- Header Section --}}
        <div class="text-center max-w-2xl mx-auto mb-20">
            <h2 class="text-indigo-400 font-bold uppercase tracking-widest text-xs mb-4">Proses Kerja</h2>
            <h3 class="text-4xl font-black text-white mb-6 tracking-tight">Alur Sistem Otomatis</h3>
            <div class="h-1.5 w-20 bg-indigo-600 mx-auto rounded-full"></div>
        </div>

        {{-- Steps Container --}}
        <div class="flex flex-col md:flex-row justify-center items-stretch gap-8 lg:gap-12">

            {{-- Step 1 --}}
            <div class="flex-1 flex flex-col items-center">
                <div class="step-card p-10 rounded-[2.5rem] text-center w-full h-full flex flex-col items-center">
                    <div
                        class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-black text-xl mb-8 shadow-xl shadow-indigo-900/40 transform -rotate-6 group-hover:rotate-0 transition-transform">
                        01
                    </div>
                    <h5 class="text-2xl font-bold text-white mb-4 tracking-tight">Scan Kartu</h5>
                    <p class="text-slate-400 text-sm leading-relaxed font-light">
                        Siswa melakukan tapping atau scan kartu QR pelajar pada terminal perangkat sekolah.
                    </p>
                </div>
            </div>

            {{-- Arrow/Line 1 (Hidden on Mobile) --}}
            <div class="hidden lg:flex items-center">
                <div class="step-line"></div>
                <i class="fas fa-chevron-right text-indigo-500/50 mx-2"></i>
            </div>

            {{-- Step 2 --}}
            <div class="flex-1 flex flex-col items-center">
                <div
                    class="step-card p-10 rounded-[2.5rem] text-center w-full h-full flex flex-col items-center border-indigo-500/20">
                    <div
                        class="w-14 h-14 bg-indigo-500 rounded-2xl flex items-center justify-center text-white font-black text-xl mb-8 shadow-xl shadow-indigo-900/40">
                        02
                    </div>
                    <h5 class="text-2xl font-bold text-white mb-4 tracking-tight">Data Diproses</h5>
                    <p class="text-slate-400 text-sm leading-relaxed font-light">
                        Server memverifikasi identitas dan mencatat jam kehadiran secara akurat ke database.
                    </p>
                </div>
            </div>

            {{-- Arrow/Line 2 (Hidden on Mobile) --}}
            <div class="hidden lg:flex items-center">
                <div class="step-line"></div>
                <i class="fas fa-chevron-right text-indigo-500/50 mx-2"></i>
            </div>

            {{-- Step 3 --}}
            <div class="flex-1 flex flex-col items-center">
                <div class="step-card p-10 rounded-[2.5rem] text-center w-full h-full flex flex-col items-center">
                    <div
                        class="w-14 h-14 bg-purple-600 rounded-2xl flex items-center justify-center text-white font-black text-xl mb-8 shadow-xl shadow-purple-900/40 transform rotate-6 group-hover:rotate-0 transition-transform">
                        03
                    </div>
                    <h5 class="text-2xl font-bold text-white mb-4 tracking-tight">Notifikasi WA</h5>
                    <p class="text-slate-400 text-sm leading-relaxed font-light">
                        Sistem mengirimkan laporan kehadiran secara instan ke WhatsApp orang tua/wali siswa.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>