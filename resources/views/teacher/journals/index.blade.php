@extends('layouts.adminlte')

@section('title', 'Riwayat Jurnal')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight flex items-center">
            <i class="fas fa-history text-indigo-500 mr-2"></i> Riwayat Jurnal
        </h1>
        <p class="text-sm text-gray-500 mt-1">Daftar aktivitas mengajar dan absensi yang telah Anda isi.</p>
    </div>
    <div class="hidden sm:block">
        <a href="{{ route('teacher.dashboard') }}"
            class="group flex items-center px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-indigo-600 transition-all duration-200 shadow-sm">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Kembali
        </a>
    </div>
</div>
@stop

@section('content')
<div class="space-y-6">
    {{-- Main Card --}}
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">

        {{-- Header --}}
        <div
            class="p-6 border-b border-gray-100 bg-gray-50/30 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center space-x-2">
                <span
                    class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold border border-indigo-100">
                    Total: {{ $journals->total() }} Jurnal
                </span>
            </div>
            {{-- Search Placeholder (Optional) --}}
            {{-- <div class="relative w-full sm:w-64">
                <input type="text" placeholder="Cari mapel atau topik..." class="...">
            </div> --}}
        </div>

        {{-- Content --}}
        @if($journals->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-24 h-24 bg-indigo-50 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-book-open text-4xl text-indigo-200"></i>
                </div>
                <h4 class="text-gray-900 font-bold text-lg">Belum Ada Riwayat Jurnal</h4>
                <p class="text-gray-500 text-sm mt-2 max-w-sm mx-auto">
                    Anda belum mengisi jurnal mengajar sama sekali. Jurnal akan muncul di sini setelah Anda mengisi absensi
                    kelas.
                </p>
                <a href="{{ route('teacher.dashboard') }}"
                    class="mt-6 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg hover:shadow-indigo-500/30 transition-all transform hover:-translate-y-1">
                    <i class="fas fa-plus mr-2"></i> Isi Jurnal Sekarang
                </a>
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead
                        class="bg-gray-50/50 text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4">Kelas & Mapel</th>
                            <th class="px-6 py-4">Topik Pembahasan</th>
                            <th class="px-6 py-4 text-center">Kehadiran</th>
                            <th class="px-6 py-4 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($journals as $journal)
                            <tr class="hover:bg-indigo-50/10 transition duration-150 group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-800">{{ $journal->date->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400 font-mono mt-0.5">
                                        {{ \Carbon\Carbon::parse($journal->start_time)->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-bold text-gray-900">{{ $journal->schedule->subject->name ?? '-' }}</span>
                                        <span class="text-xs text-gray-500 flex items-center mt-0.5">
                                            <i class="fas fa-chalkboard mr-1 text-gray-300"></i>
                                            {{ $journal->schedule->class->name ?? '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600 truncate max-w-xs" title="{{ $journal->topic }}">
                                        {{ $journal->topic }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <i class="fas fa-check-circle mr-1"></i> Selesai
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('teacher.journals.edit', $journal->id) }}"
                                        class="inline-flex w-8 h-8 items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-indigo-600 hover:border-indigo-200 transition-colors shadow-sm"
                                        title="Edit Jurnal">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile List (Cards) --}}
            <div class="md:hidden space-y-4 p-4 bg-gray-50">
                @foreach($journals as $journal)
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $journal->schedule->subject->name ?? 'Mapel' }}</h4>
                                <p class="text-xs text-indigo-600 font-bold uppercase tracking-wide mt-0.5">
                                    {{ $journal->schedule->class->name ?? 'Kelas' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-xs font-bold text-gray-800">{{ $journal->date->translatedFormat('d M') }}</div>
                                <div class="text-[10px] text-gray-400 font-mono">
                                    {{ \Carbon\Carbon::parse($journal->start_time)->format('H:i') }}</div>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-100">
                            <p class="text-xs text-gray-600 line-clamp-2 italic">
                                "{{ $journal->topic }}"
                            </p>
                        </div>

                        <div class="flex justify-between items-center">
                            <span
                                class="text-[10px] font-bold text-green-600 flex items-center bg-green-50 px-2 py-1 rounded-full">
                                <i class="fas fa-check-circle mr-1"></i> Terisi
                            </span>
                            <a href="{{ route('teacher.journals.edit', $journal->id) }}"
                                class="px-3 py-1.5 bg-white border border-gray-200 text-gray-700 text-xs font-bold rounded-lg shadow-sm hover:bg-gray-50">
                                Edit Jurnal
                            </a>
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