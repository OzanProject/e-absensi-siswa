<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TeacherAttendance;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TeacherScanController extends Controller
{
  public function index()
  {
    // Ambil data absensi hari ini untuk ditampilkan di tabel live
    $todaysAttendances = TeacherAttendance::with('user')
      ->whereDate('date', Carbon::today())
      ->orderBy('updated_at', 'desc')
      ->take(10)
      ->get();

    return view('admin.absensi.scan_teacher', compact('todaysAttendances'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'nip' => 'required|string',
    ]);

    $nip = $request->nip;

    // 1. Cari Guru berdasarkan NIP atau ID
    // Coba cari exact match NIP dulu
    $teacher = User::where('nip', $nip)->where('role', 'guru')->first();

    // Jika tidak ketemu, coba cari by ID (jika QR isinya ID)
    if (!$teacher) {
      $teacher = User::where('id', $nip)->where('role', 'guru')->first();
    }

    if (!$teacher) {
      return response()->json([
        'success' => false,
        'message' => 'Data Guru tidak ditemukan!'
      ], 404);
    }

    // 2. Ambil Setting Waktu
    $settings = Setting::whereIn('key', [
      'attendance_teacher_enter_start',
      'attendance_teacher_late_limit',
      'attendance_teacher_exit_start'
    ])->pluck('value', 'key');

    $enterStart = $settings['attendance_teacher_enter_start'] ?? '06:00';
    $lateLimit = $settings['attendance_teacher_late_limit'] ?? '07:15';
    $exitStart = $settings['attendance_teacher_exit_start'] ?? '14:00';

    $now = Carbon::now();
    $date = $now->toDateString();
    $time = $now->toTimeString();

    // 3. Cek Record Absensi Hari Ini
    $attendance = TeacherAttendance::firstOrNew([
      'user_id' => $teacher->id,
      'date' => $date
    ]);

    $statusMessage = '';
    $type = '';

    if (!$attendance->exists) {
      // --- LOGIC CLOCK IN ---

      // Cek Jam Mulai
      if ($now->lt(Carbon::createFromTimeString($enterStart))) {
        return response()->json([
          'success' => false,
          'message' => "Belum waktunya absen masuk. (Mulai: $enterStart)"
        ], 400);
      }

      $attendance->clock_in = $time;
      $attendance->photo = 'admin_scan'; // Marker bahwa ini discan oleh admin

      // Cek Keterlambatan
      $limitTime = Carbon::createFromTimeString($lateLimit);
      $attendance->status = $now->gt($limitTime) ? 'late' : 'present';

      $attendance->save();

      $statusText = $attendance->status == 'late' ? 'Terlambat' : 'Tepat Waktu';
      $statusMessage = "Berhasil Absen MASUK ($statusText)";
      $type = 'IN';

    } elseif (is_null($attendance->clock_out)) {
      // --- LOGIC CLOCK OUT ---

      // Cek Jam Pulang
      if ($now->lt(Carbon::createFromTimeString($exitStart))) {
        return response()->json([
          'success' => false,
          'message' => "Belum waktunya absen pulang. (Mulai: $exitStart)"
        ], 400);
      }

      $attendance->clock_out = $time;
      $attendance->save();

      $statusMessage = "Berhasil Absen PULANG";
      $type = 'OUT';

    } else {
      return response()->json([
        'success' => false,
        'message' => 'Guru ini sudah melengkapi absensi hari ini.'
      ], 400);
    }

    return response()->json([
      'success' => true,
      'message' => $statusMessage,
      'teacher' => $teacher->name,
      'time' => $time,
      'type' => $type
    ]);
  }
}
