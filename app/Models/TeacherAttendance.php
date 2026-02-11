<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TeacherAttendance extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'date',
    'clock_in',
    'clock_out',
    'status',
    'latitude',
    'longitude',
    'photo',
  ];

  protected $casts = [
    'date' => 'date',
    // 'clock_in' & 'clock_out' dibiarkan string/time agar formatnya HH:mm:ss sesuai database
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  // Helper untuk status badge warna
  public function getStatusColorAttribute()
  {
    return match ($this->status) {
      'present' => 'success',
      'late' => 'warning',
      'permission' => 'info',
      'sick' => 'secondary',
      'alpha' => 'danger',
      default => 'secondary',
    };
  }

  public function getStatusLabelAttribute()
  {
    return match ($this->status) {
      'present' => 'Hadir',
      'late' => 'Terlambat',
      'permission' => 'Izin',
      'sick' => 'Sakit',
      'alpha' => 'Alpha',
      default => '-',
    };
  }
}
