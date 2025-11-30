@extends('layouts.adminlte')

@section('title', 'Filter Laporan Absensi')

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Teal/Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-filter text-teal-500 mr-2"></i>
        <span>Filter Laporan Absensi</span>
    </h1>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Home</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Laporan</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-5 border-b border-gray-100">
            <h3 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i> Tentukan Periode Laporan
            </h3>
        </div>
        
        <div class="p-6">
            
            {{-- Form ini akan diarahkan ke Controller Report untuk memproses data --}}
            <form action="{{ route('report.generate') }}" method="GET" id="filterForm" class="space-y-6">
                
                @php
                    // Pastikan variabel $classes tersedia dari Controller
                    $classes = $classes ?? []; 
                    
                    // Input Class fokus ke Teal
                    $inputClass = 'w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition duration-150';
                    $errorBorder = 'border-red-500';
                    $defaultBorder = 'border-gray-300';
                    $currentMonthStart = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                    $currentDate = \Carbon\Carbon::now()->format('Y-m-d');
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    {{-- Filter Kelas --}}
                    <div>
                        <label for="class_id" class="block text-sm font-semibold text-gray-700 mb-1">Pilih Kelas</label>
                        <select name="class_id" id="class_id" 
                                class="w-full select2-form-control border @error('class_id') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} (Tingkat {{ $class->grade }})
                                </option>
                            @endforeach
                        </select>
                        @error('class_id') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                    </div>
                    
                    {{-- Dari Tanggal --}}
                    <div>
                        <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-1">Dari Tanggal <span class="text-red-600">*</span></label>
                        <input type="date" name="start_date" id="start_date" 
                            class="{{ $inputClass }} @error('start_date') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" 
                            value="{{ old('start_date', $currentMonthStart) }}" 
                            required>
                        @error('start_date') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                    </div>

                    {{-- Sampai Tanggal --}}
                    <div>
                        <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-1">Sampai Tanggal <span class="text-red-600">*</span></label>
                        <input type="date" name="end_date" id="end_date" 
                            class="{{ $inputClass }} @error('end_date') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" 
                            value="{{ old('end_date', $currentDate) }}" 
                            required>
                        @error('end_date') <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                    </div>
                </div>
            
                <div class="pt-6 border-t border-gray-100 mt-6 flex space-x-3">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-base font-medium rounded-lg shadow-sm 
                                    text-gray-700 bg-white hover:bg-gray-100 transition duration-150 transform hover:scale-[1.02]">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                    
                    {{-- Tombol Tampilkan Laporan --}}
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 border border-transparent text-base font-bold rounded-lg shadow-md 
                                    text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-offset-2 focus:ring-indigo-500/50 transition duration-150 transform hover:-translate-y-0.5" id="submitFilterBtn">
                        <i class="fas fa-search mr-2"></i> Tampilkan Laporan
                    </button>
                    
                    {{-- Tombol Export Excel --}}
                    <button type="button" id="exportExcelBtn" class="inline-flex items-center px-5 py-2.5 border border-transparent text-base font-bold rounded-lg shadow-md 
                                    text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:ring-offset-2 focus:ring-teal-500/50 transition duration-150 transform hover:-translate-y-0.5" title="Export Laporan ke Excel">
                        <i class="fas fa-file-excel mr-2"></i> Export Excel
                    </button>
                </div>
            </form>
            
            {{-- Form Tersembunyi untuk Export (Logika AMAN) --}}
            <form action="{{ route('report.export.excel') }}" method="GET" id="exportForm" class="hidden">
                <input type="hidden" name="class_id" id="export_class_id">
                <input type="hidden" name="start_date" id="export_start_date">
                <input type="hidden" name="end_date" id="export_end_date">
            </form>

        </div>
    </div>
@stop

@section('js')
<script src="{{ asset('template/adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
    // Pastikan JQuery tersedia di master layout
    
    $(function () {
        // Initialize Select2
        $('.select2-form-control').select2({ theme: 'bootstrap4', placeholder: '-- Semua Kelas --', allowClear: true });
        
        // 🚨 FUNGSI UTAMA: Menyinkronkan nilai filter dan submit form Export (LOGIKA AMAN)
        function syncAndSubmitExport() {
            const classId = $('#class_id').val() || '';
            const startDate = $('#start_date').val(); 
            const endDate = $('#end_date').val(); 
            
            // Validasi Dasar
            if (!startDate || !endDate) {
                alert('Harap isi "Dari Tanggal" dan "Sampai Tanggal" terlebih dahulu di form Filter.');
                return false;
            }

            // Sinkronkan nilai ke form tersembunyi
            $('#export_class_id').val(classId);
            $('#export_start_date').val(startDate);
            $('#export_end_date').val(endDate);

            // Lanjutkan submit form Export
            $('#exportForm').submit();
        }
        
        // 🚨 Event Listener untuk Tombol Export Excel (LOGIKA AMAN)
        $('#exportExcelBtn').on('click', function(e) {
            e.preventDefault(); 
            syncAndSubmitExport();
        });
        
        // Optimasi: Loading state untuk Tampilkan Laporan (LOGIKA AMAN)
        $('#filterForm').on('submit', function() {
            const submitBtn = $('#submitFilterBtn');
            // Cek validasi form HTML5
            if (this.checkValidity()) {
                 submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Memuat...');
            }
        });
    });
</script>
@stop

@section('css')
<style>
/* CSS KUSTOM MINIMAL UNTUK KOMPATIBILITAS */
.select2-form-control { width: 100% !important; }
.text-teal-500 { color: #20c997; } 
.bg-teal-600 { background-color: #0d9488 !important; }
.hover\:bg-teal-700:hover { background-color: #0f766e !important; }

/* Select2 Fix for AdminLTE/Bootstrap (Dipertahankan untuk Select2) */
.select2-container--bootstrap4 .select2-selection--single {
    height: calc(2.25rem + 2px) !important;
}
.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    line-height: 1.5 !important;
    padding-top: 5px !important; 
}
.text-red-600 { color: #dc3545; }
.text-indigo-600 { color: #4f46e5; }
.bg-indigo-600 { background-color: #4f46e5 !important; }
.hover\:bg-indigo-700:hover { background-color: #4338ca !important; }

</style>
@stop