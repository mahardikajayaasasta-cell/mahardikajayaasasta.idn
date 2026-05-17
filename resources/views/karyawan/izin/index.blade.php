@extends('layouts.app')

@section('title', 'Pengajuan Izin & Sakit')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Pengajuan Izin & Sakit</h1>
        <p class="text-sm text-slate-500">Ajukan surat izin atau keterangan sakit dengan mudah dan praktis.</p>
    </div>
    <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-xl border border-slate-200 text-xs text-slate-600 font-medium">
        <span class="px-2.5 py-1 bg-white rounded-lg shadow-sm border border-slate-200">Zona Waktu: Asia/Jakarta (WIB)</span>
    </div>
</div>

@if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <h3 class="text-sm font-bold text-red-800">Terdapat kesalahan input:</h3>
                <ul class="mt-1 text-xs text-red-700 list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Form Pengajuan -->
    <div class="lg:col-span-4">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-5 text-white">
                <h2 class="font-bold text-base">Buat Pengajuan Baru</h2>
                <p class="text-slate-400 text-xs mt-1">Isi formulir secara lengkap dan jujur.</p>
            </div>
            
            <form action="{{ route('karyawan.izin.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf
                
                <!-- Jenis Pengajuan -->
                <div>
                    <label for="type" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Jenis Pengajuan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex items-center justify-center p-3 rounded-2xl border-2 border-slate-200 cursor-pointer hover:bg-slate-50 transition-all checked-label">
                            <input type="radio" name="type" id="type-izin" value="izin" class="sr-only" checked onchange="toggleFormMode('izin')">
                            <div class="text-center">
                                <span class="block text-sm font-bold text-slate-800">Izin</span>
                                <span class="block text-[10px] text-slate-400 mt-0.5">Keperluan pribadi</span>
                            </div>
                        </label>
                        <label class="relative flex items-center justify-center p-3 rounded-2xl border-2 border-slate-200 cursor-pointer hover:bg-slate-50 transition-all checked-label">
                            <input type="radio" name="type" id="type-sakit" value="sakit" class="sr-only" onchange="toggleFormMode('sakit')">
                            <div class="text-center">
                                <span class="block text-sm font-bold text-slate-800">Sakit</span>
                                <span class="block text-[10px] text-slate-400 mt-0.5">Kondisi kurang sehat</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Tanggal Izin -->
                <div>
                    <label for="date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Tanggal Pengajuan</label>
                    <input type="date" name="date" id="date" value="{{ old('date', today()->format('Y-m-d')) }}" required
                        class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:outline-none transition-all">
                    <p id="date-helper" class="text-[11px] text-amber-600 font-medium mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Khusus izin wajib diajukan minimal H-1.</span>
                    </p>
                </div>

                <!-- Alasan -->
                <div>
                    <label for="reason" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Alasan Pengajuan</label>
                    <textarea name="reason" id="reason" rows="4" required placeholder="Tuliskan keterangan lengkap di sini..."
                        class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:outline-none transition-all placeholder:text-slate-400">{{ old('reason') }}</textarea>
                </div>

                <!-- Lampiran Dokumen -->
                <div>
                    <label for="attachment" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Unggah Lampiran (Opsional)</label>
                    <div class="relative group border border-dashed border-slate-300 rounded-2xl hover:border-amber-500 transition-colors p-4 text-center">
                        <input type="file" name="attachment" id="attachment" accept="image/*,application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="space-y-1">
                            <svg class="w-8 h-8 text-slate-400 mx-auto group-hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-xs font-bold text-slate-700">Pilih berkas foto/PDF</p>
                            <p class="text-[10px] text-slate-400">Maks. 2MB (Surat dokter, bukti tertulis, dll.)</p>
                        </div>
                    </div>
                </div>

                <!-- Tombol Kirim -->
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-amber-500/20 hover:shadow-amber-500/30 transition-all">
                    Kirim Pengajuan
                </button>
            </form>
        </div>
    </div>

    <!-- Riwayat Pengajuan -->
    <div class="lg:col-span-8">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-800 text-base">Riwayat Pengajuan Anda</h2>
                    <p class="text-slate-400 text-xs mt-0.5">Daftar pengajuan izin dan sakit yang telah Anda buat.</p>
                </div>
            </div>

            @if($leaves->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-700">Belum Ada Pengajuan</h3>
                    <p class="text-xs text-slate-400 mt-1">Anda belum pernah mengirim pengajuan izin atau sakit.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-4 px-6">Tanggal Pengajuan</th>
                                <th class="py-4 px-6">Jenis</th>
                                <th class="py-4 px-6">Keterangan / Alasan</th>
                                <th class="py-4 px-6">Lampiran</th>
                                <th class="py-4 px-6">Status Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach($leaves as $leave)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-4 px-6 font-semibold text-slate-800 whitespace-nowrap">
                                        {{ $leave->date->translatedFormat('d F Y') }}
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        @if($leave->type === 'izin')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                Izin Cuti
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                Sakit
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 min-w-[200px] text-slate-600">
                                        {{ $leave->reason }}
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        @if($leave->attachment)
                                            <a href="{{ $leave->attachment }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-amber-600 hover:text-amber-700 font-bold transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                Lihat Berkas
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400 font-medium">Tanpa berkas</span>
                                        @endif
                                    </td>
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
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                                Menunggu Verifikasi
                                            </span>
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
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Styling label radio yang aktif
    function updateRadioStyles() {
        document.querySelectorAll('input[name="type"]').forEach(input => {
            const label = input.closest('label');
            if (input.checked) {
                label.classList.add('border-amber-500', 'bg-amber-50/50', 'ring-2', 'ring-amber-500/20');
                label.classList.remove('border-slate-200');
            } else {
                label.classList.remove('border-amber-500', 'bg-amber-50/50', 'ring-2', 'ring-amber-500/20');
                label.classList.add('border-slate-200');
            }
        });
    }

    // Toggle mode validasi tanggal berdasarkan tipe pengajuan (H-1)
    function toggleFormMode(type) {
        const dateInput = document.getElementById('date');
        const dateHelper = document.getElementById('date-helper');
        
        // Dapatkan string tanggal hari ini & besok (WIB)
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1);

        const formatDate = (d) => {
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        if (type === 'izin') {
            // Khusus Izin: Minimal H-1 (besok)
            dateInput.min = formatDate(tomorrow);
            if (dateInput.value < formatDate(tomorrow)) {
                dateInput.value = formatDate(tomorrow);
            }
            dateHelper.innerHTML = `
                <svg class="w-3.5 h-3.5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Pengajuan izin (cuti/keperluan) wajib didaftarkan minimal H-1.</span>
            `;
            dateHelper.classList.remove('text-purple-600');
            dateHelper.classList.add('text-amber-600');
        } else {
            // Sakit: Boleh hari ini (H-0)
            dateInput.min = formatDate(today);
            dateHelper.innerHTML = `
                <svg class="w-3.5 h-3.5 shrink-0 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Pengajuan sakit dapat diajukan mulai hari ini (H-0).</span>
            `;
            dateHelper.classList.remove('text-amber-600');
            dateHelper.classList.add('text-purple-600');
        }
        
        updateRadioStyles();
    }

    // Inisialisasi awal saat load
    document.addEventListener('DOMContentLoaded', () => {
        const checkedType = document.querySelector('input[name="type"]:checked').value;
        toggleFormMode(checkedType);
    });
</script>
@endpush
