@extends('layouts.adminlte')

@section('title', 'Edit Jadwal Pelajaran')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
    <div>
         <a href="{{ route('admin.schedules.show', $schedule->class_id) }}" class="text-indigo-600 font-bold hover:text-indigo-800 transition text-sm mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Jadwal
        </a>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight flex items-center">
            <i class="fas fa-edit text-amber-500 mr-3"></i> Edit Jadwal
        </h1>
    </div>
</div>
@stop

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Form Section --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="p-8">
                {{-- Error Handling --}}
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Periksa inputan Anda:</h3>
                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.schedules.update', $schedule->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="class_id" value="{{ $schedule->class_id }}">
                    
                    {{-- Section: Data Waktu --}}
                    <div class="mb-8">
                         <h4 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">
                            <i class="fas fa-clock text-indigo-500 mr-2"></i> Waktu & Hari
                        </h4>

                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Info Kelas (Readonly) --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kelas (Tidak bisa diubah)</label>
                                <div class="relative rounded-xl shadow-sm bg-gray-50 border border-gray-200 p-3 flex items-center">
                                    <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center text-gray-600 mr-3 font-bold text-xs">
                                        {{ $schedule->class->grade }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $schedule->class->name }}</p>
                                    </div>
                                    <div class="ml-auto">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Hari --}}
                            <div class="md:col-span-2">
                                <label for="day" class="block text-sm font-bold text-gray-700 mb-2">
                                    Hari <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-xl shadow-sm group">
                                     <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-day text-gray-400 group-focus-within:text-amber-500 transition-colors"></i>
                                    </div>
                                    <select name="day" id="day" required class="focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium">
                                        @foreach($days as $day)
                                            <option value="{{ $day }}" {{ old('day', $schedule->day) == $day ? 'selected' : '' }}>
                                                {{ $day }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Jam Mulai --}}
                            <div>
                                <label for="start_time" class="block text-sm font-bold text-gray-700 mb-2">
                                    Jam Mulai <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-xl shadow-sm group">
                                     <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="far fa-clock text-gray-400 group-focus-within:text-amber-500 transition-colors"></i>
                                    </div>
                                    <input type="time" name="start_time" id="start_time"
                                        value="{{ old('start_time', \Carbon\Carbon::parse($schedule->start_time)->format('H:i')) }}"
                                        required
                                        class="focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium">
                                </div>
                            </div>

                            {{-- Jam Selesai --}}
                            <div>
                                <label for="end_time" class="block text-sm font-bold text-gray-700 mb-2">
                                    Jam Selesai <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-xl shadow-sm group">
                                     <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-history text-gray-400 group-focus-within:text-amber-500 transition-colors"></i>
                                    </div>
                                    <input type="time" name="end_time" id="end_time"
                                        value="{{ old('end_time', \Carbon\Carbon::parse($schedule->end_time)->format('H:i')) }}"
                                        required
                                        class="focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Mapel & Guru --}}
                    <div class="mb-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">
                            <i class="fas fa-book-reader text-indigo-500 mr-2"></i> Mata Pelajaran & Pengajar
                        </h4>
                        
                        <div class="space-y-6">
                            {{-- Mata Pelajaran --}}
                            <div>
                                <label for="subject_id" class="block text-sm font-bold text-gray-700 mb-2">
                                    Mata Pelajaran <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-xl shadow-sm group">
                                     <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-book text-gray-400 group-focus-within:text-amber-500 transition-colors"></i>
                                    </div>
                                    <select name="subject_id" id="subject_id" required class="focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium">
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ old('subject_id', $schedule->subject_id) == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Guru Pengampu --}}
                            <div>
                                <label for="teacher_id" class="block text-sm font-bold text-gray-700 mb-2">
                                    Guru Pengampu <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-xl shadow-sm group">
                                     <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-chalkboard-teacher text-gray-400 group-focus-within:text-amber-500 transition-colors"></i>
                                    </div>
                                    <select name="teacher_id" id="teacher_id" required class="focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium">
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $schedule->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                         <a href="{{ route('admin.schedules.show', $schedule->class_id) }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition duration-200 shadow-sm">
                            Batal
                        </a>
                        <button type="submit" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-xl transform transition hover:-translate-y-1">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
     {{-- Info Card --}}
    <div class="lg:col-span-1">
        <div class="bg-amber-50 rounded-3xl p-8 border border-amber-100 h-full">
            <h3 class="text-lg font-bold text-amber-900 mb-6 flex items-center">
                <i class="fas fa-info-circle text-amber-600 mr-2"></i> Mode Edit
            </h3>
            <div class="space-y-4">
               <p class="text-amber-800 text-sm leading-relaxed">
                   Anda sedang mengubah data jadwal yang sudah ada. Pastikan perubahan tidak merusak alur absensi yang sudah berjalan.
               </p>
               <div class="p-4 bg-white rounded-xl shadow-sm border border-amber-100 mt-4">
                   <div class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Dibuat Pada</div>
                   <div class="text-gray-800 font-mono text-sm">{{ $schedule->created_at->format('d M Y, H:i') }}</div>
               </div>
                <div class="p-4 bg-white rounded-xl shadow-sm border border-amber-100">
                   <div class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Terakhir Update</div>
                   <div class="text-gray-800 font-mono text-sm">{{ $schedule->updated_at->format('d M Y, H:i') }}</div>
               </div>
            </div>
        </div>
    </div>
</div>
@stop