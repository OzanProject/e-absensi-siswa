<!DOCTYPE html>
<html>

<head>
  <title>Laporan Absensi Guru</title>
  <style>
    body {
      font-family: sans-serif;
      font-size: 12px;
    }

    .header {
      text-align: center;
      margin-bottom: 20px;
    }

    .header h2 {
      margin: 0;
      color: #333;
    }

    .header p {
      margin: 5px 0;
      color: #555;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table,
    th,
    td {
      border: 1px solid #ddd;
    }

    th {
      background-color: #f2f2f2;
      padding: 10px;
      text-align: left;
    }

    td {
      padding: 8px;
    }

    .status-hadir {
      color: green;
      font-weight: bold;
    }

    .status-telat {
      color: orange;
      font-weight: bold;
    }

    .footer {
      position: fixed;
      bottom: 0;
      width: 100%;
      text-align: center;
      font-size: 10px;
      color: #777;
    }
  </style>
</head>

<body>
  <div class="header">
    <h2>Laporan Absensi Guru</h2>
    <p>Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width: 5%">No</th>
        <th style="width: 20%">Nama Guru</th>
        <th style="width: 15%">NIP</th>
        <th style="width: 15%">Tanggal</th>
        <th style="width: 10%">Masuk</th>
        <th style="width: 10%">Pulang</th>
        <th style="width: 10%">Metode</th>
        <th style="width: 15%">Status</th>
      </tr>
    </thead>
    <tbody>
      @forelse($absences as $a)
        <tr>
          <td style="text-align: center">{{ $loop->iteration }}</td>
          <td>{{ $a->user->name ?? '-' }}</td>
          <td>{{ $a->user->nip ?? '-' }}</td>
          <td>{{ \Carbon\Carbon::parse($a->date)->format('d/m/Y') }}</td>
          <td>{{ $a->clock_in ?? '-' }}</td>
          <td>{{ $a->clock_out ?? '-' }}</td>
          <td>
            @if($a->photo == 'qr_code') QR Code
            @elseif($a->photo == 'admin_scan') Scan Kartu
            @elseif($a->photo) Selfie
            @else Manual
            @endif
          </td>
          <td>
            @php
              $status = ucfirst($a->status);
              $class = ($a->status == 'late') ? 'status-telat' : 'status-hadir';
            @endphp
            <span class="{{ $class }}">{{ $status }}</span>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align: center; padding: 20px;">Tidak ada data absensi.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="footer">
    Dicetak pada: {{ now()->format('d/m/Y H:i') }} | Sistem E-Absensi
  </div>
</body>

</html>