@extends('layouts.app')
@section('title', 'Absensi')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Absensi Hari Ini</h2>
        <p class="text-slate-500 text-sm mt-1">{{ now()->translatedFormat('l, d F Y') }} • Waktu Server: <span id="server-time" class="font-mono font-semibold text-blue-600">{{ now()->setTimezone(config('app.timezone'))->format('H:i:s') }}</span></p>
    </div>

    <!-- Status card -->
    @if($attendance && $attendance->clock_in)
        <div class="mb-6 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-2xl p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <p class="font-semibold text-emerald-800">Absen Masuk Tercatat</p>
                <p class="text-sm text-emerald-600">Pukul {{ $attendance->clock_in->format('H:i:s') }} · Status: <span class="font-bold">{{ $attendance->status }}</span></p>
                @if($attendance->clock_out)
                    <p class="text-sm text-emerald-600 mt-1">Pulang: <span class="font-bold">{{ $attendance->clock_out->format('H:i:s') }}</span></p>
                @endif
            </div>
        </div>
    @endif

    <!-- Location selector -->
    <div class="mb-5">
        <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Lokasi Kerja</label>
        <select id="location-select" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm">
            <option value="">— Pilih lokasi —</option>
            @foreach($locations as $loc)
                <option value="{{ $loc->id }}" data-lat="{{ $loc->latitude }}" data-lon="{{ $loc->longitude }}" data-radius="{{ $loc->radius }}">
                    {{ $loc->name }} (radius {{ $loc->radius }}m)
                </option>
            @endforeach
        </select>
    </div>

    <!-- GPS Status -->
    <div id="gps-status" class="mb-5 flex items-center gap-3 p-4 bg-slate-100 rounded-xl text-sm text-slate-600">
        <div class="w-2.5 h-2.5 rounded-full bg-slate-400 animate-pulse flex-shrink-0"></div>
        <span>Menunggu izin GPS...</span>
    </div>

    <!-- Camera Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h3 class="font-semibold text-slate-700 text-sm">Foto Selfie</h3>
        </div>

        <div class="p-5">
            <!-- Video preview -->
            <div class="relative bg-slate-900 rounded-xl overflow-hidden aspect-[4/3] mb-4" id="camera-container">
                <video id="camera-video" autoplay playsinline class="w-full h-full object-cover"></video>
                <canvas id="camera-canvas" class="hidden w-full h-full object-cover absolute inset-0"></canvas>
                <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-white/60">
                    <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                    <p class="text-sm">Kamera belum aktif</p>
                </div>
            </div>

            <!-- Camera controls -->
            <div class="flex gap-3">
                <button id="btn-start-camera" type="button"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-xl hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.361a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
                    Aktifkan Kamera
                </button>
                <button id="btn-capture" type="button" disabled
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                    Ambil Foto
                </button>
                <button id="btn-retake" type="button" class="hidden px-4 py-2.5 bg-slate-100 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-200 transition-colors">
                    Ulangi
                </button>
            </div>

            <p id="photo-status" class="mt-2 text-xs text-slate-500 text-center hidden">✓ Foto berhasil diambil</p>
        </div>
    </div>

    <!-- Submit buttons -->
    <div class="grid grid-cols-2 gap-4">
        @if(!$attendance || !$attendance->clock_in)
        <button id="btn-clock-in" type="button" disabled
            class="col-span-2 flex items-center justify-center gap-3 px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold text-base rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            ABSEN MASUK
        </button>
        @elseif($attendance->clock_in && !$attendance->clock_out)
        <button id="btn-clock-out" type="button" disabled
            class="col-span-2 flex items-center justify-center gap-3 px-6 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold text-base rounded-2xl hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg shadow-emerald-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            ABSEN PULANG
        </button>
        @else
        <div class="col-span-2 text-center py-4 bg-slate-100 rounded-2xl text-slate-600 font-medium">
            ✅ Absensi hari ini sudah lengkap
        </div>
        @endif
    </div>

    <!-- Result toast -->
    <div id="result-toast" class="hidden fixed bottom-6 left-4 right-4 lg:left-auto lg:right-6 lg:w-96 p-4 rounded-2xl shadow-xl text-sm font-medium z-50 transition-all"></div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let stream = null;
let capturedPhoto = null;
let currentLat = null;
let currentLon = null;
let gpsReady = false;
let photoReady = false;

// Clock update
setInterval(() => {
    const el = document.getElementById('server-time');
    if (el) {
        const now = new Date();
        el.textContent = now.toLocaleTimeString('id-ID');
    }
}, 1000);

