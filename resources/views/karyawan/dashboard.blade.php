@extends('layouts.app')
@section('title', 'Dashboard Karyawan')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #map { height: 350px; width: 100%; border-radius: 1rem; border: 1px solid #e2e8f0; z-index: 10; }
    .nav-tabs .active { border-bottom: 2px solid #3b82f6; color: #3b82f6; }
    /* Fix for Leaflet tiles distorted by Tailwind */
    .leaflet-container img.leaflet-tile {
        max-width: none !important;
        max-height: none !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Greeting -->
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Halo, {{ auth()->user()->name }}! 👋</h2>
            <p class="text-slate-500 text-sm mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <!-- Mode Switcher -->
        <div class="flex bg-slate-100 p-1 rounded-xl nav-tabs">
            <button id="btn-mode-stats" class="px-4 py-2 text-xs font-bold rounded-lg transition-all active bg-white shadow-sm text-blue-600">
                📊 Ringkasan
            </button>
            <button id="btn-mode-map" class="px-4 py-2 text-xs font-bold rounded-lg transition-all text-slate-500 hover:text-slate-700">
                📍 Lokasi Kantor
            </button>
        </div>
    </div>

    <div id="stats-view">
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

    <!-- Map View Mode -->
    <div id="map-view" class="hidden mb-8">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="mb-4">
                <h3 class="font-bold text-slate-800 text-lg">Peta Lokasi Kantor</h3>
                <p class="text-xs text-slate-500">Berikut adalah daftar lokasi kantor yang tersedia untuk absensi.</p>
            </div>
            <div id="map"></div>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($locations as $loc)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <p class="font-bold text-slate-800 text-sm">{{ $loc->name }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $loc->address }}</p>
                    <div class="mt-2 flex items-center gap-3">
                        <span class="text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Radius: {{ $loc->radius }}m</span>
                        <a href="https://www.google.com/maps?q={{ $loc->latitude }},{{ $loc->longitude }}" target="_blank" class="text-[10px] font-bold text-blue-600 hover:underline">Buka di Maps →</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    let map = null;
    const btnStats = document.getElementById('btn-mode-stats');
    const btnMap = document.getElementById('btn-mode-map');
    const statsView = document.getElementById('stats-view');
    const mapView = document.getElementById('map-view');

    btnStats.addEventListener('click', () => {
        btnStats.className = 'px-4 py-2 text-xs font-bold rounded-lg transition-all bg-white shadow-sm text-blue-600';
        btnMap.className = 'px-4 py-2 text-xs font-bold rounded-lg transition-all text-slate-500 hover:text-slate-700';
        statsView.classList.remove('hidden');
        mapView.classList.add('hidden');
    });

    btnMap.addEventListener('click', () => {
        btnMap.className = 'px-4 py-2 text-xs font-bold rounded-lg transition-all bg-white shadow-sm text-blue-600';
        btnStats.className = 'px-4 py-2 text-xs font-bold rounded-lg transition-all text-slate-500 hover:text-slate-700';
        mapView.classList.remove('hidden');
        statsView.classList.add('hidden');
        
        if (!map) {
            initMap();
        } else {
            setTimeout(() => map.invalidateSize(), 300);
        }
    });

    function initMap() {
        map = L.map('map').setView([-6.200000, 106.816666], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        const markers = [];
        const locations = @json($locations);
        
        locations.forEach(loc => {
            L.circle([loc.latitude, loc.longitude], {
                color: '#3b82f6',
                fillColor: '#3b82f6',
                fillOpacity: 0.1,
                radius: loc.radius
            }).addTo(map);

            const marker = L.marker([loc.latitude, loc.longitude]).addTo(map)
                .bindPopup(`<b>${loc.name}</b><br>${loc.address}`);
            markers.push(marker);
        });

        if (markers.length > 0) {
            const group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.1));
        }
    }
</script>
@endpush
