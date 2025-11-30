@extends('layouts.adminlte')

@section('title', 'Manajemen Data Siswa')

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <div class="mb-2 sm:mb-0">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-user-graduate text-indigo-600 mr-2"></i> Manajemen Data Siswa
        </h1>
        <small class="text-sm text-gray-500 block mt-1">Kelola data siswa, ekspor, dan cetak kartu barcode</small>
    </div>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Siswa</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    
    {{-- CARD HEADER: Judul & Search --}}
    <div class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between items-start lg:items-center">
        <h3 class="text-xl font-bold text-gray-800 flex items-center mb-3 lg:mb-0">
            <i class="fas fa-list mr-2 text-indigo-500"></i> Daftar Seluruh Siswa
            <span class="ml-3 text-sm font-bold bg-indigo-600 text-white px-3 py-1 rounded-full shadow-md">{{ $students->total() }}</span>
        </h3>

        {{-- 💡 Search & Filter Form --}}
        <form action="{{ route('students.index') }}" method="GET" class="w-full lg:w-auto mt-3 lg:mt-0">
            <div class="flex items-center space-x-2">
                
                {{-- Dropdown Filter Kelas --}}
                <select name="class_id" onchange="this.form.submit()"
                         class="px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm appearance-none cursor-pointer transition duration-150">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" 
                                {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Input Pencarian --}}
                <input type="text" name="search" 
                        class="px-3 py-2 border border-gray-300 rounded-l-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm w-full lg:w-48 transition duration-150" 
                        placeholder="Cari Nama/NISN..." value="{{ request('search') }}">
                
                {{-- Tombol Search --}}
                <button class="bg-indigo-600 text-white p-2.5 rounded-r-lg hover:bg-indigo-700 transition duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="p-5">

        {{-- Notifikasi (Alert Tailwind) --}}
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-4" role="alert">
                <i class="fas fa-ban mr-2"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Tombol Aksi --}}
        <div class="flex flex-wrap justify-between items-center mb-6 space-y-3 sm:space-y-0">
            <div class="flex flex-wrap gap-2">
                {{-- Tambah Siswa --}}
                <a href="{{ route('students.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-1"></i> Tambah Siswa
                </a>
                {{-- Import --}}
                <a href="{{ route('students.importForm') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-100 transition duration-150">
                    <i class="fas fa-file-import mr-1"></i> Import
                </a>
                {{-- Export --}}
                <a href="{{ route('students.export') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-green-600 hover:bg-green-700 transition duration-150">
                    <i class="fas fa-file-excel mr-1"></i> Export
                </a>
                {{-- Cetak Semua (Bulk) --}}
                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-gray-800 bg-amber-400 hover:bg-amber-500 transition duration-150" onclick="confirmPrintBulk()">
                    <i class="fas fa-print mr-1"></i> Cetak Semua
                </button>
            </div>
            {{-- Hapus Massal --}}
            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-red-600 hover:bg-red-700 transition duration-150" onclick="confirmBulkDelete()">
                <i class="fas fa-trash-alt mr-1"></i> Hapus Massal
            </button>
        </div>

        {{-- Tabel Data --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-md">
            
            {{-- Form Hapus Massal (Disembunyikan, LOGIKA TIDAK BERUBAH) --}}
            <form id="bulk-delete-form" action="{{ route('students.bulkDelete') }}" method="POST" class="hidden"> 
                @csrf
                @method('DELETE')
            </form>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider w-8">
                            <input type="checkbox" id="check-all" class="rounded text-indigo-300 bg-indigo-700 border-indigo-700 focus:ring-indigo-300 focus:ring-offset-0">
                        </th>
                        <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider w-12">No</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">NISN / NIS</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Nama & Email</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Jenis Kelamin</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($students as $student)
                    <tr class="hover:bg-indigo-50/20 transition duration-150">
                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            {{-- Checkbox bulk --}}
                            <input type="checkbox" name="selected_students[]" value="{{ $student->id }}" class="bulk-checkbox rounded text-indigo-500 focus:ring-indigo-500">
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700 text-center">{{ $loop->iteration + ($students->perPage() * ($students->currentPage() - 1)) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <strong class="text-gray-900 font-semibold">{{ $student->nisn }}</strong><br>
                            <small class="text-gray-500 text-xs">NIS: {{ $student->nis ?? '-' }}</small>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <strong class="text-gray-900 font-semibold">{{ $student->name }}</strong><br>
                            @if($student->email)
                                <small class="text-gray-500 text-xs"><i class="far fa-envelope mr-1"></i> {{ $student->email }}</small>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($student->class)
                                {{-- Badge Kelas --}}
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-cyan-100 text-cyan-800 shadow-sm">
                                    {{ $student->class->name }}
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-200 text-gray-700">
                                    Tidak Ada
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $isMale = $student->gender == 'Laki-laki';
                                $genderColor = $isMale ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800';
                            @endphp
                            {{-- Badge Gender --}}
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $genderColor }}">
                                <i class="fas fa-{{ $isMale ? 'male' : 'female' }} mr-1"></i> {{ $student->gender }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColor = $student->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                            @endphp
                            {{-- Badge Status --}}
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                {{ $student->status == 'active' ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="inline-flex space-x-2">
                                
                                {{-- 🆕 TOMBOL CETAK BARCODE SATUAN --}}
                                {{-- Memanggil route students.barcode.single ke controller generateBarcode --}}
                                <a href="{{ route('students.barcode', $student->id) }}" target="_blank" 
                                    class="text-indigo-700 hover:text-indigo-900 p-2 rounded-full bg-indigo-100 hover:bg-indigo-200 transition duration-150 shadow-sm" 
                                    title="Cetak Barcode Siswa"
                                    onclick="window.open(this.href, 'CetakBarcode', 'width=800,height=600'); return false;">
                                    <i class="fas fa-print w-4 h-4"></i>
                                </a>

                                {{-- Tombol Edit --}}
                                <a href="{{ route('students.edit', $student->id) }}" class="text-amber-700 hover:text-amber-900 p-2 rounded-full bg-amber-100 hover:bg-amber-200 transition duration-150 shadow-sm" title="Edit Siswa"><i class="fas fa-edit w-4 h-4"></i></a>
                                
                                {{-- Tombol Hapus --}}
                                <button type="button" class="text-red-700 hover:text-red-900 p-2 rounded-full bg-red-100 hover:bg-red-200 transition duration-150 shadow-sm" title="Hapus Siswa" onclick="confirmDelete({{ $student->id }}, '{{ $student->name }}')"><i class="fas fa-trash w-4 h-4"></i></button>

                                @if ($student->status == 'active')
                                    {{-- Non-aktifkan --}}
                                    <button type="button" class="text-gray-700 hover:text-gray-900 p-2 rounded-full bg-gray-200 hover:bg-gray-300 transition duration-150 shadow-sm" title="Nonaktifkan Siswa" onclick="confirmStatusChange({{ $student->id }}, 'deactivate', '{{ $student->name }}')"><i class="fas fa-user-slash w-4 h-4"></i></button>
                                @else
                                    {{-- Aktifkan --}}
                                    <button type="button" class="text-green-700 hover:text-green-900 p-2 rounded-full bg-green-100 hover:bg-green-200 transition duration-150 shadow-sm" title="Aktifkan Siswa" onclick="confirmStatusChange({{ $student->id }}, 'activate', '{{ $student->name }}')"><i class="fas fa-user-check w-4 h-4"></i></button>
                                @endif
                            </div>

                            {{-- Form Hapus Satuan (Logika TIDAK BERUBAH) --}}
                            <form id="delete-form-{{ $student->id }}" action="{{ route('students.destroy', $student->id) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                            {{-- Form Status (Logika TIDAK BERUBAH) --}}
                            <form id="status-form-{{ $student->id }}-activate" action="{{ route('students.activate', $student->id) }}" method="POST" class="hidden"> @csrf @method('PUT') </form>
                            <form id="status-form-{{ $student->id }}-deactivate" action="{{ route('students.deactivate', $student->id) }}" method="POST" class="hidden"> @csrf @method('PUT') </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-user-slash fa-3x mb-3 block text-gray-300"></i>
                            Belum ada data siswa.
                            <br>
                            <a href="{{ route('students.create') }}" class="inline-flex items-center px-4 py-2 mt-3 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 transform hover:-translate-y-0.5">
                                <i class="fas fa-plus mr-1"></i> Tambah Siswa
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        {{-- Pagination --}}
        <div class="flex flex-col sm:flex-row justify-between items-center mt-6">
            <small class="text-sm text-gray-600 mb-2 sm:mb-0">
                Menampilkan **{{ $students->firstItem() ?? 0 }} - {{ $students->lastItem() ?? 0 }}** dari **{{ $students->total() }}** siswa
            </small>
            <div class="mt-2 sm:mt-0">
                {{ $students->appends(['search' => request('search'), 'class_id' => request('class_id')])->links() }} 
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

<script>
// --- LOGIKA JAVASCRIPT UTAMA (Fungsionalitas TIDAK BERUBAH) ---

// Ganti warna SweetAlert ke palet Tailwind
const SWAL_COLOR = {
    danger: '#dc2626', // red-600
    confirm: '#4f46e5', // indigo-600
    success: '#10b981', // green-500
    cancel: '#6b7280', // gray-500
    warning: '#f59e0b', // amber-500
};

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Siswa?',
        text: `Yakin ingin menghapus "${name}"? Data tidak dapat dikembalikan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: SWAL_COLOR.danger,
        cancelButtonColor: SWAL_COLOR.cancel,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true, 
    }).then((r) => { 
        if (r.isConfirmed) {
            const form = document.getElementById(`delete-form-${id}`);
            if (form) {
                form.submit(); 
            } else {
                console.error("Form delete tidak ditemukan:", `#delete-form-${id}`); 
            }
        }
    });
}

