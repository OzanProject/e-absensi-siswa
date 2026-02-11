<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    $settings = [
      [
        'key' => 'school_latitude',
        'value' => '-6.175392', // Default: Monas Jakarta
        'description' => 'Latitude Lokasi Sekolah',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'key' => 'school_longitude',
        'value' => '106.827153', // Default: Monas Jakarta
        'description' => 'Longitude Lokasi Sekolah',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'key' => 'school_radius',
        'value' => '100', // 100 meter
        'description' => 'Radius Toleransi Absensi (Meter)',
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ];


    foreach ($settings as $setting) {
      DB::table('settings')->updateOrInsert(
        ['key' => $setting['key']],
        [
          'value' => $setting['value'],
          'description' => $setting['description'],
          'created_at' => $setting['created_at'],
          'updated_at' => $setting['updated_at'],
        ]
      );
    }
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    DB::table('settings')->whereIn('key', ['school_latitude', 'school_longitude', 'school_radius'])->delete();
  }
};
