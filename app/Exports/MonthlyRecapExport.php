<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Carbon;

class MonthlyRecapExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $recapData;
    protected $daysInMonth;
    protected $monthName;

    public function __construct(array $recapData, int $daysInMonth, string $monthName)
    {
        $this->recapData = $recapData;
        $this->daysInMonth = $daysInMonth;
        $this->monthName = $monthName;
    }

    /**
    * @return array
    */
    public function array(): array
    {
        $exportArray = [];

        // Loop data rekap dari Controller
        foreach ($this->recapData as $data) {
            $row = [
                'Nama Siswa' => $data['name'],
            ];
            
            // Tambahkan status per tanggal
            for ($day = 1; $day <= $this->daysInMonth; $day++) {
                // Ambil status, default jika tidak ada
                $status = $data['status_by_day'][$day] ?? 'Alpha'; 

                // Konversi status internal (misal: 'Pulang') ke format laporan (misal: 'Hadir')
                if ($status == 'Pulang') {
                    $reportStatus = 'Hadir';
                } elseif ($status == 'Terlambat') {
                    $reportStatus = 'Terlambat';
                } elseif ($status == 'N/A') {
                    $reportStatus = '-'; // Hari Masa Depan
                } else {
                    $reportStatus = $status;
                }

                $row[$day] = $reportStatus;
            }
            $exportArray[] = $row;
        }

        return $exportArray;
    }

    /**
     * Tentukan baris judul (Header)
     * @return array
     */
    public function headings(): array
    {
        $headings = ['Nama Siswa'];
        
        // Tambahkan header tanggal (1, 2, 3, ...)
        for ($i = 1; $i <= $this->daysInMonth; $i++) {
            $headings[] = $i;
        }

        return $headings;
    }
}