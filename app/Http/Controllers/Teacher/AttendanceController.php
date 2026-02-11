<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeacherAttendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use Illuminate\Support\Facades\Crypt;

class AttendanceController extends Controller
{
  /**
   * Tampilkan Halaman Scanner QR
   */
  public function scan()
  {
    $settings = \App\Models\Setting::pluck('value', 'key');
    $schoolLat = $settings['school_latitude'] ?? 0;
    $schoolLng = $settings['school_longitude'] ?? 0;
    $schoolRadius = $settings['school_radius'] ?? 100;

    return view('teacher.attendance.scan', compact('schoolLat', 'schoolLng', 'schoolRadius'));
  }

  /**
   * Proses Absensi via QR Code
   */
  public function storeQr(Request $request)
  {
    $request->validate([
      'qr_token' => 'required',
      'latitude' => 'required',
      'longitude' => 'required',
    ]);

    try {
      // 1. Decrypt Token
      $decrypted = Crypt::decrypt($request->qr_token);
      $data = json_decode($decrypted, true);

      // 2. Anti-Replay Mechanism (Check Cache)
      // The Admin Controller stores 'qr_token_{nonce}' for 30 seconds.
      // If it's not in cache, it means it expired or is fake.
      if (!\Illuminate\Support\Facades\Cache::has('qr_token_' . $data['nonce'])) {
        return response()->json(['success' => false, 'message' => 'QR Code Kadaluarsa atau Tidak Valid.'], 400);
      }

    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => 'QR Code Rusak/Tidak Valid!'], 400);
    }

    // 3. Validate Geolocation (Server Side)
    $schoolLat = \App\Models\Setting::where('key', 'school_latitude')->value('value');
    $schoolLng = \App\Models\Setting::where('key', 'school_longitude')->value('value');
    $schoolRadius = \App\Models\Setting::where('key', 'school_radius')->value('value') ?? 100;

    if ($schoolLat && $schoolLng) {
      $distance = $this->calculateDistance($request->latitude, $request->longitude, $schoolLat, $schoolLng);
      if ($distance > $schoolRadius) {
        return response()->json([
          'success' => false,
          'message' => "Posisi Anda di luar jangkauan ($distance m). Radius: $schoolRadius m."
        ], 400);
      }
    }

    // 4. Validate IP Address (Optional)
    $enableIpCheck = \App\Models\Setting::where('key', 'enable_ip_check')->value('value') === 'true';
    if ($enableIpCheck) {
      $allowedIps = array_map('trim', explode(',', \App\Models\Setting::where('key', 'allowed_ip_addresses')->value('value')));
      $userIp = $request->ip();

      if (!in_array($userIp, $allowedIps)) {
        return response()->json([
          'success' => false,
          'message' => "IP Address Anda ($userIp) tidak diizinkan. Gunakan Wi-Fi Sekolah."
        ], 400);
      }
    }

    // 5. Process Attendance Record
    $user = Auth::user();
    $date = Carbon::now()->toDateString();
    $time = Carbon::now()->toTimeString();

    // --- DYNAMIC TIME SETTINGS ---
    $enterStart = \App\Models\Setting::where('key', 'attendance_teacher_enter_start')->value('value') ?? '06:00';
    $lateLimit = \App\Models\Setting::where('key', 'attendance_teacher_late_limit')->value('value') ?? '07:15';
    $exitStart = \App\Models\Setting::where('key', 'attendance_teacher_exit_start')->value('value') ?? '14:00';

    // Check if too early to clock in
    if (Carbon::now()->lt(Carbon::createFromTimeString($enterStart))) {
      return response()->json(['success' => false, 'message' => "Belum waktunya absen masuk. (Mulai jam $enterStart)"], 400);
    }

    $attendance = TeacherAttendance::firstOrNew([
      'user_id' => $user->id,
      'date' => $date
    ]);

    $statusMessage = '';

    if (!$attendance->exists) {
      // --- CLOCK IN ---
      $attendance->clock_in = $time;
      $attendance->latitude = $request->latitude;
      $attendance->longitude = $request->longitude;

      // Check Late Status
      $limitTime = Carbon::createFromTimeString($lateLimit);
      $attendance->status = Carbon::now()->gt($limitTime) ? 'late' : 'present';

      // Mark as QR attendance
      $attendance->photo = 'qr_code';
      $attendance->save();

      $statusMessage = 'Berhasil Absen Masuk (QR) pada ' . $time;
    } elseif (is_null($attendance->clock_out)) {
      // --- CLOCK OUT ---
      // Check if too early to clock out
      if (Carbon::now()->lt(Carbon::createFromTimeString($exitStart))) {
        return response()->json(['success' => false, 'message' => "Belum waktunya absen pulang. (Mulai jam $exitStart)"], 400);
      }

      $attendance->clock_out = $time;
      // Update location out if needed, or just keep in
      $attendance->save();

      $statusMessage = 'Berhasil Absen Pulang (QR) pada ' . $time;
    } else {
      return response()->json(['success' => false, 'message' => 'Anda sudah melengkapi absensi hari ini.'], 400);
    }

    return response()->json(['success' => true, 'message' => $statusMessage]);
  }
  // Halaman Riwayat Absensi
  public function index()
  {
    $attendances = TeacherAttendance::where('user_id', Auth::id())
      ->orderBy('date', 'desc')
      ->paginate(15);

    return view('teacher.attendance.index', compact('attendances'));
  }

  // Proses Clock In / Clock Out dari Dashboard
  public function store(Request $request)
  {
    $user = Auth::user();
    $date = Carbon::now()->toDateString();
    $time = Carbon::now()->toTimeString();

    // --- DYNAMIC TIME SETTINGS ---
    $enterStart = \App\Models\Setting::where('key', 'attendance_teacher_enter_start')->value('value') ?? '06:00';
    $lateLimit = \App\Models\Setting::where('key', 'attendance_teacher_late_limit')->value('value') ?? '07:15';
    $exitStart = \App\Models\Setting::where('key', 'attendance_teacher_exit_start')->value('value') ?? '14:00';

    // Cek apakah sudah absen hari ini
    $attendance = TeacherAttendance::firstOrNew([
      'user_id' => $user->id,
      'date' => $date
    ]);

    // Logic Location (Opsional, simpan jika ada)
    if ($request->filled('latitude') && $request->filled('longitude')) {
      $attendance->latitude = $request->latitude;
      $attendance->longitude = $request->longitude;
    }

    // Logic Server-Side Geofencing Check
    // Ambil data sekolah dari Setting
    $schoolLat = \App\Models\Setting::where('key', 'school_latitude')->value('value');
    $schoolLng = \App\Models\Setting::where('key', 'school_longitude')->value('value');
    $schoolRadius = \App\Models\Setting::where('key', 'school_radius')->value('value') ?? 100;

    // Hitung Jarak Jika Koordinat Sekolah Tersedia
    if ($request->filled('latitude') && $request->filled('longitude') && $schoolLat && $schoolLng) {
      $distance = $this->calculateDistance($request->latitude, $request->longitude, $schoolLat, $schoolLng);

      // Jika di luar radius, tolak absensi
      if ($distance > $schoolRadius) {
        return redirect()->back()->with('error', 'Gagal Absen! Posisi Anda berada ' . round($distance) . ' meter dari sekolah. (Radius Sekolah: ' . $schoolRadius . 'm)');
      }
    }

    // Logic Presensi
    if (!$attendance->exists) {
      // Check Enter Start Time
      if (Carbon::now()->lt(Carbon::createFromTimeString($enterStart))) {
        return redirect()->back()->with('error', "Gagal! Absen Masuk baru dibuka pukul $enterStart.");
      }

      // --- CLOCK IN ---
      $attendance->clock_in = $time;

      // Simpan Foto Selfie (Base64)
      if ($request->filled('photo')) {
        $image = $request->input('photo'); // data:image/jpeg;base64,...
        if (strpos($image, 'data:image') === 0) {
          $image = str_replace('data:image/jpeg;base64,', '', $image);
          $image = str_replace(' ', '+', $image);
          $imageName = 'attendance/in_' . $user->id . '_' . time() . '.jpg';

          Storage::disk('public')->put($imageName, base64_decode($image));
          $attendance->photo = $imageName;
        }
      }

      // Tentukan Status (Telat jika lewat jam lateLimit)
      $limitTime = Carbon::createFromTimeString($lateLimit);
      if (Carbon::now()->gt($limitTime)) {
        $attendance->status = 'late';
      } else {
        $attendance->status = 'present';
      }

      $attendance->save();
      return redirect()->back()->with('success', 'Berhasil Absen Masuk pada ' . $time);

    } elseif (is_null($attendance->clock_out)) {
      // Check Exit Start Time
      if (Carbon::now()->lt(Carbon::createFromTimeString($exitStart))) {
        return redirect()->back()->with('error', "Gagal! Absen Pulang baru dibuka pukul $exitStart.");
      }

      // --- CLOCK OUT ---

      // Simpan Foto Pulang (Opsional - overwrite field photo atau buat kolom baru photo_out jika perlu)
      if ($request->filled('photo')) {
        $image = $request->input('photo');
        if (strpos($image, 'data:image') === 0) {
          $image = str_replace('data:image/jpeg;base64,', '', $image);
          $image = str_replace(' ', '+', $image);
          $imageName = 'attendance/out_' . $user->id . '_' . time() . '.jpg';

          Storage::disk('public')->put($imageName, base64_decode($image));
          // Jika ingin menyimpan foto pulang juga, pastikan ada kolomnya. 
          // Saat ini kita overwrite photo lama (masuk) atau biarkan. 
          // Skenario umum: Foto Masuk yang utama.
          // $attendance->photo_out = $imageName; // Uncomment jika ada kolom baru
        }
      }

      $attendance->clock_out = $time;
      $attendance->save();
      return redirect()->back()->with('success', 'Berhasil Absen Pulang pada ' . $time);
    }

    return redirect()->back()->with('info', 'Anda sudah melengkapi absensi hari ini.');
  }

  /**
   * Hitung jarak antara dua koordinat (Haversine Formula)
   * Return dalam METER
   */
  private function calculateDistance($lat1, $lon1, $lat2, $lon2)
  {
    $earthRadius = 6371000; //meter

    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    $latDelta = $lat2 - $lat1;
    $lonDelta = $lon2 - $lon1;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
      cos($lat1) * cos($lat2) * pow(sin($lonDelta / 2), 2)));

    // Return distance in meters
    return $angle * $earthRadius;
  }
}
