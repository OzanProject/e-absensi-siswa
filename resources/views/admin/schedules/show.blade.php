@extends('layouts.adminlte')

@section('title', 'Atur Jadwal - ' . $classModel->name)

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0 px-4">
    <div>
        <a href="{{ route('admin.schedules.index') }}"
            class="group inline-flex items-center text-sm font-bold text-indigo-600 hover:text-indigo-800 transition mb-2">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Kembali ke Daftar
            Kelas
        </a>
        <h1 class="text-3xl font-extrabold text-gray-800 flex items-center">
            <span class="bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">
                Jadwal {{ $classModel->grade }} {{ $classModel->name }}
            </span>
        </h1>
        <p class="text-sm text-gray-500 mt-1 flex items-center">
            <i class="fas fa-chalkboard text-gray-400 mr-2"></i>
            {{ $classModel->major ?? 'Umum' }}
            <span class="mx-2">•</span>
            <i class="fas fa-user-tie text-gray-400 mr-2"></i>
            {{ $classModel->homeroomTeacher->user->name ?? 'Wali Kelas Belum Diatur' }}
        </p>
    </div>
    <a href="{{ route('admin.schedules.create', ['class_id' => $classModel->id]) }}"
        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg hover:shadow-indigo-500/30 transform transition hover:-translate-y-1 flex items-center">
        <i class="fas fa-plus mr-2"></i> Tambah Jadwal
    </a>
</div>
@stop

@section('content')
<div class="px-4 pb-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($days as $day)
            <div
                class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full hover:shadow-md transition-shadow duration-300">
                {{-- Header Hari --}}
                <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex justify-between items-center group">
                    <h3 class="font-bold text-lg text-gray-800 flex items-center">
                        <div
                            class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center mr-3 text-sm group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                            {{ substr($day, 0, 1) }}
                        </div>
                        {{ $day }}
                    </h3>
                    <span
                        class="text-xs font-bold {{ isset($schedules[$day]) ? 'text-indigo-600 bg-indigo-50 border-indigo-100' : 'text-gray-400 bg-gray-100 border-gray-200' }} px-3 py-1 rounded-full border">
                        {{ isset($schedules[$day]) ? count($schedules[$day]) . ' Mapel' : 'Libur' }}
                    </span>
                </div>

                {{-- List Jadwal --}}
                <div class="p-6 flex-1 bg-white">
                    @if(isset($schedules[$day]) && count($schedules[$day]) > 0)
                        <div class="relative">
                            {{-- Garis Timeline --}}
                            <div class="absolute left-3.5 top-2 bottom-2 w-0.5 bg-gray-100"></div>

                            <ul class="space-y-6 relative">
                                @foreach($schedules[$day] as $index => $schedule)
                                    <li class="relative pl-10 group">
                                        {{-- Dot Timeline --}}
                                        <div
                                            class="absolute left-0 top-1.5 w-7 h-7 bg-white border-2 border-indigo-100 rounded-full flex items-center justify-center z-10 group-hover:border-indigo-500 transition-colors">
                                            <div
                                                class="w-2.5 h-2.5 bg-indigo-400 rounded-full group-hover:bg-indigo-600 transition-colors">
                                            </div>
                                        </div>

                                        {{-- Content Card --}}
                                        <div
                                            class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all duration-200 group-hover:translate-x-1">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <h4 class="font-bold text-gray-800 text-sm leading-tight">
                                                        {{-- PERBAIKAN: Nullsafe Operator --}}
                                                        {{ $schedule->subject?->name ?? 'Mata Pelajaran Terhapus' }}
                                                    </h4>
                                                    <div
                                                        class="flex items-center text-xs text-gray-500 mt-1 font-mono bg-gray-50 inline-block px-1.5 py-0.5 rounded border border-gray-100">
                                                        <i class="far fa-clock mr-1.5 text-indigo-400"></i>
                                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                    </div>
                                                </div>

                                                {{-- Action Buttons --}}
                                                <div
                                                    class="flex items-center space-x-1 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                                    <a href="{{ route('admin.schedules.edit', $schedule->id) }}"
                                                        class="p-1.5 text-gray-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-colors">
                                                        <i class="fas fa-pencil-alt text-xs"></i>
                                                    </a>
                                                    <form action="{{ route('admin.schedules.destroy', $schedule->id) }}"
                                                        method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                                            <i class="fas fa-trash-alt text-xs"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <div class="flex items-center pt-2 border-t border-gray-50 mt-2">
                                                <div
                                                    class="w-5 h-5 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-400 text-[10px] mr-2">
                                                    <i class="fas fa-user-tie"></i>
                                                </div>
                                                <span class="text-xs text-gray-600 truncate font-medium">
                                                    {{-- PERBAIKAN: Nullsafe Operator --}}
                                                    {{ $schedule->teacher?->name ?? 'Guru Tidak Ditemukan' }}
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-3">
                                <i class="far fa-calendar-times text-2xl text-gray-300"></i>
                            </div>
                            <span class="text-sm font-bold text-gray-500">Tidak ada jadwal</span>
                            <span class="text-xs text-gray-400 mt-1">Hari ini libur atau belum diatur</span>
                        </div>
                    @endif
                </div>

                {{-- Footer Accent --}}
                <div class="h-1 bg-gradient-to-r from-indigo-500 to-purple-600 opacity-20"></div>
            </div>
        @endforeach
    </div>
</div>
@stop