@extends('layouts.adminlte')

@section('title', 'Riwayat Absensi')

@section('content_header')
<div class="flex items-center justify-between">
  <div>
    <h1 class="m-0 text-gray-800 font-bold text-2xl">Riwayat Absensi</h1>
    <p class="text-sm text-gray-500 mt-1">Catatan kehadiran harian Anda.</p>
  </div>
  <div>
    <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary btn-sm">
      <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
  </div>
</div>
@stop

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
        <tr>
          <th class="px-6 py-3">Tanggal</th>
          <th class="px-6 py-3">Metode</th>
          <th class="px-6 py-3">Jam Masuk</th>
          <th class="px-6 py-3">Jam Pulang</th>
          <th class="px-6 py-3">Status</th>
          <th class="px-6 py-3">Lokasi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($attendances as $attendance)
          <tr class="bg-white hover:bg-gray-50">
            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
              {{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l, d F Y') }}
            </td>
            <td class="px-6 py-4">
              @if($attendance->photo === 'qr_code')
                <span class="px-2 py-1 text-xs font-bold bg-purple-100 text-purple-700 rounded-full">
                  <i class="fas fa-qrcode mr-1"></i> QR Code
                </span>
              @elseif($attendance->photo)
                <span class="px-2 py-1 text-xs font-bold bg-blue-100 text-blue-700 rounded-full">
                  <i class="fas fa-camera mr-1"></i> Selfie
                </span>
                <a href="#"
                  onclick="Swal.fire({imageUrl: '{{ asset('storage/' . $attendance->photo) }}', showConfirmButton: false})"
                  class="text-xs text-blue-500 hover:underline ml-1">(Lihat)</a>
              @else
                <span class="px-2 py-1 text-xs font-bold bg-gray-100 text-gray-500 rounded-full">
                  <i class="fas fa-edit mr-1"></i> Manual
                </span>
              @endif
            </td>
            <td class="px-6 py-4 font-mono text-gray-600">
              {{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}
            </td>
            <td class="px-6 py-4 font-mono text-gray-600">
              {{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}
            </td>
            <td class="px-6 py-4">
              <span
                class="px-2 py-1 font-bold text-xs leading-tight rounded-full bg-{{ $attendance->status_color }}-100 text-{{ $attendance->status_color }}-700">
                {{ $attendance->status_label }}
              </span>
            </td>
            <td class="px-6 py-4">
              @if($attendance->latitude && $attendance->longitude)
                <a href="https://www.google.com/maps?q={{ $attendance->latitude }},{{ $attendance->longitude }}"
                  target="_blank" class="text-indigo-600 hover:underline text-xs">
                  <i class="fas fa-map-marker-alt mr-1"></i> Lihat Peta
                </a>
              @else
                <span class="text-gray-400 text-xs">-</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
              <div class="flex flex-col items-center">
                <i class="fas fa-history text-4xl mb-3 text-gray-200"></i>
                <p>Belum ada riwayat absensi.</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($attendances->hasPages())
    <div class="p-4 border-t border-gray-100">
      {{ $attendances->links() }}
    </div>
  @endif
</div>
@stop