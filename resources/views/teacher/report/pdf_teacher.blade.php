<!DOCTYPE html>
<html>

<head>
  <title>Laporan Absensi Guru</title>
  <style>
    body {
      font-family: sans-serif;
      font-size: 10px;
      margin: 0;
      padding: 0;
    }

    .container {
      padding: 20px;
    }

    .header {
      text-align: center;
      margin-bottom: 20px;
      border-bottom: 1px solid #ccc;
      padding-bottom: 10px;
    }

    .header h2 {
      margin: 0;
      font-size: 16px;
      color: #333;
    }

    .header h4 {
      margin: 0;
      font-size: 11px;
      color: #555;
    }

    .info {
      margin-bottom: 15px;
      text-align: center;
    }

    .info p {
      margin: 2px 0;
      font-size: 10px;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    .table th,
    .table td {
      border: 1px solid #ddd;
      padding: 5px 8px;
      text-align: left;
    }

    .table th {
      background-color: #4f46e5;
      /* Indigo */
      color: white;
      font-size: 10px;
      text-transform: uppercase;
    }

    .status-present {
      color: #198754;
      font-weight: bold;
    }

    .status-late {
      color: #d97706;
      font-weight: bold;
    }

    .status-absent {
      color: #dc3545;
      font-weight: bold;
    }
  </style>
</head>

<body>
  <div class="container">

    @php
      $settings = \App\Models\Setting::pluck('value', 'key');
      $schoolName = $settings['school_name'] ?? 'SEKOLAH';
      $schoolAddress = $settings['school_address'] ?? 'Alamat Sekolah';
    @endphp

    <div class="header">
      <h2>LAPORAN KEHADIRAN GURU</h2>
      <h4>{{ $schoolName }}</h4>
      <p>{{ $schoolAddress }}</p>
    </div>

    <div class="info">
      <p><strong>Nama Guru:</strong> {{ $user->name ?? '-' }}</p>
      <p><strong>NIP:</strong> {{ $user->nip ?? '-' }}</p>
      <p><strong>Periode:</strong> {{ $startDate->format('d F Y') }} s/d {{ $endDate->format('d F Y') }}</p>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal</th>
          <th>Metode</th>
          <th>Jam Masuk</th>
          <th>Jam Pulang</th>
          <th>Status</th>
          <th>Lokasi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($absences as $attendance)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
            <td>
              @if($attendance->photo === 'qr_code')
                QR Code
              @elseif($attendance->photo)
                Selfie
              @else
                Manual
              @endif
            </td>
            <td>{{ $attendance->clock_in ?? '-' }}</td>
            <td>{{ $attendance->clock_out ?? '-' }}</td>
            <td>
              <span class="status-{{ $attendance->status ?? 'present' }}">
                {{ ucfirst($attendance->status ?? 'present') }}
              </span>
            </td>
            <td>
              @if($attendance->latitude && $attendance->longitude)
                {{ $attendance->latitude }}, {{ $attendance->latitude }}
              @else
                -
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align: center;">Tidak ada data absensi.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right;">
      <p>{{ $schoolAddress }}, {{ date('d F Y') }}</p>
      <p>Guru Bersangkutan,</p>
      <br><br><br>
      <p><strong>{{ $user->name }}</strong></p>
    </div>

  </div>
</body>

</html>