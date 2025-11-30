<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Siswa
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            
            // Waktu Absensi
            $table->dateTime('attendance_time'); 
            
            // Status Absensi (Hadir, Terlambat, Izin, Sakit, Alfa)
            $table->enum('status', ['Hadir', 'Terlambat', 'Sakit', 'Izin', 'Alfa'])->default('Hadir');
            
            // Kolom Tambahan
            $table->time('late_duration')->nullable(); // Durasi keterlambatan jika Terlambat
            $table->string('reason')->nullable(); // Keterangan jika Izin/Sakit/Alfa
            $table->string('recorded_by')->nullable(); // Dicatat oleh (Wali Kelas/Sistem/Admin)

            // Kombinasi unik: Siswa hanya bisa absen sekali sehari (tanpa status keluar)
            $table->unique(['student_id', 'attendance_time']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};