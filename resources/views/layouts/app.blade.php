<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Absensi') — MJA Absensi</title>
    <meta name="description" content="Sistem Absensi Mahardika Jaya Asasta Berbasis Web dengan GPS dan Kamera">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/jpeg" href="{{ asset('logo-mja.jpg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('head')
</head>
<body class="h-full bg-slate-50 font-['Inter']">

<!-- Global Loading Overlay -->
<div id="global-loader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/90 backdrop-blur-md transition-opacity duration-300 pointer-events-none opacity-0">
    <div class="flex flex-col items-center">
        <!-- Pulse & Scale logo container -->
        <div class="relative w-28 h-28 mb-4 flex items-center justify-center">
            <!-- Ripple rings around logo -->
            <div class="absolute inset-0 rounded-full bg-amber-500/20 animate-ping opacity-75"></div>
            <div class="absolute -inset-4 rounded-full border-2 border-dashed border-amber-500/40 animate-[spin_12s_linear_infinite]"></div>
            <!-- Logo itself inside card-like circle with shadow -->
            <div class="relative w-20 h-20 bg-white rounded-full p-1.5 shadow-2xl flex items-center justify-center overflow-hidden border border-amber-400">
                <img src="{{ asset('logo-mja.jpg') }}" alt="MJA Logo" class="w-full h-full object-contain">
            </div>
        </div>
        <!-- Loading progress indicator -->
        <h2 class="text-sm font-bold text-white tracking-widest uppercase mb-1">Mahardika Jaya Asasta</h2>
        <div class="w-40 h-1 bg-slate-800 rounded-full overflow-hidden mb-2">
            <div class="h-full bg-gradient-to-r from-amber-400 to-amber-600 rounded-full w-0 animate-[loading_2s_ease-in-out_infinite]"></div>
        </div>
        <p class="text-[10px] font-medium text-slate-400 tracking-wider">Memuat Halaman...</p>
    </div>
</div>

<style>
@keyframes loading {
    0% { width: 0%; margin-left: 0%; }
    50% { width: 60%; margin-left: 20%; }
    100% { width: 0%; margin-left: 100%; }
}
</style>

<!-- Sidebar -->
<div class="flex h-full">
    <!-- Sidebar (desktop) -->
    <aside id="sidebar" class="hidden lg:flex lg:flex-col lg:w-20 hover:lg:w-64 group bg-gradient-to-b from-slate-900 to-slate-800 text-white fixed inset-y-0 left-0 z-50 shadow-2xl transition-all duration-300 overflow-hidden">
        <!-- Logo -->
        <div class="flex items-center gap-3 px-4 py-4 border-b border-slate-700">
            <div class="w-12 h-12 shrink-0 bg-white rounded-xl flex items-center justify-center shadow-lg border border-slate-700 overflow-hidden">
                <img src="{{ asset('logo-mja.jpg') }}" alt="MJA Logo" class="w-full h-full object-cover">
            </div>
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                <h1 class="text-sm font-bold text-white leading-tight">MJA Absensi</h1>
                <p class="text-[10px] text-slate-400">Mahardika Jaya Asasta</p>
            </div>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 py-6 space-y-2 overflow-y-auto overflow-x-hidden custom-scrollbar">
            @if(auth()->user()->isAdmin())
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-3 mb-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">Admin Menu</p>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">Dashboard</span>
                </a>
                <a href="{{ route('admin.rekap') }}" class="nav-link {{ request()->routeIs('admin.rekap*') ? 'active' : '' }}" title="Rekap Absensi">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">Rekap Absensi</span>
                </a>
                <a href="{{ route('admin.karyawan.index') }}" class="nav-link {{ request()->routeIs('admin.karyawan*') ? 'active' : '' }}" title="Manajemen Karyawan">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                    <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">Manajemen Karyawan</span>
                </a>
                <a href="{{ route('admin.lokasi.index') }}" class="nav-link {{ request()->routeIs('admin.lokasi*') ? 'active' : '' }}" title="Manajemen Lokasi">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">Manajemen Lokasi</span>
                </a>
                <a href="{{ route('admin.izin.index') }}" class="nav-link {{ request()->routeIs('admin.izin*') ? 'active' : '' }}" title="Verifikasi Izin">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">Verifikasi Izin</span>
                </a>
            @else
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider px-3 mb-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">Menu Utama</p>
                <a href="{{ route('karyawan.dashboard') }}" class="nav-link {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}" title="Dashboard">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">Dashboard</span>
                </a>
                <a href="{{ route('karyawan.absensi') }}" class="nav-link {{ request()->routeIs('karyawan.absensi') ? 'active' : '' }}" title="Absensi">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">Absensi</span>
                </a>
                <a href="{{ route('karyawan.riwayat') }}" class="nav-link {{ request()->routeIs('karyawan.riwayat') ? 'active' : '' }}" title="Riwayat Absensi">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">Riwayat Absensi</span>
                </a>
                <a href="{{ route('karyawan.izin') }}" class="nav-link {{ request()->routeIs('karyawan.izin*') ? 'active' : '' }}" title="Pengajuan Izin">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">Pengajuan Izin</span>
                </a>
            @endif
        </nav>

        <!-- User profile -->
        <div class="px-4 py-4 border-t border-slate-700">
            <a href="{{ auth()->user()->isAdmin() ? route('admin.profile') : route('karyawan.profile') }}" class="flex items-center gap-3 mb-3 p-2 -mx-2 rounded-xl hover:bg-slate-700/50 transition-colors group/profile" title="Pengaturan Profile">
                <div class="w-8 h-8 shrink-0 rounded-full overflow-hidden bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold text-sm shadow-md border-2 border-slate-700">
                    @if(auth()->user()->profile_photo_url)
                        <img src="{{ auth()->user()->profile_photo_url }}" class="w-full h-full object-cover">
                    @else
                        {{ substr(auth()->user()->name, 0, 1) }}
                    @endif
                </div>
                <div class="min-w-0 flex-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                    <p class="text-sm font-medium text-white truncate group-hover/profile:text-blue-400 transition-colors">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400 capitalize">{{ auth()->user()->role }}</p>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 text-sm text-slate-400 hover:text-red-400 transition-colors px-2 py-2.5 rounded-xl hover:bg-slate-700/50 whitespace-nowrap" title="Keluar">
                    <svg class="w-5 h-5 shrink-0 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 lg:ml-20 flex flex-col min-h-screen transition-all duration-300">
        <!-- Top bar (mobile) -->
        <header class="bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between lg:hidden sticky top-0 z-40 shadow-sm">
            <button id="mobile-menu-btn" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center overflow-hidden border border-slate-200 shadow-sm">
                    <img src="{{ asset('logo-mja.jpg') }}" alt="MJA Logo" class="w-full h-full object-cover">
                </div>
                <span class="font-bold text-slate-800 text-sm">MJA Absensi</span>
            </div>
            <div class="w-8 h-8 rounded-full overflow-hidden bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                @if(auth()->user()->profile_photo_url)
                    <img src="{{ auth()->user()->profile_photo_url }}" class="w-full h-full object-cover">
                @else
                    {{ substr(auth()->user()->name, 0, 1) }}
                @endif
            </div>
        </header>

        <!-- Mobile sidebar overlay -->
        <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity"></div>
        <aside id="mobile-sidebar" class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-slate-900 to-slate-800 text-white z-50 transform -translate-x-full transition-transform duration-300 lg:hidden shadow-2xl">
            <!-- same content as desktop sidebar, simplified -->
            <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700">
                <div class="w-9 h-9 shrink-0 bg-white rounded-lg flex items-center justify-center overflow-hidden border border-slate-700 shadow-lg">
                    <img src="{{ asset('logo-mja.jpg') }}" alt="MJA Logo" class="w-full h-full object-cover">
                </div>
                <span class="font-bold text-white whitespace-nowrap">MJA Absensi</span>
            </div>
            <nav class="px-4 py-6 space-y-2">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('admin.rekap') }}" class="nav-link {{ request()->routeIs('admin.rekap*') ? 'active' : '' }}">Rekap Absensi</a>
                    <a href="{{ route('admin.karyawan.index') }}" class="nav-link {{ request()->routeIs('admin.karyawan*') ? 'active' : '' }}">Karyawan</a>
                    <a href="{{ route('admin.lokasi.index') }}" class="nav-link {{ request()->routeIs('admin.lokasi*') ? 'active' : '' }}">Lokasi Kerja</a>
                    <a href="{{ route('admin.izin.index') }}" class="nav-link {{ request()->routeIs('admin.izin*') ? 'active' : '' }}">Verifikasi Izin</a>
                    <a href="{{ route('admin.profile') }}" class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">Pengaturan Profile</a>
                @else
                    <a href="{{ route('karyawan.dashboard') }}" class="nav-link {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('karyawan.absensi') }}" class="nav-link {{ request()->routeIs('karyawan.absensi') ? 'active' : '' }}">Absensi</a>
                    <a href="{{ route('karyawan.riwayat') }}" class="nav-link {{ request()->routeIs('karyawan.riwayat') ? 'active' : '' }}">Riwayat</a>
                    <a href="{{ route('karyawan.izin') }}" class="nav-link {{ request()->routeIs('karyawan.izin*') ? 'active' : '' }}">Pengajuan Izin</a>
                    <a href="{{ route('karyawan.profile') }}" class="nav-link {{ request()->routeIs('karyawan.profile') ? 'active' : '' }}">Pengaturan Profile</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="mt-6 border-t border-slate-700 pt-4">
                    @csrf
                    <button type="submit" class="nav-link w-full text-left text-red-400 hover:bg-red-500/10">Keluar</button>
                </form>
            </nav>
        </aside>

        <!-- Page content -->
        <main class="flex-1 p-4 lg:p-8 overflow-x-hidden">
            <!-- Flash messages -->
            @if(session('success'))
                <div id="flash-success" class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm">
                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div id="flash-error" class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<style type="text/tailwindcss">
.nav-link {
    @apply flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-white/10 transition-all duration-200 font-medium whitespace-nowrap;
}
.nav-link.active {
    @apply bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg;
}
/* Custom Scrollbar for sidebar to avoid width jumping */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-700 rounded-full;
}
</style>

