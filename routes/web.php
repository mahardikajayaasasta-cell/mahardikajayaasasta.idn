<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminLocationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// =============================================
// Remote Database Setup (Khusus Vercel)
// =============================================
Route::get('/setup-db', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return "Database berhasil di-migrate! <a href='/'>Klik di sini untuk Login</a>";
    } catch (\Exception $e) {
        return "Error saat menjalankan setup: " . $e->getMessage();
    }
});

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

Route::get('/debug-user', function() {
    if (auth()->check()) {
        $user = auth()->user();
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'is_admin' => $user->isAdmin(),
            'is_karyawan' => $user->isKaryawan(),
            'session_driver' => config('session.driver'),
            'session_id' => session()->getId(),
        ];
    }
    return "Not logged in. Session driver: " . config('session.driver');
});

Route::get('/debug-routes', function() {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $data = [];
    foreach ($routes as $route) {
        if (str_starts_with($route->getName(), 'admin.')) {
            $data[$route->getName()] = $route->uri();
        }
    }
    return $data;
});

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
        $locations = \App\Models\Location::active()->get();
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
});

// =============================================
// Admin Routes
// =============================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminAttendanceController::class, 'dashboard'])->name('dashboard');

    // Rekap Absensi
    Route::get('/rekap', [AdminAttendanceController::class, 'rekap'])->name('rekap');
    Route::get('/rekap/{attendance}', [AdminAttendanceController::class, 'show'])->name('rekap.show');
    Route::patch('/rekap/{attendance}/status', [AdminAttendanceController::class, 'updateStatus'])->name('rekap.status');

    // Export
    Route::get('/rekap/export/excel', [AdminAttendanceController::class, 'exportExcel'])->name('export.excel');
    Route::get('/rekap/export/pdf', [AdminAttendanceController::class, 'exportPdf'])->name('export.pdf');

    // Manajemen Karyawan
    Route::resource('/karyawan', AdminUserController::class)->except(['show']);

    // Manajemen Lokasi
    Route::resource('/lokasi', AdminLocationController::class)->except(['show']);
});
