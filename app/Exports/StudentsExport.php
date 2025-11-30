<?php

namespace App\Exports;

use App\Exports\Sheets\InstructionSheet; // 💡 PASTIKAN BARIS INI ADA
use App\Exports\Sheets\StudentDataSheet; // Pastikan ini juga diimpor
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StudentsExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $sheets = [];
        
        // Sheet 1: Template dan Instruksi
        // Pastikan Anda memanggil class yang sudah di-import
        $sheets[] = new InstructionSheet(); 
        
        // Sheet 2: Data Siswa
        $sheets[] = new StudentDataSheet(); 
        
        return $sheets;
    }
}