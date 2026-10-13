@extends('layouts.app')
@section('title', 'Detail Absensi')

@section('content')
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-800">Detail Absensi</h2>
                <p class="text-slate-500 text-sm mt-1">Informasi lengkap record kehadiran karyawan</p>
            </div>
            <a href="{{ route('admin.rekap') }}"
                class="flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-sm font-medium hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        <!-- Status & Identity Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-5">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-slate-700">Identitas Karyawan</h3>
                <span class="px-3 py-1 rounded-full text-xs font-bold
                    {{ $attendance->status === 'Hadir' ? 'bg-emerald-100 text-emerald-700' :
        ($attendance->status === 'Telat' ? 'bg-amber-100 text-amber-700' :
            ($attendance->status === 'Mangkir' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700')) }}">
                    {{ $attendance->status }}
                </span>
            </div>
            <div class="p-6 grid grid-cols-2 gap-x-8 gap-y-4">
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">Nama</p>
                    <p class="font-semibold text-slate-800 mt-1">{{ $attendance->user->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">NIP / ID Karyawan</p>
                    <p class="font-semibold text-slate-800 mt-1">{{ $attendance->user->employee_id ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">Departemen</p>
                    <p class="font-semibold text-slate-800 mt-1">{{ $attendance->user->department ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">Jabatan</p>
                    <p class="font-semibold text-slate-800 mt-1">{{ $attendance->user->position ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">Tanggal</p>
                    <p class="font-semibold text-slate-800 mt-1">{{ $attendance->date->translatedFormat('l, d F Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">Lokasi Kerja</p>
                    <p class="font-semibold text-slate-800 mt-1">{{ $attendance->location->name ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Attendance Times -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
            <!-- Clock In -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-blue-50 border-b border-blue-100">
                    <h4 class="font-semibold text-blue-800 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Absen Masuk
                    </h4>
                </div>
                <div class="p-5 space-y-3">
                    <div>
                        <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide">Jam Masuk</p>
                        <p class="text-2xl font-bold text-slate-800">
                            {{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : '—' }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-xs text-slate-400">Latitude</p>
                            <p class="font-mono text-slate-700">{{ $attendance->clock_in_latitude ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Longitude</p>
                            <p class="font-mono text-slate-700">{{ $attendance->clock_in_longitude ?? '—' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Jarak dari Kantor</p>
                        <p class="font-semibold text-slate-700">
                            {{ $attendance->clock_in_distance ? number_format($attendance->clock_in_distance, 0) . ' meter' : '—' }}
                        </p>
                    </div>
                    @if($attendance->clock_in_photo)
                        <div>
                            <p class="text-xs text-slate-400 mb-2">Foto Selfie</p>
                            @if(str_starts_with($attendance->clock_in_photo, 'data:image'))
                                <img src="{{ $attendance->clock_in_photo }}" alt="Foto Masuk"
                                    class="w-full rounded-xl object-cover aspect-square">
                            @else
                                <img src="{{ $attendance->clock_in_photo }}" alt="Foto Masuk"
                                    class="w-full rounded-xl object-cover aspect-square">
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Clock Out -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-emerald-50 border-b border-emerald-100">
                    <h4 class="font-semibold text-emerald-800 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Absen Pulang
                    </h4>
                </div>
                <div class="p-5 space-y-3">
                    <div>
                        <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide">Jam Pulang</p>
                        <p class="text-2xl font-bold text-slate-800">
                            {{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '—' }}</p>
                    </div>
                    @if($attendance->work_duration)
                        <div>
                            <p class="text-xs text-slate-400">Durasi Kerja</p>
                            <p class="font-semibold text-emerald-700">{{ $attendance->work_duration }} jam</p>
                        </div>
                    @endif
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-xs text-slate-400">Latitude</p>
                            <p class="font-mono text-slate-700">{{ $attendance->clock_out_latitude ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Longitude</p>
                            <p class="font-mono text-slate-700">{{ $attendance->clock_out_longitude ?? '—' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Jarak dari Kantor</p>
                        <p class="font-semibold text-slate-700">
                            {{ $attendance->clock_out_distance ? number_format($attendance->clock_out_distance, 0) . ' meter' : '—' }}
                        </p>
                    </div>
                    @if($attendance->clock_out_photo)
                        <div>
                            <p class="text-xs text-slate-400 mb-2">Foto Selfie</p>
                            <img src="{{ $attendance->clock_out_photo }}" alt="Foto Pulang"
                                class="w-full rounded-xl object-cover aspect-square">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Update Status Form -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-5">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-700">Update Status (Admin)</h3>
            </div>
            <form method="POST" action="{{ route('admin.rekap.status', $attendance) }}"
                class="p-6 flex flex-wrap gap-4 items-end">
                @csrf
                @method('PATCH')
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Status
                        Kehadiran</label>
                    <select name="status"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Hadir" {{ $attendance->status === 'Hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="Telat" {{ $attendance->status === 'Telat' ? 'selected' : '' }}>Telat</option>
                        <option value="Mangkir" {{ $attendance->status === 'Mangkir' ? 'selected' : '' }}>Mangkir</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Catatan
                        (Opsional)</label>
                    <input type="text" name="notes" value="{{ $attendance->notes }}" placeholder="Tambahkan catatan..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition-colors">
                    Simpan
                </button>
            </form>
        </div>
    </div>
@endsection