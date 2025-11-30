@extends('layouts.adminlte')

@section('title', 'Manajemen Data Wali Kelas')

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Purple --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div class="mb-2 sm:mb-0">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            {{-- Menggunakan warna Purple-600 --}}
            <i class="fas fa-chalkboard-teacher text-purple-600 mr-2"></i> 
            <span>Manajemen Data Wali Kelas</span>
        </h1>
    </div>
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Wali Kelas</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden"> 
    
    {{-- CARD HEADER --}}
    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center">
        <h3 class="text-xl font-bold text-gray-800 flex items-center mb-3 sm:mb-0">
            <i class="fas fa-list mr-2 text-indigo-500"></i> Daftar Akun Wali Kelas
            {{-- Menggunakan warna Purple-600 --}}
            <span class="ml-3 text-sm font-bold bg-purple-600 text-white px-3 py-1 rounded-full shadow-md">{{ $teachers->count() }}</span>
        </h3>
        <div class="flex-shrink-0">
            {{-- Tombol Tambah Wali Kelas (Mengganti blue-600 ke purple-600) --}}
            <a href="{{ route('teachers.create') }}" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md
                        text-white bg-purple-600 hover:bg-purple-700 transition duration-150 transform hover:-translate-y-0.5">
                <i class="fas fa-plus mr-1"></i> Tambah Wali Kelas
            </a>
        </div>
    </div>
    
    <div class="p-5">
        
        {{-- TABEL DATA --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white"> {{-- Tetap Dark/Gray untuk Thead --}}
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wider w-12">#</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Nama Guru</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Email (Login)</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Kelas Diampu</th>
                        <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($teachers as $teacher)
                    <tr class="hover:bg-purple-50/20 transition duration-150"> {{-- Hover effect Purple --}}
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            <div><strong>{{ $teacher->name }}</strong></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $teacher->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($teacher->homeroomTeacher && $teacher->homeroomTeacher->class)
                                {{-- Badge Kelas Diampu (Menggunakan Purple) --}}
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-purple-100 text-purple-800 shadow-sm">
                                    {{ $teacher->homeroomTeacher->class->name }} 
                                    (@if(isset($teacher->homeroomTeacher->class->grade)) Tingkat {{ $teacher->homeroomTeacher->class->grade }} @endif)
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-200 text-gray-700">
                                    Belum Diampu
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            {{-- Mengganti btn-group btn-group-sm ke inline-flex space-x-2 --}}
                            <div class="inline-flex space-x-2"> 
                                {{-- Tombol Edit (Warning -> Amber, rounded-full) --}}
                                <a href="{{ route('teachers.edit', $teacher->id) }}" class="text-amber-700 hover:text-amber-900 p-2 rounded-full bg-amber-100 hover:bg-amber-200 transition duration-150 shadow-sm" title="Edit Data">
                                    <i class="fas fa-edit w-4 h-4"></i>
                                </a>
                                {{-- Tombol Hapus (Danger -> Red, rounded-full) --}}
                                <button type="button" 
                                        class="text-red-700 hover:text-red-900 p-2 rounded-full bg-red-100 hover:bg-red-200 transition duration-150 shadow-sm" 
                                        title="Hapus Akun"
                                        onclick="confirmDelete({{ $teacher->id }}, '{{ $teacher->name }}')">
                                    <i class="fas fa-trash w-4 h-4"></i>
                                </button>
                            </div>
                            
                            {{-- Form Hapus Tersembunyi (LOGIKA TIDAK BERUBAH) --}}
                            <form id="delete-form-{{ $teacher->id }}" 
                                    action="{{ route('teachers.destroy', $teacher->id) }}" 
                                    method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-user-slash fa-3x mb-3 block text-gray-300"></i>
                            Belum ada data Wali Kelas yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

{{-- HAPUS @section('css') yang lama --}}

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // --- FUNGSI HAPUS TUNGGAL DENGAN SWEETALERT (LOGIKA TIDAK BERUBAH) ---
    function confirmDelete(id, teacherName) {
        Swal.fire({
            title: 'Hapus Akun Wali Kelas?',
            text: `Yakin ingin menghapus akun "${teacherName}"? Tindakan ini akan menghapus penugasan kelas secara otomatis.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626', // Red-600
            cancelButtonColor: '#4f46e5', // Indigo-600 (Ganti dari abu-abu ke Indigo agar konsisten dengan branding batal)
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $(`#delete-form-${id}`);
                
                if (form.length) {
                    // Logika submit yang aman
                    form.removeClass('hidden').submit(); 
                }
            }
        });
    }

    $(document).ready(function() {
        // --- Tampilkan notifikasi SweetAlert Toast untuk pesan sesi (LOGIKA TIDAK BERUBAH) ---
        @if(session('success'))
             Swal.fire({ 
                 icon: 'success', 
                 title: 'Berhasil!', 
                 text: '{{ session('success') }}', 
                 toast: true, 
                 position: 'top-end', 
                 showConfirmButton: false, 
                 timer: 3000 
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
                 timer: 3000 
             });
        @endif
    });
</script>
@stop