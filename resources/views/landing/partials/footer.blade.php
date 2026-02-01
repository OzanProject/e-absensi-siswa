@php
    use Illuminate\Support\Facades\Storage;
    $settings = $settings ?? \App\Models\Setting::pluck('value', 'key')->toArray();
    $schoolName = $settings['school_name'] ?? 'E-Absensi Siswa';
    $schoolLogoPath = $settings['school_logo'] ?? null;
    $defaultLogo = asset('images/default_logo.png');
    $finalLogo = ($schoolLogoPath && Storage::disk('public')->exists($schoolLogoPath)) ? asset('storage/' . $schoolLogoPath) : $defaultLogo;
@endphp

<footer style="background-color: #0f172a; color: #f8fafc; position: relative; width: 100%; clear: both;">
    <div
        style="height: 1px; width: 100%; background: linear-gradient(to right, transparent, #6366f1, transparent); opacity: 0.3;">
    </div>

    <div class="container mx-auto px-6 py-16">
        <div class="flex flex-wrap justify-between gap-10">

            <div style="flex: 1 1 350px; min-width: 300px;">
                <div class="flex items-center gap-4 mb-6">
                    <div style="background: white; padding: 8px; border-radius: 12px; display: inline-block;">
                        <img src="{{ $finalLogo }}" style="height: 40px; width: 40px; object-fit: contain;" alt="Logo">
                    </div>
                    <div>
                        <h2 style="font-weight: 800; font-size: 1.5rem; margin: 0; color: white;">{{ $schoolName }}</h2>
                        <span
                            style="color: #818cf8; font-size: 0.75rem; font-weight: bold; letter-spacing: 0.1em; text-transform: uppercase;">Digital
                            Platform</span>
                    </div>
                </div>
                <p style="color: #94a3b8; line-height: 1.6; max-width: 320px; font-size: 0.95rem;">
                    {{ $settings['site_description'] ?? 'Platform manajemen absensi sekolah modern yang terintegrasi, transparan, dan realtime.' }}
                </p>
                <div class="flex gap-3 mt-6">
                    @foreach(['facebook-f', 'instagram', 'twitter', 'youtube'] as $icon)
                        <a href="#"
                            style="width: 36px; height: 36px; display: flex; align-items: center; justify-center; border-radius: 8px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #94a3b8; text-decoration: none;">
                            <i class="fab fa-{{ $icon }}" style="margin: auto;"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            <div style="flex: 1 1 150px;">
                <h4
                    style="color: white; font-weight: 700; font-size: 0.9rem; margin-bottom: 24px; text-transform: uppercase; letter-spacing: 0.05em;">
                    Navigasi</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 12px;"><a href="#"
                            style="color: #94a3b8; text-decoration: none; font-size: 0.9rem;">Beranda</a></li>
                    <li style="margin-bottom: 12px;"><a href="#features"
                            style="color: #94a3b8; text-decoration: none; font-size: 0.9rem;">Fitur Unggulan</a></li>
                    <li style="margin-bottom: 12px;"><a href="#how"
                            style="color: #94a3b8; text-decoration: none; font-size: 0.9rem;">Cara Kerja</a></li>
                    <li style="margin-bottom: 12px;"><a href="{{ route('login') }}"
                            style="color: #94a3b8; text-decoration: none; font-size: 0.9rem;">Portal Login</a></li>
                </ul>
            </div>

            <div style="flex: 1 1 250px;">
                <h4
                    style="color: white; font-weight: 700; font-size: 0.9rem; margin-bottom: 24px; text-transform: uppercase; letter-spacing: 0.05em;">
                    Hubungi Kami</h4>
                <div style="display: flex; gap: 12px; margin-bottom: 20px;">
                    <i class="fas fa-map-marker-alt" style="color: #6366f1; margin-top: 4px;"></i>
                    <span
                        style="color: #94a3b8; font-size: 0.9rem; line-height: 1.5;">{{ $settings['school_address'] ?? 'Alamat sekolah belum diatur.' }}</span>
                </div>
                <div style="display: flex; gap: 12px;">
                    <i class="fas fa-phone-alt" style="color: #10b981;"></i>
                    <span style="color: #94a3b8; font-size: 0.9rem;">{{ $settings['school_phone'] ?? '-' }}</span>
                </div>
            </div>

        </div>

        <div
            style="margin-top: 60px; padding-top: 30px; border-top: 1px solid rgba(255,255,255,0.05); display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 20px;">
            <p style="color: #64748b; font-size: 0.85rem; margin: 0;">
                &copy; {{ date('Y') }} <span style="color: white; font-weight: 600;">{{ $schoolName }}</span>. All
                rights reserved.
            </p>
            <div
                style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 6px 16px; border-radius: 99px;">
                <span style="color: #6366f1; font-family: monospace; font-weight: bold; font-size: 0.75rem;">VERSION
                    {{ $settings['app_version'] ?? 'v1.2.0' }}</span>
            </div>
        </div>
    </div>
</footer>