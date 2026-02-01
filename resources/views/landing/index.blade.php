@extends('layouts.landing')

{{-- DYNAMIC DATA SETUP --}}
@php
    use Illuminate\Support\Facades\Storage;
    $settings = $settings ?? \App\Models\Setting::pluck('value', 'key')->toArray();
    $schoolName = $settings['school_name'] ?? 'E-Absensi Siswa';
@endphp

@section('title', $schoolName . ' - Sistem Absensi Digital')

@section('content')
    @include('landing.partials.hero', ['settings' => $settings])
    @include('landing.partials.features')
    @include('landing.partials.how-it-works')
@endsection