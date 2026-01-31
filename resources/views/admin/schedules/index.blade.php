@extends('layouts.adminlte')

@section('title', 'Kelola Jadwal Pelajaran')

@section('content')
<div class="space-y-6">
    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight flex items-center">
                <i class="fas fa-calendar-alt text-purple-600 mr-3"></i> Kelola Jadwal Pelajaran
            </h2>
            <nav class="flex text-sm font-medium text-gray-500 space-x-2 mt-1">
                <span class="text-gray-500">Pilih kelas untuk melihat atau mengatur jadwal.</span>
            </nav>
        </div>

        {{-- Search (Optional Future Feature UI) --}}
        <div class="relative hidden sm:block">
            <input type="text" placeholder="Cari kelas..."
                class="pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent shadow-sm w-64 transition-all">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fas fa-search text-gray-400"></i>
            </span>
        </div>
    </div>

    {{-- CLASSES GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($classes as $class)
            <a href="{{ route('admin.schedules.show', $class->id) }}" class="group block h-full">
                <div
                    class="bg-white rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 overflow-hidden transform transition-all duration-300 hover:-translate-y-1 h-full flex flex-col relative">

                    {{-- Decorative Top Bar --}}
                    <div
                        class="h-1.5 bg-gradient-to-r from-purple-500 to-indigo-600 w-full group-hover:h-2 transition-all duration-300">
                    </div>

                    <div class="p-6 flex-1 flex flex-col items-center text-center relative z-10">
                        {{-- Grade Badge --}}
                        <span
                            class="mb-4 inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-600 border border-purple-100">
                            Kelas {{ $class->grade }}
                        </span>

                        {{-- Class Name --}}
                        <h3
                            class="text-2xl font-extrabold text-gray-800 group-hover:text-indigo-600 transition-colors mb-2">
                            {{ $class->name }}
                        </h3>

                        {{-- Major --}}
                        <p class="text-sm text-gray-500 font-medium mb-4">
                            {{ $class->major ?? '-' }}
                        </p>

                        {{-- Homeroom Teacher --}}
                        <div
                            class="mt-auto w-full pt-4 border-t border-gray-50 flex items-center justify-center text-xs text-gray-400 group-hover:text-gray-600 transition-colors">
                            <i class="fas fa-user-tie mr-1.5"></i>
                            <span
                                class="truncate max-w-[150px]">{{ $class->homeroomTeacher->user->name ?? 'Belum ada Wali Kelas' }}</span>
                        </div>
                    </div>

                    {{-- Hover Icon --}}
                    <div
                        class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-10 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                        <i class="fas fa-calendar-day text-5xl text-indigo-600"></i>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    @if($classes->isEmpty())
        <div class="text-center py-16 bg-white rounded-3xl border border-dashed border-gray-200">
            <div class="bg-gray-50 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-layer-group text-gray-300 text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Belum Ada Kelas</h3>
            <p class="text-gray-500 text-sm mt-1">Tambahkan data kelas terlebih dahulu di menu Manajemen Data > Data Kelas.
            </p>
        </div>
    @endif
</div>
@stop