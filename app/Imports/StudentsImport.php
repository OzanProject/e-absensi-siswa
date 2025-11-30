<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithUpserts; // 💡 TAMBAHAN KUNCI: Untuk Update/Insert
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date; 
use Illuminate\Database\Eloquent\Collection;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsEmptyRows, WithUpserts
{
    private $classIds;
    private $rows = 0;

    public function __construct()
    {
        // Cache semua Nama Kelas dan ID-nya untuk Lookup Cepat
        $this->classIds = ClassModel::pluck('id', 'name');
    }

    /**
     * Mengembalikan kunci unik untuk Upsert (memastikan NISN di-update, bukan di-insert baru).
     */
    public function uniqueBy()
    {
        return 'nisn';
    }

    /**
     * Mengembalikan jumlah baris yang berhasil diolah.
     */
    public function getRowCount(): int
    {
        return $this->rows;
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // 1. Ambil dan Trim data wajib
        $nisn = trim($row['nisn']);
        $className = trim($row['nama_kelas']);

        // 2. Cek Kelas (Kritis)
        if (!isset($this->classIds[$className])) {
            // Karena validasi sudah menangkap, kita return null
            return null; 
        }

        $class_id = $this->classIds[$className];
        
        // 3. Konversi Tanggal Lahir
        $birthDate = null;
        if (isset($row['birth_date']) && is_numeric($row['birth_date'])) {
            $birthDate = Date::excelToDateTimeObject($row['birth_date']); 
        }

        // 4. Buat Model Siswa baru (atau update jika NISN sudah ada)
        $this->rows++;
        return new Student([
            // Kunci untuk Upsert (Harus ada di fillable)
            'nisn'          => $nisn,
            'barcode_data'  => Str::uuid()->toString(), // Generate baru hanya jika insert baru
            
            // Data yang akan di-insert/update
            'nis'           => trim($row['nis'] ?? null),
            'name'          => trim($row['nama_siswa']),
            'email'         => trim($row['email'] ?? null),
            'gender'        => trim($row['jenis_kelamin']),
            'class_id'      => $class_id,
            'phone_number'  => trim($row['nomor_telepon'] ?? null),
            'address'       => trim($row['alamat'] ?? null),
            'birth_place'   => trim($row['tempat_lahir'] ?? null),
            'birth_date'    => $birthDate,
            'status'        => 'active',
            'photo'         => 'default_avatar.png',
        ]);
    }

    /**
     * Definisikan aturan validasi untuk setiap kolom.
     */
    public function rules(): array
    {
        $classNamesArray = $this->classIds->keys()->toArray();

        // 💡 PERBAIKAN: Hapus rule 'unique' pada NISN/Email karena sudah ditangani oleh Upsert, 
        // tetapi tetap perlu 'required' dan 'numeric'.
        return [
            'nisn'          => 'required|numeric', 
            'nis'           => 'nullable|string|max:20', // Mengubah ke string untuk menangani 0 di depan
            'nama_siswa'    => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'email'         => 'nullable|email', 
            'nomor_telepon' => 'nullable|string|max:20', // Dibiarkan string untuk penanganan format
            'alamat'        => 'nullable|string|max:500', 
            'tempat_lahir'  => 'nullable|string|max:100', 
            'birth_date'    => 'nullable|numeric', // Tanggal Excel harus berupa angka
            'nama_kelas' => [
                 'required', 
                 Rule::in($classNamesArray), 
            ],
        ];
    }
    
    // --- PENGATURAN PERFORMA ---
    
    public function batchSize(): int
    {
        return 500; 
    }

    public function chunkSize(): int
    {
        return 500; 
    }
}