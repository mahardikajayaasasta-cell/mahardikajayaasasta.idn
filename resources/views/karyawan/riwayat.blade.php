@extends('layouts.app')
@section('title', 'Riwayat Absensi')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Riwayat Absensi</h2>
            <p class="text-slate-500 text-sm mt-1">{{ auth()->user()->name }}</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <select name="month" class="form-input w-auto text-xs py-2">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
            <select name="year" class="form-input w-auto text-xs py-2">
                @for($y = now()->year; $y >= now()->year - 2; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="btn-primary py-2 text-xs">Tampilkan</button>
        </form>
    </div>

    <!-- Stats bulan ini -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 text-center">
            <p class="text-3xl font-bold text-emerald-700">{{ $stats['hadir'] }}</p>
            <p class="text-xs text-emerald-600 mt-1">Hadir</p>
        </div>
        <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 text-center">
            <p class="text-3xl font-bold text-amber-700">{{ $stats['telat'] }}</p>
            <p class="text-xs text-amber-600 mt-1">Telat</p>
        </div>
        <div class="bg-red-50 border border-red-100 rounded-2xl p-4 text-center">
            <p class="text-3xl font-bold text-red-700">{{ $stats['mangkir'] }}</p>
            <p class="text-xs text-red-600 mt-1">Mangkir</p>
        </div>
    </div>

    <!-- Tabel riwayat -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold">Tanggal</th>
                        <th class="text-left px-4 py-3 font-semibold">Masuk</th>
                        <th class="text-left px-4 py-3 font-semibold">Pulang</th>
                        <th class="text-left px-4 py-3 font-semibold">Durasi</th>
                        <th class="text-left px-4 py-3 font-semibold">Lokasi</th>
                        <th class="text-left px-4 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($attendances as $att)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-medium text-slate-800">{{ $att->date->format('d/m/Y') }}</p>
                            <p class="text-xs text-slate-400">{{ $att->date->translatedFormat('l') }}</p>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $att->clock_in?->format('H:i:s') ?? '—' }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $att->clock_out?->format('H:i:s') ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-slate-500">{{ $att->work_duration ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-slate-500">{{ $att->location?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="badge-{{ strtolower($att->status) }}">{{ $att->status }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400 text-sm">Tidak ada data absensi bulan ini</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