<script>
// Loader logic
const loader = document.getElementById('global-loader');

// Show loader on page navigation
window.addEventListener('beforeunload', () => {
    loader.classList.remove('opacity-0', 'pointer-events-none');
    loader.classList.add('opacity-100');
});

// Show loader on form submissions
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', () => {
        // Jangan tampilkan loader jika form memiliki data-no-loader, .delete-form, atau .verify-form
        if (
            !form.hasAttribute('data-no-loader') && 
            !form.classList.contains('delete-form') && 
            !form.classList.contains('verify-form')
        ) {
            loader.classList.remove('opacity-0', 'pointer-events-none');
            loader.classList.add('opacity-100');
        }
    });
});

// Mobile sidebar toggle
const mobileBtn = document.getElementById('mobile-menu-btn');
const mobileSidebar = document.getElementById('mobile-sidebar');
const mobileOverlay = document.getElementById('mobile-overlay');

if (mobileBtn) {
    mobileBtn.addEventListener('click', () => {
        mobileSidebar.classList.toggle('-translate-x-full');
        mobileOverlay.classList.toggle('hidden');
    });
    mobileOverlay.addEventListener('click', () => {
        mobileSidebar.classList.add('-translate-x-full');
        mobileOverlay.classList.add('hidden');
    });
}

// Auto-dismiss flash messages
setTimeout(() => {
    ['flash-success', 'flash-error'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });
}, 4000);

// Global SweetAlert2 Confirm for Deletion
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const text = this.dataset.confirm || "Data yang dinonaktifkan tidak dapat digunakan lagi.";
        const title = this.dataset.title || "Apakah Anda Yakin?";
        
        Swal.fire({
            title: title,
            text: text,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Ya, Nonaktifkan!",
            cancelButtonText: "Batal",
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl font-semibold px-6',
                cancelButton: 'rounded-xl font-semibold px-6'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loader secara manual setelah dikonfirmasi
                const loaderEl = document.getElementById('global-loader');
                if (loaderEl) {
                    loaderEl.classList.remove('opacity-0', 'pointer-events-none');
                    loaderEl.classList.add('opacity-100');
                }
                this.submit();
            }
        });
    });
});
</script>

@stack('scripts')
</body>
</html>
