<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat akun contoh Guru 1
        User::updateOrCreate(
            ['email' => 'guru1@sekolah.sch.id'], // Cek berdasarkan email
            [
                'name' => 'Budi Santoso, S.Pd',
                'password' => Hash::make('password123'), // Password default
                'role' => 'guru',
                'is_approved' => true,
                'email_verified_at' => now(),
            ]
        );

        // Buat akun contoh Guru 2
        User::updateOrCreate(
            ['email' => 'guru2@sekolah.sch.id'],
            [
                'name' => 'Siti Aminah, M.Pd',
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'is_approved' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Data guru berhasil ditambahkan!');
    }
}
