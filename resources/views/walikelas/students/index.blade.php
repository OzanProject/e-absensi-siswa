@extends('layouts.adminlte')

@section('title', 'Data Siswa Kelas ' . ($class->name ?? ''))

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <div class="mb-2 sm:mb-0">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <i class="fas fa-users text-indigo-600 mr-2"></i> Data Siswa Kelas {{ $class->name ?? 'Anda' }}
        </h1>
        <small class="text-sm text-gray-500 block mt-1">Kelola data siswa di kelas yang Anda ampu.</small>
    </div>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <li><a href="{{ route('walikelas.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Data Siswa</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    
    <div class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between items-start lg:items-center">
        <h3 class="text-xl font-bold text-gray-800 mb-3 lg:mb-0 flex items-center">
            <i class="fas fa-list mr-2 text-indigo-500"></i> Daftar Siswa
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <span class="ml-3 text-sm font-bold bg-indigo-600 text-white px-3 py-1 rounded-full shadow-md">{{ $students->total() }}</span>
        </h3>

        {{-- Search Form (Styling Tailwind) --}}
        <form action="{{ route('walikelas.students.index') }}" method="GET" class="w-full sm:w-auto">
            <div class="flex">
                <input type="text" name="search" 
                        class="px-3 py-2 border border-gray-300 rounded-l-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm w-full transition duration-150" 
                        placeholder="Cari Nama/NISN..." value="{{ request('search') }}">
                {{-- Mengganti blue-600 ke indigo-600 --}}
                <button class="bg-indigo-600 text-white p-2.5 rounded-r-lg hover:bg-indigo-700 transition duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="p-5">

        {{-- Notifikasi (Styling Tailwind) --}}
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg relative mb-4 alert-dismissible" role="alert">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-4 alert-dismissible" role="alert">
                <i class="fas fa-ban mr-2"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Tombol Aksi (Styling Tailwind) --}}
        <div class="flex flex-wrap justify-between items-center mb-6 space-y-2 sm:space-y-0">
            <div class="flex flex-wrap gap-2">
                {{-- Tambah Siswa (Primary -> Indigo) --}}
                <a href="{{ route('walikelas.students.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-1"></i> Tambah Siswa
                </a>
                {{-- Cetak Semua Kartu (Warning -> Amber) --}}
                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-gray-800 bg-amber-400 hover:bg-amber-500 transition duration-150" onclick="confirmPrintBulk()">
                    <i class="fas fa-print mr-1"></i> Cetak Semua Kartu
                </button>
            </div>
            {{-- Hapus Massal (Danger -> Red) --}}
            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-red-600 hover:bg-red-700 transition duration-150" onclick="confirmBulkDelete()">
                <i class="fas fa-trash-alt mr-1"></i> Hapus Massal
            </button>
        </div>

        {{-- Tabel Data --}}
        <div class="overflow-x-auto shadow-sm border border-gray-200 rounded-lg"> 
            
            {{-- Form Hapus Massal (Logika AMAN) --}}
            <form id="bulk-delete-form" action="{{ route('walikelas.students.bulkDelete') }}" method="POST" class="hidden"> 
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
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider min-w-36">NISN / NIS</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider min-w-48">Nama & Email</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider w-24">Jenis Kelamin</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider w-24">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $student)
                    <tr class="hover:bg-indigo-50/20 transition duration-150">
                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            {{-- Checkbox Massal --}}
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
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $isMale = $student->gender == 'Laki-laki';
                                $genderColor = $isMale ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800';
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $genderColor }}">
                                <i class="fas fa-{{ $isMale ? 'male' : 'female' }} mr-1"></i> {{ $student->gender }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $statusColor = $student->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                {{ $student->status == 'active' ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="inline-flex space-x-1">
                                
                                {{-- Lihat Detail (Info -> Indigo) --}}
                                <a href="{{ route('walikelas.students.show', $student->id) }}" class="text-indigo-700 hover:text-indigo-900 p-1.5 rounded-full bg-indigo-100 hover:bg-indigo-200 transition duration-150" title="Lihat Detail">
                                    <i class="fas fa-eye w-4 h-4"></i>
                                </a>
                                {{-- Cetak Kartu (Secondary -> Gray/Indigo) --}}
                                <a href="{{ route('walikelas.students.barcode', $student->id) }}" class="text-gray-700 hover:text-gray-900 p-1.5 rounded-full bg-gray-200 hover:bg-gray-300 transition duration-150" target="_blank" title="Cetak Kartu">
                                    <i class="fas fa-barcode w-4 h-4"></i>
                                </a>
                                {{-- Edit (Warning -> Amber) --}}
                                <a href="{{ route('walikelas.students.edit', $student->id) }}" class="text-amber-700 hover:text-amber-900 p-1.5 rounded-full bg-amber-100 hover:bg-amber-200 transition duration-150" title="Edit Data">
                                    <i class="fas fa-edit w-4 h-4"></i>
                                </a>
                                {{-- Hapus (Danger -> Red) --}}
                                <button type="button" class="text-red-700 hover:text-red-900 p-1.5 rounded-full bg-red-100 hover:bg-red-200 transition duration-150" onclick="confirmDelete({{ $student->id }}, '{{ $student->name }}')" title="Hapus Permanen">
                                    <i class="fas fa-trash w-4 h-4"></i>
                                </button>
                            </div>

                            {{-- Form Hapus Satuan (Logika AMAN) --}}
                            <form id="delete-form-{{ $student->id }}" action="{{ route('walikelas.students.destroy', $student->id) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-user-slash fa-3x mb-3 block text-gray-300"></i>
                            Belum ada data siswa di kelas ini.
                            <br>
                            {{-- Tombol Tambah Siswa (Primary -> Indigo) --}}
                            <a href="{{ route('walikelas.students.create') }}" class="inline-flex items-center px-4 py-2 mt-3 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 transform hover:-translate-y-0.5">
                                <i class="fas fa-plus mr-1"></i> Tambah Siswa
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="flex justify-between items-center mt-6">
            <small class="text-sm text-gray-600">
                Menampilkan {{ $students->firstItem() ?? 0 }} - {{ $students->lastItem() ?? 0 }} dari {{ $students->total() }} siswa
            </small>
            <div class="mt-2 sm:mt-0">
                {{ $students->links('pagination::tailwind') }} 
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

<script>
// --- LOGIKA JAVASCRIPT UTAMA (Fungsionalitas AMAN) ---

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Siswa?',
        text: `Yakin ingin menghapus "${name}"? Data tidak dapat dikembalikan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((r) => { 
        if (r.isConfirmed) {
            // LOGIKA AMAN: Panggil submit form DELETE tersembunyi
            $(`#delete-form-${id}`).removeClass('d-none').submit(); 
        }
    });
}

