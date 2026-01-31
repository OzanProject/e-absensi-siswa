@extends('layouts.adminlte')

@section('title', 'Isi Jurnal Mengajar')

@section('content_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-4">
    <div class="mb-3 sm:mb-0">
        <h1 class="m-0 text-gray-800 font-extrabold text-2xl tracking-tight flex items-center">
            <i class="fas fa-edit text-indigo-600 mr-3"></i> Form Jurnal
        </h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Catat materi dan presensi siswa untuk sesi ini.</p>
    </div>
    <a href="{{ route('teacher.dashboard') }}"
        class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 rounded-xl font-bold text-sm text-gray-600 hover:bg-gray-50 hover:text-indigo-600 hover:border-indigo-100 transition-all shadow-sm">
        <i class="fas fa-arrow-left mr-2"></i> Kembali
    </a>
</div>
@stop

@section('content')
<form action="{{ route('teacher.journals.store', $schedule->id) }}" method="POST" id="journalForm" class="pb-32">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- LEFT COLUMN: INFO & JOURNAL DETAILS (1/3) --}}
        <div class="lg:col-span-1 space-y-6">
            
            {{-- Class Info Widget --}}
            <div class="bg-gradient-to-br from-indigo-700 to-indigo-800 rounded-3xl p-6 text-white shadow-xl shadow-indigo-200 relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 transform translate-x-4">
                    <i class="fas fa-book-open text-9xl"></i>
                </div>
                
                <div class="relative z-10">
                     <div class="flex items-center mb-5 opacity-90">
                         <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center mr-3 backdrop-blur-sm">
                            <i class="fas fa-chalkboard-teacher text-sm"></i>
                         </div>
                         <h3 class="font-bold text-lg tracking-wide">Info Kelas</h3>
                     </div>
                     
                     <div class="space-y-5">
                         <div>
                             <p class="text-indigo-200 text-[10px] font-bold uppercase tracking-widest mb-1">Mata Pelajaran</p>
                             <h2 class="text-2xl font-extrabold leading-tight text-white mb-1">{{ $schedule->subject->name }}</h2>
                             <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg bg-white/10 text-xs font-medium text-indigo-50 border border-white/20 backdrop-blur-md">
                                {{ $schedule->class->name }}
                             </span>
                         </div>
                         
                         <div>
                              <p class="text-indigo-200 text-[10px] font-bold uppercase tracking-widest mb-2">Jadwal Sesi</p>
                              <div class="flex items-center space-x-3">
                                  <div class="flex items-center bg-black/20 rounded-xl px-3 py-2 border border-white/10">
                                      <i class="far fa-clock text-indigo-300 mr-2"></i>
                                      <span class="font-mono font-bold">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</span>
                                  </div>
                                  <span class="text-indigo-300 font-bold">-</span>
                                  <div class="flex items-center bg-black/20 rounded-xl px-3 py-2 border border-white/10">
                                      <script>
                                          // Simple script to show end time icon
                                          document.write('<i class="fas fa-stopwatch text-indigo-300 mr-2"></i>');
                                      </script>
                                      <span class="font-mono font-bold">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                                  </div>
                              </div>
                         </div>
                     </div>
                </div>
            </div>

            {{-- Journal Form Widget --}}
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden relative">
                 <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                 <div class="p-6">
                    <h3 class="font-bold text-gray-800 text-lg mb-6 flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center mr-3">
                            <i class="fas fa-pen-nib"></i>
                        </span>
                        Catatan Jurnal
                    </h3>
                    
                    <div class="space-y-5">
                        <div class="form-group group">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 group-focus-within:text-indigo-600 transition-colors">
                                Topik Pembahasan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500">
                                    <i class="fas fa-heading"></i>
                                </span>
                                <input type="text" name="topic"
                                    class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition duration-200 text-sm font-semibold text-gray-800 placeholder-gray-400"
                                    placeholder="Contoh: Bab 3 - Aljabar Linear" required>
                            </div>
                        </div>
                        
                        <div class="form-group group">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 group-focus-within:text-indigo-600 transition-colors">
                                Catatan Tambahan
                            </label>
                            <textarea name="notes"
                                class="w-full px-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition duration-200 text-sm text-gray-800 placeholder-gray-400 resize-none"
                                rows="4" placeholder="PR, kejadian khusus, atau catatan evaluasi..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Tips Card --}}
            <div class="bg-amber-50 rounded-2xl p-4 border border-amber-100 flex items-start gap-3">
                <i class="fas fa-lightbulb text-amber-500 mt-1"></i>
                <div class="text-sm text-amber-800">
                    <strong class="block mb-1">Tips Efisiensi</strong>
                    Gunakan tombol "Tandai Semua Hadir" di kanan atas tabel untuk mempercepat pengisian absen jika mayoritas siswa hadir.
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: ATTENDANCE LIST (2/3) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden flex flex-col h-full">
                
                {{-- Header --}}
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/30 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center">
                        <span class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mr-3 shadow-sm">
                            <i class="fas fa-users"></i>
                        </span>
                        <div>
                            <h3 class="font-bold text-gray-800 text-lg">Absensi Siswa</h3>
                            <p class="text-xs text-gray-500 font-medium">{{ $students->count() }} Terdaftar</p>
                        </div>
                    </div>
                    
                    <button type="button" onclick="setAll('Hadir')" class="w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 transition-all text-sm shadow-sm flex items-center justify-center group active:scale-95">
                        <i class="fas fa-check-double mr-2 text-gray-400 group-hover:text-emerald-500"></i> Tandai Semua Hadir
                    </button>
                </div>

                {{-- Table Content --}}
                <div class="flex-1 overflow-x-auto bg-white">
                    @if($students->isEmpty())
                        <div class="flex flex-col items-center justify-center h-64 text-center p-8">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-user-slash text-3xl text-gray-300"></i>
                            </div>
                            <h4 class="text-gray-900 font-bold text-lg">Kelas Kosong</h4>
                            <p class="text-gray-500 text-sm mt-1 max-w-xs">Tidak ada data siswa yang terdaftar di kelas ini. Hubungi admin jika ini kesalahan.</p>
                        </div>
                    @else
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50/80 text-xs uppercase tracking-wider text-gray-500 font-extrabold sticky top-0 z-10 backdrop-blur-sm border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4 w-16 text-center text-gray-400">#</th>
                                    <th class="px-6 py-4">Informasi Siswa</th>
                                    <th class="px-6 py-4 w-1/2">Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($students as $index => $student)
                                    <tr class="hover:bg-indigo-50/20 transition-colors group">
                                        <td class="px-6 py-4 text-center text-gray-400 font-mono text-sm group-hover:text-indigo-400">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-600 font-bold text-xs mr-3 border border-gray-100 shadow-sm">
                                                    {{ substr($student->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-800 text-sm group-hover:text-indigo-700 transition-colors">{{ $student->name }}</div>
                                                    <div class="text-[10px] text-gray-400 font-mono mt-0.5 bg-gray-100 inline-block px-1.5 py-0.5 rounded">{{ $student->nisn }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            {{-- Custom Radio Group --}}
                                            <div class="inline-flex bg-gray-100 p-1 rounded-xl shadow-inner gap-1">
                                                @php 
                                                    $statuses = [
                                                        'Hadir' => ['label' => 'H', 'active' => 'bg-emerald-500 text-white shadow-md', 'inactive' => 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-100'],
                                                        'Sakit' => ['label' => 'S', 'active' => 'bg-blue-500 text-white shadow-md', 'inactive' => 'text-gray-500 hover:text-blue-600 hover:bg-blue-100'],
                                                        'Izin' =>  ['label' => 'I', 'active' => 'bg-amber-500 text-white shadow-md', 'inactive' => 'text-gray-500 hover:text-amber-600 hover:bg-amber-100'],
                                                        'Alpha' => ['label' => 'A', 'active' => 'bg-red-500 text-white shadow-md', 'inactive' => 'text-gray-500 hover:text-red-600 hover:bg-red-100'],
                                                        'Terlambat' => ['label' => 'T', 'active' => 'bg-purple-500 text-white shadow-md', 'inactive' => 'text-gray-500 hover:text-purple-600 hover:bg-purple-100'],
                                                    ];
                                                @endphp

                                                @foreach($statuses as $key => $style)
                                                    <label class="relative cursor-pointer group/label">
                                                        <input type="radio" name="attendances[{{ $student->id }}]" value="{{ $key }}" class="peer sr-only attendance-radio" {{ $key == 'Hadir' ? 'checked' : '' }} onchange="updateSummary()">
                                                        
                                                        {{-- Visual Button --}}
                                                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center font-bold text-sm transition-all duration-200 {{ $style['inactive'] }} peer-checked:{{ $style['active'] }} peer-focus:ring-2 ring-offset-1 ring-indigo-200">
                                                            {{ $style['label'] }}
                                                        </div>
                                                        
                                                        {{-- Tooltip --}}
                                                        <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-[10px] px-2 py-1 rounded opacity-0 peer-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-20">
                                                            {{ $key }}
                                                        </div>
                                                    </label>
                                                @endforeach
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

    {{-- FIXED ACTION FOOTER --}}
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-[0_-5px_20px_-10px_rgba(0,0,0,0.1)] z-50 px-4 sm:px-8 py-4 bg-white/90 backdrop-blur-lg">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            
            {{-- Live Summary --}}
            <div class="flex items-center gap-2 sm:gap-6 w-full sm:w-auto justify-center sm:justify-start order-2 sm:order-1 overflow-x-auto py-1 hide-scrollbar">
                <div class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest hidden sm:block mr-2">
                    Ringkasan:
                </div>
                {{-- Summary items populated via JS --}}
                <div class="flex gap-2 sm:gap-4">
                     <div class="flex items-center px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-100">
                         <div class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></div>
                         <span class="text-xs font-bold text-gray-600 mr-1.5">Hadir</span>
                         <span id="count-Hadir" class="text-sm font-extrabold text-emerald-700">0</span>
                     </div>
                     <div class="flex items-center px-3 py-1.5 rounded-lg bg-blue-50 border border-blue-100">
                         <div class="w-2 h-2 rounded-full bg-blue-500 mr-2"></div>
                         <span class="text-xs font-bold text-gray-600 mr-1.5">Sakit</span>
                         <span id="count-Sakit" class="text-sm font-extrabold text-blue-700">0</span>
                     </div>
                     <div class="flex items-center px-3 py-1.5 rounded-lg bg-amber-50 border border-amber-100">
                         <div class="w-2 h-2 rounded-full bg-amber-500 mr-2"></div>
                         <span class="text-xs font-bold text-gray-600 mr-1.5">Izin</span>
                         <span id="count-Izin" class="text-sm font-extrabold text-amber-700">0</span>
                     </div>
                     <div class="flex items-center px-3 py-1.5 rounded-lg bg-red-50 border border-red-100">
                         <div class="w-2 h-2 rounded-full bg-red-500 mr-2"></div>
                         <span class="text-xs font-bold text-gray-600 mr-1.5">Alpha</span>
                         <span id="count-Alpha" class="text-sm font-extrabold text-red-700">0</span>
                     </div>
                </div>
            </div>

            {{-- Action Button --}}
            <button type="submit" id="submitBtn" class="order-1 sm:order-2 w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-2xl shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 flex items-center justify-center text-sm sm:text-base">
                <i class="fas fa-save mr-2.5"></i> Simpan Jurnal & Absensi
            </button>
        </div>
    </div>
</form>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // 1. Live Summary Logic
    function updateSummary() {
        const counts = { 'Hadir': 0, 'Sakit': 0, 'Izin': 0, 'Alpha': 0, 'Terlambat': 0 };
        document.querySelectorAll('.attendance-radio:checked').forEach(radio => {
            const val = radio.value;
            if (counts.hasOwnProperty(val)) counts[val]++;
        });

        // Update DOM with animation
        for (const [key, value] of Object.entries(counts)) {
            const el = document.getElementById(`count-${key}`);
            if(el) {
                // Simple animation
                if(el.innerText != value) {
                    el.classList.add('scale-150', 'text-indigo-600');
                    setTimeout(() => el.classList.remove('scale-150', 'text-indigo-600'), 200);
                }
                el.innerText = value;
            }
        }
    }

    // 2. Set All Helper
    function setAll(status) {
        document.querySelectorAll(`input[type="radio"][value="${status}"]`).forEach(radio => {
            radio.checked = true;
        });
        updateSummary();
        
        // Fun Toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'bottom-start',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            customClass: {
                popup: 'colored-toast'
            }
        });
        Toast.fire({
            icon: 'success',
            title: `Semua siswa ditandai ${status}!`
        });
    }

    // 3. Init
    document.addEventListener('DOMContentLoaded', () => {
        updateSummary();
        
        // Form Submit state
        document.getElementById('journalForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Menyimpan...';
            btn.classList.add('opacity-80', 'cursor-not-allowed');
        });
    });
</script>
<style>
    /* Hide scrollbar for gallery but keep functionality */
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@stop
