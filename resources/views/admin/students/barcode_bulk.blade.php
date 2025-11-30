<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Pelajar Massal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <style>
        /* === GLOBAL SETUP === */
        * { box-sizing: border-box; }
        body {
            font-family: 'Poppins', Arial, sans-serif; margin: 0; padding: 0;
            background-color: #f1f3f6; color: #212529; min-height: 100vh;
            display: flex; flex-direction: column; align-items: center;
        }

        /* 🚨 ATUR HALAMAN CETAK (LANDSCAPE) */
        @page { size: A4 landscape; margin: 8mm; } 
        
        /* --- PRINT GRID AREA --- */
        .print-area {
            display: flex; flex-wrap: wrap; justify-content: center; 
            gap: 10px; 
            padding: 10px; width: 100%; max-width: 1100px;
        }
        .card-container {
            width: 380px; 
            height: 240px; 
            background: linear-gradient(135deg, #ffffff 70%, #e8f0ff 100%);
            border: 2px solid #0d6efd; border-radius: 12px; overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15); 
            display: grid;
            grid-template-rows: auto 1fr auto; 
            page-break-inside: avoid; 
        }

        /* === HEADER === */
        .header { background-color: #0d6efd; color: white; display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; border-bottom: 2px solid #084298; border-top-left-radius: 10px; border-top-right-radius: 10px; }
        .logo { width: 42px; height: 42px; object-fit: cover; border-radius: 6px; border: 2px solid #fff; }
        .header-text { text-align: right; line-height: 1.2; }
        .header-text h4 { font-size: 14px; margin: 0; font-weight: 700; }
        .header-text p { font-size: 10px; margin: 0; opacity: 0.9; }

        /* === CONTENT WRAPPER (1fr area) === */
        .details-wrapper { 
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        /* === DETAILS & FOTO === */
        .details { 
            padding: 10px 15px 0px; 
            display: flex; justify-content: space-between; 
            align-items: center; flex-shrink: 0; 
        }
        .info { flex: 1; padding-right: 10px; } 
        .details-row { display: flex; font-size: 12px; margin-bottom: 4px; } 
        .details-row strong { width: 70px; color: #333; font-weight: 600; flex-shrink: 0; }
        .details-data { color: #212529; font-weight: 500; }

        /* === FOTO SISWA === */
        .photo-container { flex-shrink: 0; text-align: center; }
        .photo { width: 75px; height: 95px; border: 1.5px solid #0d6efd; border-radius: 6px; object-fit: cover; background-color: #f1f1f1; }

        /* === BARCODE (QR CODE) === */
        .barcode-area { 
            flex-grow: 1; 
            text-align: center; 
            padding: 5px 15px 5px; 
            display: flex;
            flex-direction: column;
            justify-content: center; 
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
        .barcode-id { font-size: 10px; color: #6c757d; display: block; line-height: 1.2; word-break: break-all; }

        /* === FOOTER === */
        .footer { 
            background-color: #0d6efd; color: white; text-align: center; 
            padding: 5px 0; font-size: 10px; font-weight: 600; letter-spacing: 0.3px; 
            border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;
        }

        /* === NO PRINT BUTTON BAR === */
        .no-print { 
            position: sticky; top: 0; z-index: 100; text-align: center; 
            padding: 15px; border-bottom: 2px dashed #ccc; background-color: #fff; 
            width: 100%; box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }
        .no-print button { background-color: #28a745; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: 600; margin: 0 5px; }
        
        @media print {
            body { background: white; padding: 0; margin: 0; display: block; }
            .print-area { display: flex; flex-wrap: wrap; justify-content: flex-start; gap: 10px; padding: 0; margin: 0; width: auto; }
            .card-container { 
                border: 1px solid #000; 
                box-shadow: none !important; 
                margin: 5px; 
            }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨 Cetak Sekarang</button>
        <button onclick="window.close()" class="btn-close">Tutup Jendela</button>
        <p>Pastikan orientasi cetak adalah <b>LANDSCAPE</b> dan margin <b>0.5 cm</b>.</p>
    </div>

    <div class="print-area">
        @foreach($barcodeData as $data)
            @php
                // use Illuminate\Support\Carbon; DIHAPUS KARENA MENYEBABKAN SYNTAX ERROR DI DALAM LOOP BLADE
                
                $student = $data['student'];
                
                // LOGIKA FOTO SISWA
                $photoPath = ($student->photo && $student->photo != 'default_avatar.png' && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->photo))
                                ? asset('storage/' . $student->photo) 
                                : asset('images/default/student.png'); 

                // 💡 PERBAIKAN KRITIS: Pengecekan Aman untuk Tanggal Lahir
                $birthDateFormatted = ($student->birth_date && ($student->birth_date instanceof \Illuminate\Support\Carbon || $student->birth_date instanceof \DateTime)) 
                                      ? $student->birth_date->format('d M Y') 
                                      : 'N/A';
                                      
                $birthDetail = ($student->birth_place ? $student->birth_place . ', ' : '') . $birthDateFormatted;
                $statusText = $student->status == 'active' ? 'AKTIF' : 'NON-AKTIF';

                // LOGIKA LOGO SEKOLAH
                $schoolLogoPath = $settings['school_logo'] ?? 'default/logo.png';
                $logoUrl = (\Illuminate\Support\Facades\Storage::disk('public')->exists($schoolLogoPath)) 
                            ? asset('storage/' . $schoolLogoPath) 
                            : asset('images/default/logo.png');
            @endphp
            <div class="card-container">
                <div class="header">
                    <img src="{{ $logoUrl }}" alt="Logo Sekolah" class="logo">
                    <div class="header-text">
                        <h4>KARTU PELAJAR</h4>
                        <p>{{ $settings['school_name'] ?? 'NAMA SEKOLAH' }}</p>
                    </div>
                </div>

                {{-- CONTENT WRAPPER --}}
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
                        {{-- QR Code --}}
                        {!! $data['qrcode_svg'] !!} 
                        <div class="barcode-id">{{ $student->barcode_data }}</div>
                    </div>
                </div>
                {{-- END CONTENT WRAPPER --}}

                <div class="footer">
                    Gunakan kartu ini untuk absensi digital. Harap jaga baik-baik.
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>