@extends('layouts.adminlte')

@section('title', 'Tambah Kelas Baru')

@section('content_header')
{{-- HEADER DENGAN AKSE WARNA INDIGO --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-plus text-indigo-600 mr-2"></i>
        <span>Tambah Kelas Baru</span>
    </h1>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('classes.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Data Kelas</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Tambah Kelas</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    {{-- Tata Letak Grid (2/3 dan 1/3) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-6"> 
        
        {{-- KOLOM KIRI: FORM UTAMA (2/3 Kolom) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100" id="classFormCard">
                
                <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chalkboard mr-2 text-indigo-500"></i> Form Tambah Kelas
                    </h3>
                    <div class="flex-shrink-0">
                        <a href="{{ route('classes.index') }}" 
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-lg 
                                     shadow-sm text-gray-700 bg-white hover:bg-gray-100 transition duration-150 transform hover:scale-[1.02]">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="p-6"> {{-- Padding lebih besar --}}
                    <form action="{{ route('classes.store') }}" method="POST" id="classForm">
                        @csrf
                        
                        {{-- 💡 Helper yang Disederhanakan untuk Input Styling --}}
                        @php
                            // Base class: Styling umum (padding, shadow, rounded)
                            $baseInputClass = 'w-full px-3 py-2 rounded-lg shadow-sm focus:outline-none transition duration-150';
                            
                            // Kelas Normal (Border + Focus Normal)
                            $normalClass = 'border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500';
                            
                            // Kelas Error (Border + Focus Error)
                            $errorClass = 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-red-500';
                        @endphp

                        {{-- Nama Kelas --}}
                        <div class="mb-5">
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">
                                Nama Kelas <span class="text-red-600">*</span>
                            </label>
                            @php $nameStatusClass = $errors->has('name') ? $errorClass : $normalClass; @endphp
                            <input type="text"
                                name="name"
                                id="name"
                                {{-- Menggabungkan base class, border-width (border), dan status class --}}
                                class="{{ $baseInputClass }} border {{ $nameStatusClass }}"
                                value="{{ old('name') }}"
                                placeholder="Contoh: 7A, X RPL 1, Kelas 4"
                                required
                                autofocus>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                            <small class="mt-1 text-xs text-gray-500 block">
                                Nama Kelas yang unik (Contoh: X RPL 1, XI AKL 2, 7A).
                            </small>
                        </div>

                        {{-- Tingkat dan Jurusan --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6"> {{-- Gap lebih besar --}}
                            
                            {{-- Tingkat / Kelas --}}
                            <div class="mb-5">
                                <label for="grade" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Tingkat / Kelas <span class="text-red-600">*</span>
                                </label>
                                @php $gradeStatusClass = $errors->has('grade') ? $errorClass : $normalClass; @endphp
                                <select name="grade" 
                                    id="grade" 
                                    {{-- Kelas Select2 diubah, tambahkan border --}}
                                    class="select2-form-control border {{ $gradeStatusClass }}" 
                                    required>
                                    <option value="">Pilih Tingkat (1-12)</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('grade') == $i ? 'selected' : '' }}>
                                            Kelas {{ $i }} 
                                            @if ($i <= 6) (SD) @elseif ($i <= 9) (SMP) @else (SMA/SMK) @endif
                                        </option>
                                    @endfor
                                </select>
                                @error('grade')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Jurusan/Bagian --}}
                            <div class="mb-5">
                                <label for="major" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Jurusan/Bagian
                                </label>
                                @php $majorStatusClass = $errors->has('major') ? $errorClass : $normalClass; @endphp
                                <input type="text"
                                    name="major"
                                    id="major"
                                    {{-- Menggabungkan base class, border-width (border), dan status class --}}
                                    class="{{ $baseInputClass }} border {{ $majorStatusClass }}"
                                    value="{{ old('major') }}"
                                    placeholder="Contoh: RPL, TKJ, MM">
                                @error('major')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                                @enderror
                                <small class="mt-1 text-xs text-gray-500 block">
                                    Kosongkan jika tidak ada jurusan (SD/SMP).
                                </small>
                            </div>
                        </div>

                        {{-- Keterangan --}}
                        <div class="mb-5">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                            @php $descStatusClass = $errors->has('description') ? $errorClass : $normalClass; @endphp
                            <textarea name="description"
                                id="description"
                                {{-- Menggabungkan base class, border-width (border), dan status class --}}
                                class="{{ $baseInputClass }} border {{ $descStatusClass }}"
                                rows="3"
                                placeholder="Keterangan tambahan tentang kelas...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="mt-6 flex space-x-3">
                            <button type="submit" 
                                    class="inline-flex items-center px-5 py-2.5 border border-transparent text-base font-bold rounded-lg shadow-md 
                                            text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-green-500/50 
                                            transition duration-150 transform hover:-translate-y-0.5" 
                                    id="submitBtn">
                                <i class="fas fa-save mr-2"></i> Simpan Data
                            </button>
                            <a href="{{ route('classes.index') }}" 
                               class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-base font-medium rounded-lg 
                                      text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                                <i class="fas fa-times mr-2"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: INFORMASI & STATISTIK (1/3 Kolom) --}}
        <div class="lg:col-span-1 mt-6 lg:mt-0">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-indigo-500"></i>
                        Informasi & Panduan
                    </h3>
                </div>
                
                <div class="p-6">
                    
                    {{-- Tips Card --}}
                    <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-6 text-indigo-700 rounded-lg">
                        <h6 class="text-base font-bold mb-2 flex items-center">
                            <i class="fas fa-lightbulb mr-2"></i>Tips Panduan:
                        </h6>
                        <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                            <li>**Tingkat 1-6:** Jenjang Sekolah Dasar (SD).</li>
                            <li>**Tingkat 7-9:** Jenjang Sekolah Menengah Pertama (SMP).</li>
                            <li>**Tingkat 10-12:** Jenjang Sekolah Menengah Atas/Kejuruan (SMA/SMK).</li>
                            <li>Nama Kelas harus **unik** di seluruh sistem.</li>
                        </ul>
                    </div>

                    {{-- Statistik Card --}}
                    <small class="text-xs text-gray-500 font-bold block mb-3 border-b pb-1 uppercase tracking-wider">Statistik Kelas Saat Ini:</small>
                    
                    <div class="space-y-3">
                        @php
                            // Catatan: Model ClassModel harus sudah di-import di atas (Controller/Master Layout)
                            // Jika belum, pastikan Anda menggunakan namespace lengkap: \App\Models\ClassModel::count()
                            $totalClass = \App\Models\ClassModel::count();
                            $totalSD = \App\Models\ClassModel::whereBetween('grade', [1, 6])->count();
                            $totalSMP = \App\Models\ClassModel::whereBetween('grade', [7, 9])->count();
                            $totalSMASMK = \App\Models\ClassModel::whereBetween('grade', [10, 12])->count();
                            
                            $stats = [
                                ['Total Kelas', $totalClass, 'fas fa-graduation-cap', 'indigo'],
                                ['Kelas SD (1-6)', $totalSD, 'fas fa-child', 'green'],
                                ['Kelas SMP (7-9)', $totalSMP, 'fas fa-users', 'cyan'],
                                ['Kelas SMA/SMK (10-12)', $totalSMASMK, 'fas fa-university', 'orange'],
                            ];
                        @endphp

                        @foreach($stats as $stat)
                        <div class="flex justify-between items-center text-sm p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition duration-150">
                            <span class="flex items-center text-gray-600">
                                <i class="fas {{ $stat[2] }} mr-2 w-4 text-{{ $stat[3] }}-500"></i> {{ $stat[0] }}:
                            </span>
                            <strong class="font-bold text-lg text-gray-900">{{ $stat[1] }}</strong>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
$(document).ready(function() {
    
    // Inisialisasi Select2
    $('#grade').select2({
        theme: 'bootstrap4',
        placeholder: 'Pilih Tingkat (1-12)',
        allowClear: true,
        width: '100%' 
    });

    // FUNGSI SUBMIT LOADING STATE
    $('#classForm').on('submit', function() {
        const submitBtn = $('#submitBtn');
        // Tambahkan efek hover/loading
        submitBtn.prop('disabled', true).addClass('transform transition duration-150 ease-in-out').html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
    });

    // AUTO-FORMAT NAMA KELAS
    $('#grade, #major').on('change keyup', function() {
        const grade = $('#grade').val();
        const major = $('#major').val().toUpperCase().trim();
        
        if (grade) {
            let baseName = '';
            // Mengambil angka tingkat dari option yang dipilih
            const selectedText = $('#grade option:selected').text().trim().match(/\d+/);
            const gradeNumber = selectedText ? selectedText[0] : grade;
            
            baseName = `${gradeNumber}${major ? ' ' + major : ''}`;
            
            let currentName = $('#name').val().toUpperCase().trim();
            
            // Coba ekstrak angka terakhir (misal: dari "X RPL 1" ambil "1")
            let numberMatch = currentName.match(/\d+$/);
            let number = numberMatch ? numberMatch[0] : '1';
            
            let newName = `${baseName} ${number}`;

            // Hanya update jika input nama kelas kosong atau dimulai dengan tingkat yang sama
            if (!currentName || currentName.startsWith(gradeNumber)) {
                $('#name').val(newName.toUpperCase().trim());
            }
        }
    });
});
</script>
@stop