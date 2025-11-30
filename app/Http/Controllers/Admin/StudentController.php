<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Milon\Barcode\DNS1D; // Pastikan ini ada
use Illuminate\Validation\ValidationException;
use Exception;

class StudentController extends Controller
{
    /**
     * Tampilkan daftar semua siswa. (READ)
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $classId = $request->get('class_id'); // 💡 Ambil filter class_id

        // Ambil semua kelas untuk filter dropdown, diurutkan berdasarkan grade
        $classes = ClassModel::orderBy('grade', 'asc')->orderBy('name', 'asc')->get();

        $query = Student::with('class')
                        // 💡 PENGURUTAN KUNCI: Gunakan JOIN untuk mengakses kolom kelas
                        ->join('classes', 'students.class_id', '=', 'classes.id'); 

        // 1. Logic Pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                 // Cari di tabel students
                 $q->where('students.name', 'like', "%{$search}%")
                   ->orWhere('nisn', 'like', "%{$search}%")
                   ->orWhere('nis', 'like', "%{$search}%");
            });
        }
        
        // 2. Logic Filter Kelas
        if ($classId) {
            $query->where('classes.id', $classId);
        }

        // 3. Logic Pengurutan Final (Kelas 7 dulu, kemudian 8, lalu abjad di dalam kelas)
        $students = $query->orderBy('classes.grade', 'asc') // Urutkan Tingkat (7, 8, 9)
                          ->orderBy('classes.name', 'asc')  // Urutkan Nama Kelas (7A, 7B)
                          ->orderBy('students.name', 'asc') // Urutkan Nama Siswa
                          ->select('students.*') // Penting: Pilih kembali semua kolom students
                          ->paginate(15)
                          ->withQueryString(); // Memastikan filter tetap ada saat paginasi
        
        return view('admin.students.index', compact('students', 'classes'));
    }

    /**
     * Tampilkan form untuk membuat siswa baru. (CREATE - Form)
     */
    public function create()
    {
        $classes = ClassModel::where('status', 'active')
            ->orderBy('grade')
            ->orderBy('name')
            ->get();
            
        return view('admin.students.create', compact('classes'));
    }

    /**
     * Simpan data siswa baru ke database, termasuk upload foto. (CREATE - Store)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nisn' => 'required|string|unique:students,nisn|max:20',
            'nis' => 'nullable|string|unique:students,nis|max:20',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:students,email|max:255',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'class_id' => 'required|exists:classes,id',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'photo' => 'nullable|image|max:1024|mimes:jpg,jpeg,png', 
        ]);

        $data = $request->all();
        
        // 2. Penanganan Upload Foto
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos/students', 'public');
            $data['photo'] = $path;
        } else {
            $data['photo'] = 'default_avatar.png'; 
        }
        
        // **Hapus: Logika generate barcode_data manual dihapus.**
        
        // 3. Set status default (jika tidak di-set di Model)
        $data['status'] = 'active';

        // 4. Simpan ke Database (UUID barcode_data otomatis terisi dari Student Model boot event)
        Student::create($data);

        return redirect()->route('students.index')
                             ->with('success', 'Data siswa berhasil ditambahkan! Foto dan Kartu pelajar siap dicetak.');
    }

    /**
     * Tampilkan form untuk mengedit siswa. (UPDATE - Form)
     */
    public function edit(Student $student)
    {
        $classes = ClassModel::where('status', 'active')
            ->orderBy('grade')
            ->orderBy('name')
            ->get();
            
        return view('admin.students.edit', compact('student', 'classes'));
    }

