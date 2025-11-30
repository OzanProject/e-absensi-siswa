<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homeroom_teachers', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke User (Wali Kelas)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Relasi ke Kelas
            $table->foreignId('class_id')->constrained('classes')->onDelete('restrict'); 
            
            // Pastikan satu kelas hanya memiliki satu wali kelas, dan satu user hanya bisa jadi wali kelas satu kelas.
            $table->unique(['user_id', 'class_id']); 
            $table->unique('class_id'); // Satu kelas hanya boleh punya satu wali kelas

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homeroom_teachers');
    }
};