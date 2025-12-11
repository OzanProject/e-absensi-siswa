@extends('layouts.adminlte')

@section('title', 'Laporan Absensi')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-file-alt text-purple-600 mr-3"></i>
            Laporan Absensi
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Filter dan unduh laporan kehadiran siswa.</p>
    </div>
    <nav class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150"><i class="fas fa-home"></i></a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-bold">Laporan</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-4 lg:gap-6">
        
        {{-- CARD KIRI: FORM FILTER (3 Kolom) --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-purple-50/30">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-filter mr-2 text-purple-600"></i> Filter Laporan
                    </h3>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('report.generate') }}" method="GET" id="filterForm" class="space-y-6">
                        @php
                            $classes = $classes ?? []; 
                            $inputClass = 'w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition duration-200 bg-gray-50 focus:bg-white text-gray-800';
                            $inputErrorClass = 'w-full px-4 py-3 rounded-xl border border-red-300 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition duration-200 bg-red-50 text-red-900';
                            
                            $currentMonthStart = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                            $currentDate = \Carbon\Carbon::now()->format('Y-m-d');
                        @endphp
        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            
                            {{-- Filter Kelas --}}
                            <div>
                                <label for="class_id" class="block text-sm font-bold text-gray-700 mb-2">Pilih Kelas</label>
                                <div class="relative">
                                    <select name="class_id" id="class_id" class="w-full select2-form-control">
                                        <option value="">-- Semua Kelas --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }} (Tingkat {{ $class->grade }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('class_id') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                            
                            {{-- Dari Tanggal --}}
                            <div>
                                <label for="start_date" class="block text-sm font-bold text-gray-700 mb-2">Dari Tanggal <span class="text-red-500">*</span></label>
                                <div class="relative">
                                     <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-alt text-gray-400"></i>
                                    </div>
                                    <input type="date" name="start_date" id="start_date" 
                                        class="pl-10 {{ $errors->has('start_date') ? $inputErrorClass : $inputClass }}" 
                                        value="{{ old('start_date', $currentMonthStart) }}" 
                                        required>
                                </div>
                                @error('start_date') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
        
                            {{-- Sampai Tanggal --}}
                            <div>
                                <label for="end_date" class="block text-sm font-bold text-gray-700 mb-2">Sampai Tanggal <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-check text-gray-400"></i>
                                    </div>
                                    <input type="date" name="end_date" id="end_date" 
                                        class="pl-10 {{ $errors->has('end_date') ? $inputErrorClass : $inputClass }}" 
                                        value="{{ old('end_date', $currentDate) }}" 
                                        required>
                                </div>
                                @error('end_date') <p class="mt-2 text-sm text-red-600 font-medium"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>
                    
                        <div class="pt-6 border-t border-gray-100 mt-6 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 justify-end items-center">
                            {{-- Tombol Tampilkan Laporan --}}
                            <button type="submit" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-xl shadow-lg 
                                           text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 
                                           focus:ring-4 focus:ring-purple-500/50 transition duration-150 transform hover:-translate-y-0.5" 
                                    id="submitFilterBtn">
                                <i class="fas fa-search mr-2"></i> Tampilkan Laporan
                            </button>
                            
                            {{-- Tombol Export Excel --}}
                            <button type="button" id="exportExcelBtn" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-xl shadow-md 
                                           text-white bg-green-500 hover:bg-green-600 focus:ring-4 focus:ring-green-500/50 transition duration-150 transform hover:-translate-y-0.5">
                                <i class="fas fa-file-excel mr-2"></i> Export Excel
                            </button>
                        </div>
                    </form>
                    
                    {{-- Form Tersembunyi untuk Export --}}
                    <form action="{{ route('report.export.excel') }}" method="GET" id="exportForm" class="hidden">
                        <input type="hidden" name="class_id" id="export_class_id">
                        <input type="hidden" name="start_date" id="export_start_date">
                        <input type="hidden" name="end_date" id="export_end_date">
                    </form>
                </div>
            </div>
        </div>

        {{-- CARD KANAN: PANDUAN (1 Kolom) --}}
        <div class="lg:col-span-1 mt-6 lg:mt-0">
             <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center"><i class="fas fa-info-circle mr-2 text-indigo-500"></i> Panduan</h3>
                </div>
                <div class="p-6 text-sm space-y-4 text-gray-600">
                    <div class="space-y-3">
                        <div class="flex items-start">
                             <div class="bg-purple-100 p-2 rounded-lg text-purple-600 mr-3 flex-shrink-0">
                                <i class="fas fa-filter"></i>
                             </div>
                            <div>
                                <strong class="block text-gray-800">Filter Data</strong>
                                <span class="text-xs">Pilih kelas dan rentang tanggal untuk melihat data spesifik.</span>
                            </div>
                        </div>
                        <div class="flex items-start">
                             <div class="bg-green-100 p-2 rounded-lg text-green-600 mr-3 flex-shrink-0">
                                <i class="fas fa-file-excel"></i>
                             </div>
                            <div>
                                <strong class="block text-gray-800">Export Excel</strong>
                                <span class="text-xs">Unduh laporan lengkap dalam format .xlsx untuk diolah lebih lanjut.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="{{ asset('template/adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function () {
        // Initialize Select2 with Bootstrap 4 theme
        $('.select2-form-control').select2({ theme: 'bootstrap4', placeholder: '-- Semua Kelas --', allowClear: true });
        
        // Custom styling fix for Select2 container to match Tailwind input height
        $('.select2-container .select2-selection--single').css('height', '50px');
        $('.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered').css({
            'line-height': '48px',
            'padding-left': '1rem' 
        });

        // Function: Sync values & Submit Export
        function syncAndSubmitExport() {
            const classId = $('#class_id').val() || '';
            const startDate = $('#start_date').val(); 
            const endDate = $('#end_date').val(); 
            
            if (!startDate || !endDate) {
                 Swal.fire({
                    icon: 'warning',
                    title: 'Filter Belum Lengkap',
                    text: 'Harap isi "Dari Tanggal" dan "Sampai Tanggal" terlebih dahulu.',
                    confirmButtonColor: '#6366f1'
                });
                return false;
            }

            $('#export_class_id').val(classId);
            $('#export_start_date').val(startDate);
            $('#export_end_date').val(endDate);

            $('#exportForm').submit();
        }
        
        // Event Listener: Export Button
        $('#exportExcelBtn').on('click', function(e) {
            e.preventDefault(); 
            syncAndSubmitExport();
        });
        
        // Event Listener: Submit Loading State
        $('#filterForm').on('submit', function() {
            if (this.checkValidity()) {
                 $('#submitFilterBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Memuat...');
            }
        });
    });
</script>
@stop

@section('css')
<style>
/* CSS Override untuk Select2 agar selaras dengan input Tailwind */
.select2-container--bootstrap4 .select2-selection--single {
    border: 1px solid #e5e7eb !important; /* border-gray-200 */
    border-radius: 0.75rem !important; /* rounded-xl */
    background-color: #f9fafb !important; /* bg-gray-50 */
}
.select2-container--bootstrap4 .select2-selection--single:focus {
    border-color: #a855f7 !important; /* border-purple-500 */
    box-shadow: 0 0 0 4px rgba(168, 85, 247, 0.1) !important;
}
</style>
@stop