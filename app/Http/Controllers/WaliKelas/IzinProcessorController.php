<?php

namespace App\Http\Controllers\WaliKelas;

use Carbon\Carbon;
use App\Models\Absence;
use App\Models\IzinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IzinProcessorController extends Controller
{
    // Helper untuk mendapatkan class ID Wali Kelas
    protected function getClassId()
    {
        return Auth::user()->homeroomTeacher->class_id ?? null;
    }

    /**
     * Tampilkan daftar permintaan izin yang Pending untuk kelas yang diampu.
     */
    public function index()
    {
        $classId = $this->getClassId();

        if (!$classId) {
            return redirect()->route('walikelas.dashboard')
                             ->with('error', 'Anda belum mengampu kelas.');
        }

        // Ambil semua permintaan Izin/Sakit yang statusnya Pending atau Approved
        $izinRequests = IzinRequest::with(['student.class'])
            ->whereHas('student', function ($query) use ($classId) {
                // Batasi hanya siswa di kelas Wali Kelas ini
                $query->where('class_id', $classId);
            })
            // Urutkan yang Pending di atas, lalu berdasarkan tanggal permintaan terbaru
            ->orderByRaw("FIELD(status, 'Pending', 'Approved', 'Rejected')")
            ->orderBy('request_date', 'desc')
            ->paginate(15);

        return view('walikelas.izin.index', compact('izinRequests'));
    }

    /**
     * Proses persetujuan permintaan izin/sakit.
     */
    public function approve(IzinRequest $izinRequest)
    {
        $classId = $this->getClassId();

        // 🛑 Otorisasi Kritis: Pastikan Wali Kelas berhak memproses request ini
        if ($izinRequest->student->class_id !== $classId) {
            abort(403, 'Akses Ditolak. Permintaan bukan dari kelas yang Anda ampu.');
        }

        if ($izinRequest->status !== 'Pending') {
            return redirect()->back()->with('error', 'Permintaan ini sudah diproses.');
        }
        
        DB::beginTransaction();
        try {
            // 1. Catat status Approved di tabel izin_requests
            $izinRequest->update([
                'status' => 'Approved',
                'approved_by' => Auth::id(),
            ]);

            // 2. Buat record Absensi di tabel absences
            // Catatan: Jika ada scan masuk hari itu, record baru ini akan gagal (UNIQUE constraint)
            Absence::firstOrCreate(
                [
                    'student_id' => $izinRequest->student_id,
                    'attendance_time' => $izinRequest->request_date, // Gunakan tanggal yang diminta sebagai waktu masuk
                ],
                [
                    'status' => $izinRequest->type, // 'Sakit' atau 'Izin'
                    'notes' => "Pengajuan dari Orang Tua: " . $izinRequest->reason,
                    'recorded_by' => 'Wali Kelas: ' . Auth::user()->name,
                    'is_manual_corrected' => true, // Dianggap sebagai input manual oleh WK
                    'correction_note' => 'Disetujui berdasarkan pengajuan online',
                ]
            );

            DB::commit();
            return redirect()->back()->with('success', "Permintaan Izin/Sakit untuk {$izinRequest->student->name} berhasil disetujui dan dicatat di absensi harian.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal approve izin request ID {$izinRequest->id}: " . $e->getMessage());
            return redirect()->back()->with('error', "Gagal memproses persetujuan. Kemungkinan siswa sudah memiliki record absensi (Hadir/Terlambat) hari itu.");
        }
    }

    /**
     * Proses penolakan permintaan izin/sakit.
     */
    public function reject(IzinRequest $izinRequest)
    {
        $classId = $this->getClassId();

        if ($izinRequest->student->class_id !== $classId) {
            abort(403, 'Akses Ditolak.');
        }

        if ($izinRequest->status !== 'Pending') {
            return redirect()->back()->with('error', 'Permintaan ini sudah diproses.');
        }

        $izinRequest->update([
            'status' => 'Rejected',
            'approved_by' => Auth::id(),
            'correction_note' => 'Ditolak oleh Wali Kelas.',
        ]);

        return redirect()->back()->with('success', "Permintaan Izin/Sakit untuk {$izinRequest->student->name} berhasil ditolak.");
    }
}