function confirmBulkDelete() {
    // Cari semua checkbox yang tercentang dengan class 'bulk-checkbox'
    const selectedIds = $('input.bulk-checkbox:checked').map(function(){
        return this.value;
    }).get();
    
    const count = selectedIds.length;

    if (count === 0) return Swal.fire('Perhatian!', 'Pilih minimal satu siswa.', 'info');
    Swal.fire({
        title: 'Hapus Massal?',
        text: `Anda akan menghapus ${count} siswa terpilih dari kelas Anda.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonText: 'Batal',
        confirmButtonText: 'Ya, Hapus'
    }).then((r) => { 
        if (r.isConfirmed) {
            const bulkForm = $('#bulk-delete-form');
            
            // Pindahkan ID siswa ke form bulk delete (LOGIKA AMAN)
            bulkForm.empty(); 
            bulkForm.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
            bulkForm.append('<input type="hidden" name="_method" value="DELETE">');
            
            selectedIds.forEach(id => {
                bulkForm.append('<input type="hidden" name="selected_students[]" value="' + id + '">');
            });

            // Submit form
            bulkForm.removeClass('d-none hidden').submit(); 
        }
    });
}

function confirmPrintBulk() {
    Swal.fire({
        title: 'Cetak Kartu Massal?',
        text: 'Akan membuka halaman cetak untuk semua kartu siswa AKTIF di kelas ini.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Ya, Cetak',
        cancelButtonText: 'Batal'
    }).then((r) => { 
        if (r.isConfirmed) {
            // LOGIKA AMAN: Buka rute cetak massal Wali Kelas di tab baru
            window.open('{{ route("walikelas.students.barcode.bulk") }}', '_blank'); 
        }
    });
}

$('#check-all').on('click', function() {
    $('input.bulk-checkbox').prop('checked', $(this).prop('checked'));
});

$(document).ready(function() {
    // Auto-hide alerts
    setTimeout(() => $('.alert-dismissible').slideUp(300, function() { $(this).remove(); }), 5000); 
});
</script>
@stop

@push('css')
<style>
/* --- MINIMAL CUSTOM CSS FOR TAILWIND --- */
.text-indigo-600 { color: #4f46e5; }
.text-amber-500 { color: #f59e0b; }
.border-amber-500 { border-color: #f59e0b; }
.text-red-600 { color: #dc3545; }
.border-red-600 { border-color: #dc3545; }
.text-blue-600 { color: #2563eb; }
.border-blue-600 { border-color: #2563eb; }
.text-green-600 { color: #10b981; }
.border-green-600 { border-color: #059669; }

/* Warna Kartu Izin Pending */
.text-purple-600 { color: #9333ea; }
.border-purple-600 { border-color: #9333ea; }

/* FIXES untuk Tombol Aksi */
.btn-group-sm .btn { padding: 0.25rem 0.5rem; font-size: 0.875rem; line-height: 1.5; border-radius: 0.2rem; }
.btn-group-sm { display: inline-flex; }

/* Styling Button Khusus */
.btn-primary { background-color: #4f46e5; border-color: #4f46e5; color: #fff; } /* Indigo Primary */
.btn-info { background-color: #17a2b8; border-color: #17a2b8; color: #fff; } 
.btn-secondary { background-color: #6c757d; border-color: #6c757d; color: #fff; }
.btn-warning { background-color: #ffc107; border-color: #ffc107; color: #212529; }
.btn-danger { background-color: #dc3545; border-color: #dc3545; color: #fff; }
</style>
@endpush