// GPS Geolocation
function initGPS() {
    const gpsStatus = document.getElementById('gps-status');

    if (!navigator.geolocation) {
        gpsStatus.innerHTML = `<div class="w-2.5 h-2.5 rounded-full bg-red-500 flex-shrink-0"></div><span class="text-red-600">Browser tidak mendukung GPS</span>`;
        return;
    }

    gpsStatus.innerHTML = `<div class="w-2.5 h-2.5 rounded-full bg-yellow-500 animate-pulse flex-shrink-0"></div><span>Mendapatkan lokasi GPS...</span>`;

    navigator.geolocation.watchPosition(
        (pos) => {
            currentLat = pos.coords.latitude;
            currentLon = pos.coords.longitude;
            const acc = Math.round(pos.coords.accuracy);
            gpsReady = true;

            const loc = document.getElementById('location-select');
            let distanceInfo = '';
            if (loc && loc.value) {
                const selOpt = loc.options[loc.selectedIndex];
                const officeLat = parseFloat(selOpt.dataset.lat);
                const officeLon = parseFloat(selOpt.dataset.lon);
                const radius = parseInt(selOpt.dataset.radius);
                const dist = Math.round(haversineJS(currentLat, currentLon, officeLat, officeLon));
                const within = dist <= radius;
                distanceInfo = ` · ${within ? '✅' : '❌'} ${dist}m dari kantor (radius ${radius}m)`;
            }

            gpsStatus.innerHTML = `<div class="w-2.5 h-2.5 rounded-full bg-emerald-500 flex-shrink-0"></div><span class="text-emerald-700">GPS aktif · Akurasi ±${acc}m${distanceInfo}</span>`;
            checkReady();
        },
        (err) => {
            gpsStatus.innerHTML = `<div class="w-2.5 h-2.5 rounded-full bg-red-500 flex-shrink-0"></div><span class="text-red-600">Gagal: ${err.message}</span>`;
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
}

// Haversine for JS distance display
function haversineJS(lat1, lon1, lat2, lon2) {
    const R = 6371000;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLon/2)**2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
}

// Camera & GPS Init
document.getElementById('btn-start-camera').addEventListener('click', async () => {
    // Jalankan GPS secara paralel, tidak menunggu kamera
    initGPS();

    try {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error("Browser atau perangkat Anda tidak mendukung akses kamera.");
        }
        
        // Hapus constraint width & height yang kaku karena sering gagal (OverconstrainedError) di HP
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'user' }, 
            audio: false 
        });
        
        const video = document.getElementById('camera-video');
        video.srcObject = stream;
        document.getElementById('camera-placeholder').style.display = 'none';
        document.getElementById('btn-capture').disabled = false;
        document.getElementById('btn-start-camera').textContent = '✓ Kamera Aktif';
        document.getElementById('btn-start-camera').disabled = true;
    } catch (e) {
        let errorMsg = e.message;
        if (e.name === 'NotAllowedError') errorMsg = "Izin kamera ditolak oleh browser.";
        else if (e.name === 'NotFoundError') errorMsg = "Tidak ada kamera yang terdeteksi di perangkat ini.";
        showToast('Gagal mengakses kamera: ' + errorMsg, 'error');
    }
});

// Capture photo
document.getElementById('btn-capture').addEventListener('click', () => {
    const video = document.getElementById('camera-video');
    const canvas = document.getElementById('camera-canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = video.videoWidth || 640;
    canvas.height = video.videoHeight || 480;
    ctx.drawImage(video, 0, 0);

    capturedPhoto = canvas.toDataURL('image/jpeg', 0.85);
    canvas.classList.remove('hidden');
    video.classList.add('hidden');
    document.getElementById('btn-capture').classList.add('hidden');
    document.getElementById('btn-retake').classList.remove('hidden');
    document.getElementById('photo-status').classList.remove('hidden');
    photoReady = true;
    checkReady();
});

// Retake
document.getElementById('btn-retake').addEventListener('click', () => {
    document.getElementById('camera-canvas').classList.add('hidden');
    document.getElementById('camera-video').classList.remove('hidden');
    document.getElementById('btn-capture').classList.remove('hidden');
    document.getElementById('btn-retake').classList.add('hidden');
    document.getElementById('photo-status').classList.add('hidden');
    capturedPhoto = null;
    photoReady = false;
    checkReady();
});

// Enable submit button when ready
function checkReady() {
    const ready = gpsReady && photoReady && document.getElementById('location-select').value !== '';
    const btnIn = document.getElementById('btn-clock-in');
    const btnOut = document.getElementById('btn-clock-out');
    if (btnIn) btnIn.disabled = !ready;
    if (btnOut) btnOut.disabled = !ready;
}

document.getElementById('location-select').addEventListener('change', checkReady);

// Clock In
const btnClockIn = document.getElementById('btn-clock-in');
if (btnClockIn) {
    btnClockIn.addEventListener('click', () => submitAttendance('{{ route("karyawan.clock-in") }}', btnClockIn));
}
const btnClockOut = document.getElementById('btn-clock-out');
if (btnClockOut) {
    btnClockOut.addEventListener('click', () => submitAttendance('{{ route("karyawan.clock-out") }}', btnClockOut));
}

async function submitAttendance(url, btn) {
    const locationId = document.getElementById('location-select').value;
    if (!locationId || !currentLat || !capturedPhoto) {
        showToast('Pastikan lokasi, GPS, dan foto sudah siap.', 'error');
        return;
    }

    btn.disabled = true;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg> Memproses...';

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({
                latitude: currentLat,
                longitude: currentLon,
                photo: capturedPhoto,
                location_id: locationId,
            }),
        });

        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 2000);
        } else {
            showToast(data.message, 'error');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    } catch (e) {
        showToast('Terjadi kesalahan jaringan. Coba lagi.', 'error');
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

function showToast(msg, type) {
    const toast = document.getElementById('result-toast');
    toast.className = `fixed bottom-6 left-4 right-4 lg:left-auto lg:right-6 lg:w-96 p-4 rounded-2xl shadow-xl text-sm font-medium z-50 transition-all ${
        type === 'success'
            ? 'bg-emerald-600 text-white'
            : 'bg-red-600 text-white'
    }`;
    toast.textContent = msg;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 5000);
}
</script>
@endpush
