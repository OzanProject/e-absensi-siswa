<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi</title>
    {{-- Tambahkan font Poppins jika DomPDF mendukungnya, jika tidak, gunakan Sans-serif --}}
    <style>
        body { font-family: sans-serif; font-size: 10px; margin: 0; padding: 0; }
        .container { padding: 20px; }
        
        /* === HEADER DAN BRANDING === */
        .header { 
            text-align: center; 
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        .header-content {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        .header img {
            width: 40px; /* Ukuran Logo */
            height: 40px;
            object-fit: cover;
            margin-right: 15px;
        }
        .header h2 { margin: 0; font-size: 16px; color: #333; }
        .header h4 { margin: 0; font-size: 11px; color: #555; }

        /* === INFO LAPORAN === */
        .info { margin-bottom: 15px; }
        .info p { 
            margin: 2px 0; 
            font-size: 10px; 
            text-align: center;
        }

        /* === TABEL === */
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { 
            border: 1px solid #ddd; 
            padding: 5px 8px; 
            text-align: left; 
        }
        .table th { 
            background-color: #0d6efd; 
            color: white; /* Header putih */
            font-size: 10px; 
            text-transform: uppercase;
        }
        .table td { 
            font-size: 9px; 
        }

        /* === STATUS COLORS === */
        .status-hadir { color: #198754; font-weight: bold; } /* Hijau */
        .status-terlambat { color: #ffc107; font-weight: bold; } /* Kuning */
        .status-absen, .status-izin, .status-sakit { color: #dc3545; font-weight: bold; } /* Merah */
    </style>
</head>
<body>
    <div class="container">
        
        @php
            // Asumsi $settings tersedia (dikiriman dari Controller)
            $settings = $settings ?? ['school_name' => 'E-ABSENSI SEKOLAH', 'school_logo' => 'default/logo.png']; 
            $logoPath = public_path('storage/' . ($settings['school_logo'] ?? 'default/logo.png'));
        @endphp
        
        <div class="header">
            <div class="header-content">
                {{-- 🚨 Tampilkan Logo (Menggunakan public_path untuk DomPDF) --}}
                @if(file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo Sekolah">
                @endif
                <div>
                    <h2>LAPORAN ABSENSI SISWA</h2>
                    <h4>{{ $settings['school_name'] ?? 'NAMA SEKOLAH BELUM DISET' }}</h4>
                </div>
            </div>
            
            <div class="info">
                <p>Periode: {{ $startDate->format('d F Y') }} s/d {{ $endDate->format('d F Y') }}</p>
                <p>Kelas: @if($class) {{ $class->name }} @else Semua Kelas @endif</p>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>NISN</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Status</th>
                    <th>Terlambat (Menit)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absences as $absence)
                @php
                    $status = $absence->status ?? 'N/A';
                    // Menghapus spasi dan mengubah ke lowercase untuk kelas CSS
                    $statusClass = 'status-' . strtolower(str_replace([' ', '-'], '', $status)); 
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $absence->attendance_time->format('d/m/Y') }}</td>
                    <td>{{ $absence->attendance_time->format('H:i:s') }}</td>
                    <td>{{ $absence->student->nisn ?? 'N/A' }}</td>
                    <td>{{ $absence->student->name ?? 'N/A' }}</td>
                    <td>{{ $absence->student->class->name ?? 'N/A' }}</td>
                    <td class="{{ $statusClass }}">{{ $status }}</td>
                    <td>{{ ($status == 'Terlambat' && $absence->late_duration) ? $absence->late_duration . ' min' : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data absensi dalam periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
    </div>
</body>
</html>