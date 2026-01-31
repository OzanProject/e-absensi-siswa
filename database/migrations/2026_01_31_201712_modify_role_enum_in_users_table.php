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
        // Menggunakan raw statement agar lebih aman untuk Enum di MySQL
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'wali_kelas', 'orang_tua', 'guru') NOT NULL DEFAULT 'orang_tua'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum semula (PERINGATAN: ini bisa error jika ada data 'guru' di database)
        // Kita tidak akan menghapus data, tapi jika di-rollback, status 'guru' mungkin jadi string kosong atau truncation error.
        // Sebaiknya hati-hati saat rollback di production.

        // Cek dulu apakah ada data 'guru' sebelum rollback (opsional, tapi native SQL rollback biasanya main hajar)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'wali_kelas', 'orang_tua') NOT NULL DEFAULT 'orang_tua'");
    }
};
