<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClassModel;
use App\Models\Subject;

class Schedule extends Model
{
    protected $fillable = ['class_id', 'subject_id', 'teacher_id', 'day', 'start_time', 'end_time'];

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        // Asumsi guru adalah User dengan role 'guru' (atau user_id di schedule)
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
