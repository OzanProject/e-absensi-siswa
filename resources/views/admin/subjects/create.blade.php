@extends('layouts.adminlte')

@section('title', 'Tambah Mata Pelajaran')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight flex items-center">
            <i class="fas fa-plus-circle text-purple-600 mr-3"></i> Tambah Mata Pelajaran
        </h1>
        <p class="text-sm text-gray-500 mt-1">Tambahkan mata pelajaran baru ke dalam sistem.</p>
    </div>
    <a href="{{ route('admin.subjects.index') }}"
        class="group flex items-center px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-indigo-600 transition-all duration-200 shadow-sm">
        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Kembali
    </a>
</div>
@stop

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Form Section --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="p-8">
                {{-- Error Handling --}}
                @if ($errors->any())
                    <div
                        class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg animate__animated animate__fadeIn">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada inputan Anda:</h3>
                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.subjects.store') }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        {{-- Nama Mapel --}}
                        <div>
                            <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                                Nama Mata Pelajaran <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-xl shadow-sm group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i
                                        class="fas fa-book text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                </div>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium placeholder-gray-400"
                                    placeholder="Contoh: Matematika Wajib">
                            </div>
                        </div>

                        {{-- Kode Mapel --}}
                        <div>
                            <label for="code" class="block text-sm font-bold text-gray-700 mb-2">
                                Kode Mata Pelajaran (Opsional)
                            </label>
                            <div class="relative rounded-xl shadow-sm group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i
                                        class="fas fa-barcode text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                </div>
                                <input type="text" name="code" id="code" value="{{ old('code') }}"
                                    class="focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium placeholder-gray-400"
                                    placeholder="Contoh: MTK-001">
                            </div>
                            <p class="mt-2 text-xs text-gray-500 flex items-center">
                                <i class="fas fa-info-circle mr-1"></i> Kode unik untuk identifikasi mata pelajaran.
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                        <button type="reset"
                            class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition duration-200 shadow-sm">
                            <i class="fas fa-undo mr-2 text-gray-500"></i> Reset
                        </button>
                        <button type="submit"
                            class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-xl transform transition hover:-translate-y-1">
                            <i class="fas fa-save mr-2"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="lg:col-span-1">
        <div class="bg-indigo-50 rounded-3xl p-8 border border-indigo-100 h-full">
            <h3 class="text-lg font-bold text-indigo-900 mb-4 flex items-center">
                <div class="w-8 h-8 rounded-lg bg-indigo-200 flex items-center justify-center mr-3">
                    <i class="fas fa-lightbulb text-indigo-600"></i>
                </div>
                Informasi & Tips
            </h3>
            <div class="space-y-4">
                <div class="p-4 bg-white rounded-2xl shadow-sm border border-indigo-50">
                    <h5 class="font-bold text-gray-800 text-sm mb-1">Penamaan Mapel</h5>
                    <p class="text-gray-600 text-xs leading-relaxed">
                        Gunakan nama yang spesifik, misalnya "Bahasa Indonesia Kelas 7" jika kurikulum berbeda tiap
                        jenjang.
                    </p>
                </div>
                <div class="p-4 bg-white rounded-2xl shadow-sm border border-indigo-50">
                    <h5 class="font-bold text-gray-800 text-sm mb-1">Kode Mapel</h5>
                    <p class="text-gray-600 text-xs leading-relaxed">
                        Kode membantu saat import/export data. Gunakan format konsisten seperti
                        <code>MAPEL-NOMOR</code>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop