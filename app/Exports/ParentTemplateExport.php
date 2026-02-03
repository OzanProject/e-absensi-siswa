<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ParentTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Contoh Data Dummy
        return new Collection([
            [
                'nama' => 'Budi Santoso',
                'email' => 'budi.santoso@contoh.com',
                'nomor_telepon' => '081234567890',
                'hubungan' => 'Ayah',
                'nisn_siswa' => '0012345678'
            ],
            [
                'nama' => 'Siti Aminah',
                'email' => 'siti.aminah@contoh.com',
                'nomor_telepon' => '089876543210',
                'hubungan' => 'Ibu',
                'nisn_siswa' => '0087654321'
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'nama',
            'email',
            'nomor_telepon',
            'hubungan',
            'nisn_siswa',
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1    => ['font' => ['bold' => true]],
        ];
    }
}
