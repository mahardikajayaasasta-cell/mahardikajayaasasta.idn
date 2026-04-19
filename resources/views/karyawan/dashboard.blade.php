@extends('layouts.app')
@section('title', 'Dashboard Karyawan')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Greeting -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Halo, {{ auth()->user()->name }}! 👋</h2>
        <p class="text-slate-500 text-sm mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    <!-- Status Card Today -->
    @php
        $statusColor = match($attendance?->status) {
            'Hadir'   => ['bg' => 'from-emerald-500 to-teal-500', 'badge' => 'bg-emerald-100 text-emerald-700'],
            'Telat'   => ['bg' => 'from-amber-500 to-orange-500', 'badge' => 'bg-amber-100 text-amber-700'],
            'Mangkir' => ['bg' => 'from-red-500 to-rose-500', 'badge' => 'bg-red-100 text-red-700'],
            default   => ['bg' => 'from-slate-400 to-slate-500', 'badge' => 'bg-slate-100 text-slate-600'],
        };
    @endphp
    <div class="bg-gradient-to-r {{ $statusColor['bg'] }} rounded-2xl p-6 text-white mb-6 shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 80% 20%, white 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="relative">
            <p class="text-white/80 text-sm font-medium">Status Absensi Hari Ini</p>
            <h3 class="text-3xl font-bold mt-1">{{ $attendance?->status ?? 'Belum Absen' }}</h3>
            <div class="flex flex-wrap gap-4 mt-4 text-sm">
                <div>
                    <p class="text-white/70">Masuk</p>
                    <p class="font-bold">{{ $attendance?->clock_in?->format('H:i') ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-white/70">Pulang</p>
                    <p class="font-bold">{{ $attendance?->clock_out?->format('H:i') ?? '—' }}</p>
                </div>
                @if($attendance?->location)
                <div>
                    <p class="text-white/70">Lokasi</p>
                    <p class="font-bold">{{ $attendance->location->name }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Action -->
    @if(!$attendance || !$attendance->clock_in)
    <a href="{{ route('karyawan.absensi') }}"
        class="block w-full mb-6 py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold text-center text-lg rounded-2xl shadow-lg shadow-blue-200 hover:from-blue-700 hover:to-indigo-700 transition-all active:scale-95">
        📍 ABSEN MASUK SEKARANG
    </a>
    @elseif($attendance->clock_in && !$attendance->clock_out)
    <a href="{{ route('karyawan.absensi') }}"
        class="block w-full mb-6 py-5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold text-center text-lg rounded-2xl shadow-lg shadow-emerald-200 hover:from-emerald-700 hover:to-teal-700 transition-all active:scale-95">
        🏠 ABSEN PULANG SEKARANG
    </a>
    @else
    <div class="mb-6 py-5 bg-slate-100 text-slate-600 font-semibold text-center text-base rounded-2xl">
        ✅ Absensi hari ini sudah selesai
    </div>
    @endif

    <!-- Recent History -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-700">Riwayat 7 Hari Terakhir</h3>
            <a href="{{ route('karyawan.riwayat') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lihat semua →</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($recentHistory as $rec)
            <div class="flex items-center justify-between px-5 py-3">
                <div>
                    <p class="text-sm font-medium text-slate-700">{{ $rec->date->translatedFormat('l, d M') }}</p>
                    <p class="text-xs text-slate-400">
                        {{ $rec->clock_in?->format('H:i') ?? '—' }} →
                        {{ $rec->clock_out?->format('H:i') ?? '—' }}
                    </p>
                </div>
                <span class="text-xs font-semibold px-3 py-1 rounded-full
                    {{ $rec->status === 'Hadir' ? 'bg-emerald-100 text-emerald-700' :
                       ($rec->status === 'Telat' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                    {{ $rec->status }}
                </span>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada riwayat absensi</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
