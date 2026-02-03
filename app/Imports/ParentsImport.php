<?php

namespace App\Imports;

use App\Models\User;
use App\Models\ParentModel;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; 
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ParentsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // 1. Validasi Manual per baris (agar bisa skip yang error tanpa stop semua)
            // Atau biarkan throw error jika ingin strict. Kita coba approach: Validasi Basic dulu.
            
            $nama = trim($row['nama']);
            $email = trim($row['email']);
            // Nomor telepon = Password Default
            $phone = trim($row['nomor_telepon']);
            // Sanitasi nomor telepon (hapus spasi, -, +62 jadi 0)
            $phone = preg_replace('/[^0-9]/', '', $phone);
            if(substr($phone, 0, 2) == '62') {
                $phone = '0' . substr($phone, 2);
            }

            $hubungan = trim($row['hubungan'] ?? 'Wali');
            $nisnSiswa = trim($row['nisn_siswa']);

            // Skip jika data mandatory kosong
            if (empty($nama) || empty($email) || empty($phone) || empty($nisnSiswa)) {
                continue; 
            }

            // Cek duplikasi Email (User) dan Phone (Parent)
            if (User::where('email', $email)->exists() || ParentModel::where('phone_number', $phone)->exists()) {
                // Skip duplikat untuk menghindari error SQL
                // (Idealnya kita log row ini, tapi untuk MVP kita skip)
                continue; 
            }

            // Cari Siswa berdasarkan NISN
            $student = Student::where('nisn', $nisnSiswa)->first();
            if (!$student) {
                // Siswa tidak ditemukan, skip row ini (karena ortu harus punya anak di sistem)
                continue;
            }

            // 2. Buat User Account
            $user = User::create([
                'name' => $nama,
                'email' => $email,
                'password' => Hash::make($phone), // Password = No Telp
                'role' => 'orang_tua',
                'is_approved' => true,
            ]);

            // 3. Buat Parent Record
            $parent = ParentModel::create([
                'user_id' => $user->id,
                'name' => $nama,
                'phone_number' => $phone,
                'relation_status' => $hubungan,
            ]);

            // 4. Hubungkan dengan Siswa (Attach)
            // Cek dulu apakah sudah terhubung (untuk keamanan, meski baru create)
            if (!$parent->students()->where('student_id', $student->id)->exists()) {
                $parent->students()->attach($student->id);
            }
        }
    }

    /**
     * Rules validasi dasar (optional jika pakai ToCollection manual)
     * Tapi berguna untuk memastikan structure Excel benar.
     */
    public function rules(): array
    {
        return [
            'nama' => 'required',
            'email' => 'required|email',
            'nomor_telepon' => 'required',
            'nisn_siswa' => 'required',
        ];
    }
}