function confirmStatusChange(id, action, name) {
    const title = action === 'activate' ? 'Aktifkan Siswa?' : 'Nonaktifkan Siswa?';
    const color = action === 'activate' ? SWAL_COLOR.success : SWAL_COLOR.cancel;
    
    Swal.fire({
        title: title,
        text: `Yakin ingin mengubah status "${name}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: color,
        cancelButtonColor: SWAL_COLOR.confirm,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    }).then((r) => { 
        if (r.isConfirmed) {
            const form = document.getElementById(`status-form-${id}-${action}`);
            if (form) {
                 form.submit(); 
            }
        }
    });
}

function confirmBulkDelete() {
    const selectedIds = $('input.bulk-checkbox:checked').map(function(){
        return this.value;
    }).get();
    
    const count = selectedIds.length;

    if (count === 0) return Swal.fire('Perhatian!', 'Pilih minimal satu siswa.', 'info');
    Swal.fire({
        title: 'Hapus Massal?',
        text: `Anda akan menghapus ${count} siswa terpilih.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: SWAL_COLOR.danger,
        cancelButtonColor: SWAL_COLOR.cancel,
        cancelButtonText: 'Batal',
        confirmButtonText: 'Ya, Hapus'
    }).then((r) => { 
        if (r.isConfirmed) {
            const bulkForm = $('#bulk-delete-form');
            
            bulkForm.empty(); 
            bulkForm.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
            bulkForm.append('<input type="hidden" name="_method" value="DELETE">');
            
            selectedIds.forEach(id => {
                bulkForm.append('<input type="hidden" name="selected_students[]" value="' + id + '">');
            });

            bulkForm.removeClass('hidden').submit(); 
        }
    });
}

