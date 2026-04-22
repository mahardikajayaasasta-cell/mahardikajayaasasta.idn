@extends('layouts.app')
@section('title', 'Manajemen Lokasi')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Manajemen Lokasi Kerja</h2>
            <p class="text-slate-500 text-sm mt-1">Kelola titik koordinat dan pengaturan jam kerja kantor/cabang</p>
        </div>
        <a href="{{ route('admin.lokasi.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-all shadow-md shadow-blue-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Lokasi
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                    <tr>
                        <th class="text-left px-5 py-4 font-semibold tracking-wider">Lokasi</th>
                        <th class="text-left px-4 py-4 font-semibold tracking-wider">Jam Kerja</th>
                        <th class="text-left px-4 py-4 font-semibold tracking-wider">Koordinat & Radius</th>
                        <th class="text-left px-4 py-4 font-semibold tracking-wider">Status</th>
                        <th class="text-right px-5 py-4 font-semibold tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($locations as $loc)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-800">{{ $loc->name }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $loc->address ?? 'No address' }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-mono bg-blue-50 text-blue-700 px-2 py-0.5 rounded border border-blue-100 w-fit">
                                    Masuk: {{ substr($loc->work_start, 0, 5) }}
                                </span>
                                <span class="text-xs font-mono bg-amber-50 text-amber-700 px-2 py-0.5 rounded border border-amber-100 w-fit">
                                    Telat: {{ substr($loc->late_after, 0, 5) }}
                                </span>
                                <span class="text-xs font-mono bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-200 w-fit">
                                    Pulang: {{ substr($loc->work_end, 0, 5) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-slate-600 text-xs font-mono">{{ $loc->latitude }}, {{ $loc->longitude }}</p>
                            <p class="text-xs text-blue-600 font-semibold mt-1">Radius: {{ $loc->radius }}m</p>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $loc->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                {{ $loc->is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.lokasi.edit', $loc) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                </a>
                                @if($loc->is_active)
                                <form action="{{ route('admin.lokasi.destroy', $loc) }}" method="POST" onsubmit="return confirm('Nonaktifkan lokasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Nonaktifkan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.lokasi.update', $loc) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="name" value="{{ $loc->name }}">
                                    <input type="hidden" name="latitude" value="{{ $loc->latitude }}">
                                    <input type="hidden" name="longitude" value="{{ $loc->longitude }}">
                                    <input type="hidden" name="radius" value="{{ $loc->radius }}">
                                    <input type="hidden" name="work_start" value="{{ substr($loc->work_start, 0, 5) }}">
                                    <input type="hidden" name="work_end" value="{{ substr($loc->work_end, 0, 5) }}">
                                    <input type="hidden" name="late_after" value="{{ substr($loc->late_after, 0, 5) }}">
                                    <input type="hidden" name="is_active" value="1">
                                    <button type="submit" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Aktifkan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                </form>
                                @endif
                                <a href="https://www.google.com/maps?q={{ $loc->latitude }},{{ $loc->longitude }}" target="_blank" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Buka di Maps">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <p class="text-slate-400 font-medium">Belum ada data lokasi.</p>
                                <a href="{{ route('admin.lokasi.create') }}" class="text-blue-600 text-sm font-semibold hover:underline mt-1">Tambah lokasi pertama</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($locations->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $locations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
