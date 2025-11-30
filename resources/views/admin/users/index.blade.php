@extends('layouts.adminlte')

@section('title', 'Manajemen Pengguna Sistem')

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-users-cog text-indigo-600 mr-2"></i>
        <span>Manajemen Pengguna Sistem</span>
    </h1>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Pengguna</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
@php
    // Asumsi: $users (paginated), $tab (current tab), etc.
    $currentTab = $tab ?? 'all'; 
@endphp

<div class="bg-white rounded-xl shadow-lg border border-gray-100">
    
    {{-- CARD HEADER: Judul, Count & Search --}}
    <div class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between items-start lg:items-center">
        <h3 class="text-xl font-bold text-gray-800 flex items-center mb-3 lg:mb-0">
            <i class="fas fa-list-alt mr-2 text-indigo-600"></i> Daftar Akun Sistem
            <span class="ml-3 text-sm font-bold bg-indigo-600 text-white px-3 py-1 rounded-full shadow-md">{{ $users->total() }}</span>
        </h3>
        
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 items-center w-full lg:w-auto">
            
            {{-- Form Pencarian --}}
            <form action="{{ route('admin.users.index') }}" method="GET" class="flex w-full sm:w-64">
                <input type="hidden" name="tab" value="{{ $currentTab }}">
                <input type="search" name="search" 
                        class="px-3 py-2 border border-gray-300 rounded-l-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm w-full transition duration-150" 
                        placeholder="Cari Nama/Email..." value="{{ request('search') }}">
                <button type="submit" class="bg-indigo-600 text-white p-2.5 rounded-r-lg hover:bg-indigo-700 transition duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            
            {{-- Tombol Tambah --}}
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-bold rounded-lg shadow-md 
                    text-white bg-green-600 hover:bg-green-700 transition duration-150 w-full sm:w-auto transform hover:-translate-y-0.5">
                <i class="fas fa-user-plus mr-1"></i> Tambah Pengguna Baru
            </a>
        </div>
    </div>

    <div class="p-5">
        
        {{-- Tabs Navigasi --}}
        <div class="flex border-b border-gray-200 mb-6 space-x-4 overflow-x-auto">
            @php
                $tabClasses = 'pb-3 px-1 text-sm font-semibold transition duration-150 whitespace-nowrap';
                $activeTabClasses = 'text-indigo-600 border-b-2 border-indigo-600';
            @endphp

            <a class="{{ $tabClasses }} {{ $currentTab === 'all' ? $activeTabClasses : 'text-gray-500 hover:text-indigo-600' }}" 
               href="{{ route('admin.users.index', ['tab' => 'all', 'search' => request('search')]) }}">
                <i class="fas fa-users mr-1"></i> Semua Pengguna
            </a>
            <a class="{{ $tabClasses }} {{ $currentTab === 'pending' ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-500 hover:text-red-600' }}" 
               href="{{ route('admin.users.index', ['tab' => 'pending', 'search' => request('search')]) }}">
                <i class="fas fa-hourglass-half mr-1"></i> Menunggu Persetujuan
            </a>
            <a class="{{ $tabClasses }} {{ $currentTab === 'super_admin_list' ? $activeTabClasses : 'text-gray-500 hover:text-indigo-600' }}" 
               href="{{ route('admin.users.index', ['tab' => 'super_admin_list', 'search' => request('search')]) }}">
                <i class="fas fa-user-shield mr-1"></i> Super Admin
            </a>
        </div>

        {{-- Form Aksi Massal (Target untuk JS, method diubah di JS) --}}
        <form id="bulk-action-form" method="POST" class="hidden"> 
            @csrf
            {{-- Method dan data akan diisi oleh JavaScript --}}
        </form>

        @if($currentTab !== 'super_admin_list')
        {{-- Tombol Aksi Massal (Styling Tailwind) --}}
        <div class="flex flex-wrap items-center mb-4 gap-2">
            
            {{-- Tombol Setujui Massal --}}
            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-green-600 hover:bg-green-700 transition duration-150 transform hover:-translate-y-0.5" 
                    onclick="confirmBulkApprove()">
                <i class="fas fa-check-double mr-1"></i> Setujui Massal
            </button>
            
            {{-- Tombol Ubah Status Massal --}}
            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-gray-800 bg-amber-400 hover:bg-amber-500 transition duration-150 transform hover:-translate-y-0.5" 
                    onclick="confirmBulkToggle()">
                <i class="fas fa-toggle-on mr-1"></i> Ubah Status Massal
            </button>
            
            {{-- Tombol Hapus Massal --}}
            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-red-600 hover:bg-red-700 transition duration-150 transform hover:-translate-y-0.5" 
                    onclick="confirmBulkDelete()">
                <i class="fas fa-trash-alt mr-1"></i> Hapus Massal
            </button>
            
            <span id="selected-count" class="ml-4 text-sm text-indigo-600 font-semibold" style="display:none;"></span>
        </div>
        @endif 

        {{-- Wrapper Tabel (Scroll Horizontal) --}}
        <div class="overflow-x-auto shadow-sm border border-gray-200 rounded-lg"> 
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider w-8">
                            @if($currentTab !== 'super_admin_list')
                            <input type="checkbox" id="check-all" class="rounded text-indigo-300 bg-indigo-700 border-indigo-700 focus:ring-indigo-300 focus:ring-offset-0">
                            @endif
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wider w-12">No.</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider min-w-36">Nama Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider min-w-48">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider min-w-24">Peran</th>
                        <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider w-24">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-indigo-50/20 transition duration-150">
                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm">
                            @if(!$user->isSuperAdmin())
                            <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" class="bulk-checkbox rounded text-indigo-500 focus:ring-indigo-500">
                            @endif
                        </td>
                        {{-- Penomoran Berurutan --}}
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">{{ $loop->iteration + $users->firstItem() - 1 }}</td> 
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <strong class="text-gray-900">{{ $user->name }}</strong>
                            <span class="block text-xs text-gray-500">Bergabung: {{ $user->created_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $roleClassMap = [
                                    'super_admin' => 'bg-indigo-600 text-white',
                                    'wali_kelas' => 'bg-purple-100 text-purple-800',
                                    'orang_tua' => 'bg-orange-100 text-orange-800',
                                ];
                                $badgeClass = $roleClassMap[$user->role] ?? 'bg-gray-300 text-gray-800';
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">{{ $user->role_label }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($user->isSuperAdmin())
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-lock mr-1"></i> Aktif</span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $user->is_approved ? 'green' : 'yellow' }}-100 text-{{ $user->is_approved ? 'green' : 'yellow' }}-800">{{ $user->is_approved ? 'Disetujui' : 'Menunggu' }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if(!$user->isSuperAdmin())
                            <div class="inline-flex space-x-2">
                                {{-- Tombol Toggle Approval --}}
                                <button title="{{ $user->is_approved ? 'Tangguhkan Akun' : 'Setujui Akun' }}" 
                                    type="button" 
                                    class="p-2 rounded-full shadow-md transition duration-150 transform hover:scale-110 
                                           {{ $user->is_approved ? 'text-red-600 bg-red-50 hover:bg-red-100' : 'text-green-600 bg-green-50 hover:bg-green-100' }} js-toggle-approval" 
                                    data-user-id="{{ $user->id }}">
                                    <i class="fas fa-user-{{ $user->is_approved ? 'slash' : 'check' }} w-4 h-4"></i>
                                </button>

                                {{-- Tombol Edit --}}
                                <a title="Edit Akun" href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="p-2 rounded-full shadow-md text-amber-600 bg-amber-50 hover:bg-amber-100 transition duration-150 transform hover:scale-110">
                                    <i class="fas fa-edit w-4 h-4"></i>
                                </a>

                                {{-- Tombol Hapus --}}
                                <button title="Hapus Akun" type="button" 
                                        class="p-2 rounded-full shadow-md text-red-600 bg-red-50 hover:bg-red-100 transition duration-150 transform hover:scale-110" 
                                        onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                                    <i class="fas fa-trash w-4 h-4"></i>
                                </button>
                            </div>
                            @endif
                        </td>
                    </tr>
                    
                    {{-- Formulir Tersembunyi (Template per baris) --}}
                    <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="hidden">
                        @csrf 
                        @method('DELETE') 
                    </form>
                    <form id="toggle-form-{{ $user->id }}" action="{{ route('admin.users.toggleApproval', $user->id) }}" method="POST" class="hidden">@csrf @method('PUT')</form>
                    
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-user-slash fa-3x mb-3 block text-gray-300"></i> Tidak ada pengguna dalam kategori ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex justify-between items-center">
            <div class="text-sm text-gray-600">
                Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} akun
            </div>
            {{-- Pagination Links --}}
            <div class="mt-2 sm:mt-0">
                {{ $users->appends(['tab' => $currentTab, 'search' => request('search')])->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>

{{-- FORM PENGHAPUSAN DAN TOGGLE INDIVIDUAL (Template di luar tabel) --}}
<form id="individual-delete-form" action="" method="POST" class="hidden">
    @csrf 
    @method('DELETE') 
</form>
<form id="individual-toggle-form" action="" method="POST" class="hidden">
    @csrf 
    @method('PUT') 
</form>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
/* =======================================================
   1. UTILITY FUNCTIONS
   ======================================================= */
function getSelectedIds() {
    // Mendapatkan ID dari semua checkbox yang tercentang
    return Array.from(document.querySelectorAll('.bulk-checkbox:checked')).map(cb => cb.value);
}
function updateSelectedCount() {
    // Memperbarui tampilan jumlah item yang dipilih
    const count = getSelectedIds().length;
    const countDisplay = document.getElementById('selected-count');
    if (countDisplay) {
        if (count > 0) {
            countDisplay.textContent = `(${count} dipilih)`;
            countDisplay.style.display = 'inline';
        } else {
            countDisplay.style.display = 'none';
        }
    }
}

/* =======================================================
   2. CHECKBOX LOGIC & TOGGLE INDIVIDUAL
   ======================================================= */
document.addEventListener('DOMContentLoaded', () => {
    // Logic Checkbox Massal
    const checkAll = document.getElementById('check-all');
    const checkboxes = document.querySelectorAll('.bulk-checkbox');

    if (checkAll) {
        checkAll.addEventListener('change', () => {
            checkboxes.forEach(cb => {
                if (!cb.disabled) {
                    cb.checked = checkAll.checked;
                }
            });
            updateSelectedCount();
        });
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateSelectedCount));
    updateSelectedCount(); 
    
    // Logic Toggle Approval Individu (PUT) - LOGIKA AMAN
    $('.js-toggle-approval').on('click', function() {
        const userId = $(this).data('userId'); 
        const form = document.getElementById('individual-toggle-form'); // Target form template
        
        if (form) {
            // Konfirmasi sebelum submit
            Swal.fire({
                title: 'Ubah Status Persetujuan?',
                text: 'Status persetujuan akun ini akan dibalikkan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan'
            }).then((result) => {
                if (result.isConfirmed) {
                    // 🔥 KUNCI LOGIKA AMAN: Set action dan method PUT sebelum submit
                    form.action = '{{ url('admin/users') }}/' + userId + '/toggle-approval'; 
                    form.submit();
                }
            });
        }
    });
});


/* =======================================================
   3. SWEETALERT CONFIRMATIONS & ACTIONS
   ======================================================= */

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Akun?',
        text: `Yakin ingin menghapus akun "${name}"? Tindakan ini tidak dapat dikembalikan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33' // Red
    }).then(result => {
        if (result.isConfirmed) {
            // 🔥 KUNCI: Pemicuan submit menggunakan form template individu
            const form = document.getElementById('individual-delete-form'); 

            if (form) {
                // Set action yang spesifik
                form.action = '{{ url('admin/users') }}/' + id; 
                form.submit(); 
            }
        }
    });
}

// Handler untuk Tombol Bulk Actions (Menggunakan method yang benar di performBulkSubmission)
function confirmBulkApprove() {
    // Route: admin.users.bulkApprove (Method: GET)
    submitBulkAction('{{ route('admin.users.bulkApprove') }}', 'setujui');
}

function confirmBulkDelete() {
    // Route: admin.users.bulkDelete (Method: GET)
    submitBulkAction('{{ route('admin.users.bulkDelete') }}', 'hapus');
}

function confirmBulkToggle() {
    // Route: admin.users.bulkToggleApproval (Method: GET)
    submitBulkAction('{{ route('admin.users.bulkToggleApproval') }}', 'ubah status');
}


// Fungsi inti untuk memicu SweetAlert dan mengirim data (Bulk) - LOGIKA AMAN
function submitBulkAction(actionRoute, actionType) {
    const ids = getSelectedIds();
    
    if (!ids.length) {
        return Swal.fire('Perhatian', `Pilih minimal satu akun untuk di${actionType}.`, 'warning');
    }

    let title, icon, confirmButtonText, confirmButtonColor, method;

    // KUNCI PERBAIKAN: Selalu gunakan GET karena route Anda GET
    method = 'GET';

    if (actionType === 'hapus') {
        title = `Hapus ${ids.length} Pengguna?`;
        icon = 'warning';
        confirmButtonText = 'Ya, Hapus Permanen!';
        confirmButtonColor = '#d33';
    } else if (actionType === 'setujui') {
        title = `Setujui ${ids.length} Akun?`;
        icon = 'question';
        confirmButtonText = 'Ya, Setujui!';
        confirmButtonColor = '#28a745';
    } else if (actionType === 'ubah status') {
        title = `Ubah Status ${ids.length} Pengguna?`;
        icon = 'question';
        confirmButtonText = 'Ya, Ubah Status';
        confirmButtonColor = '#ffc107';

        // Opsi untuk Ubah Status (Select)
        return Swal.fire({
            title: title,
            text: `Pilih status baru untuk ${ids.length} pengguna:`,
            input: 'select',
            inputOptions: {
                'active': 'Aktifkan (Setujui)',
                'inactive': 'Nonaktifkan',
                'pending': 'Menunggu Persetujuan'
            },
            inputPlaceholder: 'Pilih status baru',
            showCancelButton: true,
            confirmButtonColor: confirmButtonColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmButtonText,
            preConfirm: (value) => {
                if (!value) {
                    Swal.showValidationMessage('Anda harus memilih status baru!');
                }
                return value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Panggil proses pengiriman setelah mendapatkan nilai status
                // Menggunakan method GET untuk BulkToggleApproval
                performBulkSubmission(actionRoute, ids, 'bulkToggleApproval', result.value, 'GET'); 
            }
        });
    }
    
    // Konfirmasi Default (Hapus/Setujui Massal)
    Swal.fire({
        title: title,
        text: `Anda akan melakukan aksi pada ${ids.length} pengguna terpilih.`,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: confirmButtonColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            // Menggunakan method GET untuk BulkApprove dan BulkDelete
            performBulkSubmission(actionRoute, ids, actionType, null, method);
        }
    });
}

// 🔥 FUNGSI BARU: Mengirim data ke controller (LOGIKA AMAN)
// Menggunakan Form GET secara eksplisit untuk mengirim data array IDs
function performBulkSubmission(actionRoute, ids, actionType, statusValue = null, method) {
    const form = document.getElementById('bulk-action-form');
    const $form = $(form);
    
    // Set Action dan Method (Menggunakan GET karena route Anda GET)
    $form.attr('action', actionRoute);
    $form.attr('method', 'GET'); // Set method ke GET
    
    // Hapus input lama kecuali CSRF (di sini kita buang semua kecuali ID)
    $form.empty(); 

    // Tambahkan ID yang Dipilih sebagai array di URL Query
    ids.forEach(id => {
        $form.append('<input type="hidden" name="selected_users[]" value="' + id + '">');
    });

    // Tambahkan Status Value jika ada
    if (statusValue) {
        $form.append('<input type="hidden" name="status" value="' + statusValue + '">');
    }
    
    // Submit (menggunakan submit native untuk memastikan format GET array yang benar)
    $form.removeClass('hidden').submit();
}


/* =======================================================
   4. TOAST MESSAGE (SweetAlert)
   ======================================================= */
document.addEventListener('DOMContentLoaded', () => {
    // Logic untuk menampilkan notifikasi sukses/gagal
    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
    @endif
    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Gagal!', text: '{{ session('error') }}', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
    @endif
});

// Auto-hide alerts (JQuery)
$(document).ready(function() {
    // Refresh count saat DOM siap
    updateSelectedCount();
    
    // Auto hide alerts HTML biasa
    setTimeout(function() {
        $('.alert').fadeOut(400, function() { $(this).remove(); });
    }, 5000);
});
</script>
@endsection

@section('css')
<style>
/* --- MINIMAL CUSTOM CSS UNTUK KOMPATIBILITAS DAN BADGE --- */
.bg-indigo-600 { background-color: #4f46e5 !important; }
.text-indigo-600 { color: #4f46e5; }
.bg-purple-100 { background-color: #f3e8ff; }
.text-purple-800 { color: #6b21a8; }
.bg-orange-100 { background-color: #fff7ed; }
.text-orange-800 { color: #9a3412; }
.bg-gray-800 { background-color: #1f2937 !important; }

/* FIXES */
.d-block { display: block !important; }
.d-none { display: none !important; } 
</style>
@endsection