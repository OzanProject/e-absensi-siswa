@extends('layouts.adminlte')

@section('title', 'Manajemen Data Orang Tua')

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Orange/Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <div class="mb-2 sm:mb-0">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-hands-helping text-orange-500 mr-2"></i>
            <span>Manajemen Data Orang Tua</span>
        </h1>
    </div>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Orang Tua</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    
    {{-- CARD HEADER: Judul, Count & Search --}}
    <div class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between items-start lg:items-center">
        <h3 class="text-xl font-bold text-gray-800 mb-3 lg:mb-0 flex items-center">
            <i class="fas fa-users mr-2 text-indigo-500"></i> Daftar Akun Orang Tua
            {{-- Badge Count (Warna Orange) --}}
            <span class="ml-3 text-sm font-bold bg-orange-500 text-white px-3 py-1 rounded-full shadow-md">{{ $parents->total() }}</span>
        </h3>
        
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 items-center w-full sm:w-auto">
            
            {{-- FORM PENCARIAN (Dikonversi ke Tailwind) --}}
            <form action="{{ route('parents.index') }}" method="GET" class="flex w-full sm:w-64">
                <input type="text" name="search" 
                        class="px-3 py-2 border border-gray-300 rounded-l-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm w-full transition duration-150" 
                        placeholder="Cari Nama/HP/Anak..." value="{{ request('search') }}">
                {{-- Tombol Search (Warna Indigo) --}}
                <button type="submit" class="bg-indigo-600 text-white p-2.5 rounded-r-lg hover:bg-indigo-700 transition duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            
            {{-- Tombol Tambah (Warna Orange) --}}
            <a href="{{ route('parents.create') }}" class="inline-flex justify-center items-center px-4 py-2 text-sm font-bold rounded-lg shadow-md
                    text-white bg-orange-500 hover:bg-orange-600 transition duration-150 w-full sm:w-auto transform hover:-translate-y-0.5">
                <i class="fas fa-plus mr-1"></i> Tambah Orang Tua
            </a>
        </div>
    </div>
    
    <div class="p-5">
        
        {{-- Notifikasi Sukses/Error (Styling Tailwind) --}}
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg relative mb-4" role="alert">
                <i class="icon fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-4" role="alert">
                <i class="icon fas fa-ban mr-2"></i> {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wider w-12">#</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Nama Orang Tua (Status)</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider w-1/4">Kontak & Login</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Anak Diampu</th>
                        <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($parents as $parent)
                    <tr class="hover:bg-orange-50/20 transition duration-150"> {{-- Hover effect Orange --}}
                        {{-- Penomoran Paginasi --}}
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $loop->iteration + (($parents->currentPage() - 1) * $parents->perPage()) }}
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900"><strong>{{ $parent->name }}</strong></div>
                            {{-- Badge Status Hubungan (Warna Amber) --}}
                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-bold rounded-full bg-amber-100 text-amber-800 shadow-sm mt-0.5">
                                {{ $parent->relation_status ?? 'Wali' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{-- Kontak & Login (Ikon Diberi Warna Indigo/Green) --}}
                            <small class="block text-xs font-medium"><i class="fas fa-envelope mr-1 text-indigo-600"></i> {{ $parent->user->email ?? 'N/A' }}</small>
                            <small class="block text-xs mt-0.5 font-medium"><i class="fas fa-phone mr-1 text-green-600"></i> {{ $parent->phone_number ?? '-' }}</small>
                        </td>
                        <td class="px-6 py-4">
                            @forelse($parent->students as $student)
                                {{-- Badge Anak Diampu (Warna Cyan) --}}
                                <span class="px-2 py-0.5 inline-block text-xs leading-5 font-bold rounded-full bg-cyan-100 text-cyan-800 mb-1 shadow-sm">
                                    {{ $student->name }} ({{ $student->class->name ?? 'N/A' }})
                                </span>
                            @empty
                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-200 text-gray-700">
                                    Tidak Ada Anak Ditautkan
                                </span>
                            @endforelse
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            {{-- Tombol Aksi (Dikonversi ke Tailwind Rounded-Full) --}}
                            <div class="inline-flex space-x-2">
                                {{-- Edit (Warning -> Amber) --}}
                                <a href="{{ route('parents.edit', $parent->id) }}" class="text-amber-700 hover:text-amber-900 p-2 rounded-full bg-amber-100 hover:bg-amber-200 transition duration-150 shadow-sm" title="Edit Data">
                                    <i class="fas fa-edit w-4 h-4"></i>
                                </a>
                                {{-- Hapus (Danger -> Red) --}}
                                <button type="button" 
                                        class="text-red-700 hover:text-red-900 p-2 rounded-full bg-red-100 hover:bg-red-200 transition duration-150 shadow-sm" 
                                        title="Hapus Akun"
                                        onclick="confirmDelete({{ $parent->id }}, '{{ $parent->name }}')">
                                    <i class="fas fa-trash w-4 h-4"></i>
                                </button>
                            </div>
                            
                            {{-- Form Hapus Tersembunyi (LOGIKA TIDAK BERUBAH) --}}
                            <form id="delete-form-{{ $parent->id }}" 
                                    action="{{ route('parents.destroy', $parent->id) }}" 
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
                            Belum ada data Orang Tua yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- PAGINASI --}}
        <div class="flex justify-between items-center mt-6">
            <small class="text-sm text-gray-600">
                Menampilkan 
                <strong class="font-bold">{{ $parents->firstItem() ?? 0 }}</strong> 
                sampai 
                <strong class="font-bold">{{ $parents->lastItem() ?? 0 }}</strong> 
                dari 
                <strong class="font-bold">{{ $parents->total() }}</strong> 
                Orang Tua
            </small>
            {{-- Menggunakan view Tailwind untuk pagination --}}
            <div class="mt-2 sm:mt-0">
                {{ $parents->appends(['search' => request('search')])->links() }}
            </div>
        </div>
    </div>
</div>
@stop

{{-- HAPUS @section('css') yang lama --}}

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // --- FUNGSI HAPUS TUNGGAL DENGAN SWEETALERT (LOGIKA TIDAK BERUBAH) ---
    function confirmDelete(id, parentName) {
        Swal.fire({
            title: 'Hapus Akun Orang Tua?',
            text: `Yakin ingin menghapus akun "${parentName}"? Ini akan memutuskan tautan ke semua anak siswa.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626', // red-600
            cancelButtonColor: '#4f46e5', // indigo-600 (Ganti dari abu-abu ke Indigo)
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById(`delete-form-${id}`);
                
                if (form) {
                    // Pastikan class d-none/hidden dihapus sebelum submit
                    form.classList.remove('d-none'); 
                    form.classList.remove('hidden'); 
                    form.submit();
                }
            }
        });
    }

    $(document).ready(function() {
        // Tampilkan notifikasi SweetAlert Toast untuk pesan sesi (LOGIKA TIDAK BERUBAH)
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
        
        // Auto-hide alerts (Menggunakan jQuery untuk alerts HTML biasa)
        setTimeout(function() {
            $('.alert').fadeOut(400, function() { $(this).remove(); });
        }, 5000);
    });
</script>
@stop