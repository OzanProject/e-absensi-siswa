<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izin_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('request_date'); // Tanggal izin berlaku
            $table->enum('type', ['Sakit', 'Izin']); // Jenis izin
            $table->text('reason'); // Keterangan dari orang tua
            $table->string('attachment_path')->nullable(); // Path foto surat/bukti
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('approved_by')->nullable()->references('id')->on('users'); // Wali Kelas/Admin yang menyetujui
            $table->timestamps();
            
            $table->unique(['student_id', 'request_date'], 'student_date_unique'); // Hanya boleh 1 izin per siswa per hari
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izin_requests');
    }
};