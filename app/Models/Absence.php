<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 
        'attendance_time',
        'checkout_time', 
        'status', 
        'late_duration', 
        'reason', 
        'recorded_by'
    ];

    protected $casts = [
        'attendance_time' => 'datetime',
        'checkout_time' => 'datetime', // Penting untuk absensi pulang
    ];

    /**
     * Relasi Many-to-One ke Siswa
     */
    public function student()
    {
        // Absence memiliki satu Siswa
        return $this->belongsTo(Student::class, 'student_id');
    }
}