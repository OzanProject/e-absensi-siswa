@extends('layouts.adminlte') 

@section('title', 'Manajemen Absensi Kelas ' . ($class->name ?? ''))

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Cyan/Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        {{-- Mengganti text-cyan-600 ke teal-600 --}}
        <i class="fas fa-calendar-alt text-teal-600 mr-2"></i>
        <span>Manajemen Absensi Kelas Hari Ini</span>
    </h1>
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <li><a href="{{ route('walikelas.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Absensi Manual</li>
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
    @if(session('error') || session('warning'))
        @php
            $alertType = session('error') ? 'red' : 'amber';
            $alertText = session('error') ?? session('warning');
            $iconClass = session('error') ? 'fas fa-ban' : 'fas fa-exclamation-triangle';
        @endphp
        <div class="bg-{{ $alertType }}-50 border-l-4 border-{{ $alertType }}-500 text-{{ $alertType }}-700 p-4 rounded-lg relative mb-4 alert-dismissible" role="alert">
            <i class="{{ $iconClass }} mr-2"></i> {{ $alertText }}
        </div>
    @endif

    {{-- Mengganti row dan col-md-X dengan Grid Tailwind (1/3 dan 2/3) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- KOLOM KIRI: FORM ABSENSI MANUAL (1/3) --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-5 border-b border-gray-100">
                    <h5 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-pen-square mr-2 text-indigo-500"></i> Catat Absensi Manual</h5>
                </div>
                <div class="p-6">
                    
                    {{-- Alert Validasi Error dari Controller --}}
                    @if ($errors->any())
                        <div class="bg-red-50 text-red-700 font-semibold px-3 py-2 rounded-lg mb-4 text-sm border border-red-200">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Harap periksa data input Anda.
                        </div>
                    @endif
                    
                    <form id="manualAttendanceForm" action="{{ route('walikelas.absensi.manual.store') }}" method="POST" class="space-y-5">
                        @csrf
                        
                        @php
                            // Fokus ke Green untuk Input
                            $inputClass = 'w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-150';
                            $errorBorder = 'border-red-500';
                            $defaultBorder = 'border-gray-300';
                        @endphp

                        {{-- Pilih Siswa (Select2) --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Siswa <span class="text-red-600">*</span></label>
                            <select class="w-full select2bs4 {{ $defaultBorder }} @error('nis') {{ $errorBorder }} @enderror" name="nis" id="manualStudentSelect" required>
                                <option value="">Pilih Siswa...</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->nis }}" {{ old('nis') == $student->nis ? 'selected' : '' }}>
                                        {{ $student->name }} ({{ $student->nis }})
                                    </option>
                                @endforeach
                            </select>
                            @error('nis') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            <small class="text-xs text-gray-500 mt-1 block">Hanya siswa aktif di kelas Anda.</small>
                        </div>
                        
                        {{-- Status Kehadiran --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Status Kehadiran <span class="text-red-600">*</span></label>
                            <select class="w-full px-3 py-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('status') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" name="status" id="attendanceStatus" required>
                                <option value="">Pilih Status</option>
                                @foreach(['Hadir', 'Terlambat', 'Sakit', 'Izin', 'Alpha'] as $status)
                                    <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                            @error('status') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                        
                        {{-- Keterangan (Opsional) --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan (Opsional)</label>
                            <textarea class="w-full px-3 py-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('notes') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" name="notes" id="attendanceNote" rows="2">{{ old('notes') }}</textarea>
                            @error('notes') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                        </div>
                        
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 text-base font-bold rounded-lg shadow-md 
                                text-white bg-green-600 hover:bg-green-700 transition duration-150 mt-4 transform hover:-translate-y-0.5" id="manualSubmitBtn">
                            <i class="fas fa-save mr-2"></i> Simpan Absensi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: TABEL LOG HARI INI (2/3) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <h5 class="text-xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0"><i class="fas fa-history mr-2 text-indigo-500"></i> Log Absensi Hari Ini (Koreksi)</h5>
                    
                    {{-- Tombol Kirim Notifikasi WA Massal --}}
                    <form action="{{ route('walikelas.absensi.send_daily_absences') }}" method="POST" id="sendWaForm" class="flex-shrink-0">
                        @csrf
                        {{-- Styling Button WA (Purple) --}}
                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-bold bg-purple-600 hover:bg-purple-700 text-white shadow-md rounded-lg transition duration-150 transform hover:scale-[1.02]" id="sendWaBtn">
                            <i class="fab fa-whatsapp mr-1"></i> Kirim Notifikasi Absen Hari Ini
                        </button>
                    </form>
                </div>
                
                <div class="p-0">
                    <div class="overflow-x-auto rounded-b-xl">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider w-12">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider min-w-40">Nama Siswa</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider min-w-24">Waktu Masuk</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider min-w-24">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider min-w-32">Keterangan</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100" id="todayAttendanceBody">
                                @php $rowNumber = 1; @endphp
                                @forelse ($todayAttendance as $att)
                                    {{-- Hanya tampilkan record yang BELUM PULANG (untuk dikoreksi) --}}
                                    @if ($att->checkout_time) @continue @endif 
                                    
                                    @php
                                        // LOGIKA MAPPING STATUS AMAN (Menggunakan Sintaks Sederhana)
                                        $statusMap = [
                                            'Hadir' => 'bg-green-100 text-green-800',
                                            'Terlambat' => 'bg-amber-100 text-amber-800',
                                            'Sakit' => 'bg-cyan-100 text-cyan-800',
                                            'Izin' => 'bg-blue-100 text-blue-800',
                                            'Alpha' => 'bg-red-100 text-red-800'
                                        ];
                                        $statusClass = $statusMap[$att->status] ?? 'bg-gray-200 text-gray-700';
                                        $displayTime = $att->attendance_time ? \Carbon\Carbon::parse($att->attendance_time)->format('H:i:s') : 'N/A';
                                    @endphp
                                    <tr data-attendance-id="{{ $att->id }}" class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700 text-center">{{ $rowNumber++ }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $att->student->name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $displayTime }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $statusClass }}">{{ ucfirst($att->status) }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $att->notes ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            {{-- Tombol Aksi (Styling Tailwind Rounded-Full) --}}
                                            <div class="inline-flex space-x-1">
                                                {{-- LINK KE FORM EDIT --}}
                                                <a href="{{ route('walikelas.absensi.manual.edit', $att->id) }}" class="text-amber-700 hover:text-amber-900 p-1.5 rounded-full bg-amber-100 hover:bg-amber-200 transition duration-150" title="Edit Status">
                                                    <i class="fas fa-edit w-4 h-4"></i>
                                                </a>
                                                {{-- Tombol Hapus --}}
                                                <button type="button" class="text-red-700 hover:text-red-900 p-1.5 rounded-full bg-red-100 hover:bg-red-200 transition duration-150" onclick="confirmDeleteAttendance('{{ $att->id }}')" title="Hapus">
                                                    <i class="fas fa-trash w-4 h-4"></i>
                                                </button>
                                            </div>
                                            {{-- FORM HAPUS UNTUK TOMBOL DI ATAS --}}
                                            <form id="delete-att-form-{{ $att->id }}" action="{{ route('walikelas.absensi.destroy', $att->id) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada absensi tercatat hari ini yang memerlukan koreksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="{{ asset('template/adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<script>
    
    $(document).ready(function() {
        // Initialize Select2 
        // Menggunakan styling Bootstrap4 Select2 untuk kompatibilitas
        $('.select2bs4').select2({ theme: 'bootstrap4', placeholder: 'Pilih Siswa...', allowClear: true });

        // FUNGSI SUBMIT MANUAL (Loading State) - LOGIKA AMAN
        $('#manualAttendanceForm').on('submit', function() {
            const submitBtn = $('#manualSubmitBtn');
            if (this.checkValidity() === false) {
                 return;
            }
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');
        });
        
        // 🚨 FUNGSI SUBMIT WA MASSAL (Loading State & Konfirmasi) - LOGIKA AMAN
        window.confirmSendWa = function() {
            const form = $('#sendWaForm');
            const submitBtn = $('#sendWaBtn');
            
            Swal.fire({
                title: 'Kirim Notifikasi Massal?',
                text: "Anda akan mengirim pesan WhatsApp kepada semua orang tua/wali siswa yang berstatus Sakit, Izin, atau Alpha hari ini.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#9333ea',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fab fa-whatsapp"></i> Ya, Kirim!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...');
                    form.off('submit').submit(); 
                }
            });
        };
        
        // Atur listener WA Massal
        $('#sendWaForm').on('submit', function(e) {
            e.preventDefault();
            confirmSendWa();
        });


        // FUNGSI HAPUS ABSENSI (SweetAlert2) - LOGIKA AMAN
        window.confirmDeleteAttendance = function(attendanceId) {
            Swal.fire({
                title: 'Hapus Absensi?',
                text: "Yakin ingin menghapus catatan absensi ini? Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = $(`#delete-att-form-${attendanceId}`);
                    if (form.length) {
                        form.submit(); 
                    }
                }
            });
        };
        
        // Auto-dismiss alerts
        setTimeout(function() {
            $('.alert-dismissible').fadeOut(400);
        }, 5000);
    });
</script>
@endsection

@section('css')
<style>
/* --- MINIMAL CUSTOM CSS FOR TAILWIND --- */
.text-indigo-600 { color: #4f46e5; }
.text-indigo-500 { color: #6366f1; } /* Icon history/list */
.bg-indigo-100 { background-color: #e0e7ff; } 
.text-amber-700 { color: #b45309; }
.bg-amber-100 { background-color: #fef3c7; }
.text-red-700 { color: #b91c1c; }
.bg-red-100 { background-color: #fee2e2; }
.text-green-700 { color: #059669; }

/* Warna Custom WA Button */
.bg-purple-600 { background-color: #9333ea !important; }
.hover\:bg-purple-700:hover { background-color: #7e22ce !important; }

/* Select2 Fix */
.select2-container--bootstrap4 .select2-selection--single,
.select2-container--bootstrap4 .select2-selection--multiple {
    height: calc(2.25rem + 2px) !important;
}
.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    line-height: 1.5 !important;
    padding-top: 5px !important; 
}
</style>
@endsection