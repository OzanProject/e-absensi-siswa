<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeachingJournal extends Model
{
    protected $fillable = ['schedule_id', 'teacher_id', 'date', 'start_time', 'end_time', 'topic', 'notes'];

    protected $casts = ['date' => 'date'];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function attendances()
    {
        return $this->hasMany(SubjectAttendance::class, 'teaching_journal_id');
    }
}
