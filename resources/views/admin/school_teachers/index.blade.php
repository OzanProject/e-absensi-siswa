@extends('layouts.adminlte')

@section('title', 'Data Guru')

@section('content')
<div class="space-y-6">
    {{-- PAGE HEADER & BREADCRUMB --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Manajemen Data Guru</h2>
            <nav class="flex text-sm font-medium text-gray-500 space-x-2 mt-1" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}"
                    class="text-indigo-600 hover:text-indigo-800 transition">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-600">Data Guru</span>
            </nav>
        </div>
        <a href="{{ route('admin.school-teachers.create') }}"
            class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-bold rounded-xl text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5">
            <i class="fas fa-plus mr-2"></i> Tambah Guru Baru
        </a>
    </div>

    {{-- MAIN CONTENT CARD --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">

        {{-- TABLE HEADER --}}
        <div class="p-6 border-b border-gray-100 bg-gray-50/30 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-chalkboard-teacher text-purple-600 mr-2"></i> Daftar Guru Pengajar
            </h3>
        </div>

        {{-- TABLE CONTENT --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-gray-50/50 text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-100">
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4">Profil Guru</th>
                        <th class="px-6 py-4">Email / Kontak</th>
                        <th class="px-6 py-4 text-center">Status Akun</th>
                        <th class="px-6 py-4 text-center">Bergabung Sejak</th>
                        <th class="px-6 py-4 text-center w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($teachers as $index => $teacher)
                        <tr class="hover:bg-gray-50/50 transition duration-150 group">
                            <td class="px-6 py-4 text-center font-medium text-gray-500">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center text-indigo-700 font-bold text-sm shadow-sm border border-white ring-2 ring-indigo-50">
                                        {{ substr($teacher->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $teacher->name }}</div>
                                        <div class="text-xs text-gray-400">NIP: -</div> {{-- Placeholder jika ada NIP nanti
                                        --}}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="bg-gray-50 text-gray-600 px-2 py-1 rounded text-xs font-mono border border-gray-200">
                                    {{ $teacher->email }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($teacher->is_approved)
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                        <i class="fas fa-check-circle mr-1"></i> Aktif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                        <i class="fas fa-times-circle mr-1"></i> Non-Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-500">
                                {{ $teacher->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center items-center space-x-2">
                                    <a href="{{ route('admin.school-teachers.edit', $teacher->id) }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 hover:scale-110 transition shadow-sm border border-amber-200"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button onclick="confirmDelete('{{ $teacher->id }}', '{{ $teacher->name }}')"
                                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-100 hover:scale-110 transition shadow-sm border border-red-200"
                                        title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>

                                    <form id="delete-form-{{ $teacher->id }}"
                                        action="{{ route('admin.school-teachers.destroy', $teacher->id) }}" method="POST"
                                        class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-50 rounded-full p-6 mb-4">
                                        <i class="fas fa-chalkboard-teacher text-gray-300 text-4xl"></i>
                                    </div>
                                    <h4 class="text-gray-900 font-bold text-lg">Belum ada data guru</h4>
                                    <p class="text-gray-500 text-sm mt-1 max-w-xs mx-auto">Mulai tambahkan guru untuk
                                        mengelola jadwal pelajaran dan kelas.</p>
                                    <a href="{{ route('admin.school-teachers.create') }}"
                                        class="mt-4 text-indigo-600 font-bold hover:underline">
                                        Tambahkan Guru Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER INFO --}}
        <div
            class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-between items-center text-xs text-gray-500">
            <span>Menampilkan {{ $teachers->count() }} guru terdaftar</span>
            <span>Terakhir diperbarui: {{ date('d M Y H:i') }}</span>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Guru?',
            text: "Anda akan menghapus guru " + name + ". Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}',
        });
    @endif
</script>
@stop