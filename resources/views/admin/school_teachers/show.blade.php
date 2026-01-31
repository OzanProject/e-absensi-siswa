@extends('layouts.adminlte')

@section('title', 'Detail Guru')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight flex items-center">
            <i class="fas fa-id-badge text-purple-600 mr-3"></i> Detail Guru
        </h1>
        <p class="text-sm text-gray-500 mt-1">Informasi lengkap dan jadwal mengajar.</p>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('admin.school-teachers.index') }}"
            class="group flex items-center px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-indigo-600 transition-all duration-200 shadow-sm">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Kembali
        </a>
        <a href="{{ route('admin.school-teachers.edit', $teacher->id) }}"
            class="flex items-center px-4 py-2 bg-amber-50 text-amber-600 border border-amber-200 rounded-xl hover:bg-amber-100 transition-all duration-200 shadow-sm">
            <i class="fas fa-edit mr-2"></i> Edit Profil
        </a>
    </div>
</div>
@stop

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left: Profile Card --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden relative">
            <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-purple-500 to-indigo-600"></div>
            <div class="px-6 pb-6 text-center relative pt-12">
                <div class="w-24 h-24 mx-auto rounded-full bg-white p-1 shadow-lg mb-4">
                    <div
                        class="w-full h-full rounded-full bg-indigo-50 flex items-center justify-center text-3xl font-bold text-indigo-600 uppercase">
                        {{ substr($teacher->name, 0, 1) }}
                    </div>
                </div>

                <h2 class="text-xl font-bold text-gray-800">{{ $teacher->name }}</h2>
                <p
                    class="text-sm text-gray-500 mb-4 bg-indigo-50 inline-block px-3 py-1 rounded-full mt-2 font-medium text-indigo-700">
                    Guru Mata Pelajaran</p>

                <div class="flex justify-center mb-6 space-x-2">
                    @if($teacher->is_approved)
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                            <i class="fas fa-check-circle mr-1"></i> Akun Aktif
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                            <i class="fas fa-times-circle mr-1"></i> Non-Aktif
                        </span>
                    @endif
                </div>

                <div class="text-left bg-gray-50/50 rounded-2xl p-5 space-y-4 border border-gray-100">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 flex items-center"><i class="fas fa-envelope w-5 opacity-70"></i>
                            Email</span>
                        <span class="font-medium text-gray-800">{{ $teacher->email }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 flex items-center"><i
                                class="fas fa-calendar-check w-5 opacity-70"></i> Bergabung</span>
                        <span class="font-medium text-gray-800">{{ $teacher->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm pt-3 border-t border-gray-200">
                        <span class="text-gray-500 flex items-center"><i class="fas fa-layer-group w-5 opacity-70"></i>
                            Total Sesi</span>
                        <span
                            class="font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded">{{ $schedules->count() }}
                            Jadwal</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Call To Action Card --}}
        <div
            class="mt-6 bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden group">
            <div
                class="absolute -right-10 -top-10 text-white opacity-10 text-9xl transform rotate-12 group-hover:rotate-0 transition-all duration-500">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <h3 class="text-lg font-bold mb-2 relative z-10">Atur Jadwal Guru</h3>
            <p class="text-indigo-100 text-sm mb-6 relative z-10 leading-relaxed">
                Ingin menambahkan atau mengubah mata pelajaran yang diampu guru ini? Lakukan di menu Atur Jadwal.
            </p>
            <a href="{{ route('admin.schedules.index', ['teacher_id' => $teacher->id]) }}"
                class="block w-full text-center py-3 bg-white text-indigo-700 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl hover:bg-gray-50 hover:-translate-y-0.5 transition-all relative z-10">
                <i class="fas fa-cog mr-2"></i> Kelola Jadwal
            </a>
        </div>
    </div>

    {{-- Right: Schedule --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden h-full flex flex-col">
            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/30 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-lg flex items-center">
                    <i class="fas fa-chalkboard-teacher text-indigo-500 mr-2"></i> Jadwal Mengajar Minggu Ini
                </h3>
            </div>

            <div class="flex-1 overflow-auto">
                @if($schedules->isEmpty())
                    <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                        <div class="bg-gray-50 rounded-full w-20 h-20 flex items-center justify-center mb-4">
                            <i class="far fa-calendar-times text-3xl text-gray-300"></i>
                        </div>
                        <h4 class="text-gray-900 font-bold text-lg mb-1">Belum Ada Jadwal</h4>
                        <p class="text-gray-500 text-sm max-w-sm mb-6">
                            Guru ini belum memiliki jadwal mengajar aktif untuk minggu ini.
                        </p>
                        <a href="{{ route('admin.schedules.index') }}"
                            class="px-6 py-2 bg-indigo-50 text-indigo-600 font-bold rounded-xl hover:bg-indigo-100 transition-colors text-sm">
                            Buat Jadwal Baru
                        </a>
                    </div>
                @else
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-bold">
                                <th class="px-8 py-4">Hari</th>
                                <th class="px-6 py-4">Waktu</th>
                                <th class="px-6 py-4">Kelas</th>
                                <th class="px-6 py-4">Mata Pelajaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($schedules as $schedule)
                                <tr class="hover:bg-gray-50/30 transition-colors group">
                                    <td class="px-8 py-4 w-40">
                                        <span
                                            class="inline-block w-full text-center font-bold text-gray-700 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-sm capitalize shadow-sm group-hover:border-indigo-200 group-hover:text-indigo-700 transition-colors">
                                            {{ $schedule->day }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                        <div class="flex items-center">
                                            <i class="far fa-clock text-gray-400 mr-2"></i>
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded text-sm">
                                            {{ $schedule->class->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 rounded-full bg-purple-500 mr-2"></div>
                                            <span class="font-medium text-gray-700">{{ $schedule->subject->name }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@stop