@extends('layouts.adminlte')

@section('title', 'Proses Permintaan Izin Daring')

@section('content_header')
{{-- HEADER: Menggunakan Tailwind & Warna Purple/Indigo --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <h1 class="text-2xl font-bold text-gray-800 flex items-center mb-2 sm:mb-0">
        <i class="fas fa-envelope-open-text text-purple-600 mr-2"></i>
        <span>Proses Permintaan Izin Kelas {{ Auth::user()->homeroomTeacher->class->name ?? 'N/A' }}</span>
    </h1>
</div>
@stop

@section('content')
    {{-- Notifikasi Sukses/Error (Styling Tailwind) --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg relative mb-6 alert-dismissible" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-6 alert-dismissible" role="alert">
            <i class="fas fa-ban mr-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- CARD CONTAINER (Mengganti class Bootstrap card) --}}
    <div class="bg-white shadow-xl rounded-xl border border-gray-100 overflow-hidden">
        
        {{-- CARD HEADER (Mengganti bg-primary) --}}
        <div class="px-5 py-4 bg-indigo-600 text-white flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold flex items-center"><i class="fas fa-list-ul mr-2"></i> Daftar Permintaan Izin Daring</h3>
                <p class="text-sm text-indigo-100 mt-1">Total Permintaan: {{ $izinRequests->total() }}</p>
            </div>
        </div>
        
        {{-- CARD BODY (Tabel) --}}
        <div class="p-0">
            <div class="overflow-x-auto">
                {{-- TABLE --}}
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Anak</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal Izin</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Diajukan Oleh</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($izinRequests as $request)
                        <tr class="hover:bg-purple-50/50 transition duration-150">
                            {{-- Nama Siswa & Keterangan --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <strong class="text-gray-900">{{ $request->student->name ?? '-' }}</strong><br>
                                <small class="text-gray-500">Keterangan: {{ $request->reason }}</small>
                            </td>
                            {{-- Kelas --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $request->student->class->name ?? 'N/A' }}</td>
                            {{-- Tanggal Izin --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">{{ $request->request_date->format('d M Y') }}</td>
                            {{-- Jenis --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $typeClass = $request->type == 'Sakit' ? 'bg-cyan-100 text-cyan-800' : 'bg-amber-100 text-amber-800';
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full {{ $typeClass }}">{{ $request->type }}</span>
                            </td>
                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @php
                                    $statusMap = ['Pending' => 'bg-yellow-100 text-yellow-800', 'Approved' => 'bg-green-100 text-green-800', 'Rejected' => 'bg-red-100 text-red-800'];
                                    $statusClass = $statusMap[$request->status] ?? 'bg-gray-200 text-gray-700';
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full {{ $statusClass }}">
                                    {{ $request->status }}
                                </span>
                                @if($request->approved_by)
                                    <br><small class="text-gray-500">Oleh: {{ $request->approver->name ?? 'Admin' }}</small>
                                @endif
                            </td>
                            {{-- Diajukan Oleh --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $request->created_at->format('d/m H:i') }}</td>
                            {{-- Aksi --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <div class="inline-flex space-x-1 items-center">
                                    @if($request->attachment_path)
                                        <a href="{{ asset('storage/' . $request->attachment_path) }}" target="_blank" 
                                           class="text-indigo-600 hover:text-indigo-800 p-2 rounded-full bg-indigo-50 transition duration-150 shadow-sm" title="Lihat Lampiran">
                                            <i class="fas fa-paperclip w-4 h-4"></i>
                                        </a>
                                    @endif

                                    @if($request->status === 'Pending')
                                        <div class="inline-flex space-x-1">
                                            {{-- Setujui (Success) --}}
                                            <button type="button" 
                                                    class="text-green-700 hover:text-green-900 p-2 rounded-full bg-green-100 transition duration-150 shadow-sm" 
                                                    onclick="confirmProcess('{{ $request->id }}', 'approve')" title="Setujui">
                                                <i class="fas fa-check w-4 h-4"></i>
                                            </button>
                                            {{-- Tolak (Danger) --}}
                                            <button type="button" 
                                                    class="text-red-700 hover:text-red-900 p-2 rounded-full bg-red-100 transition duration-150 shadow-sm" 
                                                    onclick="confirmProcess('{{ $request->id }}', 'reject')" title="Tolak">
                                                <i class="fas fa-times w-4 h-4"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 text-center text-gray-500 py-6">Tidak ada permintaan izin yang perlu diproses saat ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- CARD FOOTER (Pagination) --}}
        <div class="px-5 py-3 border-t border-gray-200 flex justify-between items-center">
            {{ $izinRequests->links('pagination::tailwind') }}
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // 🚨 Logika Submit untuk Approve/Reject (LOGIKA AMAN)
    window.confirmProcess = function(id, action) {
        const title = action === 'approve' ? 'Setujui Izin?' : 'Tolak Izin?';
        const icon = action === 'approve' ? 'question' : 'warning';
        const color = action === 'approve' ? '#28a745' : '#dc3545'; // Green-600 vs Red-600

        Swal.fire({
            title: title,
            text: `Anda akan ${action === 'approve' ? 'menyetujui' : 'menolak'} permintaan ini. Absensi harian akan diupdate.`,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: color,
            cancelButtonColor: '#6c757d',
            confirmButtonText: action === 'approve' ? 'Ya, Setujui' : 'Ya, Tolak',
        }).then((result) => {
            if (result.isConfirmed) {
                // KUNCI: Membuat form dinamis untuk POST request
                const form = document.createElement('form');
                // Menggunakan URL helper untuk memastikan rute POST yang benar (LOGIKA AMAN)
                form.action = '{{ url('walikelas/izin') }}/' + id + '/' + action; 
                form.method = 'POST';
                form.style.display = 'none';

                // Tambahkan CSRF Token
                const csrfToken = document.createElement('input');
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Auto-dismiss alerts (Tailwind alerts)
    $(document).ready(function() {
        setTimeout(function() {
            $('.alert-dismissible').fadeOut(400, function() { $(this).remove(); });
        }, 5000);
    });
</script>
@endsection

@section('css')
<style>
/* --- MINIMAL CUSTOM CSS FOR TAILWIND --- */
.text-purple-600 { color: #9333ea; }
.bg-indigo-600 { background-color: #4f46e5 !important; }

/* Warna Custom Badges */
.bg-cyan-100 { background-color: #cffafe; }
.text-cyan-800 { color: #0e7490; }
.bg-amber-100 { background-color: #fef3c7; }
.text-amber-800 { color: #9a3412; }

/* Bootstrap overrides for correct appearance */
.table { width: 100%; border-collapse: collapse; }
.table-striped tbody tr:nth-of-type(odd) { background-color: #f9fafb; } /* gray-50 */
.table-valign-middle td, .table-valign-middle th { vertical-align: middle !important; }
</style>
@endsection