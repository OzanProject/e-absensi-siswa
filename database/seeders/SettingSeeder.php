<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'school_latitude',
                'value' => '-7.241477971122267',
                'description' => 'Latitude lokasi sekolah (SMPN 4 KADUPANDAK)'
            ],
            [
                'key' => 'school_longitude',
                'value' => '107.06889987318459',
                'description' => 'Longitude lokasi sekolah (SMPN 4 KADUPANDAK)'
            ],
            [
                'key' => 'school_radius_meters',
                'value' => '500', // 500 meter untuk testing mudah
                'description' => 'Radius toleransi absensi dalam meter'
            ],
            [
                'key' => 'allowed_ip_addresses',
                'value' => '127.0.0.1,::1', // Localhost
                'description' => 'Daftar IP Address yang diizinkan (pisahkan dengan koma). Kosongkan jika ingin menonaktifkan.'
            ],
            [
                'key' => 'enable_location_check',
                'value' => 'true',
                'description' => 'Aktifkan validasi lokasi (true/false)'
            ],
            [
                'key' => 'enable_ip_check',
                'value' => 'false', // Default false dulu agar tidak memblokir user saat dev
                'description' => 'Aktifkan validasi IP Address (true/false)'
            ],
            // Pastikan setting lama juga ada jika belum
            [
                'key' => 'attendance_start_time',
                'value' => '07:00',
                'description' => 'Jam Masuk Sekolah'
            ],
            [
                'key' => 'attendance_end_time',
                'value' => '15:00',
                'description' => 'Jam Pulang Sekolah'
            ],
            [
                'key' => 'late_tolerance_minutes',
                'value' => '15',
                'description' => 'Toleransi keterlambatan (menit)'
            ]
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'description' => $setting['description']
                ]
            );
        }
    }
}