function confirmPrintBulk() {
    Swal.fire({
        title: 'Cetak Semua?',
        text: 'Akan membuka halaman cetak untuk semua kartu siswa aktif.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Cetak',
        cancelButtonText: 'Batal',
        confirmButtonColor: SWAL_COLOR.confirm, 
        cancelButtonColor: SWAL_COLOR.cancel, 
    }).then((r) => { if (r.isConfirmed) window.open('{{ route("students.barcode.bulk") }}', '_blank'); });
}

$('#check-all').on('click', function() {
    $('input.bulk-checkbox').prop('checked', $(this).prop('checked'));
});

$(document).ready(function() {
    // 1. TUTUP MODAL LOADING
    @if(session('close_loading'))
        Swal.close(); 
    @endif
    
    // 2. TAMPILKAN SWEETALERT UNTUK NOTIFIKASI SESI
    @if(session('success')) 
        Swal.fire({ 
            icon: 'success', 
            title: 'Berhasil!', 
            text: '{{ session('success') }}', 
            toast: true, 
            position: 'top-end', 
            showConfirmButton: false, 
            timer: 5000 
        });
    @endif

    @if(session('error')) 
        Swal.fire({ 
            icon: 'error', 
            title: 'Gagal!', 
            text: '{{ session('error') }}', 
            toast: true, 
            position: 'top-end', 
            showConfirmButton: false, 
            timer: 5000 
        });
    @endif
    
    // Auto-hide alerts (untuk alert HTML biasa jika ada)
    setTimeout(() => $('.alert-dismissible').slideUp(300, function() { $(this).remove(); }), 5000); 
});
</script>
@stop