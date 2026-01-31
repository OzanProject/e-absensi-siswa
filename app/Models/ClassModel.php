<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    // 🔥 PERBAIKAN SEBELUMNYA: Ini sudah benar, menentukan nama tabel eksplisit.
    protected $table = 'classes';

    // ✅ PERBAIKAN SEKARANG: Tambahkan major dan description ke fillable
    protected $fillable = [
        'name',
        'grade',
        'major',      // <-- DITAMBAHKAN
        'description' // <-- DITAMBAHKAN
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function homeroomTeacher()
    {
        // Relasi Kebalikan: Satu Kelas memiliki satu Wali Kelas
        // Asumsi model HomeroomTeacher ada
        return $this->hasOne(HomeroomTeacher::class, 'class_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }
}