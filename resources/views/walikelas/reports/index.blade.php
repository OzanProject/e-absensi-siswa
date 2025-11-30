@extends('layouts.adminlte')

@section('title', 'Filter Laporan Absensi')

@section('content_header')
{{-- HEADER: Menggunakan Flexbox Tailwind --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-chart-line text-red-600 mr-2"></i>
        <span>Laporan Absensi Kelas **{{ $class->name }}**</span>
    </h1>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="{{ route('walikelas.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Laporan</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
        <div class="p-5 border-b border-gray-100">
            <h3 class="text-xl font-semibold text-gray-800 flex items-center"><i class="fas fa-filter mr-2 text-gray-500"></i> Filter Periode Laporan</h3>
        </div>
        
        <div class="p-5">
            
            <p class="text-lg text-gray-600 mb-6">Laporan akan difilter secara otomatis untuk Kelas **{{ $class->name }}** (Tingkat {{ $class->grade }}).</p>

            {{-- Form ini akan diarahkan ke Controller Report untuk memproses data --}}
            <form action="{{ route('walikelas.report.generate') }}" method="GET" id="reportFilterForm">
                
                @php
                    $inputClass = 'w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500';
                    $errorBorder = 'border-red-500';
                    $defaultBorder = 'border-gray-300';
                    $currentMonthStart = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                    $currentDate = \Carbon\Carbon::now()->format('Y-m-d');
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    {{-- Tanggal Awal --}}
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal <span class="text-red-600">*</span></label>
                        <input type="date" name="start_date" id="start_date" 
                                class="{{ $inputClass }} @error('start_date') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" 
                                value="{{ old('start_date', $currentMonthStart) }}" 
                                required>
                        @error('start_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Sampai Tanggal --}}
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal <span class="text-red-600">*</span></label>
                        <input type="date" name="end_date" id="end_date" 
                                class="{{ $inputClass }} @error('end_date') {{ $errorBorder }} @else {{ $defaultBorder }} @enderror" 
                                value="{{ old('end_date', $currentDate) }}" 
                                required>
                        @error('end_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                
                <div class="mt-6 border-t border-gray-200 pt-4 flex space-x-3">
                    <a href="{{ route('walikelas.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-base font-medium rounded-lg shadow-sm 
                           text-gray-700 bg-white hover:bg-gray-50 transition duration-150">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-lg shadow-sm 
                           text-white bg-blue-600 hover:bg-blue-700 transition duration-150" id="submitFilterBtn">
                        <i class="fas fa-search mr-2"></i> Tampilkan Laporan
                    </button>
                    
                    {{-- Tombol Export (Jika diperlukan, bisa ditambahkan di sini dengan logic sync) --}}
                    {{-- Saat ini tombol export dihapus karena form export tidak didefinisikan --}}
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    // Pastikan JQuery tersedia di master layout
    
    $(document).ready(function() {
        // 🚨 FUNGSI SUBMIT LOADING STATE
        $('#reportFilterForm').on('submit', function() {
            const submitBtn = $('#submitFilterBtn');
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            
            // Cek validasi minimal (karena HTML5 'required' mungkin tidak tampil jelas)
            if (startDate && endDate) {
                // Tampilkan loading state
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Memuat Laporan...');
            }
        });

        // Auto-dismiss alerts (jika ada)
        setTimeout(function() {
            $('.alert').fadeOut(400);
        }, 5000);
    });
</script>
@stop

@section('css')
<style>
/* --- MINIMAL CUSTOM CSS FOR TAILWIND --- */
.text-red-600 { color: #dc3545; }
.text-indigo-600 { color: #4f46e5; }
.text-blue-600 { color: #2563eb; }
</style>
@stop