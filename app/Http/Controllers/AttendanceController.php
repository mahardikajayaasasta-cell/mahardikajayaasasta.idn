<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Carbon;

class AttendanceController
{
    /**
     * Halaman absensi karyawan
     */
    public function index()
    {
        $user = Auth::user();
        $today = today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $locations = Location::active()->get();

        return view('karyawan.absensi', compact('attendance', 'locations', 'today'));
    }

    /**
     * Proses Clock In
     */
    public function clockIn(Request $request)
    {
        try {
            $request->validate([
                'latitude'    => 'required|numeric|between:-90,90',
                'longitude'   => 'required|numeric|between:-180,180',
                'photo'       => 'required|string',        // base64
                'location_id' => 'required|exists:locations,id',
            ]);

            $user     = Auth::user();
            $today    = today();

            // Cek apakah sudah absen hari ini
            $existing = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();

            if ($existing && $existing->clock_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen masuk hari ini.',
                ], 422);
            }

            // Validasi GPS dengan Haversine
            $location = Location::findOrFail($request->location_id);
            $check = isWithinRadius(
                (float) $request->latitude,
                (float) $request->longitude,
                (float) $location->latitude,
                (float) $location->longitude,
                $location->radius
            );

            if (!$check['within']) {
                return response()->json([
                    'success'  => false,
                    'message'  => "Anda berada di luar radius lokasi kerja. Jarak Anda: {$check['distance']} meter.",
                    'distance' => $check['distance'],
                ], 422);
            }

            // Upload foto ke Cloudinary
            $photoUrl = $this->uploadPhotoToCloudinary($request->photo, $user->id, 'clock_in');

            if (!$photoUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengunggah foto. Pastikan koneksi internet stabil dan Cloudinary aktif.',
                ], 500);
            }

            // Tentukan status (Hadir/Telat) berdasarkan jam server
            $now       = now()->setTimezone('Asia/Jakarta');
            
            try {
                $lateAfter = Carbon::parse($location->late_after)->setDate(
                    $now->year, $now->month, $now->day
                );
            } catch (\Exception $e) {
                $lateAfter = $now->copy()->startOfDay()->addHours(8)->addMinutes(30);
            }
            
            $status = $now->greaterThan($lateAfter) ? 'Telat' : 'Hadir';

            // Simpan atau update record
            $attendance = Attendance::updateOrCreate(
                ['user_id' => $user->id, 'date' => $today],
                [
                    'location_id'        => $location->id,
                    'clock_in'           => $now,
                    'clock_in_latitude'  => $request->latitude,
                    'clock_in_longitude' => $request->longitude,
                    'clock_in_photo'     => $photoUrl,
                    'clock_in_distance'  => $check['distance'],
                    'status'             => $status,
                ]
            );

            return response()->json([
                'success'    => true,
                'message'    => "Absen masuk berhasil! Status: {$status}",
                'status'     => $status,
                'clock_in'   => $now->format('H:i:s'),
                'distance'   => $check['distance'],
                'photo_url'  => $photoUrl,
            ]);
        } catch (\Throwable $e) {
            \Log::error('CLOCK IN CRASH: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'SERVER ERROR: ' . $e->getMessage() . ' in ' . basename($e->getFile()) . ':' . $e->getLine(),
            ], 500);
        }
    }

    /**
     * Proses Clock Out
     */
    public function clockOut(Request $request)
    {
        $request->validate([
            'latitude'    => 'required|numeric|between:-90,90',
            'longitude'   => 'required|numeric|between:-180,180',
            'photo'       => 'required|string',
            'location_id' => 'required|exists:locations,id',
        ]);

        $user  = Auth::user();
        $today = today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->clock_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan absen masuk hari ini.',
            ], 422);
        }

        if ($attendance->clock_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen pulang hari ini.',
            ], 422);
        }

        // Validasi GPS
        $location = Location::findOrFail($request->location_id);
        $check = isWithinRadius(
            (float) $request->latitude,
            (float) $request->longitude,
            (float) $location->latitude,
            (float) $location->longitude,
            $location->radius
        );

        if (!$check['within']) {
            return response()->json([
                'success'  => false,
                'message'  => "Anda berada di luar radius lokasi kerja. Jarak Anda: {$check['distance']} meter.",
                'distance' => $check['distance'],
            ], 422);
        }

        // Upload foto clock out
        $photoUrl = $this->uploadPhotoToCloudinary($request->photo, $user->id, 'clock_out');

        if (!$photoUrl) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah foto. Silahkan coba lagi.',
            ], 500);
        }

        $now = now()->setTimezone(config('app.timezone', 'Asia/Jakarta'));

        $attendance->update([
            'clock_out'           => $now,
            'clock_out_latitude'  => $request->latitude,
            'clock_out_longitude' => $request->longitude,
            'clock_out_photo'     => $photoUrl,
            'clock_out_distance'  => $check['distance'],
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Absen pulang berhasil!',
            'clock_out' => $now->format('H:i:s'),
            'distance'  => $check['distance'],
            'photo_url' => $photoUrl,
        ]);
    }

    /**
     * Riwayat absensi karyawan (personal)
     */
    public function history(Request $request)
    {
        $user = Auth::user();

        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $attendances = Attendance::where('user_id', $user->id)
            ->byMonth($year, $month)
            ->orderBy('date', 'desc')
            ->get();

        $stats = [
            'hadir'  => $attendances->where('status', 'Hadir')->count(),
            'telat'  => $attendances->where('status', 'Telat')->count(),
            'mangkir' => $attendances->where('status', 'Mangkir')->count(),
        ];

        return view('karyawan.riwayat', compact('attendances', 'stats', 'month', 'year'));
    }

    /**
     * Upload foto base64 ke Cloudinary
     */
    private function uploadPhotoToCloudinary(string $base64Photo, int $userId, string $type): ?string
    {
        try {
            // Pastikan string base64 memiliki prefix yang benar
            if (!str_starts_with($base64Photo, 'data:image')) {
                $base64Photo = 'data:image/jpeg;base64,' . $base64Photo;
            }

            // CEK: Jika Cloudinary tidak dikonfigurasi, simpan langsung ke Database (Base64)
            if (!config('cloudinary.cloud_url') && !env('CLOUDINARY_URL')) {
                \Log::info("Cloudinary tidak aktif. Menggunakan penyimpanan Database untuk User {$userId}.");
                return $base64Photo; // Kembalikan string base64 untuk disimpan di DB
            }

            // Mencoba upload ke Cloudinary
            try {
                $result = Cloudinary::upload($base64Photo, [
                    'folder'         => 'absensi/' . date('Y/m'),
                    'public_id'      => "user_{$userId}_{$type}_" . time(),
                    'transformation' => [
                        'width'   => 600, // Kecilkan ukuran agar hemat bandwith
                        'quality' => 'auto',
                        'format'  => 'webp',
                    ],
                ]);
                return $result->getSecurePath();
            } catch (\Exception $cloudinaryErr) {
                \Log::warning("Gagal ke Cloudinary: " . $cloudinaryErr->getMessage() . ". Fallback ke Database.");
                return $base64Photo; // Fallback ke DB jika upload gagal
            }

        } catch (\Exception $e) {
            \Log::error('Attendance Photo Process Error: ' . $e->getMessage());
            return $base64Photo; // Pilihan terakhir: tetap simpan base64 agar absen tidak gagal
        }
    }
}
