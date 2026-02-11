<?php

namespace App\Exports;

use App\Models\TeacherAttendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class TeacherAttendanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
  private $attendances;
  private $rowNumber = 0;

  public function __construct(Collection $attendances)
  {
    $this->attendances = $attendances;
  }

  public function title(): string
  {
    return 'Laporan Absensi Saya';
  }

  public function collection()
  {
    return $this->attendances;
  }

  public function headings(): array
  {
    return [
      'No',
      'Tanggal',
      'Metode',
      'Jam Masuk',
      'Jam Pulang',
      'Status',
      'Lokasi Masuk (Lat, Long)',
    ];
  }

  public function map($attendance): array
  {
    $this->rowNumber++;
    $status = ucfirst($attendance->status ?? 'present');
    $location = ($attendance->latitude && $attendance->longitude)
      ? "{$attendance->latitude}, {$attendance->longitude}"
      : '-';

    $method = ($attendance->photo === 'qr_code') ? 'QR Code' : (($attendance->photo) ? 'Selfie' : 'Manual');

    return [
      $this->rowNumber,
      \Carbon\Carbon::parse($attendance->date)->format('d/m/Y'),
      $method,
      $attendance->clock_in ?? '-',
      $attendance->clock_out ?? '-',
      $status,
      $location,
    ];
  }

  public function styles(Worksheet $sheet)
  {
    return [
      1 => [
        'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
        'fill' => [
          'fillType' => Fill::FILL_SOLID,
          'color' => ['argb' => 'FF4F46E5'], // Indigo
        ]
      ],
    ];
  }
}
