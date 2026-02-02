<?php

namespace App\Exports;

use App\Models\Absence;
use Carbon\Carbon; // Tetap dipertahankan untuk type hinting
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill; // Import Fill Class
use Illuminate\Support\Collection; // 💡 Diperlukan untuk type hint Collection

class AbsenceReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    private $absences; // Koleksi data yang sudah difilter
    private $rowNumber = 0;

    /**
     * 💡 PERBAIKAN KRITIS: Hanya menerima koleksi data yang sudah di-query dari Controller
     */
    public function __construct(Collection $absences)
    {
        $this->absences = $absences;
    }

    /**
     * Mengatur nama sheet/lembar kerja di Excel.
     */
    public function title(): string
    {
        return 'Laporan Absensi';
    }

    /**
     * Mengembalikan koleksi absensi yang sudah di-query dari Controller.
     */
    public function collection()
    {
        // 💡 KUNCI: Langsung mengembalikan koleksi yang sudah disortir dan difilter
        return $this->absences;
    }

    /**
     * Definisi Header/Judul Kolom Excel.
     */
    public function headings(): array
    {
        return [
            'No',
            'Jenis',
            'Tanggal',
            'Waktu',
            'NISN',
            'Nama Siswa',
            'Kelas',
            'Status',
            'Detail',
            'Lokasi/Mapel',
        ];
    }

    /**
     * Mapping data ke kolom header.
     */
    public function map($absence): array
    {
        $this->rowNumber++;
        $status = $absence->status ?? 'N/A';
        $type = $absence->type ?? '-';
        
        $detail = '-';
        $location = '-';

        if ($type == 'Harian') {
            $detail = ($status == 'Terlambat') ? ($absence->raw_data->late_duration ?? 0) . ' min' : '-';
            $location = ($absence->raw_data->latitude ?? '-') . ', ' . ($absence->raw_data->longitude ?? '-');
        } elseif ($type == 'Mapel') {
             $detail = $absence->raw_data->journal->schedule->subject->name ?? '-';
             $location = $absence->raw_data->journal->teacher->name ?? '-';
        }

        return [
            $this->rowNumber,
            $type,
            $absence->date->format('d/m/Y'),
            $absence->date->format('H:i:s'),
            $absence->student->nisn ?? 'N/A',
            $absence->student->name ?? 'Siswa Dihapus',
            $absence->class_name ?? 'N/A',
            $status,
            $detail,
            $location,
        ];
    }

    /**
     * Tambahkan style pada header (baris 1).
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style baris pertama (Header)
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID, // Menggunakan Fill Class yang di-import
                    'color' => ['argb' => 'FF198754'], // Warna Hijau Sukses
                ]
            ],
        ];
    }
}