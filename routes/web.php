<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminLocationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// =============================================
// Note: /setup-db route has been removed for security reasons. 
// Do not expose Artisan commands via public HTTP GET routes.
// =============================================


// =============================================
// Self-Healing Migration (Otomatis Setup DB)
// =============================================
try {
    // Hanya jalan jika tabel users belum ada (tandanya database kosong)
    if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    }
} catch (\Exception $e) {
    // Abaikan error saat booting
}

// =============================================
// Public Routes
// =============================================
Route::get('/', function () {
    if (\Illuminate\Support\Facades\Auth::check()) {
        return redirect(\Illuminate\Support\Facades\Auth::user()->isAdmin() ? '/admin/dashboard' : '/karyawan/dashboard');
    }
    return redirect('/login');
});

// Auth routes (Laravel Breeze)
require __DIR__.'/auth.php';

// Debug routes removed for security.

// =============================================
// Dashboard redirect setelah login
// =============================================
Route::middleware('auth')->get('/dashboard', function () {
    if (Auth::user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('karyawan.dashboard');
})->name('dashboard');

// =============================================
// Karyawan Routes
// =============================================
Route::middleware(['auth', 'karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $user = Auth::user();
        $today = today();
        $attendance = $user->todayAttendance();
        $locations = $user->location_id 
            ? \App\Models\Location::where('id', $user->location_id)->get() 
            : \App\Models\Location::active()->get();
        $recentHistory = \App\Models\Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')->take(7)->get();

        return view('karyawan.dashboard', compact('user', 'attendance', 'locations', 'recentHistory', 'today'));
    })->name('dashboard');

    // Absensi
    Route::get('/absensi', [AttendanceController::class, 'index'])->name('absensi');
    Route::post('/absensi/clock-in', [AttendanceController::class, 'clockIn'])->name('clock-in');
    Route::post('/absensi/clock-out', [AttendanceController::class, 'clockOut'])->name('clock-out');

    // Riwayat
    Route::get('/riwayat', [AttendanceController::class, 'history'])->name('riwayat');

    // Pengajuan Izin & Sakit
    Route::get('/izin', [\App\Http\Controllers\LeaveController::class, 'index'])->name('izin');
    Route::post('/izin', [\App\Http\Controllers\LeaveController::class, 'store'])->name('izin.store');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\KaryawanProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\KaryawanProfileController::class, 'update'])->name('profile.update');
});

// =============================================
// Admin Routes
// =============================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Profile
    Route::get('/profile', [\App\Http\Controllers\Admin\AdminProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Admin\AdminProfileController::class, 'update'])->name('profile.update');

    // Dashboard
    Route::get('/dashboard', [AdminAttendanceController::class, 'dashboard'])->name('dashboard');

    // Rekap Absensi
    Route::get('/rekap', [AdminAttendanceController::class, 'rekap'])->name('rekap');
    Route::get('/rekap/{attendance}', [AdminAttendanceController::class, 'show'])->name('rekap.show');
    Route::patch('/rekap/{attendance}/status', [AdminAttendanceController::class, 'updateStatus'])->name('rekap.status');

    // Verifikasi Izin & Sakit
    Route::get('/izin', [\App\Http\Controllers\Admin\AdminLeaveController::class, 'index'])->name('izin.index');
    Route::patch('/izin/{leave}/verify', [\App\Http\Controllers\Admin\AdminLeaveController::class, 'verify'])->name('izin.verify');

    // Export
    Route::get('/rekap/export/excel', [AdminAttendanceController::class, 'exportExcel'])->name('export.excel');
    Route::get('/rekap/export/pdf', [AdminAttendanceController::class, 'exportPdf'])->name('export.pdf');

    // Manajemen Karyawan
    Route::resource('/karyawan', AdminUserController::class)->except(['show']);

    // Manajemen Lokasi
    Route::resource('/lokasi', AdminLocationController::class)->except(['show']);

    // SEMENTARA: Rute inject data dummy (hapus setelah selesai testing)
    Route::get('/seed-dummy', function () {
        try {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            return response()->json(['success' => true, 'message' => 'Data dummy berhasil disuntikkan!', 'output' => \Illuminate\Support\Facades\Artisan::output()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    })->name('seed-dummy');
});
