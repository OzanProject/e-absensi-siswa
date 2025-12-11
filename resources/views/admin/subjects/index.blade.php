@extends('layouts.adminlte')

@section('title', 'Kelola Mata Pelajaran')

@section('content_header')
<div class="flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-800 flex items-center">
            <i class="fas fa-book-open text-purple-600 mr-3"></i> Mata Pelajaran
        </h1>
        <p class="text-sm text-gray-500 mt-1">Kelola daftar mata pelajaran sekolah.</p>
    </div>
    <a href="{{ route('admin.subjects.create') }}" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-2 px-4 rounded-xl shadow-lg transform transition hover:-translate-y-1">
        <i class="fas fa-plus mr-2"></i> Tambah Mapel
    </a>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-indigo-100 uppercase bg-gradient-to-r from-purple-600 to-indigo-600">
                            <tr>
                                <th scope="col" class="px-6 py-4 rounded-tl-2xl">No</th>
                                <th scope="col" class="px-6 py-4">Kode</th>
                                <th scope="col" class="px-6 py-4">Nama Mata Pelajaran</th>
                                <th scope="col" class="px-6 py-4 rounded-tr-2xl text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subjects as $index => $subject)
                            <tr class="bg-white border-b hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $subjects->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="bg-gray-100 text-gray-800 text-xs font-mono font-medium px-2.5 py-0.5 rounded border border-gray-200">
                                        {{ $subject->code ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-700">
                                    {{ $subject->name }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="text-white bg-amber-400 hover:bg-amber-500 focus:ring-4 focus:ring-amber-300 font-medium rounded-lg text-sm px-3 py-2 transition focus:outline-none">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteSubject({{ $subject->id }})" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2 transition focus:outline-none">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $subject->id }}" action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic bg-gray-50">
                                    Belum ada data mata pelajaran.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $subjects->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
    function deleteSubject(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data mata pelajaran akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@stop
