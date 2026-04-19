@extends('layouts.app')
@section('title', 'Rekap Absensi')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Rekap Absensi</h2>
            <p class="text-slate-500 text-sm mt-1">Filter dan lihat data absensi karyawan</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.export.excel', request()->query()) }}"
               class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-xl hover:bg-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel
            </a>
            <a href="{{ route('admin.export.pdf', request()->query()) }}"
               class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-xl hover:bg-red-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                PDF
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.rekap') }}" class="bg-white rounded-2xl border border-slate-200 p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Karyawan</label>
                <select name="user_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Karyawan</option>
                    @foreach($karyawanList as $k)
                        <option value="{{ $k->id }}" {{ $userId == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Status</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="Hadir" {{ $status === 'Hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="Telat" {{ $status === 'Telat' ? 'selected' : '' }}>Telat</option>
                    <option value="Mangkir" {{ $status === 'Mangkir' ? 'selected' : '' }}>Mangkir</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex gap-2">
            <button type="submit" class="px-5 py-2 bg-slate-800 text-white text-sm font-medium rounded-xl hover:bg-slate-700 transition-colors">
                Filter
            </button>
            <a href="{{ route('admin.rekap') }}" class="px-5 py-2 bg-slate-100 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-200 transition-colors">
                Reset
            </a>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold">Karyawan</th>
                        <th class="text-left px-4 py-3 font-semibold">Tanggal</th>
                        <th class="text-left px-4 py-3 font-semibold">Masuk</th>
                        <th class="text-left px-4 py-3 font-semibold">Pulang</th>
                        <th class="text-left px-4 py-3 font-semibold">Lokasi</th>
                        <th class="text-left px-4 py-3 font-semibold">Status</th>
                        <th class="text-left px-4 py-3 font-semibold">Foto</th>
                        <th class="text-left px-4 py-3 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($attendances as $att)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-800">{{ $att->user->name }}</p>
                            <p class="text-xs text-slate-400">{{ $att->user->employee_id }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-600 text-xs">{{ $att->date->format('d/m/Y') }}<br>{{ $att->date->translatedFormat('l') }}</td>
                        <td class="px-4 py-3 text-slate-600 font-mono text-xs">{{ $att->clock_in?->format('H:i:s') ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600 font-mono text-xs">{{ $att->clock_out?->format('H:i:s') ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600 text-xs">{{ $att->location?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $att->status === 'Hadir' ? 'bg-emerald-100 text-emerald-700' :
                                   ($att->status === 'Telat' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                {{ $att->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-1.5">
                                @if($att->clock_in_photo)
                                <a href="{{ $att->clock_in_photo }}" target="_blank" title="Foto Masuk">
                                    <img src="{{ $att->clock_in_photo }}" class="w-8 h-8 rounded-lg object-cover hover:scale-110 transition-transform border border-slate-200">
                                </a>
                                @endif
                                @if($att->clock_out_photo)
                                <a href="{{ $att->clock_out_photo }}" target="_blank" title="Foto Pulang">
                                    <img src="{{ $att->clock_out_photo }}" class="w-8 h-8 rounded-lg object-cover hover:scale-110 transition-transform border border-slate-200 opacity-70">
                                </a>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.rekap.show', $att->id) }}"
                               class="text-xs text-blue-600 hover:text-blue-700 font-medium">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-10 text-center text-slate-400 text-sm">Tidak ada data absensi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
