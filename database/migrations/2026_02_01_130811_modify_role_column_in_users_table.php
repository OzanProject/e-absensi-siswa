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
        // Menggunakan Raw SQL untuk mengubah tipe kolom ENUM karena Doctrine DBAL memiliki keterbatasan dengan ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'guru', 'wali_kelas', 'orang_tua', 'kepala_sekolah', 'siswa') NOT NULL DEFAULT 'orang_tua'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'wali_kelas', 'orang_tua') NOT NULL DEFAULT 'orang_tua'");
    }
};
