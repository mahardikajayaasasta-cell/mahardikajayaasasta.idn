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
    // SEMENTARA: Inject data hari ini
    Route::get('/seed-today', function () {
        try {
            $locations = \App\Models\Location::all();
            $users = \App\Models\User::where('role', 'karyawan')->get();
            $today = \Illuminate\Support\Carbon::today();
            $attCount = 0;

            if ($locations->isNotEmpty() && $users->isNotEmpty()) {
                foreach ($users as $u) {
                    $exists = \App\Models\Attendance::where('user_id', $u->id)->whereDate('date', $today)->exists();
                    if ($exists) continue;

                    $loc = $locations->random();
                    $isLate = rand(0, 100) > 80;

                    $clockIn = $isLate
                        ? $today->copy()->setTime(rand(8, 9), rand(31, 59), 0)
                        : $today->copy()->setTime(rand(7, 8), rand(0, 29), 0);
                    $hasClockedOut = rand(0, 1) > 0;
                    $clockOut = $hasClockedOut ? $clockIn->copy()->addHours(rand(8, 10))->addMinutes(rand(0, 59)) : null;

                    \App\Models\Attendance::create([
                        'user_id' => $u->id,
                        'location_id' => $loc->id,
                        'date' => $today,
                        'clock_in' => $clockIn,
                        'clock_in_latitude' => $loc->latitude + (rand(-100, 100) / 1000000),
                        'clock_in_longitude' => $loc->longitude + (rand(-100, 100) / 1000000),
                        'clock_in_distance' => rand(5, max(6, $loc->radius - 5)),
                        'clock_in_photo' => 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=random&color=fff&size=600',
                        'clock_out' => $clockOut,
                        'clock_out_latitude' => $hasClockedOut ? $loc->latitude + (rand(-100, 100) / 1000000) : null,
                        'clock_out_longitude' => $hasClockedOut ? $loc->longitude + (rand(-100, 100) / 1000000) : null,
                        'clock_out_distance' => $hasClockedOut ? rand(5, max(6, $loc->radius - 5)) : null,
                        'clock_out_photo' => $hasClockedOut ? 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=random&color=fff&size=600' : null,
                        'status' => $isLate ? 'Telat' : 'Hadir',
                    ]);
                    $attCount++;
                }
            }
            return response()->json(['success' => true, 'message' => "Data hari ini berhasil disuntikkan ($attCount record)!"]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    });

});

// =============================================
// Keep-Alive Ping (Mencegah Database Tertidur / Lag Saat Login)
// =============================================
Route::get('/api/ping-db', function () {
    try {
        // Eksekusi query sederhana untuk memastikan database tetap aktif
        \Illuminate\Support\Facades\DB::select('SELECT 1');
        return response()->json(['status' => 'ok', 'message' => 'Database is awake.']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'DB Sleep: ' . $e->getMessage()], 500);
    }
});
