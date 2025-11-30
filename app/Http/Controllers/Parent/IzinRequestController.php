<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParentModel;
use App\Models\IzinRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class IzinRequestController extends Controller
{
    // Helper untuk otorisasi dan mendapatkan data orang tua
    protected function getParentData()
    {
        $user = Auth::user();
        $parentRecord = ParentModel::where('user_id', $user->id)->first();
        
        if (!$parentRecord) {
             abort(403, 'Akses Ditolak. Akun Anda belum terhubung ke data siswa.');
        }
        
        return $parentRecord;
    }

    /**
     * Tampilkan riwayat permintaan izin dan form pengajuan.
     */
    public function index()
    {
        $parentRecord = $this->getParentData();
        $studentIds = $parentRecord->students->pluck('id');
        
        $requests = IzinRequest::with('student.class')
            ->whereIn('student_id', $studentIds)
            ->orderBy('request_date', 'desc')
            ->paginate(10);
            
        return view('orangtua.izin.index', compact('parentRecord', 'requests'));
    }

    /**
     * Proses pengajuan form izin/sakit.
     */
    public function store(Request $request)
    {
        $parentRecord = $this->getParentData();
        $studentIds = $parentRecord->students->pluck('id')->toArray();
        
        $request->validate([
            'student_id' => 'required|in:' . implode(',', $studentIds), // Pastikan anak yang dipilih adalah anak sendiri
            'request_date' => 'required|date|after_or_equal:today', // Tidak boleh tanggal lampau
            'type' => 'required|in:Sakit,Izin',
            'reason' => 'required|string|max:500',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Max 2MB
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments/izin', 'public');
        }
        
        // Cek duplikasi: Apakah sudah ada permintaan yang Pending/Approved untuk tanggal tersebut?
        $existingRequest = IzinRequest::where('student_id', $request->student_id)
                                      ->where('request_date', $request->request_date)
                                      ->whereIn('status', ['Pending', 'Approved'])
                                      ->exists();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'Permintaan izin/sakit untuk tanggal ini sudah ada atau sedang diproses.');
        }

        IzinRequest::create([
            'student_id' => $request->student_id,
            'request_date' => $request->request_date,
            'type' => $request->type,
            'reason' => $request->reason,
            'attachment_path' => $attachmentPath,
            'status' => 'Pending',
        ]);

        return redirect()->route('orangtua.izin.index')->with('success', 'Permintaan Izin/Sakit berhasil diajukan. Menunggu persetujuan Wali Kelas.');
    }
}