@extends('layouts.app')
@section('title', $action === 'create' ? 'Tambah Lokasi' : 'Edit Lokasi')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.lokasi.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-blue-600 transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>
        <h2 class="text-2xl font-bold text-slate-800">{{ $action === 'create' ? 'Tambah Lokasi Kerja' : 'Edit Lokasi Kerja' }}</h2>
        <p class="text-slate-500 text-sm mt-1">Atur koordinat GPS dan jam operasional absensi</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <form action="{{ $action === 'create' ? route('admin.lokasi.store') : route('admin.lokasi.update', $location) }}" method="POST" class="p-6 md:p-8">
            @csrf
            @if($action === 'edit')
                @method('PUT')
            @endif

            <div class="space-y-6">
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lokasi / Cabang</label>
                        <input type="text" name="name" value="{{ old('name', $location->name) }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all"
                            placeholder="Contoh: Kantor Pusat, Cabang Jakarta">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat Lengkap</label>
                        <textarea name="address" rows="2"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all"
                            placeholder="Alamat lengkap lokasi...">{{ old('address', $location->address) }}</textarea>
                        @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- GPS Specs -->
                <div class="pt-4 border-t border-slate-100">
                    <p class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        Pengaturan Geofencing (GPS)
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Latitude</label>
                            <input type="text" name="latitude" value="{{ old('latitude', $location->latitude) }}" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 font-mono text-sm focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all"
                                placeholder="-6.123456">
                            @error('latitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Longitude</label>
                            <input type="text" name="longitude" value="{{ old('longitude', $location->longitude) }}" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 font-mono text-sm focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all"
                                placeholder="106.123456">
                            @error('longitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Radius (Meter)</label>
                            <input type="number" name="radius" value="{{ old('radius', $location->radius ?? 100) }}" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 font-mono text-sm focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all"
                                placeholder="100">
                            @error('radius') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-slate-400 italic">Dapatkan koordinat melalui Google Maps. Radius menentukan seberapa jauh karyawan bisa absen dari titik pusat.</p>
                </div>

                <!-- Work Hours -->
                <div class="pt-4 border-t border-slate-100">
                    <p class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Jam Kerja & Toleransi
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Jam Masuk</label>
                            <input type="time" name="work_start" value="{{ old('work_start', substr($location->work_start ?? '08:00', 0, 5)) }}" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 font-mono text-sm focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all">
                            @error('work_start') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Batas Terlambat</label>
                            <input type="time" name="late_after" value="{{ old('late_after', substr($location->late_after ?? '08:15', 0, 5)) }}" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 font-mono text-sm focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all">
                            @error('late_after') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Jam Pulang</label>
                            <input type="time" name="work_end" value="{{ old('work_end', substr($location->work_end ?? '17:00', 0, 5)) }}" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 font-mono text-sm focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all">
                            @error('work_end') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                @if($action === 'edit')
                <div class="pt-4 border-t border-slate-100">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $location->is_active) ? 'checked' : '' }}
                            class="w-5 h-5 rounded-lg border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-semibold text-slate-700 group-hover:text-blue-600 transition-colors">Lokasi ini Aktif</span>
                    </label>
                </div>
                @endif
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.lokasi.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 rounded-xl transition-all">
                    Batal
                </a>
                <button type="submit" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-blue-200">
                    {{ $action === 'create' ? 'Simpan Lokasi' : 'Simpan Perubahan' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
