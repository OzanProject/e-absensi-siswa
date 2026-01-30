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
use Illuminate\Database\Eloquent\Collection; // 💡 Diperlukan untuk type hint Collection

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
            'Tanggal',
            'Waktu Absen',
            'NISN',
            'Nama Siswa',
            'Kelas',
            'Status',
            'Keterlambatan (Menit)',
            'Latitude',
            'Longitude',
            'IP Address',
        ];
    }

    /**
     * Mapping data ke kolom header.
     */
    public function map($absence): array
    {
        $this->rowNumber++;
        $status = $absence->status ?? 'N/A';

        return [
            $this->rowNumber,
            $absence->attendance_time->format('d/m/Y'),
            $absence->attendance_time->format('H:i:s'),
            $absence->student->nisn ?? 'N/A',
            $absence->student->name ?? 'Siswa Dihapus',
            $absence->student->class->name ?? 'N/A',
            $status,
            ($status == 'Terlambat') ? $absence->late_duration . ' min' : '-',
            $absence->latitude ?? '-',
            $absence->longitude ?? '-',
            $absence->ip_address ?? '-',
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