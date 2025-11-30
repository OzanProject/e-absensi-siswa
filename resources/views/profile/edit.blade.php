@extends('layouts.adminlte')

@section('title', 'Edit Profil Akun')

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        {{-- Mengganti blue-600 ke indigo-600 --}}
        <i class="fas fa-user-circle text-indigo-600 mr-2"></i>
        <span>{{ __('Profile') }}</span>
    </h1>
    
    <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
        <ol class="flex space-x-2">
            {{-- Mengganti blue-600 ke indigo-600 --}}
            <li><a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Profil</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
    {{-- Grid Layout untuk Konten Profil --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- KOLOM KIRI (2/3): Update Info & Password --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. Update Profile Information --}}
            {{-- Mengganti p-5 menjadi p-6, shadow-lg, dan border-gray-100 --}}
            <div class="bg-white p-6 shadow-xl rounded-xl border border-gray-100">
                <div class="max-w-xl">
                    {{-- LOGIKA AMAN: INCLUDE PARTIAL --}}
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- 2. Update Password --}}
            <div class="bg-white p-6 shadow-xl rounded-xl border border-gray-100">
                <div class="max-w-xl">
                    {{-- LOGIKA AMAN: INCLUDE PARTIAL --}}
                    @include('profile.partials.update-password-form')
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN (1/3): Delete Account & Info --}}
        <div class="lg:col-span-1 space-y-6 mt-6 lg:mt-0">
            
            {{-- 3. Delete User Account --}}
            {{-- Menggunakan background merah muda (pink) untuk peringatan --}}
            <div class="bg-red-50 p-6 shadow-xl rounded-xl border border-red-200">
                <div class="max-w-xl">
                    {{-- LOGIKA AMAN: INCLUDE PARTIAL --}}
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            
            {{-- OPTIONAL: Info Card --}}
            {{-- Menggunakan border kiri Indigo untuk visual informasi --}}
            <div class="bg-white p-4 shadow-md rounded-xl border-l-4 border-indigo-500">
                <h4 class="font-bold text-lg text-indigo-700 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i> Informasi Akun
                </h4>
                <p class="text-sm text-gray-600 mt-2">
                    Anda dapat memperbarui informasi pribadi dan kata sandi Anda di sini. Untuk keamanan, harap gunakan kata sandi yang kuat.
                </p>
            </div>
            
        </div>
    </div>
@stop