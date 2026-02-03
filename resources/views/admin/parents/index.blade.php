@extends('layouts.adminlte')

@section('title', 'Manajemen Data Orang Tua')

@section('content_header')
{{-- HEADER: Premium UI dengan Gradient Text --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div class="mb-3 sm:mb-0">
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-users-cog text-purple-600 mr-3"></i>
            Manajemen Orang Tua
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Kelola data akun orang tua dan relasi dengan siswa.</p>
    </div>
    
    <nav class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150"><i class="fas fa-home"></i></a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-bold">Data Orang Tua</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
<div class="container-fluid px-0">
    
    {{-- STATS SUMMARY (Opsional, menambah kesan premium) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl p-5 text-white shadow-lg relative overflow-hidden">
             <div class="relative z-10">
                 <h4 class="text-3xl font-bold">{{ $parents->total() }}</h4>
                 <p class="text-purple-100 text-sm font-medium">Total Akun Orang Tua</p>
             </div>
             <i class="fas fa-users absolute right-0 bottom-0 opacity-20 text-6xl transform translate-x-2 translate-y-2"></i>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-lg border border-purple-100 flex items-center space-x-4">
             <div class="bg-green-100 p-3 rounded-xl text-green-600">
                 <i class="fas fa-user-check text-xl"></i>
             </div>
             <div>
                 <p class="text-xs text-gray-500 uppercase font-bold tracking-wide">Status Aktif</p>
                 <h4 class="text-xl font-bold text-gray-800">100%</h4>
             </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-lg border border-purple-100 flex items-center space-x-4">
             <div class="bg-blue-100 p-3 rounded-xl text-blue-600">
                 <i class="fas fa-child text-xl"></i>
             </div>
             <div>
                 <p class="text-xs text-gray-500 uppercase font-bold tracking-wide">Relasi Siswa</p>
                 <h4 class="text-xl font-bold text-gray-800">Terpantau</h4>
             </div>
        </div>
    </div>

    {{-- CARD UTAMA --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
    
        {{-- TOOLBAR --}}
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/50">
            
            {{-- SELECT & BULK ACTIONS --}}
            <div id="bulk-action-container" class="hidden flex items-center space-x-2 w-full md:w-auto p-2 bg-red-50 rounded-xl border border-red-100">
                <span id="selected-count" class="text-xs font-bold text-red-700">0 Dipilih</span>
                <button type="button" onclick="confirmBulkDelete()" 
                   class="inline-flex justify-center items-center px-3 py-1.5 border border-transparent text-xs font-bold rounded-lg text-white bg-red-600 hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash-alt mr-1"></i> Hapus Masal
                </button>
                <form id="bulk-delete-form" action="{{ route('parents.bulkDelete') }}" method="POST" class="hidden">
                    @csrf @method('DELETE')
                    <input type="hidden" name="selected_parents" id="selected-parents-input">
                </form>
            </div>

            {{-- SEARCH --}}
            <form action="{{ route('parents.index') }}" method="GET" class="relative w-full md:w-96 group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                </div>
                <input type="text" name="search" 
                       class="block w-full pl-10 pr-4 py-3 border-gray-200 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 transition duration-150 ease-in-out sm:text-sm shadow-sm" 
                       placeholder="Cari Nama, HP, atau Nama Anak..." value="{{ request('search') }}">
            </form>

            {{-- ACTIONS (Normal) --}}
            <div id="normal-actions" class="flex items-center space-x-3 w-full md:w-auto">
                {{-- Template --}}
                <a href="{{ route('parents.template') }}" 
                   class="inline-flex justify-center items-center px-4 py-3 border border-gray-200 text-sm font-bold rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 shadow-sm transition-all duration-200">
                    <i class="fas fa-file-excel text-green-600 mr-2"></i> Template
                </a>
                
                {{-- Import Trigger --}}
                <button type="button" onclick="openImportModal()" 
                   class="inline-flex justify-center items-center px-4 py-3 border border-transparent text-sm font-bold rounded-xl text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-md transform hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-file-upload mr-2"></i> Import Excel
                </button>

                {{-- Create --}}
                <a href="{{ route('parents.create') }}" 
                   class="flex-1 md:flex-none inline-flex justify-center items-center px-6 py-3 border border-transparent text-sm font-bold rounded-xl text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 shadow-md transform hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i> Tambah Orang Tua
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-center w-10">
                             <input type="checkbox" id="check-all" class="rounded text-purple-600 border-gray-300 focus:ring-purple-500">
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Orang Tua
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Kontak & Login
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Siswa (Anak)
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-32">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($parents as $parent)
                    <tr class="hover:bg-purple-50/30 transition-colors duration-150 group">
                        
                        {{-- Checkbox --}}
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" name="selected_parents[]" value="{{ $parent->id }}" class="bulk-checkbox rounded text-purple-600 border-gray-300 focus:ring-purple-500">
                        </td>

                        {{-- Nama & Status --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-lg shadow-inner">
                                        {{ substr($parent->name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900 group-hover:text-purple-700 transition-colors">{{ $parent->name }}</div>
                                    <div class="text-xs text-gray-500">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $parent->relation_status ?? 'Wali' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Kontak --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 flex items-center mb-1">
                                <i class="fas fa-envelope w-4 text-gray-400 mr-2"></i> {{ $parent->user->email ?? '-' }}
                            </div>
                            <div class="text-sm text-gray-500 flex items-center">
                                <i class="fas fa-phone w-4 text-green-500 mr-2"></i> {{ $parent->phone_number ?? '-' }}
                            </div>
                        </td>

                        {{-- Siswa Diampu --}}
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                @forelse($parent->students as $student)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ $student->name }}
                                        <span class="ml-1 text-blue-400 text-[10px]">({{ $student->class->name ?? '?' }})</span>
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400 italic">Belum ada siswa ditautkan</span>
                                @endforelse
                            </div>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                {{-- Edit --}}
                                <a href="{{ route('parents.edit', $parent->id) }}" 
                                   class="text-amber-500 hover:text-white hover:bg-amber-500 p-2 rounded-lg transition-all duration-200 border border-amber-200 hover:border-amber-500 group-hover:shadow-sm" 
                                   title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Delete --}}
                                <button type="button" 
                                        onclick="confirmDelete({{ $parent->id }}, '{{ $parent->name }}')"
                                        class="text-red-500 hover:text-white hover:bg-red-500 p-2 rounded-lg transition-all duration-200 border border-red-200 hover:border-red-500 group-hover:shadow-sm"
                                        title="Hapus Akun">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>

                            {{-- Form Hapus --}}
                            <form id="delete-form-{{ $parent->id }}" action="{{ route('parents.destroy', $parent->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gray-50 rounded-full p-6 mb-4">
                                    <i class="fas fa-user-friends text-gray-300 text-5xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Belum Ada Data Orang Tua</h3>
                                <p class="text-gray-500 mt-1 mb-4 max-w-sm">Data orang tua yang Anda tambahkan akan muncul di sini.</p>
                                <a href="{{ route('parents.create') }}" class="text-purple-600 font-bold hover:underline">
                                    <i class="fas fa-plus mr-1"></i> Tambah Baru Sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($parents->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
            {{ $parents->appends(['search' => request('search')])->links() }}
        </div>
        @endif
        
    </div>
</div>

{{-- MODAL IMPORT --}}
<div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeImportModal()"></div>

        {{-- Center trick --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal Panel --}}
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form action="{{ route('parents.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-file-excel text-green-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Import Data Orang Tua
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4">
                                    Upload file Excel (.xlsx) sesuai template. Sistem akan otomatis membuat akun User dan menautkan ke Siswa berdasarkan NISN.
                                </p>
                                
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:bg-gray-50 transition-colors bg-gray-50">
                                    <div class="space-y-1 text-center">
                                        <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-3"></i>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Upload a file</span>
                                                <input id="file-upload" name="file" type="file" class="sr-only" accept=".xlsx, .xls, .csv" required>
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            XLSX, XLS up to 10MB
                                        </p>
                                        <p id="filename-display" class="text-xs text-green-600 font-bold mt-2 hidden"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Import Data
                    </button>
                    <button type="button" onclick="closeImportModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Modal Functions
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }
    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    // File Name Display
    document.getElementById('file-upload').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        const display = document.getElementById('filename-display');
        if (fileName) {
            display.textContent = 'Selected: ' + fileName;
            display.classList.remove('hidden');
        } else {
            display.classList.add('hidden');
        }
    });

    // Konstanta Warna SweetAlert (Sesuaikan dengan Tema)
    const SWAL_COLOR = {
        confirm: '#6366f1', // Indigo 500
        cancel: '#9ca3af',  // Gray 400
        danger: '#ef4444',   // Red 500
        success: '#10b981'
    };

    // ---------- 1. CHECKBOX & BULK UI LOGIC ----------
    document.addEventListener('DOMContentLoaded', () => {
        const checkAll = document.getElementById('check-all');
        const checkboxes = document.querySelectorAll('.bulk-checkbox');
        const bulkContainer = document.getElementById('bulk-action-container');
        const normalActions = document.getElementById('normal-actions');
        const selectedCountLabel = document.getElementById('selected-count');
        const selectedInput = document.getElementById('selected-parents-input');

        function updateBulkUI() {
            const selected = Array.from(document.querySelectorAll('.bulk-checkbox:checked')).map(cb => cb.value);
            const count = selected.length;

            if (count > 0) {
                bulkContainer.classList.remove('hidden');
                normalActions.classList.add('hidden'); // Sembunyikan aksi normal agar tidak penuh
                selectedCountLabel.textContent = `${count} Dipilih`;
                selectedInput.value = selected.join(',');
            } else {
                bulkContainer.classList.add('hidden');
                normalActions.classList.remove('hidden');
                selectedInput.value = '';
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', () => {
                checkboxes.forEach(cb => cb.checked = checkAll.checked);
                updateBulkUI();
            });
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateBulkUI));
    });

    // ---------- 2. ACTIONS ----------

    // Hapus Masal
    function confirmBulkDelete() {
        const count = document.querySelectorAll('.bulk-checkbox:checked').length;
        if (count === 0) return;

        Swal.fire({
            title: `Hapus ${count} Data Orang Tua?`,
            text: "Aksi ini akan menghapus akun login dan melepaskan relasi siswa secara permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: SWAL_COLOR.danger,
            cancelButtonColor: SWAL_COLOR.cancel,
            confirmButtonText: 'Ya, Hapus Semua',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('bulk-delete-form').submit();
            }
        });
    }

    // Fungsi Hapus Global (Single)
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Akun Orang Tua?',
            text: `Yakin ingin menghapus data "${name}"? Akses login dan relasi siswa akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: SWAL_COLOR.danger,
            cancelButtonColor: SWAL_COLOR.cancel,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById(`delete-form-${id}`);
                if (form) form.submit();
            }
        });
    }

    // DIRECT SCRIPT INJECTION UNTUK ALERT SESSION
    @if(session('success'))
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{!! session('success') !!}",
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    background: '#fff',
                    iconColor: '#10b981'
                });
            }, 300); // Delay sedikit agar render stabil
        });
    @endif

    @if(session('error'))
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{!! session('error') !!}",
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                });
            }, 300);
        });
    @endif
</script>
@stop