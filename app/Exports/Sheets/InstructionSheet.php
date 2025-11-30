<?php

namespace App\Exports\Sheets; // 🛑 INI ADALAH BARIS KRITIS

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class InstructionSheet implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    /**
     * Mengatur nama sheet/lembar kerja di Excel.
     */
    public function title(): string
    {
        return 'TEMPLATE_IMPORT';
    }

    /**
     * Definisi Header/Judul Kolom Excel (Hanya Kunci Bersih).
     */
    public function headings(): array
    {
        return [
            'NISN',
            'NIS',
            'NAMA_SISWA',
            'JENIS_KELAMIN', 
            'NAMA_KELAS', 
            'EMAIL',
            'PHONE_NUMBER',
            'TEMPAT_LAHIR',
            'BIRTH_DATE', 
            'ADDRESS',
        ];
    }

    /**
     * Baris contoh untuk Template.
     */
    public function array(): array
    {
        // Baris pertama adalah keterangan format untuk pengguna
        $infoRow = [
            'Wajib diisi',
            'Opsional',
            'Wajib diisi',
            'Laki-laki / Perempuan',
            'Harus Sesuai di DB',
            'email@contoh.com',
            '628xxxx',
            'Kota',
            'YYYY-MM-DD',
            'Alamat Lengkap',
        ];

        // Baris kedua adalah contoh data
        $exampleRow = [
            '1234567890', 
            '12345',      
            'Siti Nurhaliza', 
            'Perempuan',  
            '7A', 
            'siti@example.com', 
            '628123456789', 
            'Jakarta',    
            '2005-08-17', 
            'Jl. Mawar No. 5, Jakarta Pusat',
        ];

        return [
            $infoRow,
            $exampleRow
        ];
    }
}