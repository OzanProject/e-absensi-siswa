@extends('layouts.adminlte')

@section('title', 'Form Pengajuan Izin/Sakit')

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Orange/Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-file-medical-alt text-orange-500 mr-2"></i>
        <span>Pengajuan Izin / Sakit Online</span>
    </h1>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <li><a href="{{ route('orangtua.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Pengajuan Izin</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    {{-- Notifikasi Sukses/Error (Styling Tailwind) --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg relative mb-6 alert-dismissible" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-6 alert-dismissible" role="alert">
            <i class="fas fa-ban mr-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- KOLOM KIRI: FORM PENGAJUAN (1/3) --}}
        <div class="lg:col-span-1">
            {{-- Mengganti card menjadi box Tailwind --}}
            <div class="bg-white shadow-xl rounded-xl border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 bg-amber-500 text-white">
                    <h3 class="text-xl font-bold"><i class="fas fa-paper-plane mr-2"></i> Ajukan Permintaan</h3>
                </div>
                <div class="p-5">
                    
                    @php
                        // Fokus ke Orange untuk Input
                        $inputClass = 'w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-150';
                        $errorInputClass = 'w-full px-3 py-2 border border-red-500 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500';
                        $defaultBorder = 'border-gray-300';
                    @endphp

                    @if ($errors->any())
                        <div class="bg-red-50 text-red-700 font-semibold px-3 py-2 rounded-lg mb-4 text-sm border border-red-200">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Harap periksa kembali input Anda.
                        </div>
                    @endif

                    <form action="{{ route('orangtua.izin.store') }}" method="POST" enctype="multipart/form-data" id="izinForm">
                        @csrf
                        
                        <div class="space-y-4">
                            
                            {{-- Pilih Anak --}}
                            <div class="form-group">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Anak <span class="text-red-600">*</span></label>
                                <select name="student_id" class="w-full select2-form-control {{ $defaultBorder }} @error('student_id') {{ $errorInputClass }} @enderror" required>
                                    <option value="">Pilih Anak...</option>
                                    @foreach($parentRecord->students as $student)
                                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }} ({{ $student->class->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')<div class="text-sm text-red-600 mt-2"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</div>@enderror
                            </div>

                            {{-- Tanggal Berlaku --}}
                            <div class="form-group">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Berlaku <span class="text-red-600">*</span></label>
                                <input type="date" name="request_date" class="{{ $inputClass }} @error('request_date') {{ $errorInputClass }} @enderror" value="{{ old('request_date', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                                @error('request_date')<div class="text-sm text-red-600 mt-2"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</div>@enderror
                            </div>

                            {{-- Jenis Permintaan --}}
                            <div class="form-group">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Permintaan <span class="text-red-600">*</span></label>
                                <select name="type" class="{{ $inputClass }} @error('type') {{ $errorInputClass }} @enderror" required>
                                    <option value="Sakit" {{ old('type') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="Izin" {{ old('type') == 'Izin' ? 'selected' : '' }}>Izin</option>
                                </select>
                                @error('type')<div class="text-sm text-red-600 mt-2"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</div>@enderror
                            </div>
                            
                            {{-- Alasan / Keterangan --}}
                            <div class="form-group">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Alasan / Keterangan <span class="text-danger">*</span></label>
                                <textarea name="reason" rows="3" class="{{ $inputClass }} @error('reason') {{ $errorInputClass }} @enderror" required>{{ old('reason') }}</textarea>
                                @error('reason')<div class="text-sm text-red-600 mt-2"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</div>@enderror
                            </div>

                            {{-- Lampiran --}}
                            <div class="form-group">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Lampiran (Surat Dokter/Foto Surat Izin)</label>
                                {{-- Styling File Input Tailwind --}}
                                <input type="file" name="attachment" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100 @error('attachment') border-red-500 @enderror" accept="image/*, application/pdf">
                                <small class="text-xs text-gray-500 mt-1 block">Max 2MB (JPG, PNG, PDF)</small>
                                @error('attachment')<div class="text-sm text-red-600 mt-2"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</div>@enderror
                            </div>

                            {{-- Tombol Submit (Warna Orange) --}}
                            <button type="submit" class="btn-block w-full inline-flex justify-center items-center px-4 py-2.5 text-base font-bold rounded-lg shadow-md 
                                    text-gray-800 bg-amber-400 hover:bg-amber-500 focus:ring-4 focus:ring-offset-2 focus:ring-amber-500/50 transition duration-150 transform hover:-translate-y-0.5 mt-4" id="submitIzinBtn">
                                <i class="fas fa-paper-plane mr-2"></i> Ajukan Izin
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: RIWAYAT PENGAJUAN (2/3) --}}
        <div class="lg:col-span-2">
            <div class="bg-white shadow-xl rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 bg-indigo-600 text-white">
                    <h3 class="text-xl font-bold"><i class="fas fa-history mr-2"></i> Riwayat Pengajuan Izin / Sakit</h3>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-bold text-gray-600 uppercase">Anak</th>
                                    <th class="px-3 py-3 text-left text-xs font-bold text-gray-600 uppercase">Tanggal Izin</th>
                                    <th class="px-3 py-3 text-left text-xs font-bold text-gray-600 uppercase">Jenis</th>
                                    <th class="px-3 py-3 text-left text-xs font-bold text-gray-600 uppercase">Keterangan</th>
                                    <th class="px-3 py-3 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                                    <th class="px-3 py-3 text-left text-xs font-bold text-gray-600 uppercase">Lampiran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($requests as $req)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-3 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $req->student->name ?? '-' }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-700">{{ \Carbon\Carbon::parse($req->request_date)->isoFormat('D MMM Y') }}</td> 
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-700">{{ $req->type }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($req->reason, 40) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        @php
                                            $statusMap = ['Pending' => 'bg-yellow-100 text-yellow-800', 'Approved' => 'bg-green-100 text-green-800', 'Rejected' => 'bg-red-100 text-red-800'];
                                            $statusClass = $statusMap[$req->status] ?? 'bg-gray-200 text-gray-700';
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full {{ $statusClass }}">
                                            {{ $req->status }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        @if($req->attachment_path)
                                            {{-- Styling Button Lampiran (Mengganti btn-outline-info) --}}
                                            <a href="{{ asset('storage/' . $req->attachment_path) }}" target="_blank" 
                                               class="text-indigo-600 hover:text-indigo-800 p-1.5 rounded-full bg-indigo-100 transition duration-150" title="Lihat Lampiran">
                                                <i class="fas fa-file-alt w-4 h-4"></i>
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-gray-500 py-6">Belum ada riwayat pengajuan izin/sakit.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination (Styling Tailwind) --}}
                    <div class="mt-4 flex justify-end">
                         {{ $requests->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="{{ asset('template/adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
    // Pastikan JQuery tersedia di master layout
    
    $(document).ready(function() {
        // Initialize Select2 untuk Pilih Anak
        $('select[name="student_id"]').select2({ 
            theme: 'bootstrap4', 
            placeholder: 'Pilih Anak...', 
            allowClear: true,
            width: '100%' 
        });

        // 🚨 FUNGSI SUBMIT LOADING STATE (LOGIKA AMAN)
        $('#izinForm').on('submit', function() {
            const form = this;
            const submitBtn = $('#submitIzinBtn');

            if (form.checkValidity() === false) {
                 return;
            }
            
            // Tampilkan loading state dan nonaktifkan tombol
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...');
        });
        
        // Auto-dismiss alerts
        setTimeout(function() {
            $('.alert-dismissible').fadeOut(400, function() { $(this).remove(); });
        }, 5000);
    });
</script>
@endsection

@section('css')
<style>
/* --- MINIMAL CUSTOM CSS FOR TAILWIND --- */
.text-orange-500 { color: #f97316; }
.bg-amber-400 { background-color: #fbbf24; }
.hover\:bg-amber-500:hover { background-color: #f59e0b; }

/* Warna Custom Blocks */
.bg-indigo-600 { background-color: #4f46e5 !important; }
.text-indigo-600 { color: #4f46e5; }

/* Select2 Fix */
.select2-container--bootstrap4 .select2-selection--single {
    height: calc(2.25rem + 2px) !important;
}
.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    line-height: 1.5 !important;
    padding-top: 5px !important; 
}

/* Fixes for Tailwind Input Styling */
.form-control {
    width: 100%;
    /* Override Bootstrap form-control */
}
</style>
@endsection