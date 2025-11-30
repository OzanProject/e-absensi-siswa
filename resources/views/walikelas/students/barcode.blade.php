<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Pelajar - {{ $student->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        /* === GLOBAL SETUP === */
        * { box-sizing: border-box; }
        body { 
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f2f5f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        /* UKURAN KERTAS SESUAI PERMINTAAN AWAL */
        @page { size: A4 portrait; margin: 10mm; }

        /* === CARD CONTAINER === */
        .card-container {
            width: 380px;
            height: 240px; 
            background: linear-gradient(135deg, #ffffff 70%, #e8f0ff 100%);
            border: 2px solid #0d6efd;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.12);
            /* Grid: Header(auto) | Konten (1fr) | Footer (auto) */
            display: grid;
            grid-template-rows: auto 1fr auto; 
            position: relative;
        }

        /* === HEADER === */
        .header {
            background-color: #0d6efd;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 16px;
            border-bottom: 2px solid #084298;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .logo { width: 42px; height: 42px; object-fit: cover; border-radius: 6px; border: 2px solid #fff; }
        .header-text { text-align: right; line-height: 1.2; }
        .header-text h4 { font-size: 14px; margin: 0; font-weight: 700; }
        .header-text p { font-size: 10px; margin: 0; opacity: 0.9; }

        /* === DETAILS WRAPPER (Baris 2 Grid) === */
        .details-wrapper { 
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .details {
            padding: 10px 15px 0px; 
            display: flex;
            justify-content: space-between;
            align-items: flex-start; /* Mengubah center menjadi flex-start agar info tetap di atas */
            flex-shrink: 0; 
        }

        .info { flex: 1; padding-right: 10px; }
        .details-row { 
            display: flex; 
            font-size: 12px; 
            margin-bottom: 4px; 
        }
        .details-row strong { width: 70px; color: #333; font-weight: 600; flex-shrink: 0; }
        .details-data { color: #212529; font-weight: 500; } 

        /* === FOTO SISWA === */
        .photo-container { flex-shrink: 0; text-align: center; }
        .photo {
            width: 75px; 
            height: 95px; 
            border: 1.5px solid #0d6efd;
            border-radius: 6px;
            object-fit: cover;
            background-color: #f1f1f1;
        }

        /* === BARCODE (QR CODE) === */
        .barcode-area {
            flex-grow: 1; 
            text-align: center;
            padding: 5px 15px 0; 
            display: flex;
            flex-direction: column;
            justify-content: center; 
            /* Merubah layout menjadi kolom dan rata tengah untuk QR Code */
        }
        .barcode-area svg { 
            width: 50px; 
            height: 50px; 
            margin-bottom: 2px; 
            border: 1px solid #ddd;
            padding: 3px;
            border-radius: 4px;
            display: block; 
            margin-left: auto;
            margin-right: auto;
        } 
        .barcode-id { 
            font-size: 10px; 
            color: #6c757d; 
            display: block; 
            line-height: 1.2;
            word-break: break-all;
        }

        /* === FOOTER & BUTTON === */
        .footer-wrapper {
            background-color: #0d6efd;
            color: white;
            padding: 5px 10px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.3px;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            z-index: 10;
        }
        .footer-print-button {
            background-color: #198754; 
            color: white; 
            border: none;
            padding: 8px 15px; 
            border-radius: 6px; 
            font-size: 12px;
            cursor: pointer; 
            font-weight: 600; 
            transition: background 0.2s;
            display: block; 
            margin: 0 auto; 
        }
        .footer-print-button:hover { background-color: #157347; }


        @media print {
            body { background: white; margin: 0; padding: 0; display: block; }
            .card-container { box-shadow: none; border: 1px solid #000; margin: auto; }
            .footer-print-button { display: none; } 
            /* Menghilangkan tombol cetak saat proses print */
        }
    </style>
</head>
<body onload="window.print()">
    @php
        // LOGIKA PATH FOTO SISWA
        $photoPath = ($student->photo && $student->photo != 'default_avatar.png' && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->photo)) 
                        ? asset('storage/' . $student->photo) 
                        : asset('images/default/student.png'); // Pastikan path default ini ada

        // LOGIKA PATH LOGO SEKOLAH
        $schoolLogoPath = $settings['school_logo'] ?? 'default/logo.png';
        $logoUrl = (\Illuminate\Support\Facades\Storage::disk('public')->exists($schoolLogoPath)) 
                    ? asset('storage/' . $schoolLogoPath) 
                    : asset('images/default/logo.png'); // Pastikan path default ini ada

        // Format Tanggal Lahir (Menggunakan data Carbon karena di-cast di Model)
        $birthDetail = ($student->birth_place ? $student->birth_place . ', ' : '') 
                       . ($student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d M Y') : 'N/A');
        
        $statusText = $student->status == 'active' ? 'AKTIF' : 'NON-AKTIF';
    @endphp

    <div class="card-container">
        <div class="header">
            <img src="{{ $logoUrl }}" alt="Logo Sekolah" class="logo">
            <div class="header-text">
                <h4>KARTU PELAJAR</h4>
                <p>{{ $settings['school_name'] ?? 'NAMA SEKOLAH' }}</p>
            </div>
        </div>

        {{-- WRAPPER BARU: Mengandung Details dan QR Code --}}
        <div class="details-wrapper">
            <div class="details">
                <div class="info">
                    <div class="details-row">
                        <strong>Nama:</strong>
                        <span class="details-data">{{ $student->name }}</span>
                    </div>
                    <div class="details-row">
                        <strong>NISN:</strong>
                        <span class="details-data">{{ $student->nisn }}</span>
                    </div>
                    <div class="details-row">
                        <strong>Kelas:</strong>
                        <span class="details-data">{{ $student->class->name ?? 'N/A' }}</span>
                    </div>
                    {{-- FIELD LAHIR & STATUS --}}
                    <div class="details-row">
                        <strong>Lahir:</strong>
                        <span class="details-data">{{ $birthDetail }}</span>
                    </div>
                    <div class="details-row">
                        <strong>Status:</strong>
                        <span class="details-data">{{ $statusText }}</span>
                    </div>
                </div>

                <div class="photo-container">
                    <img src="{{ $photoPath }}" alt="Foto Siswa" class="photo">
                </div>
            </div>

            <div class="barcode-area">
                {{-- Variabel $qrcode_svg diisi dari controller --}}
                {!! $qrcode_svg !!} 
                <div class="barcode-id">{{ $student->barcode_data }}</div>
            </div>
        </div>
        {{-- END DETAILS-WRAPPER --}}

        {{-- FOOTER WRAPPER --}}
        <div class="footer-wrapper">
            <span class="footer-text">Gunakan kartu ini untuk absensi digital. Harap jaga baik-baik.</span>
            <button class="footer-print-button" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak Kartu
            </button>
        </div>
    </div>
</body>
</html>