    /**
     * Perbarui data siswa di database. (UPDATE - Store)
     */
    public function update(Request $request, Student $student)
    {
        // Validasi, pastikan NISN/NIS/Email mengecualikan ID siswa saat ini
        $request->validate([
            'nisn' => 'required|string|unique:students,nisn,' . $student->id . '|max:20',
            'nis' => 'nullable|string|unique:students,nis,' . $student->id . '|max:20',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:students,email,' . $student->id . '|max:255',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'class_id' => 'required|exists:classes,id',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
            'photo' => 'nullable|image|max:1024|mimes:jpg,jpeg,png', // Opsi update foto
        ]);

        $data = $request->all();

        // 🚨 Penanganan Update Foto
        if ($request->hasFile('photo')) {
        // 1. Hapus foto lama jika ada dan bukan foto default
        if ($student->photo && $student->photo != 'default_avatar.png') {
            Storage::disk('public')->delete($student->photo);
        }
        // 2. Simpan foto baru
        $path = $request->file('photo')->store('photos/students', 'public');
        $data['photo'] = $path;
    } 
    // Jika tidak ada file baru diupload, $data['photo'] tidak ada, dan field 'photo' lama tetap dipertahankan saat update.

    $student->update($data);

    return redirect()->route('students.index')
                     ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Hapus data siswa dari database. (DELETE)
     */
    public function destroy(Student $student)
    {
        $studentName = $student->name;
        
        // 🚨 Hapus file foto dari storage sebelum menghapus record
        if ($student->photo && $student->photo != 'default_avatar.png') {
            Storage::disk('public')->delete($student->photo);
        }
        
        $student->delete();
        
        return redirect()->route('students.index')
            ->with('success', "Data siswa {$studentName} berhasil dihapus.");
    }

    /**
     * Tampilkan kartu pelajar dan generate barcode. (FITUR BARCODE TUNGGAL)
     */
    public function generateBarcode(Student $student)
    {
        $student->loadMissing('class'); 
        
        if (!$student->barcode_data) {
            $student->update(['barcode_data' => Str::uuid()->toString()]);
            $student->refresh(); 
        }

        $barcode_string = $student->barcode_data; 
        
        // 🚨 Panggil non-statis method melalui instance objek untuk PNG
        $qrcode_svg = QrCode::size(200)
                        ->margin(2)
                        ->format('svg')
                        ->generate($barcode_string);
    
        // Muat settings untuk logo/nama sekolah di kartu
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        
        // Kirim QR code dalam bentuk SVG string
        return view('admin.students.barcode', compact('student', 'qrcode_svg', 'settings'));
    }

    /**
     * Tampilkan form untuk mengimport data siswa.
     */
    public function importForm()
    {
        $classes = ClassModel::where('status', 'active')
            ->orderBy('grade')
            ->orderBy('name')
            ->get();
            
        return view('admin.students.import_form', compact('classes'));
    }

    /**
     * Proses import data siswa dari file Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048', 
        ]);

        try {
            $import = new StudentsImport;
            Excel::import($import, $request->file('file'));

            $importedCount = $import->getRowCount(); 

            if ($importedCount > 0) {
                $message = "✅ Import data berhasil! Total {$importedCount} siswa ditambahkan/diperbarui.";
                // 💡 KUNCI: Kirim sesi success dan flag close_loading
                return redirect()->route('students.index')->with('success', $message)->with('close_loading', true); 
            } else {
                $message = "⚠️ Import berhasil, tetapi tidak ada data siswa valid yang ditemukan di file Anda.";
                return redirect()->route('students.importForm')->with('error', $message)->with('close_loading', true); 
            }

        } catch (ValidationException $e) {
            // Menangkap kesalahan validasi dari Maatwebsite/Excel
            return redirect()->back()
                            ->withErrors($e->errors())
                            ->with('error', 'Gagal mengimport data karena kesalahan validasi. Mohon periksa log validasi.')
                            ->with('close_loading', true); // Kirim flag saat validasi gagal
                            
        } catch (Exception $e) { 
            return redirect()->back()
                            ->with('error', 'Terjadi kesalahan sistem saat mengimport data: ' . $e->getMessage())
                            ->with('close_loading', true); // Kirim flag saat error sistem
        }
    }

    /**
     * Proses export data siswa ke file Excel.
     */
    public function export()
    {
        // Menggunakan class StudentsExport yang kini mengimplementasikan WithMultipleSheets
        return Excel::download(new StudentsExport, 'data_siswa_template_'. date('Ymd_His') .'.xlsx');
    }

    /**
     * Generate barcode untuk semua siswa (bulk)
     */
    public function generateBulkBarcode()
    {
        $students = Student::with('class')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc') // Sesuai permintaan: Terbaru di atas
            ->get();
        
        $barcodeData = [];
        
        // Muat settings untuk logo/nama sekolah di kartu
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        foreach ($students as $student) {
            if (!$student->barcode_data) {
                $student->update(['barcode_data' => Str::uuid()->toString()]);
                $student = $student->fresh();
            }

            // 🚨 PERUBAHAN KRUSIAL: Generate QR Code dalam format SVG
            $qrcode_svg = QrCode::size(100) // Ukuran kecil untuk bulk
                                ->margin(2)
                                ->format('svg')
                                ->generate($student->barcode_data);
            
            $barcodeData[] = [
                'student' => $student,
                'qrcode_svg' => $qrcode_svg // Mengirim QR SVG string
            ];
        }
        
        return view('admin.students.barcode_bulk', compact('barcodeData', 'settings'));
    }

    /**
     * Non-aktifkan siswa (soft delete alternative)
     */
    public function deactivate(Student $student)
    {
        $student->update(['status' => 'inactive']);
        
        return redirect()->route('students.index')
                         ->with('success', "Siswa {$student->name} berhasil dinon-aktifkan.");
    }

    /**
     * Tampilkan detail siswa tertentu. (READ - Detail)
     * Ini dipanggil oleh Route: GET /admin/students/{student}
     */
    public function show(Student $student)
    {
        // Untuk saat ini, kita bisa langsung redirect ke halaman edit
        // Atau buat halaman detail terpisah (admin.students.show)
        
        return redirect()->route('students.edit', $student);
        
        // JIKA ANDA INGIN HALAMAN DETAIL:
        // $student->load('class', 'absences'); 
        // return view('admin.students.show', compact('student'));
    }

    /**
     * Aktifkan siswa
     */
    public function activate(Student $student)
    {
        $student->update(['status' => 'active']);
        
        return redirect()->route('students.index')
                         ->with('success', "Siswa {$student->name} berhasil diaktifkan.");
    }

    /**
     * Proses penghapusan massal (Bulk Delete) siswa.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'selected_students' => 'required|array',
            'selected_students.*' => 'exists:students,id',
        ], [
            'selected_students.required' => 'Pilih minimal satu siswa untuk dihapus.'
        ]);

        $students = Student::whereIn('id', $request->selected_students)->get();
        $count = $students->count();

        // 🚨 Hapus semua file foto sebelum menghapus record
        foreach ($students as $student) {
            if ($student->photo && $student->photo != 'default_avatar.png') {
                Storage::disk('public')->delete($student->photo);
            }
        }

        Student::whereIn('id', $request->selected_students)->delete();

        return redirect()->route('students.index')
                         ->with('success', "{$count} data siswa berhasil dihapus secara massal.");
    }
}