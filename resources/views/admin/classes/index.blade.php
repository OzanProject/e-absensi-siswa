@extends('layouts.adminlte')

@section('title', 'Manajemen Data Kelas')

@section('content_header')
{{-- CUSTOM HEADER --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    {{-- Judul Halaman --}}
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        {{-- Ganti blue-600 ke indigo-600 --}}
        <i class="fas fa-school text-indigo-600 mr-2"></i>
        <span>Manajemen Data Kelas</span>
    </h1>
    
    {{-- Breadcrumb --}}
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Data Kelas</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
{{-- CARD UTAMA: Menggunakan rounded-xl untuk konsistensi --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden"> 
    
    {{-- CARD HEADER --}}
    <div class="p-5 border-b border-gray-100 flex justify-between items-center">
        <h3 class="text-xl font-bold text-gray-800 flex items-center">
            Daftar Kelas
            {{-- Ganti blue-600 ke indigo-600 --}}
            <span class="ml-3 text-sm font-bold bg-indigo-600 text-white px-3 py-1 rounded-full shadow-md">{{ $classes->total() }}</span> 
        </h3>
        <div>
            <a href="{{ route('classes.create') }}" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg 
                       shadow-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-indigo-500/50 transition duration-150 transform hover:-translate-y-0.5"> 
                <i class="fas fa-plus mr-2"></i> Tambah Kelas
            </a>
        </div>
    </div>

    {{-- CARD BODY --}}
    <div class="p-5">
        
        {{-- ✅ ALERT SUCCESS & ERROR (Menggunakan ikon yang lebih baik) --}}
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg relative mb-4" role="alert">
                <i class="icon fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-4" role="alert">
                <i class="icon fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        {{-- ✅ TABEL DATA (Design Minimalis & Modern) --}}
        <div class="overflow-x-auto">
            {{-- Tambahkan border-collapse dan text-gray-700 untuk tampilan yang lebih solid --}}
            <table class="min-w-full divide-y divide-gray-200 border-collapse"> 
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider w-12">No</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tingkat</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jurusan</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100 text-gray-700"> {{-- Divide-y lebih tipis --}}
                    @forelse($classes as $class)
                        <tr class="hover:bg-indigo-50/20 transition duration-150"> {{-- Hover effect Indigo --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $loop->iteration + (($classes->currentPage() - 1) * $classes->perPage()) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">{{ $class->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{-- Badge Tingkat Dibuat Lebih Halus --}}
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-indigo-100 text-indigo-800"> 
                                    {{ $class->grade }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $class->major ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="inline-flex space-x-2"> 
                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('classes.edit', $class->id) }}" 
                                        class="text-amber-700 hover:text-amber-900 p-2 rounded-full bg-amber-100 hover:bg-amber-200 transition duration-150 shadow-sm" title="Edit Kelas">
                                        <i class="fas fa-edit w-4 h-4"></i>
                                    </a>
                                    {{-- Tombol Hapus --}}
                                    <button type="button" 
                                            class="text-red-700 hover:text-red-900 p-2 rounded-full bg-red-100 hover:bg-red-200 transition duration-150 shadow-sm" title="Hapus Kelas"
                                            onclick="confirmDelete({{ $class->id }}, '{{ $class->name }}')">
                                        <i class="fas fa-trash w-4 h-4"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $class->id }}" action="{{ route('classes.destroy', $class->id) }}" 
                                      method="POST" class="hidden"> 
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox fa-3x mb-3 block text-gray-300"></i>
                                Belum ada data kelas yang ditambahkan.
                                <br>
                                {{-- Tombol diubah ke Indigo --}}
                                <a href="{{ route('classes.create') }}" class="inline-flex items-center px-4 py-2 mt-4 text-sm font-medium rounded-lg 
                                        shadow-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 transform hover:-translate-y-0.5">
                                    <i class="fas fa-plus mr-2"></i> Tambah Kelas Pertama
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ✅ PAGINATION --}}
        @if($classes->total() > 0)
        <div class="flex flex-col sm:flex-row justify-between items-center mt-6">
            <div class="text-sm text-gray-600 mb-4 sm:mb-0">
                Menampilkan 
                <strong class="font-bold">{{ $classes->firstItem() ?? 0 }}</strong> 
                sampai 
                <strong class="font-bold">{{ $classes->lastItem() ?? 0 }}</strong> 
                dari 
                <strong class="font-bold">{{ $classes->total() }}</strong> 
                hasil
            </div>
            <div class="mt-4 sm:mt-0">
                {{-- Memastikan Anda menggunakan custom view pagination Tailwind atau sudah mengganti default-nya --}}
                {{ $classes->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@stop

@section('js')
{{-- SweetAlert JS dan Logika Delete Confirmation (Logika Tidak Diubah, hanya warna) --}}
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

<script>
function confirmDelete(id, className) {
    Swal.fire({
        title: 'Hapus Kelas?',
        html: `Yakin ingin menghapus kelas <strong>${className}</strong>?<br>
        Data terkait seperti siswa dan wali kelas juga dapat terpengaruh.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626', // red-600
        cancelButtonColor: '#4f46e5', // indigo-600
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    });
}

$(document).ready(function() {
    // Menghilangkan alert (menggunakan jQuery yang sudah dimuat)
    setTimeout(() => $('.alert').slideUp(300, function() { $(this).remove(); }), 5000); 
    
    // Tooltip (Jika Anda memiliki JS Bootstrap dimuat di master layout)
    // Jika tidak menggunakan Bootstrap JS, hapus baris ini atau ganti dengan library tooltip Tailwind
    // $('[title]').tooltip();
});
</script>
@stop