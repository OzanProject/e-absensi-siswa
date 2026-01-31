@extends('layouts.adminlte')

@section('title', 'Laporan Absensi')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
    <div>
        <h1 class="m-0 text-gray-800 font-bold text-2xl tracking-tight flex items-center">
            <i class="fas fa-chart-pie text-indigo-500 mr-2"></i> Laporan Absensi
        </h1>
        <p class="text-sm text-gray-500 mt-1">Rekap kehadiran siswa berdasarkan jurnal mengajar Anda.</p>
    </div>
    <div class="hidden sm:block">
        <a href="{{ route('teacher.dashboard') }}" class="group flex items-center px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-indigo-600 transition-all duration-200 shadow-sm">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Kembali
        </a>
    </div>
</div>
@stop

@section('content')
<div class="space-y-6">
    
    {{-- Filter Card --}}
    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-6">
            <form action="{{ route('teacher.report.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full md:w-1/3">
                     <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Kelas</label>
                     <div class="relative">
                         <i class="fas fa-school absolute left-3 top-3.5 text-gray-400"></i>
                         <select name="class_id" class="w-full pl-10 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition p-2.5">
                             <option value="">-- Semua Kelas --</option>
                             @foreach($classes as $c)
                                 <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                     {{ $c->name }}
                                 </option>
                             @endforeach
                         </select>
                     </div>
                </div>
                
                <div class="w-full md:w-1/3">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Rentang Tanggal</label>
                    <div class="relative">
                        <i class="fas fa-calendar absolute left-3 top-3.5 text-gray-400"></i>
                         <input type="date" name="date" value="{{ request('date') }}" class="w-full pl-10 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition p-2.5">
                    </div>
                </div>

                <div class="w-full md:w-auto">
                    <button type="submit" class="w-full px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center justify-center">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                </div>
                 @if(request()->has('class_id') || request()->has('date'))
                    <div class="w-full md:w-auto">
                        <a href="{{ route('teacher.report.index') }}" class="w-full px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all flex items-center justify-center">
                            Reset
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Results --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">
             <div class="flex items-center space-x-2">
                 <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold border border-indigo-100">
                    Menampilkan {{ $journals->count() }} Data
                 </span>
             </div>
        </div>

        @if($journals->isEmpty())
             <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                     <i class="fas fa-search text-3xl text-gray-300"></i>
                </div>
                <h4 class="text-gray-900 font-bold text-base">Tidak Ada Data Laporan</h4>
                <p class="text-gray-500 text-sm mt-1 max-w-xs mx-auto">
                    Coba sesuaikan filter pencarian Anda.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                {{-- Desktop Table --}}
                <table class="w-full text-left border-collapse hidden md:table">
                    <thead class="bg-gray-50/50 text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4">Kelas</th>
                            <th class="px-6 py-4">Materi</th>
                            <th class="px-6 py-4 text-center">Kehadiran</th>
                            <th class="px-6 py-4 w-48">Progress Absen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($journals as $journal)
                            @php
                                $totalSiswa = $journal->schedule->class->students->count();
                                $hadirCount = \App\Models\SubjectAttendance::where('teaching_journal_id', $journal->id)->where('status', 'Hadir')->count();
                                $persentase = $totalSiswa > 0 ? ($hadirCount / $totalSiswa) * 100 : 0;
                                $color = $persentase >= 90 ? 'bg-emerald-500' : ($persentase >= 70 ? 'bg-indigo-500' : 'bg-amber-500');
                            @endphp
                            <tr class="hover:bg-indigo-50/10 transition duration-150 group">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-800">{{ $journal->date->translatedFormat('d M Y') }}</div>
                                    <div class="text-xs text-gray-400 font-mono mt-0.5">
                                        {{ \Carbon\Carbon::parse($journal->start_time)->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                     <span class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-bold border border-gray-200">
                                         {{ $journal->schedule->class->name }}
                                     </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700 font-medium truncate max-w-xs" title="{{ $journal->topic }}">
                                        {{ $journal->topic }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-lg font-bold text-gray-800">
                                        {{ $hadirCount }}<span class="text-gray-400 text-sm font-normal">/{{ $totalSiswa }}</span>
                                    </div>
                                    <div class="text-[10px] text-gray-400 uppercase font-bold tracking-wide">Hadir</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-1 w-full bg-gray-100 rounded-full h-2.5 mr-2">
                                            <div class="{{ $color }} h-2.5 rounded-full" style="width: {{ $persentase }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-gray-600 w-8 text-right">{{ round($persentase) }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card List --}}
            <div class="md:hidden space-y-4 p-4 bg-gray-50">
                @foreach($journals as $journal)
                    @php
                        $totalSiswa = $journal->schedule->class->students->count();
                        $hadirCount = \App\Models\SubjectAttendance::where('teaching_journal_id', $journal->id)->where('status', 'Hadir')->count();
                        $persentase = $totalSiswa > 0 ? ($hadirCount / $totalSiswa) * 100 : 0;
                        $color = $persentase >= 90 ? 'bg-emerald-500' : ($persentase >= 70 ? 'bg-indigo-500' : 'bg-amber-500');
                    @endphp
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                        <div class="flex justify-between items-start mb-2">
                             <div>
                                 <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-100 mb-1 inline-block">
                                     {{ $journal->schedule->class->name }}
                                 </span>
                                 <h4 class="font-bold text-gray-800 text-sm">{{ $journal->topic }}</h4>
                             </div>
                             <div class="text-right">
                                 <div class="text-xs font-bold text-gray-800">{{ $journal->date->translatedFormat('d M') }}</div>
                             </div>
                        </div>

                        <div class="flex items-center mt-3 bg-gray-50 p-3 rounded-xl">
                            <div class="flex-1">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="font-bold text-gray-500">Kehadiran</span>
                                    <span class="font-bold text-gray-800">{{ $hadirCount }} / {{ $totalSiswa }} Siswa</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="{{ $color }} h-2 rounded-full" style="width: {{ $persentase }}%"></div>
                                </div>
                            </div>
                            <div class="ml-3 pl-3 border-l border-gray-200">
                                <span class="text-sm font-bold text-gray-700">{{ round($persentase) }}%</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @endif
        
        {{-- Pagination --}}
        @if($journals->hasPages())
            <div class="p-6 border-t border-gray-100 bg-white">
                {{ $journals->links() }}
            </div>
        @endif
    </div>
</div>
@stop