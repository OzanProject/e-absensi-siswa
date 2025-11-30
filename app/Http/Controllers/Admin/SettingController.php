<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr; // Helper array Laravel

class SettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan umum dan muat data yang ada.
     */
    public function index()
    {
        // Muat semua pengaturan ke dalam array key => value
        $settings = Setting::pluck('value', 'key')->toArray();

        // Definisikan key labels untuk view
        $keys = [
            'school_name' => 'Nama Sekolah',
            'attendance_start_time' => 'Jam Mulai Absensi (HH:MM)',
            // 💡 BARU: Jam Pulang
            'attendance_end_time' => 'Jam Mulai Pulang (HH:MM)', 
            'late_tolerance_minutes' => 'Toleransi Keterlambatan (menit)',
            'wa_api_endpoint' => 'Endpoint API WhatsApp',
            'wa_api_key' => 'Kunci API WhatsApp',
            'school_logo' => 'Logo Sekolah (URL/Path)', 
        ];

        return view('admin.settings.index', compact('settings', 'keys'));
    }

    /**
     * Perbarui pengaturan umum sistem dan tangani upload logo.
     */
    public function update(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'school_name' => 'required|string|max:255',
            // 💡 SOLUSI FIX: Mengubah format validasi dari H:i:s menjadi H:i
            'attendance_start_time' => 'required|date_format:H:i',
            'attendance_end_time' => 'required|date_format:H:i', 
            'late_tolerance_minutes' => 'required|integer|min:0',
            'school_logo_file' => 'nullable|mimes:jpeg,png,jpg|max:2048', 
            'wa_api_endpoint' => 'nullable|url|max:255',
            'wa_api_key' => 'nullable|string|max:255',
        ]);
        
        $logoPath = null;
        
        // Ambil hanya data setting yang valid, tanpa token, method, dan input file.
        $dataToSave = $request->except(['_token', '_method', 'school_logo_file']);

        DB::beginTransaction();
        try {
            // 2. Proses Upload Logo Sekolah
            if ($request->hasFile('school_logo_file')) {
                // A. Ambil pengaturan logo lama
                $oldLogoSetting = Setting::where('key', 'school_logo')->first();
                $oldLogoPath = $oldLogoSetting->value ?? null;
                
                // B. Hapus logo lama dari storage jika ada
                if ($oldLogoPath && Storage::disk('public')->exists($oldLogoPath)) {
                    Storage::disk('public')->delete($oldLogoPath);
                }
                
                // C. Simpan file baru
                $logoPath = $request->file('school_logo_file')->store('logo', 'public');
            }

            // 3. Simpan/Perbarui Pengaturan Lain
            
            // Masukkan path logo baru ke array data yang akan disimpan
            if ($logoPath) {
                 $dataToSave['school_logo'] = $logoPath;
            }

            // Loop dan simpan ke database menggunakan updateOrCreate
            foreach ($dataToSave as $key => $value) {
                // Pastikan nilai null dikonversi ke string kosong
                $value = $value ?? ''; 
                
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
            
            DB::commit();
            return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil disimpan dan logo diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Jika terjadi error setelah upload (rollback), hapus file yang baru diupload
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                 Storage::disk('public')->delete($logoPath);
            }

            return redirect()->back()->with('error', 'Gagal menyimpan pengaturan. Error: ' . $e->getMessage())->withInput();
        }
    }
}