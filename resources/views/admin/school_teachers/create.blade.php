@extends('layouts.adminlte')

@section('title', 'Tambah Guru')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight flex items-center">
            <i class="fas fa-user-plus text-purple-600 mr-3"></i> Tambah Guru Baru
        </h1>
        <p class="text-sm text-gray-500 mt-1">Buat akun guru baru untuk mengakses sistem.</p>
    </div>
    <a href="{{ route('admin.school-teachers.index') }}"
        class="group flex items-center px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-indigo-600 transition-all duration-200 shadow-sm">
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
                    <div
                        class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg animate__animated animate__fadeIn">
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

                <form action="{{ route('admin.school-teachers.store') }}" method="POST">
                    @csrf

                    {{-- Section: Profil --}}
                    <div class="mb-8">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">
                            <i class="fas fa-id-card text-indigo-500 mr-2"></i> Profil & Identitas
                        </h4>

                        {{-- Nama --}}
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                                Nama Lengkap & Gelar <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-xl shadow-sm group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i
                                        class="fas fa-user-tie text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                </div>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium placeholder-gray-400"
                                    placeholder="Contoh: Budi Santoso, S.Pd">
                            </div>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-bold text-gray-700 mb-2">
                                Alamat Email <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-xl shadow-sm group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i
                                        class="fas fa-envelope text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                </div>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                    class="focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium placeholder-gray-400"
                                    placeholder="guru@sekolah.sch.id">
                            </div>
                        </div>
                    </div>

                    {{-- Section: Keamanan --}}
                    <div class="mb-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">
                            <i class="fas fa-shield-alt text-indigo-500 mr-2"></i> Keamanan Akun
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Password --}}
                            <div>
                                <label for="password" class="block text-sm font-bold text-gray-700 mb-2">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-xl shadow-sm group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i
                                            class="fas fa-lock text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                    </div>
                                    <input type="password" name="password" id="password" required
                                        class="focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium placeholder-gray-400"
                                        placeholder="Minimal 8 karakter">
                                </div>
                            </div>

                            {{-- Confirm Password --}}
                            <div>
                                <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-2">
                                    Konfirmasi Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-xl shadow-sm group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i
                                            class="fas fa-check-circle text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                    </div>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        required
                                        class="focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 block w-full pl-11 border-gray-300 rounded-xl py-3.5 transition-all text-sm font-medium placeholder-gray-400"
                                        placeholder="Ulangi password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                        <button type="reset"
                            class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition duration-200 shadow-sm">
                            <i class="fas fa-undo mr-2 text-gray-500"></i> Reset
                        </button>
                        <button type="submit"
                            class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-xl transform transition hover:-translate-y-1">
                            <i class="fas fa-save mr-2"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="lg:col-span-1">
        <div class="bg-indigo-50 rounded-3xl p-8 border border-indigo-100 h-full">
            <h3 class="text-lg font-bold text-indigo-900 mb-6 flex items-center">
                <div class="w-10 h-10 rounded-xl bg-indigo-200 flex items-center justify-center mr-3 shadow-sm">
                    <i class="fas fa-lightbulb text-indigo-600"></i>
                </div>
                Informasi & Tips
            </h3>
            <div class="space-y-6">
                <div class="p-4 bg-white rounded-2xl shadow-sm border border-indigo-50">
                    <h5 class="font-bold text-gray-800 text-sm mb-2"><i
                            class="fas fa-user-tag text-indigo-500 mr-1"></i> Role Otomatis</h5>
                    <p class="text-gray-600 text-xs leading-relaxed">
                        Akun yang dibuat melalui halaman ini akan otomatis mendapatkan Role <b>"Guru"</b> dan status
                        <b>Approved</b> (Aktif).
                    </p>
                </div>

                <div class="p-4 bg-white rounded-2xl shadow-sm border border-indigo-50">
                    <h5 class="font-bold text-gray-800 text-sm mb-2"><i class="fas fa-key text-indigo-500 mr-1"></i>
                        Keamanan</h5>
                    <p class="text-gray-600 text-xs leading-relaxed">
                        Pastikan menggunakan password yang kuat (minimal 8 karakter kombinasi huruf dan angka) untuk
                        keamanan data sekolah.
                    </p>
                </div>

                <div class="p-4 bg-white rounded-2xl shadow-sm border border-indigo-50">
                    <h5 class="font-bold text-gray-800 text-sm mb-2"><i
                            class="fas fa-calendar-alt text-indigo-500 mr-1"></i> Jadwal Mengajar</h5>
                    <p class="text-gray-600 text-xs leading-relaxed">
                        Setelah akun dibuat, Anda dapat mengatur jadwal mengajar guru ini di menu <b>"Atur Jadwal"</b>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop