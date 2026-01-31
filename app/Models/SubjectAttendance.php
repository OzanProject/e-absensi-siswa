<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectAttendance extends Model
{
    protected $fillable = ['teaching_journal_id', 'student_id', 'status', 'notes'];

    public function journal()
    {
        return $this->belongsTo(TeachingJournal::class, 'teaching_journal_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
