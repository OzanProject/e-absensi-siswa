@extends('layouts.adminlte')

@section('title', 'Pengaturan Umum Sistem')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div>
        <h1
            class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-cogs text-purple-600 mr-3"></i>
            Pengaturan Sistem
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Konfigurasi data sekolah dan parameter operasional sistem.</p>
    </div>
    <nav class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100"
        aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}"
                    class="text-indigo-600 hover:text-indigo-800 transition duration-150"><i
                        class="fas fa-home"></i></a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-bold">Pengaturan</li>
        </ol>
    </nav>
</div>
@stop

@section('css')
<style>
    /* Toggle Switch Custom */
    .toggle-checkbox:checked {
        right: 0;
        border-color: #6875f5;
    }

    .toggle-checkbox:checked+.toggle-label {
        background-color: #6875f5;
    }

    /* Smooth Inputs */
    .form-input-custom {
        transition: all 0.3s ease;
    }

    .form-input-custom:focus {
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    /* File Input Wrapper */
    .file-drop-area {
        position: relative;
        display: flex;
        align-items: center;
        padding: 20px;
        border: 2px dashed #cbd5e0;
        border-radius: 12px;
        transition: 0.2s;
        background-color: #f8fafc;
    }

    .file-drop-area.is-active {
        background-color: #eef2ff;
        border-color: #6366f1;
    }
</style>
@stop

@section('content')
<form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pb-20">

        {{-- KOLOM KIRI: MAIN SETTINGS (8/12) --}}
        <div class="lg:col-span-8 space-y-8">

            {{-- 1. KARTU IDENTITAS SEKOLAH --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative">
                <div class="absolute top-0 left-0 w-2 h-full bg-indigo-500"></div>

                <div class="p-6 sm:p-8">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 mr-4">
                            <i class="fas fa-school text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Identitas Instansi</h3>
                            <p class="text-sm text-gray-500">Informasi dasar profil sekolah/instansi.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        {{-- Nama Sekolah --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Sekolah / Instansi <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="school_name" id="school_name"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white text-gray-800 focus:border-indigo-500 focus:ring-0 form-input-custom"
                                value="{{ old('school_name', $settings['school_name'] ?? '') }}"
                                placeholder="Contoh: SMA Negeri 1 Maju Jaya" required>
                        </div>

                        {{-- Logo Upload --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Logo Instansi</label>
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-20 h-20 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center p-1 overflow-hidden shrink-0">
                                    @php
                                        $logoPath = !empty($settings['school_logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['school_logo'])
                                            ? asset('storage/' . $settings['school_logo'])
                                            : asset('images/default_logo.png');
                                    @endphp
                                    <img src="{{ $logoPath }}" id="logo-preview-img"
                                        class="max-w-full max-h-full object-contain">
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="school_logo_file" id="school_logo_file"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-gray-200 rounded-lg bg-gray-50"
                                        accept="image/*">
                                    <p class="text-xs text-gray-400 mt-2">Format: PNG, JPG (Max 2MB). Disarankan
                                        transparan.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Lengkap</label>
                            <textarea name="school_address" rows="3"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white text-gray-800 focus:border-indigo-500 focus:ring-0 form-input-custom placeholder-gray-300"
                                placeholder="Jalan Raya No. 123...">{{ old('school_address', $settings['school_address'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. KARTU ATURAN ABSENSI (WAKTU & LOKASI) --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative">
                <div class="absolute top-0 left-0 w-2 h-full bg-emerald-500"></div>

                <div class="p-6 sm:p-8">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 mr-4">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Aturan & Validasi Absensi</h3>
                            <p class="text-sm text-gray-500">Pengaturan jam masuk, pulang, dan validasi lokasi.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                        {{-- Jam Masuk --}}
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Jam
                                Masuk</label>
                            <input type="time" name="attendance_start_time"
                                class="w-full text-2xl font-bold text-gray-800 bg-transparent border-none p-0 focus:ring-0"
                                value="{{ substr($settings['attendance_start_time'] ?? '07:00', 0, 5) }}" required>
                            <p class="text-xs text-gray-400 mt-1">Acuan perhitungan terlambat</p>
                        </div>

                        {{-- Jam Pulang --}}
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Jam
                                Pulang</label>
                            <input type="time" name="attendance_end_time"
                                class="w-full text-2xl font-bold text-gray-800 bg-transparent border-none p-0 focus:ring-0"
                                value="{{ substr($settings['attendance_end_time'] ?? '15:00', 0, 5) }}" required>
                            <p class="text-xs text-gray-400 mt-1">Akses scan pulang terbuka</p>
                        </div>

                        {{-- Toleransi --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Toleransi Keterlambatan
                                (Menit)</label>
                            <div class="relative">
                                <input type="number" name="late_tolerance_minutes"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-0 form-input-custom pl-12"
                                    value="{{ $settings['late_tolerance_minutes'] ?? 10 }}" min="0">
                                <div class="absolute left-4 top-3.5 text-gray-400 font-bold">
                                    <i class="fas fa-stopwatch"></i>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2 border-t border-gray-100 my-2"></div>

                        {{-- LOKASI --}}
                        <div class="md:col-span-2">
                            <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-map-marked-alt text-emerald-500 mr-2"></i> Koordinat & Radius Sekolah
                            </h4>

                            {{-- Extractor --}}
                            <div class="bg-blue-50/50 rounded-xl p-4 border border-blue-100 mb-4">
                                <label class="text-xs font-bold text-blue-600 block mb-2">Auto-fill dari Google Maps
                                    Embed Code:</label>
                                <div class="flex gap-2">
                                    <input type="text" id="gmaps_embed_code"
                                        class="flex-1 text-xs border-blue-200 rounded-lg focus:ring-blue-500"
                                        placeholder='Paste <iframe src="..."> here...'>
                                    <button type="button" id="btnExtractCoords"
                                        class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-700 transition">Ambil</button>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-bold text-gray-500">Latitude</label>
                                    <input type="text" name="school_latitude" id="school_latitude"
                                        class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 focus:bg-white transition"
                                        value="{{ $settings['school_latitude'] ?? '' }}">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500">Longitude</label>
                                    <input type="text" name="school_longitude" id="school_longitude"
                                        class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 focus:bg-white transition"
                                        value="{{ $settings['school_longitude'] ?? '' }}">
                                </div>
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Radius Jarak (Meter)</label>
                                <input type="number" name="school_radius_meters"
                                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-0"
                                    value="{{ $settings['school_radius_meters'] ?? 100 }}">
                                <p class="text-xs text-gray-400 mt-1">Siswa diluar radius scan akan ditolak.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. KONTAK & SOSIAL MEDIA --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative collapse-card">
                <div class="p-6 cursor-pointer flex justify-between items-center"
                    onclick="document.getElementById('contact-content').classList.toggle('hidden')">
                    <div class="flex items-center">
                        <i class="fas fa-address-book text-gray-400 mr-3 text-lg"></i>
                        <h3 class="font-bold text-gray-600">Kontak & Media Sosial</h3>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </div>
                <div id="contact-content" class="hidden p-6 border-t border-gray-100 bg-gray-50/30">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Email Resmi</label>
                            <input type="email" name="school_email"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300"
                                value="{{ $settings['school_email'] ?? '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">No. Telepon</label>
                            <input type="text" name="school_phone"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300"
                                value="{{ $settings['school_phone'] ?? '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Link Facebook</label>
                            <input type="url" name="social_facebook"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300"
                                value="{{ $settings['social_facebook'] ?? '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Link Instagram</label>
                            <input type="url" name="social_instagram"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300"
                                value="{{ $settings['social_instagram'] ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. WHATSAPP & INTEGRASI --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative collapse-card">
                <div class="p-6 cursor-pointer flex justify-between items-center"
                    onclick="document.getElementById('wa-content').classList.toggle('hidden')">
                    <div class="flex items-center">
                        <i class="fab fa-whatsapp text-green-500 mr-3 text-lg"></i>
                        <h3 class="font-bold text-gray-600">Integrasi WhatsApp Gateway</h3>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </div>
                <div id="wa-content" class="hidden p-6 border-t border-gray-100 bg-gray-50/30">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">API Endpoint URL</label>
                            <input type="url" name="wa_api_endpoint"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 font-mono text-sm"
                                value="{{ $settings['wa_api_endpoint'] ?? '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">API Token/Key</label>
                            <input type="password" name="wa_api_key"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 font-mono text-sm"
                                value="{{ $settings['wa_api_key'] ?? '' }}">
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Dibutuhkan layanan pihak ketiga (WA Gateway) untuk fitur
                        notifikasi.</p>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: ACTIONS & TOGGLES (4/12) --}}
        <div class="lg:col-span-4 space-y-6">

            {{-- TOMBOL SIMPAN (STICKY-ISH) --}}
            <div class="bg-white rounded-2xl shadow-lg border border-indigo-100 p-6 sticky top-24 z-10">
                <button type="submit" id="save-btn"
                    class="w-full py-4 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold text-lg shadow-xl shadow-indigo-200 hover:shadow-indigo-300 hover:scale-[1.02] transition-all transform flex justify-center items-center">
                    <i class="fas fa-save mr-2"></i> Simpan Pengaturan
                </button>
                <p class="text-center text-xs text-gray-400 mt-3">Terakhir diupdate: {{ now()->format('d M H:i') }}</p>
            </div>

            {{-- KARTU MONITORING SETTINGS --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800">Fitur Keamanan</h3>
                    <p class="text-xs text-gray-500">Aktifkan validasi ketat</p>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Toggle Lokasi --}}
                    <div class="flex items-center justify-between group">
                        <div class="pr-4">
                            <span class="block font-bold text-gray-700 text-sm">Validasi GPS</span>
                            <span class="text-xs text-gray-400">Wajibkan GPS aktif saat scan</span>
                        </div>
                        <div
                            class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                            <input type="hidden" name="enable_location_check" value="false">
                            <input type="checkbox" name="enable_location_check" id="enable_location_check" value="true"
                                class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer transition-all duration-300"
                                {{ ($settings['enable_location_check'] ?? 'false') === 'true' ? 'checked' : '' }}>
                            <label for="enable_location_check"
                                class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer transition-colors duration-300"></label>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- Toggle IP --}}
                    <div class="flex items-center justify-between group">
                        <div class="pr-4">
                            <span class="block font-bold text-gray-700 text-sm">Validasi IP</span>
                            <span class="text-xs text-gray-400">Batasi akses hanya IP tertentu</span>
                        </div>
                        <div
                            class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                            <input type="hidden" name="enable_ip_check" value="false">
                            <input type="checkbox" name="enable_ip_check" id="enable_ip_check" value="true"
                                class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer transition-all duration-300"
                                {{ ($settings['enable_ip_check'] ?? 'false') === 'true' ? 'checked' : '' }}>
                            <label for="enable_ip_check"
                                class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer transition-colors duration-300"></label>
                        </div>
                    </div>

                    {{-- IP Input (Conditional visibility handled via JS usually, but kept simple here) --}}
                    <div class="pt-2">
                        <label class="text-xs font-bold text-gray-500 mb-1 block">Daftar IP Whitelist</label>
                        <input type="text" name="allowed_ip_addresses"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs font-mono bg-gray-50 focus:bg-white"
                            placeholder="192.168.1.1, 10.10.10.10"
                            value="{{ $settings['allowed_ip_addresses'] ?? '' }}">
                    </div>

                </div>
            </div>

            {{-- PREVIEW CARD --}}
            <div
                class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl shadow-xl text-white p-6 relative overflow-hidden">
                <i class="fas fa-file-invoice absolute -right-4 -bottom-4 text-9xl text-white opacity-10"></i>
                <h4 class="font-bold mb-4 z-10 relative">Preview Kop Laporan</h4>
                <div class="bg-white rounded-lg p-4 text-center z-10 relative shadow-lg">
                    <img src="{{ $logoPath }}" id="preview-kop-logo-side"
                        class="h-12 w-auto mx-auto mb-2 object-contain">
                    <h5 class="text-gray-900 font-bold text-sm leading-tight" id="preview-kop-text-side">
                        {{ $settings['school_name'] ?? 'Nama Instansi' }}</h5>
                    <p class="text-[10px] text-gray-500 mt-1 truncate">
                        {{ $settings['school_address'] ?? 'Alamat instansi...' }}</p>
                </div>
            </div>

        </div>
    </div>
</form>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {

        // --- 1. LIVE PREVIEW ---
        const logoInput = $('#school_logo_file');
        const logoPreview = $('#logo-preview-img');
        const kopLogo = $('#preview-kop-logo-side');

        logoInput.change(function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    logoPreview.attr('src', e.target.result);
                    kopLogo.attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        $('#school_name').on('input', function () {
            $('#preview-kop-text-side').text($(this).val() || 'Nama Instansi');
        });

        // --- 2. MAP EXTRACTOR ---
        $('#btnExtractCoords').click(function () {
            const code = $('#gmaps_embed_code').val();
            if (!code) return;

            // Try to match standard google maps embed patterns
            const longMatch = code.match(/!2d([\d.-]+)/);
            const latMatch = code.match(/!3d([\d.-]+)/);

            if (latMatch && longMatch) {
                $('#school_latitude').val(latMatch[1]);
                $('#school_longitude').val(longMatch[1]);
                Swal.fire({ icon: 'success', title: 'Koordinat Ditemukan!', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', text: 'Tidak dapat mengekstrak koordinat. Pastikan kodenya benar.', toast: true });
            }
        });

        // --- 3. FORM SUBMIT ANIMATION ---
        $('#settingsForm').on('submit', function () {
            const btn = $('#save-btn');
            btn.html('<i class="fas fa-circle-notch fa-spin mr-2"></i> Menyimpan...').prop('disabled', true).addClass('opacity-75 cursor-not-allowed');
        });

        // --- 4. ALERTS ---
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil', text: '{{ session('success') }}', timer: 2000, showConfirmButton: false });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Gagal', text: '{{ session('error') }}' });
        @endif
    });
</script>
@stop