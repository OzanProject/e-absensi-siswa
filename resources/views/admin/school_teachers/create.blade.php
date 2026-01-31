@extends('layouts.adminlte')

@section('title', 'Tambah Guru')

@section('content_header')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center px-4 py-6 space-y-4 md:space-y-0">
    <div>
        <nav class="flex mb-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-xs font-medium uppercase tracking-wider text-gray-400">
                <li class="inline-flex items-center">Admin</li>
                <li><i class="fas fa-chevron-right mx-1 text-[10px]"></i></li>
                <li>Manajemen Guru</li>
            </ol>
        </nav>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Tambah Guru Baru
        </h1>
        <p class="text-base text-gray-500 mt-1">Lengkapi formulir di bawah untuk mendaftarkan pendidik baru.</p>
    </div>
    <div class="flex items-center space-x-3">
        <a href="{{ route('admin.school-teachers.index') }}"
            class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
            <i class="fas fa-arrow-left mr-2 text-xs"></i> Kembali ke Daftar
        </a>
    </div>
</div>
@stop

@section('content')
<div class="px-4 pb-12">
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        
        {{-- Main Form Section --}}
        <div class="xl:col-span-8">
            <form action="{{ route('admin.school-teachers.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    {{-- Header Form --}}
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-800">Informasi Pribadi</h3>
                        <p class="text-sm text-gray-500">Gunakan nama lengkap beserta gelar akademik.</p>
                    </div>

                    <div class="p-8 space-y-6">
                        {{-- Error Handling --}}
                        @if ($errors->any())
                            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md animate__animated animate__fadeIn">
                                <div class="flex">
                                    <i class="fas fa-circle-exclamation text-red-500 mt-1"></i>
                                    <div class="ml-3">
                                        <p class="text-sm font-bold text-red-800">Mohon perbaiki kesalahan berikut:</p>
                                        <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nama --}}
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-id-badge text-gray-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                    </div>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                        class="block w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all placeholder-gray-400"
                                        placeholder="Contoh: Dr. Ahmad Subarjo, M.Pd">
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="md:col-span-2">
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Institusi <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                    </div>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                        class="block w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all"
                                        placeholder="nama.guru@sekolah.sch.id">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Security Section --}}
                    <div class="px-8 py-6 border-t border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-800">Keamanan Akun</h3>
                        <p class="text-sm text-gray-500">Atur kata sandi untuk akses login pertama kali.</p>
                    </div>
                    
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-key text-gray-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                </div>
                                <input type="password" name="password" id="password" required
                                    class="block w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all"
                                    placeholder="••••••••">
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-shield-check text-gray-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                </div>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                    class="block w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    {{-- Footer Action --}}
                    <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 flex flex-col md:flex-row md:justify-end space-y-3 md:space-y-0 md:space-x-4">
                        <button type="reset" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">
                            Reset Form
                        </button>
                        <button type="submit"
                            class="inline-flex justify-center items-center px-10 py-3 border border-transparent text-sm font-bold rounded-xl shadow-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                            <i class="fas fa-check-circle mr-2"></i> Simpan Data Guru
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Side Info --}}
        <div class="xl:col-span-4 space-y-6">
            <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-8 shadow-lg text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-lightbulb mr-3 opacity-80"></i> Bantuan Cepat
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-white/20 rounded-full flex items-center justify-center text-xs font-bold mt-1">1</div>
                            <p class="ml-3 text-sm text-indigo-50 leading-relaxed">
                                <strong>Role Otomatis:</strong> Akun baru akan langsung memiliki akses sebagai <b>Guru Aktif</b>.
                            </p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-white/20 rounded-full flex items-center justify-center text-xs font-bold mt-1">2</div>
                            <p class="ml-3 text-sm text-indigo-50 leading-relaxed">
                                <strong>Email Unik:</strong> Pastikan alamat email belum pernah terdaftar sebelumnya di sistem.
                            </p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-white/20 rounded-full flex items-center justify-center text-xs font-bold mt-1">3</div>
                            <p class="ml-3 text-sm text-indigo-50 leading-relaxed">
                                <strong>Jadwal:</strong> Setelah ini, Anda dapat langsung menuju menu <b>Atur Jadwal</b> untuk memplot jam mengajar.
                            </p>
                        </div>
                    </div>
                </div>
                {{-- Decorative Circle --}}
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                <h4 class="text-sm font-bold text-gray-900 mb-4 flex items-center uppercase tracking-widest">
                    <i class="fas fa-info-circle mr-2 text-indigo-500"></i> Standar Keamanan
                </h4>
                <ul class="text-xs text-gray-500 space-y-3">
                    <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Minimal 8 Karakter</li>
                    <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Enkripsi hash satu arah</li>
                    <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Audit log pendaftaran</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@stop