@extends('layouts.adminlte')

@section('title', 'Edit Jurnal Mengajar')

@section('content_header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="m-0 text-gray-800 font-bold text-2xl tracking-tight">Edit Jurnal</h1>
        <p class="text-sm text-gray-500 mt-1">Perbarui data jurnal dan absensi siswa.</p>
    </div>
    <a href="{{ route('teacher.journals.index') }}"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl font-bold text-xs text-gray-600 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:text-indigo-600 transition ease-in-out duration-150">
        <i class="fas fa-arrow-left mr-2"></i> Batal
    </a>
</div>
@stop

@section('content')
<form action="{{ route('teacher.journals.update', $journal->id) }}" method="POST" id="journalForm">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KOLOM KIRI: INFO & FORM JURNAL --}}
        <div class="lg:col-span-1 space-y-6">
            
            {{-- Info Card --}}
            <div class="bg-indigo-600 rounded-3xl p-6 text-white shadow-lg shadow-indigo-200 relative overflow-hidden">
                <div class="relative z-10">
                     <div class="flex items-center mb-4 opacity-80">
                         <i class="fas fa-edit text-xl mr-2"></i>
                         <h3 class="font-bold text-lg">Mengedit Jurnal</h3>
                     </div>
                     
                     <div class="space-y-4">
                         <div>
                             <p class="text-indigo-200 text-xs font-bold uppercase tracking-wider mb-1">Mata Pelajaran</p>
                             <h2 class="text-2xl font-bold leading-tight">{{ $schedule->subject->name }}</h2>
                         </div>
                         
                         <div class="flex justify-between items-end">
                             <div>
                                 <p class="text-indigo-200 text-xs font-bold uppercase tracking-wider mb-1">Kelas</p>
                                 <p class="text-xl font-bold">{{ $schedule->class->name }}</p>
                             </div>
                             <div class="text-right">
                                  <p class="text-indigo-200 text-xs font-bold uppercase tracking-wider mb-1">Tanggal</p>
                                  <p class="text-lg font-mono font-medium bg-indigo-500/30 px-2 py-1 rounded-lg inline-block">
                                      {{ $journal->date->format('d M Y') }}
                                  </p>
                             </div>
                         </div>
                     </div>
                </div>
                
                {{-- Decor --}}
                <div class="absolute -right-6 -bottom-6 text-indigo-500 opacity-20">
                     <i class="fas fa-pencil-alt text-9xl"></i>
                </div>
            </div>

            {{-- Jurnal Input Card --}}
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center">
                        <i class="fas fa-pen-fancy text-indigo-500 mr-2"></i> Detail Jurnal
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="form-group">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Topik / Materi Pembahasan <span class="text-red-500">*</span></label>
                            <input type="text" name="topic" value="{{ old('topic', $journal->topic) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition p-3 text-sm font-medium"
                                required>
                        </div>
                        
                        <div class="form-group">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Tambahan (Opsional)</label>
                            <textarea name="notes"
                                class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition p-3 text-sm"
                                rows="4">{{ old('notes', $journal->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Submit for Desktop (Sticky) --}}
            <div class="hidden lg:block sticky top-6">
                <button type="submit" id="submitBtnDesktop" class="w-full py-4 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200 flex items-center justify-center text-lg">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </div>

        {{-- KOLOM KANAN: ABSENSI LIST --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <h3 class="font-bold text-gray-800 text-lg flex items-center">
                        <i class="fas fa-user-check text-emerald-500 mr-2"></i> Absensi Siswa
                        <span class="ml-2 bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-lg">{{ $students->count() }} Siswa</span>
                    </h3>
                    
                    <button type="button" class="w-full sm:w-auto px-4 py-2 bg-emerald-100 text-emerald-700 font-bold rounded-xl hover:bg-emerald-200 transition-colors text-sm shadow-sm" onclick="setAll('Hadir')">
                        <i class="fas fa-check-double mr-2"></i> Reset Semua Hadir
                    </button>
                </div>

                <div class="p-0">
                     <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-white text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-100 sticky top-0 z-10 shadow-sm">
                                <tr>
                                    <th class="px-6 py-4 w-12 text-center">No</th>
                                    <th class="px-6 py-4">Nama Siswa</th>
                                    <th class="px-6 py-4 text-center">Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($students as $index => $student)
                                    @php
                                        $currentStatus = $attendances[$student->id] ?? 'Hadir';
                                    @endphp
                                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                                        <td class="px-6 py-4 text-center text-gray-400 font-medium">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-800 text-sm group-hover:text-indigo-700 transition">{{ $student->name }}</div>
                                            <div class="text-xs text-gray-400 font-mono">{{ $student->nisn }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-center flex-wrap gap-1 sm:gap-2">
                                                @php 
                                                    $statuses = [
                                                        'Hadir' => ['class' => 'peer-checked:bg-emerald-500 peer-checked:border-emerald-500', 'label' => 'H', 'icon' => 'fa-check', 'bg' => 'hover:bg-emerald-50 text-emerald-600', 'tip' => 'Hadir'],
                                                        'Izin' => ['class' => 'peer-checked:bg-amber-400 peer-checked:border-amber-400', 'label' => 'I', 'icon' => 'fa-envelope', 'bg' => 'hover:bg-amber-50 text-amber-500', 'tip' => 'Izin'],
                                                        'Sakit' => ['class' => 'peer-checked:bg-blue-400 peer-checked:border-blue-400', 'label' => 'S', 'icon' => 'fa-procedures', 'bg' => 'hover:bg-blue-50 text-blue-500', 'tip' => 'Sakit'],
                                                        'Alpha' => ['class' => 'peer-checked:bg-red-500 peer-checked:border-red-500', 'label' => 'A', 'icon' => 'fa-times', 'bg' => 'hover:bg-red-50 text-red-500', 'tip' => 'Alpha'],
                                                        'Terlambat' => ['class' => 'peer-checked:bg-gray-500 peer-checked:border-gray-500', 'label' => 'T', 'icon' => 'fa-stopwatch', 'bg' => 'hover:bg-gray-50 text-gray-500', 'tip' => 'Terlambat'],
                                                    ];
                                                @endphp
                                                
                                                @foreach($statuses as $key => $style)
                                                    <label class="cursor-pointer relative group/radio" title="{{ $style['tip'] }}">
                                                        <input type="radio" name="attendances[{{ $student->id }}]" value="{{ $key }}" class="peer sr-only" {{ $currentStatus == $key ? 'checked' : '' }}>
                                                        
                                                        <div class="w-10 h-10 sm:w-11 sm:h-11 flex flex-col items-center justify-center rounded-xl border-2 border-gray-100 bg-white transition-all duration-200 {{ $style['class'] }} peer-checked:text-white peer-checked:shadow-md peer-focus:ring-2 peer-focus:ring-indigo-300 {{ $style['bg'] }}">
                                                            <span class="font-bold text-sm">{{ $style['label'] }}</span>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Mobile Submit (Floating) --}}
        <div class="lg:hidden fixed bottom-6 left-6 right-6 z-50">
             <button type="submit" id="submitBtnMobile" class="w-full py-4 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-bold rounded-2xl shadow-2xl hover:shadow-amber-500/50 flex items-center justify-center text-lg backdrop-blur-sm">
                <i class="fas fa-save mr-2"></i> Update Data
            </button>
        </div>
    </div>
</form>

@section('js')
<script>
    function setAll(status) {
        const radios = document.querySelectorAll(`input[type="radio"][value="${status}"]`);
        radios.forEach(radio => {
            radio.checked = true;
        });
        
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });

        Toast.fire({
            icon: 'success',
            title: 'Semua siswa ditandai ' + status
        });
    }

    // Submit Loading State
    const form = document.querySelector('#journalForm');
    form.addEventListener('submit', function() {
        const btnDesktop = document.querySelector('#submitBtnDesktop');
        const btnMobile = document.querySelector('#submitBtnMobile');
        
        if(btnDesktop) {
            btnDesktop.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Menyimpan...';
            btnDesktop.disabled = true;
            btnDesktop.classList.add('opacity-75');
        }
        
        if(btnMobile) {
            btnMobile.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Menyimpan...';
            btnMobile.disabled = true;
             btnMobile.classList.add('opacity-75');
        }
    });
</script>
@stop
@stop
