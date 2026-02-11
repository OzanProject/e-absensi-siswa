@extends('layouts.adminlte')

@section('title', 'Laporan Absensi Siswa')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <div class="mb-3 sm:mb-0">
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center">
            <i class="fas fa-chart-line text-purple-600 mr-3"></i>
            Laporan Absensi
        </h1>
        <p class="text-sm text-gray-500 mt-1">Laporan kehadiran siswa pada mata pelajaran Anda.</p>
    </div>
    <nav class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100">
        <ol class="flex space-x-2">
            <li><a href="{{ route('teacher.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150"><i class="fas fa-home"></i></a></li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-800 font-bold">Laporan</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
<div class="space-y-6">

    {{-- TABS NAVIGATION --}}
    <div class="flex space-x-2 border-b border-gray-200">
        <a href="{{ route('teacher.report.index', ['type' => 'student']) }}" 
           class="px-6 py-3 font-bold text-sm rounded-t-xl transition-all {{ $reportType == 'student' ? 'bg-indigo-600 text-white shadow-lg transform -translate-y-1' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700' }}">
           <i class="fas fa-users mr-2"></i> Laporan Siswa
        </a>
        <a href="{{ route('teacher.report.index', ['type' => 'teacher']) }}" 
           class="px-6 py-3 font-bold text-sm rounded-t-xl transition-all {{ $reportType == 'teacher' ? 'bg-indigo-600 text-white shadow-lg transform -translate-y-1' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700' }}">
           <i class="fas fa-user-clock mr-2"></i> Laporan Saya
        </a>
    </div>

    {{-- FILTER SECTION --}}
    <div class="bg-white rounded-b-3xl rounded-tr-3xl shadow-xl border border-gray-100 overflow-hidden transform transition hover:shadow-2xl duration-300">
        <div class="p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                <span class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-filter"></i>
                </span>
                Filter Laporan {{ $reportType == 'teacher' ? 'Absensi Saya' : 'Absensi Siswa' }}
            </h3>
            
            <form action="{{ route('teacher.report.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <input type="hidden" name="type" value="{{ $reportType }}">

                {{-- KELAS (Hanya Muncul di Report Student) --}}
                @if($reportType == 'student')
                <div class="col-span-1 md:col-span-1">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pilih Kelas</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-chalkboard-teacher text-indigo-400 group-focus-within:text-indigo-600 transition-colors"></i>
                        </div>
                        <select name="class_id" class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl leading-5 bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all shadow-sm">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                {{-- TANGGAL MULAI --}}
                <div class="col-span-1 md:col-span-1">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Dari Tanggal</label>
                    <div class="relative group">
                         <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar-alt text-indigo-400 group-focus-within:text-indigo-600 transition-colors"></i>
                        </div>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl leading-5 bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all shadow-sm">
                    </div>
                </div>

                {{-- TANGGAL SELESAI --}}
                <div class="col-span-1 md:col-span-1">
                     <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Sampai Tanggal</label>
                    <div class="relative group">
                         <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar-check text-indigo-400 group-focus-within:text-indigo-600 transition-colors"></i>
                        </div>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                             class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl leading-5 bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all shadow-sm">
                    </div>
                </div>

                {{-- BUTTONS --}}
                <div class="col-span-1 flex items-end space-x-2">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold py-2.5 px-4 rounded-xl focus:outline-none focus:shadow-outline transform hover:-translate-y-0.5 transition-all shadow-md">
                        <i class="fas fa-search mr-2"></i> Tampilkan
                    </button>
                    @if(request('class_id') || request('start_date'))
                         <a href="{{ route('teacher.report.index', ['type' => $reportType]) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2.5 px-4 rounded-xl focus:outline-none focus:shadow-outline transform hover:-translate-y-0.5 transition-all shadow-sm" title="Reset Filter">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- RESULT CARD --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        {{-- CARD HEADER --}}
        <div class="p-6 border-b border-gray-100 bg-purple-50/30 flex flex-col lg:flex-row justify-between items-start lg:items-center">
            <div class="mb-4 lg:mb-0">
                <h3 class="text-lg font-bold text-gray-800">
                    Hasil Laporan {{ $reportType == 'teacher' ? 'Absensi Saya' : 'Absensi Siswa' }}
                    @if(request('class_id') && $reportType == 'student')
                        <span class="text-purple-600">Kelas {{ $classes->find(request('class_id'))->name ?? '' }}</span>
                    @endif
                </h3>
                 <p class="text-sm text-gray-500 mt-1">
                    <i class="far fa-calendar-alt mr-1"></i> Periode:
                    <span class="font-medium text-gray-700">{{ $startDate->format('d/m/Y') }}</span> s/d <span class="font-medium text-gray-700">{{ $endDate->format('d/m/Y') }}</span>
                </p>
            </div>

            {{-- EXPORT BUTTONS --}}
            <div class="flex space-x-3">
                 <form action="{{ route('teacher.report.export.excel') }}" method="GET" class="inline-flex">
                    <input type="hidden" name="type" value="{{ $reportType }}">
                    <input type="hidden" name="class_id" value="{{ request('class_id') }}">
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-bold rounded-xl shadow-sm text-white bg-green-500 hover:bg-green-600 transition duration-150 transform hover:-translate-y-0.5">
                        <i class="fas fa-file-excel mr-2"></i> Excel
                    </button>
                </form>

                <form action="{{ route('teacher.report.export.pdf') }}" method="GET" class="inline-flex" target="_blank">
                    <input type="hidden" name="type" value="{{ $reportType }}">
                    <input type="hidden" name="class_id" value="{{ request('class_id') }}">
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-bold rounded-xl shadow-sm text-white bg-red-500 hover:bg-red-600 transition duration-150 transform hover:-translate-y-0.5">
                        <i class="fas fa-file-pdf mr-2"></i> PDF
                    </button>
                </form>
            </div>
        </div>

        <div class="p-0">
            @if($absences->isEmpty())
                <div class="p-12 text-center">
                    <div class="inline-block p-4 rounded-full bg-indigo-50 text-indigo-500 mb-4">
                        <i class="fas fa-search fa-3x"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Tidak Ada Data</h3>
                    <p class="text-gray-500 mt-2">Belum ada data absensi pada periode dan kelas yang dipilih.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    @if($reportType == 'teacher')
                        {{-- TABLE TEACHER --}}
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jam Pulang</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Foto Masuk</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($absences as $a)
                                    <tr class="hover:bg-gray-50/50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-medium">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($a->date)->translatedFormat('l, d F Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-emerald-600 font-mono font-bold">{{ $a->clock_in ?? '-' }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-rose-600 font-mono font-bold">{{ $a->clock_out ?? '-' }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $status = $a->status ?? 'present';
                                                $color = $status == 'late' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700';
                                                $label = $status == 'late' ? 'Terlambat' : 'Hadir';
                                            @endphp
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $color }}">
                                                {{ $label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($a->photo)
                                            <a href="{{ Storage::url($a->photo) }}" target="_blank" class="block w-10 h-10 rounded-full overflow-hidden border-2 border-indigo-100 hover:border-indigo-500 transition-colors">
                                                <img src="{{ Storage::url($a->photo) }}" class="w-full h-full object-cover">
                                            </a>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        {{-- TABLE STUDENT (Old Logic) --}}
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Siswa</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Mapel</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($absences as $absence)
                                    <tr class="hover:bg-gray-50/50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-medium">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-800">{{ $absence->student->name ?? 'Siswa Dihapus' }}</div>
                                            <div class="text-xs text-gray-500">NIS: {{ $absence->student->nis ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $absence->class_name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 font-medium">{{ $absence->date->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $absence->date->format('H:i') }} WIB</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusStyles = [
                                                    'Hadir' => 'bg-green-100 text-green-700',
                                                    'Terlambat' => 'bg-amber-100 text-amber-700',
                                                    'Absen' => 'bg-red-100 text-red-700', // Usually system uses Alpha?
                                                    'Alpha' => 'bg-red-100 text-red-700',
                                                    'Izin' => 'bg-blue-100 text-blue-700',
                                                    'Sakit' => 'bg-purple-100 text-purple-700',
                                                ];
                                                $st = ucfirst(strtolower($absence->status));
                                                $style = $statusStyles[$st] ?? 'bg-gray-100 text-gray-600';
                                            @endphp
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $style }}">
                                                {{ $absence->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {!! $absence->detail_html !!}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    // Optional: Add client-side logic if needed
</script>
@stop