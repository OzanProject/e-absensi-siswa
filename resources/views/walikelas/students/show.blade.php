@extends('layouts.adminlte')

@section('title', 'Detail Siswa: ' . ($student->name ?? 'N/A'))

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-id-card-alt text-purple-600 mr-2"></i>
        <span>Detail Siswa: {{ $student->name ?? 'N/A' }}</span>
    </h1>
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('walikelas.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('walikelas.students.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Data Siswa</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Detail</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    {{-- Notifikasi Sukses/Error (Styling Tailwind) --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg relative mb-4 alert-dismissible" role="alert">
            <i class="icon fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-4 alert-dismissible" role="alert">
            <i class="icon fas fa-ban mr-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM KIRI: FOTO & INFORMASI UTAMA (1/3) --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 text-center">
                
                {{-- Foto Siswa --}}
                @php
                    $photoPath = $student->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->photo) 
                                 ? asset('storage/' . $student->photo) 
                                 : asset('images/default_avatar.png');
                @endphp
                <img src="{{ $photoPath }}" alt="Foto {{ $student->name }}" 
                     class="w-36 h-36 rounded-full mx-auto mb-4 object-cover border-4 border-purple-400 shadow-md">
                
                <h4 class="text-xl font-extrabold text-gray-900">{{ $student->name }}</h4>
                <p class="text-md text-gray-600 font-semibold">{{ $student->class->name ?? 'N/A' }}</p>
                
                <div class="mt-4 space-y-2 border-t pt-4 border-gray-100">
                    <div class="text-sm flex justify-between px-2"><strong class="text-gray-700">NISN</strong><span class="text-gray-900 font-semibold">{{ $student->nisn }}</span></div>
                    <div class="text-sm flex justify-between px-2"><strong class="text-gray-700">NIS</strong><span class="text-gray-900 font-semibold">{{ $student->nis ?? '-' }}</span></div>
                    <p class="text-sm pt-2">Status: 
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full 
                            {{ $student->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($student->status) }}
                        </span>
                    </p>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-6 border-t pt-4 border-gray-100 flex flex-col space-y-2">
                    <a href="{{ route('walikelas.students.barcode', $student->id) }}" target="_blank"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md 
                              text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 transform hover:-translate-y-0.5">
                        <i class="fas fa-print mr-2"></i> Cetak Kartu Pelajar
                    </a>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: DETAIL, RELASI, & RIWAYAT (2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- 1. Informasi Pribadi --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-info-circle mr-2 text-indigo-500"></i> Informasi Pribadi</h3>
                </div>
                <div class="p-6 text-sm space-y-3">
                    <div class="grid grid-cols-2 border-b pb-2"><strong class="w-1/3 text-gray-600">Email</strong><span class="w-2/3 text-gray-900">{{ $student->email ?? '-' }}</span></div>
                    <div class="grid grid-cols-2 border-b pb-2"><strong class="w-1/3 text-gray-600">Jenis Kelamin</strong><span class="w-2/3 text-gray-900">{{ $student->gender }}</span></div>
                    <div class="grid grid-cols-2 border-b pb-2"><strong class="w-1/3 text-gray-600">No. HP Siswa</strong><span class="w-2/3 text-gray-900">{{ $student->phone_number ?? '-' }}</span></div>
                    <div class="grid grid-cols-2 border-b pb-2"><strong class="w-1/3 text-gray-600">TTL</strong><span class="w-2/3 text-gray-900">{{ $student->birth_place ?? '-' }}, {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->isoFormat('D MMMM YYYY') : '-' }}</span></div>
                    <div class="grid grid-cols-2"><strong class="w-1/3 text-gray-600">Alamat</strong><span class="w-2/3 text-gray-900">{{ $student->address ?? '-' }}</span></div>
                </div>
            </div>

            {{-- 2. RELASI ORANG TUA/WALI --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-link mr-2 text-purple-600"></i> Relasi Orang Tua/Wali (Kontak Notifikasi)</h3>
                </div>
                <div class="p-6 text-sm">
                    @forelse($student->parents as $parent)
                    {{-- Item Relasi --}}
                    <div class="border border-gray-100 p-3 rounded-lg mb-3 flex justify-between items-center bg-purple-50 hover:bg-purple-100 transition duration-150">
                        <div>
                            <strong class="text-gray-900">{{ $parent->name }}</strong> 
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-indigo-100 text-indigo-800 ml-2">
                                {{ $parent->relation_status }}
                            </span>
                            <p class="text-xs text-gray-600 mt-1">
                                <i class="fas fa-phone mr-1"></i> **Nomor WA:** {{ $parent->phone_number }}
                            </p>
                        </div>
                        {{-- Tombol Edit (Styling Tailwind Amber) --}}
                        <a href="{{ route('walikelas.parents.edit', $parent->id) }}" class="text-amber-700 hover:text-amber-900 p-2 rounded-full bg-amber-100 hover:bg-amber-200 transition duration-150 shadow-sm" title="Edit Kontak & Relasi">
                            <i class="fas fa-edit w-4 h-4"></i>
                        </a>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-3">Tidak ada orang tua/wali yang terhubung.</div>
                    @endforelse
                </div>
            </div>
            
            {{-- 3. Riwayat Absensi Terakhir --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-history mr-2 text-indigo-500"></i> 10 Riwayat Absensi Terakhir</h3>
                </div>
                <div class="p-6">
                    @if($historyAbsences->isEmpty())
                        <p class="text-gray-500 text-center">Belum ada riwayat absensi.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase">Tanggal</th>
                                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase">Masuk</th>
                                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase">Pulang</th>
                                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($historyAbsences as $absence)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $absence->attendance_time->format('d M Y') }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ $absence->attendance_time->format('H:i') }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ $absence->checkout_time ? $absence->checkout_time->format('H:i') : '-' }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full 
                                                    {{ $absence->status == 'Hadir' ? 'bg-green-100 text-green-800' : ($absence->status == 'Terlambat' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ $absence->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    // Pastikan SweetAlert2 dimuat jika ada notifikasi
    $(document).ready(function() {
        // Auto-dismiss alerts
        setTimeout(function() {
            $('.alert-dismissible').fadeOut(400, function() { $(this).remove(); });
        }, 5000);
    });
</script>
@endsection

@section('css')
<style>
/* --- MINIMAL CUSTOM CSS FOR TAILWIND --- */
.text-indigo-600 { color: #4f46e5; }
.text-amber-500 { color: #f59e0b; }
.text-red-600 { color: #dc3545; }
.text-purple-600 { color: #9333ea; }
.bg-purple-50 { background-color: #f5f3ff; } /* Untuk background relasi ORTU */
.bg-purple-100 { background-color: #ede9fe; } /* Hover Relasi ORTU */
.text-indigo-500 { color: #6366f1; } /* Icon history */
.text-amber-700 { color: #b45309; }
.bg-amber-100 { background-color: #fef3c7; }
</style>
@endsection