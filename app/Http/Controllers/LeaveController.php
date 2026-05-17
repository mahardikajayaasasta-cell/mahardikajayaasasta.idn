<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\Attendance;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class LeaveController
{
    public function index()
    {
        $user = auth()->user();
        $leaves = Leave::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->paginate(10);
            
        // Hitung kuota cuti tahunan (Maksimal 12 Hari)
        $currentYear = now()->year;
        $usedCuti = Leave::where('user_id', $user->id)
            ->where('type', 'cuti')
            ->whereIn('status', ['approved', 'pending'])
            ->whereYear('date', $currentYear)
            ->count();
            
        $remainingCuti = max(0, 12 - $usedCuti);
            
        return view('karyawan.izin.index', compact('leaves', 'user', 'usedCuti', 'remainingCuti'));
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();

            $request->validate([
                'type' => 'required|in:izin,sakit,cuti',
                'date' => 'required|date',
                'reason' => 'required|string|max:1000',
                'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            ], [
                'type.required' => 'Jenis pengajuan wajib dipilih.',
                'date.required' => 'Tanggal pengajuan wajib diisi.',
                'reason.required' => 'Alasan pengajuan wajib diisi.',
                'attachment.max' => 'Ukuran file surat/dokumen maksimal 2MB.',
                'attachment.mimes' => 'Format dokumen harus jpeg, png, jpg, atau pdf.',
            ]);

            $date = $request->date;
            $year = Carbon::parse($date)->year;

            // 1. Validasi: tidak boleh melakukan pengajuan izin/sakit/cuti di tanggal yang sama (duplikat)
            $exists = Leave::where('user_id', $user->id)
                ->whereDate('date', $date)
                ->exists();
            if ($exists) {
                return back()->withErrors(['date' => 'Anda sudah memiliki pengajuan izin/sakit/cuti pada tanggal ini.'])->withInput();
            }

            // Validasi: tidak boleh mengajukan izin jika sudah ada absensi pada hari itu
            $hasAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $date)
                ->exists();
            if ($hasAttendance) {
                return back()->withErrors(['date' => 'Tidak dapat mengajukan izin karena Anda sudah tercatat hadir/absen pada tanggal tersebut.'])->withInput();
            }

            // 2. Validasi: pengajuan 'izin' dan 'cuti' minimal H-1
            if (in_array($request->type, ['izin', 'cuti'])) {
                $tomorrow = today()->addDay()->format('Y-m-d');
                if ($date < $tomorrow) {
                    $typeName = $request->type === 'cuti' ? 'cuti' : 'izin';
                    return back()->withErrors(['date' => "Pengajuan {$typeName} harus diajukan minimal H-1 sebelum tanggal pengajuan."])->withInput();
                }
            }

            // 3. Validasi: jatah cuti maksimal 12 hari per tahun kalender
            if ($request->type === 'cuti') {
                $usedCutiCount = Leave::where('user_id', $user->id)
                    ->where('type', 'cuti')
                    ->whereIn('status', ['approved', 'pending'])
                    ->whereYear('date', $year)
                    ->count();
                    
                if ($usedCutiCount >= 12) {
                    return back()->withErrors(['type' => "Batas jatah cuti tahunan Anda untuk tahun {$year} telah habis (Maksimal 12 hari per tahun, terpakai/pending: {$usedCutiCount} hari)."])->withInput();
                }
            }

            // Upload surat/bukti jika ada
            $attachmentUrl = null;
            if ($request->hasFile('attachment')) {
                $attachmentUrl = Cloudinary::upload(
                    $request->file('attachment')->getRealPath(),
                    ['folder' => 'leave_attachments']
                )->getSecurePath();
            }

            Leave::create([
                'user_id' => $user->id,
                'type' => $request->type,
                'date' => $date,
                'reason' => $request->reason,
                'attachment' => $attachmentUrl,
                'status' => 'pending',
            ]);

            return redirect()->route('karyawan.izin')->with('success', 'Pengajuan ' . ucfirst($request->type) . ' berhasil dikirim dan sedang menunggu verifikasi admin.');
        } catch (\Exception $e) {
            Log::error('Karyawan Leave Store Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }
}
