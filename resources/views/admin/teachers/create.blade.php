@extends('layouts.adminlte')

@section('title', 'Tambah Wali Kelas Baru')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight flex items-center">
            <i class="fas fa-user-plus text-purple-600 mr-2"></i> Tambah Wali Kelas
        </h1>
        <p class="text-sm text-gray-500 mt-1">Buat akun untuk wali kelas baru dan atur penugasan.</p>
    </div>
    <a href="{{ route('teachers.index') }}" class="group flex items-center px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-indigo-600 transition-all duration-200 shadow-sm">
        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Kembali
    </a>
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

                <form action="{{ route('teachers.store') }}" method="POST" id="teacherForm">
                    @csrf
                    
                    {{-- Section: Data Akun --}}
                    <div class="mb-8">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">
                            <i class="fas fa-id-card text-indigo-500 mr-2"></i> Data Akun
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nama --}}
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-xl shadow-sm group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                                           class="focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium placeholder-gray-400"
                                           placeholder="Contoh: Budi Santoso, S.Pd.">
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="md:col-span-2">
                                <label for="email" class="block text-sm font-bold text-gray-700 mb-2">
                                    Email (Untuk Login) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-xl shadow-sm group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                    </div>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                                           class="focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium placeholder-gray-400"
                                           placeholder="nama@sekolah.sch.id">
                                </div>
                            </div>
                            
                            {{-- Password --}}
                            <div class="md:col-span-2">
                                <label for="password" class="block text-sm font-bold text-gray-700 mb-2">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-xl shadow-sm group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                    </div>
                                    <input type="password" name="password" id="password" required 
                                           class="focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium placeholder-gray-400"
                                           placeholder="Minimal 8 karakter">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Penugasan --}}
                    <div class="mb-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">
                            <i class="fas fa-chalkboard-teacher text-indigo-500 mr-2"></i> Penugasan Kelas
                        </h4>

                        <div>
                            <label for="class_id" class="block text-sm font-bold text-gray-700 mb-2">
                                Wali Kelas Untuk (Opsional)
                            </label>
                            <div class="relative rounded-xl shadow-sm group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-school text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                </div>
                                <select name="class_id" id="class_id" class="focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium placeholder-gray-400">
                                    <option value="">-- Pilih Kelas (Kosongkan jika belum ada) --</option>
                                    @foreach($availableClasses as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->grade }} - {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 ml-1">
                                <i class="fas fa-info-circle mr-1"></i> Hanya kelas yang <b>belum memiliki wali kelas</b> yang muncul di sini.
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                        <button type="reset" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition duration-200 shadow-sm">
                            <i class="fas fa-undo mr-2 text-gray-500"></i> Reset
                        </button>
                        <button type="submit" id="submitBtn" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-xl transform transition hover:-translate-y-1">
                            <i class="fas fa-save mr-2"></i> Simpan Data
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
                Informasi Role
            </h3>
            <div class="space-y-6">
                <div class="p-4 bg-white rounded-2xl shadow-sm border border-purple-50">
                    <h5 class="font-bold text-gray-800 text-sm mb-2"><i class="fas fa-user-shield text-purple-500 mr-1"></i> Hak Akses</h5>
                    <p class="text-gray-600 text-xs leading-relaxed">
                        Akun ini akan otomatis mendapatkan role <b>Wali Kelas</b>. Mereka dapat mengelola absensi siswa di kelas yang ditugaskan.
                    </p>
                </div>

                <div class="p-4 bg-white rounded-2xl shadow-sm border border-purple-50">
                    <h5 class="font-bold text-gray-800 text-sm mb-2"><i class="fas fa-key text-purple-500 mr-1"></i> Keamanan</h5>
                    <p class="text-gray-600 text-xs leading-relaxed">
                        Gunakan password yang kuat. Password dapat diubah kembali oleh admin atau wali kelas terkait.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#teacherForm').on('submit', function() {
            if (this.checkValidity() === false) return; 
            const submitBtn = $('#submitBtn');
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');
        });
    });
</script>
@stop