<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeroomTeacher extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 'class_id'
    ];
    
    // Relasi ke User (Wali Kelas)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    // Relasi ke Kelas yang diampu
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}