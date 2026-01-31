@extends('layouts.adminlte')

@section('title', 'Tambah Jadwal Pelajaran')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight flex items-center">
            <i class="fas fa-calendar-plus text-purple-600 mr-3"></i> Tambah Jadwal
        </h1>
        <p class="text-sm text-gray-500 mt-1">Buat jadwal pelajaran baru untuk kelas.</p>
    </div>
    @if($preselectedClass)
    <a href="{{ route('admin.schedules.show', $preselectedClass->id) }}" class="group flex items-center px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-indigo-600 transition-all duration-200 shadow-sm">
        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Kembali
    </a>
    @else
    <a href="{{ route('admin.schedules.index') }}" class="group flex items-center px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-indigo-600 transition-all duration-200 shadow-sm">
        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Kembali
    </a>
    @endif
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
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg animate__animated animate__fadeIn">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada inputan Anda:</h3>
                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.schedules.store') }}" method="POST">
                    @csrf
                    
                    {{-- Section: Data Kelas & Waktu --}}
                    <div class="mb-8">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">
                            <i class="fas fa-clock text-indigo-500 mr-2"></i> Waktu & Kelas
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Pilih Kelas --}}
                            <div class="md:col-span-2">
                                <label for="class_id" class="block text-sm font-bold text-gray-700 mb-2">
                                    Target Kelas <span class="text-red-500">*</span>
                                </label>
                                @if($preselectedClass)
                                    <input type="hidden" name="class_id" value="{{ $preselectedClass->id }}">
                                    <div class="relative rounded-xl shadow-sm bg-gray-50 border border-gray-200 p-3 flex items-center">
                                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 mr-3 font-bold text-xs">
                                            {{ $preselectedClass->grade }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">{{ $preselectedClass->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $preselectedClass->major ?? 'Umum' }}</p>
                                        </div>
                                        <div class="ml-auto">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                    </div>
                                @else
                                    <div class="relative rounded-xl shadow-sm group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-chalkboard text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                        </div>
                                        <select name="class_id" id="class_id" required class="focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium placeholder-gray-400">
                                            <option value="">-- Pilih Kelas --</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                    Kelas {{ $class->grade }} - {{ $class->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>

                            {{-- Hari --}}
                            <div class="md:col-span-2">
                                <label for="day" class="block text-sm font-bold text-gray-700 mb-2">
                                    Hari <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-xl shadow-sm group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-day text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                    </div>
                                    <select name="day" id="day" required class="focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium placeholder-gray-400">
                                        <option value="">-- Pilih Hari --</option>
                                        @foreach($days as $day)
                                            <option value="{{ $day }}" {{ old('day') == $day ? 'selected' : '' }}>{{ $day }}</option>
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
                                        <i class="far fa-clock text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                    </div>
                                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" required 
                                           class="focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium placeholder-gray-400">
                                </div>
                            </div>

                            {{-- Jam Selesai --}}
                            <div>
                                <label for="end_time" class="block text-sm font-bold text-gray-700 mb-2">
                                    Jam Selesai <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-xl shadow-sm group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-history text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                    </div>
                                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" required 
                                           class="focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium placeholder-gray-400">
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
                                        <i class="fas fa-book text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                    </div>
                                    <select name="subject_id" id="subject_id" required class="focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium placeholder-gray-400">
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
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
                                        <i class="fas fa-chalkboard-teacher text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                    </div>
                                    <select name="teacher_id" id="teacher_id" required class="focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium placeholder-gray-400">
                                        <option value="">-- Pilih Guru --</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <p class="text-xs text-gray-500 mt-2 ml-1">
                                    <i class="fas fa-info-circle mr-1"></i> Guru ini akan tercatat mengampu mapel di kelas dan jadwal ini.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                        <button type="reset" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition duration-200 shadow-sm">
                            <i class="fas fa-undo mr-2 text-gray-500"></i> Reset
                        </button>
                        <button type="submit" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-xl transform transition hover:-translate-y-1">
                            <i class="fas fa-save mr-2"></i> Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- Info Card --}}
    <div class="lg:col-span-1">
        <div class="bg-purple-50 rounded-3xl p-8 border border-purple-100 h-full">
            <h3 class="text-lg font-bold text-purple-900 mb-6 flex items-center">
                <div class="w-10 h-10 rounded-xl bg-purple-200 flex items-center justify-center mr-3 shadow-sm">
                    <i class="fas fa-lightbulb text-purple-600"></i>
                </div>
                Tips Jadwal
            </h3>
            <div class="space-y-6">
                <div class="p-4 bg-white rounded-2xl shadow-sm border border-purple-50">
                    <h5 class="font-bold text-gray-800 text-sm mb-2"><i class="fas fa-clock text-purple-500 mr-1"></i> Durasi Jam</h5>
                    <p class="text-gray-600 text-xs leading-relaxed">
                        Pastikan jam mulai dan selesai tidak bertabrakan dengan pelajaran lain di hari yang sama.
                    </p>
                </div>

                <div class="p-4 bg-white rounded-2xl shadow-sm border border-purple-50">
                    <h5 class="font-bold text-gray-800 text-sm mb-2"><i class="fas fa-user-check text-purple-500 mr-1"></i> Guru Pengampu</h5>
                    <p class="text-gray-600 text-xs leading-relaxed">
                        Guru yang dipilih di sini otomatis akan memiliki akses absen untuk kelas ini pada jam yang ditentukan.
                    </p>
                </div>
                
                 <div class="p-4 bg-indigo-600 rounded-2xl shadow-lg border border-indigo-500 text-white relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 text-white opacity-10 text-6xl">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h5 class="font-bold text-white text-sm mb-2 relative z-10">Sudah Selesai?</h5>
                    <p class="text-indigo-100 text-xs leading-relaxed relative z-10">
                        Klik tombol <b>Simpan</b> di bawah jika semua data sudah benar. Jadwal akan langsung aktif.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
