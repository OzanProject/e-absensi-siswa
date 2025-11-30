<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IzinRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 
        'request_date', 
        'type', 
        'reason', 
        'attachment_path', 
        'status', 
        'approved_by'
    ];

    protected $casts = [
        'request_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}