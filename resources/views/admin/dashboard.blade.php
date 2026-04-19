@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Dashboard Admin</h2>
        <p class="text-slate-500 text-sm mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        @php
            $statCards = [
                ['label' => 'Total Karyawan', 'value' => $stats['total_karyawan'], 'color' => 'from-slate-600 to-slate-700', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857'],
                ['label' => 'Hadir', 'value' => $stats['hadir_hari_ini'], 'color' => 'from-emerald-500 to-teal-600', 'icon' => 'M5 13l4 4L19 7'],
                ['label' => 'Telat', 'value' => $stats['telat_hari_ini'], 'color' => 'from-amber-500 to-orange-500', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Mangkir', 'value' => $stats['mangkir_hari_ini'], 'color' => 'from-red-500 to-rose-600', 'icon' => 'M6 18L18 6M6 6l12 12'],
                ['label' => 'Belum Absen', 'value' => $stats['belum_absen'], 'color' => 'from-violet-500 to-purple-600', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
        @endphp
        @foreach($statCards as $card)
        <div class="bg-gradient-to-br {{ $card['color'] }} rounded-2xl p-5 text-white shadow-lg">
            <svg class="w-5 h-5 opacity-80 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
            </svg>
            <p class="text-3xl font-bold">{{ $card['value'] }}</p>
            <p class="text-white/80 text-xs mt-1">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-8">
        <a href="{{ route('admin.rekap') }}" class="flex items-center gap-3 bg-white border border-slate-200 rounded-xl px-4 py-3 hover:shadow-md transition-all group">
            <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <span class="text-sm font-semibold text-slate-700">Rekap Absensi</span>
        </a>
        <a href="{{ route('admin.karyawan.index') }}" class="flex items-center gap-3 bg-white border border-slate-200 rounded-xl px-4 py-3 hover:shadow-md transition-all group">
            <div class="w-9 h-9 bg-indigo-50 rounded-lg flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <span class="text-sm font-semibold text-slate-700">Karyawan</span>
        </a>
        <a href="{{ route('admin.lokasi.index') }}" class="flex items-center gap-3 bg-white border border-slate-200 rounded-xl px-4 py-3 hover:shadow-md transition-all group">
            <div class="w-9 h-9 bg-emerald-50 rounded-lg flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
            </div>
            <span class="text-sm font-semibold text-slate-700">Lokasi Kerja</span>
        </a>
        <a href="{{ route('admin.export.excel', ['date_from' => today()->format('Y-m-d'), 'date_to' => today()->format('Y-m-d')]) }}"
            class="flex items-center gap-3 bg-white border border-slate-200 rounded-xl px-4 py-3 hover:shadow-md transition-all group">
            <div class="w-9 h-9 bg-green-50 rounded-lg flex items-center justify-center group-hover:bg-green-100 transition-colors">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <span class="text-sm font-semibold text-slate-700">Ekspor Excel</span>
        </a>
    </div>

    <!-- Recent Attendances Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-700">Absensi Hari Ini</h3>
            <a href="{{ route('admin.rekap') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lihat semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold">Karyawan</th>
                        <th class="text-left px-4 py-3 font-semibold">Masuk</th>
                        <th class="text-left px-4 py-3 font-semibold">Pulang</th>
                        <th class="text-left px-4 py-3 font-semibold">Status</th>
                        <th class="text-left px-4 py-3 font-semibold">Foto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentAttendances as $att)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-medium text-slate-800">{{ $att->user->name }}</p>
                            <p class="text-xs text-slate-400">{{ $att->user->department }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-600 font-mono text-xs">{{ $att->clock_in?->format('H:i:s') ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600 font-mono text-xs">{{ $att->clock_out?->format('H:i:s') ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $att->status === 'Hadir' ? 'bg-emerald-100 text-emerald-700' :
                                   ($att->status === 'Telat' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                {{ $att->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($att->clock_in_photo)
                            <a href="{{ $att->clock_in_photo }}" target="_blank">
                                <img src="{{ $att->clock_in_photo }}" alt="Foto" class="w-8 h-8 rounded-lg object-cover hover:scale-110 transition-transform">
                            </a>
                            @else
                            <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400 text-sm">Belum ada absensi hari ini</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
