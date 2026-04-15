<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix: ENUM status semula 'Alfa' diganti menjadi 'Alpha' agar konsisten
     * dengan validasi controller yang menggunakan 'Alpha'.
     */
    public function up(): void
    {
        // Step 1: Update semua data lama 'Alfa' menjadi 'Alpha' terlebih dahulu
        DB::table('absences')->where('status', 'Alfa')->update(['status' => 'Alpha']);

        // Step 2: Ubah definisi ENUM kolom status
        DB::statement("ALTER TABLE `absences` MODIFY COLUMN `status` ENUM('Hadir','Terlambat','Sakit','Izin','Alpha') NOT NULL DEFAULT 'Hadir'");
    }

    public function down(): void
    {
        // Rollback: kembalikan data 'Alpha' ke 'Alfa'
        DB::table('absences')->where('status', 'Alpha')->update(['status' => 'Alfa']);

        // Kembalikan ENUM ke definisi lama
        DB::statement("ALTER TABLE `absences` MODIFY COLUMN `status` ENUM('Hadir','Terlambat','Sakit','Izin','Alfa') NOT NULL DEFAULT 'Hadir'");
    }
};
