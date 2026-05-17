@extends('layouts.app')

@section('title', 'Verifikasi Izin & Sakit Karyawan')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Verifikasi Izin & Sakit</h1>
        <p class="text-sm text-slate-500">Periksa, setujui, atau tolak permohonan izin/sakit dari seluruh karyawan MJA.</p>
    </div>
</div>

<!-- Filter Status -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6 flex flex-wrap gap-2 items-center justify-between">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.izin.index') }}" 
            class="px-4 py-2 text-xs font-bold rounded-xl border {{ $status === '' ? 'bg-slate-900 text-white border-slate-950' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }} transition-colors">
            Semua Status
        </a>
        <a href="{{ route('admin.izin.index', ['status' => 'pending']) }}" 
            class="px-4 py-2 text-xs font-bold rounded-xl border {{ $status === 'pending' ? 'bg-amber-500 text-white border-amber-600' : 'bg-amber-50/50 text-amber-700 border-amber-200 hover:bg-amber-100/50' }} transition-colors">
            Menunggu Verifikasi
        </a>
        <a href="{{ route('admin.izin.index', ['status' => 'approved']) }}" 
            class="px-4 py-2 text-xs font-bold rounded-xl border {{ $status === 'approved' ? 'bg-emerald-600 text-white border-emerald-700' : 'bg-emerald-50/50 text-emerald-700 border-emerald-200 hover:bg-emerald-100/50' }} transition-colors">
            Telah Disetujui
        </a>
        <a href="{{ route('admin.izin.index', ['status' => 'rejected']) }}" 
            class="px-4 py-2 text-xs font-bold rounded-xl border {{ $status === 'rejected' ? 'bg-rose-600 text-white border-rose-700' : 'bg-rose-50/50 text-rose-700 border-rose-200 hover:bg-rose-100/50' }} transition-colors">
            Telah Ditolak
        </a>
    </div>
    <div class="text-xs text-slate-400 font-medium">
        Menampilkan data pengajuan cuti, izin, dan sakit.
    </div>
</div>

@if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-sm font-semibold text-red-800">
                {{ $errors->first() }}
            </div>
        </div>
    </div>
@endif

<!-- Daftar Pengajuan -->
<div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
    @if($leaves->isEmpty())
        <div class="p-16 text-center">
            <div class="w-20 h-20 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-700">Tidak Ada Pengajuan</h3>
            <p class="text-sm text-slate-400 mt-1.5">Tidak ditemukan berkas pengajuan izin/sakit pada filter status ini.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Karyawan</th>
                        <th class="py-4 px-6">Tanggal Pengajuan</th>
                        <th class="py-4 px-6">Jenis</th>
                        <th class="py-4 px-6">Alasan / Keterangan</th>
                        <th class="py-4 px-6">Lampiran</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-center">Verifikasi Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach($leaves as $leave)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Karyawan info -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full overflow-hidden bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold text-sm shadow-sm border border-slate-200">
                                        @if($leave->user->profile_photo_url)
                                            <img src="{{ $leave->user->profile_photo_url }}" class="w-full h-full object-cover">
                                        @else
                                            {{ substr($leave->user->name, 0, 1) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $leave->user->name }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium">NIP: {{ $leave->user->employee_id ?? 'N/A' }} | {{ $leave->user->position ?? 'Karyawan' }}</p>
                                    </div>
                                </div>
                            </td>
                            <!-- Tanggal -->
                            <td class="py-4 px-6 font-semibold text-slate-700 whitespace-nowrap">
                                @if($leave->end_date && !$leave->end_date->eq($leave->date))
                                    {{ $leave->date->translatedFormat('d/m/Y') }} - {{ $leave->end_date->translatedFormat('d/m/Y') }}
                                    <span class="block text-[10px] text-slate-400 font-medium">Durasi: {{ $leave->duration }} Hari</span>
                                @else
                                    {{ $leave->date->translatedFormat('d F Y') }}
                                    <span class="block text-[10px] text-slate-400 font-medium">Durasi: 1 Hari</span>
                                @endif
                            </td>
                            <!-- Jenis -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                @if($leave->type === 'izin')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        Izin Cuti
                                    </span>
                                @elseif($leave->type === 'cuti')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Cuti Tahunan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        Sakit
                                    </span>
                                @endif
                            </td>
                            <!-- Alasan -->
                            <td class="py-4 px-6 min-w-[220px] max-w-[320px] text-slate-600 leading-relaxed">
                                {{ $leave->reason }}
                            </td>
                            <!-- Lampiran -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                @if($leave->attachment)
                                    <a href="{{ $leave->attachment }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-amber-600 hover:text-amber-700 font-bold transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Buka Berkas
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">Tanpa berkas</span>
                                @endif
                            </td>
                            <!-- Status -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                @if($leave->status === 'approved')
                                    <div class="flex flex-col">
                                        <span class="inline-flex items-center w-fit px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Disetujui
                                        </span>
                                        @if($leave->verifier)
                                            <span class="text-[9px] text-slate-400 mt-1">Oleh: {{ $leave->verifier->name }}</span>
                                        @endif
                                    </div>
                                @elseif($leave->status === 'rejected')
                                    <div class="flex flex-col">
                                        <span class="inline-flex items-center w-fit px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Ditolak
                                        </span>
                                        @if($leave->verifier)
                                            <span class="text-[9px] text-slate-400 mt-1">Oleh: {{ $leave->verifier->name }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Menunggu Verifikasi
                                    </span>
                                @endif
                            </td>
                            <!-- Tindakan -->
                            <td class="py-4 px-6 whitespace-nowrap text-center">
                                @if($leave->status === 'pending')
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Form Approve -->
                                        <form action="{{ route('admin.izin.verify', $leave->id) }}" method="POST" class="inline-block verify-form" data-status="approved" data-name="{{ $leave->user->name }}" data-type="{{ ucfirst($leave->type) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-600/10 transition-colors">
                                                Setujui
                                            </button>
                                        </form>
                                        
                                        <!-- Form Reject -->
                                        <form action="{{ route('admin.izin.verify', $leave->id) }}" method="POST" class="inline-block verify-form" data-status="rejected" data-name="{{ $leave->user->name }}" data-type="{{ ucfirst($leave->type) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-600/10 transition-colors">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 font-medium italic">Selesai diverifikasi</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($leaves->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $leaves->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Konfirmasi SweetAlert2 sebelum memverifikasi pengajuan izin/sakit
    document.querySelectorAll('.verify-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const status = this.dataset.status;
            const name = this.dataset.name;
            const type = this.dataset.type;
            
            const isApproved = status === 'approved';
            const actionText = isApproved ? 'menyetujui' : 'menolak';
            const confirmBtnColor = isApproved ? '#059669' : '#e11d48';
            
            Swal.fire({
                title: isApproved ? 'Setujui Pengajuan?' : 'Tolak Pengajuan?',
                text: `Apakah Anda yakin ingin ${actionText} pengajuan ${type} dari karyawan ${name}?`,
                icon: isApproved ? 'success' : 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmBtnColor,
                cancelButtonColor: '#64748b',
                confirmButtonText: isApproved ? 'Ya, Setujui!' : 'Ya, Tolak!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl font-semibold px-6',
                    cancelButton: 'rounded-xl font-semibold px-6'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush
