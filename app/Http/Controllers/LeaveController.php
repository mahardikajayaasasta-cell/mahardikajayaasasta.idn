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
            ->get()
            ->sum('duration');
            
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
                'end_date' => 'nullable|date|after_or_equal:date',
                'reason' => 'required|string|max:1000',
                'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            ], [
                'type.required' => 'Jenis pengajuan wajib dipilih.',
                'date.required' => 'Tanggal mulai wajib diisi.',
                'end_date.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai.',
                'reason.required' => 'Alasan pengajuan wajib diisi.',
                'attachment.max' => 'Ukuran file surat/dokumen maksimal 2MB.',
                'attachment.mimes' => 'Format dokumen harus jpeg, png, jpg, atau pdf.',
            ]);

            $startDate = Carbon::parse($request->date);
            $endDate = $request->end_date ? Carbon::parse($request->end_date) : $startDate;
            $duration = $startDate->diffInDays($endDate) + 1;
            $year = $startDate->year;

            // 1. Validasi Maksimal Durasi Pengajuan
            // Cuti: Maksimal 2 Hari per pengajuan
            if ($request->type === 'cuti' && $duration > 2) {
                return back()->withErrors(['end_date' => 'Maksimal sekali pengajuan cuti adalah 2 hari berturut-turut. Jika membutuhkan lebih dari 2 hari, silakan ajukan secara terpisah setelah masuk kembali.'])->withInput();
            }

            // Izin: Maksimal 1 Hari per pengajuan
            if ($request->type === 'izin' && $duration > 1) {
                return back()->withErrors(['end_date' => 'Maksimal pengajuan izin adalah 1 hari per pengajuan agar terdeteksi hari masuk kerja berikutnya.'])->withInput();
            }

            // 2. Validasi Tumpang Tindih Tanggal (Overlap Check)
            $overlap = Leave::where('user_id', $user->id)
                ->where(function($q) use ($startDate, $endDate) {
                    $q->where(function($sub) use ($startDate, $endDate) {
                        $sub->whereRaw('date <= ?', [$endDate->format('Y-m-d')])
                           ->whereRaw('COALESCE(end_date, date) >= ?', [$startDate->format('Y-m-d')]);
                    });
                })
                ->exists();
                
            if ($overlap) {
                return back()->withErrors(['date' => 'Anda sudah memiliki pengajuan izin/sakit/cuti yang aktif pada tanggal atau rentang tanggal tersebut.'])->withInput();
            }

            // 3. Validasi Kehadiran
            $hasAttendance = Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->exists();
            if ($hasAttendance) {
                return back()->withErrors(['date' => 'Tidak dapat mengajukan izin/cuti/sakit karena Anda sudah tercatat melakukan absensi/kehadiran pada salah satu tanggal dalam rentang tersebut.'])->withInput();
            }

            // 4. Validasi H-1 untuk Izin & Cuti
            if (in_array($request->type, ['izin', 'cuti'])) {
                $tomorrow = today()->addDay()->format('Y-m-d');
                if ($startDate->format('Y-m-d') < $tomorrow) {
                    $typeName = $request->type === 'cuti' ? 'cuti' : 'izin';
                    return back()->withErrors(['date' => "Pengajuan {$typeName} harus diajukan minimal H-1 sebelum tanggal mulai pengajuan."])->withInput();
                }
            }

            // 5. Validasi Kuota Cuti Tahunan (12 Hari)
            if ($request->type === 'cuti') {
                $usedCutiCount = Leave::where('user_id', $user->id)
                    ->where('type', 'cuti')
                    ->whereIn('status', ['approved', 'pending'])
                    ->whereYear('date', $year)
                    ->get()
                    ->sum('duration');
                    
                if ($usedCutiCount + $duration > 12) {
                    $sisa = max(0, 12 - $usedCutiCount);
                    return back()->withErrors(['type' => "Batas jatah cuti tahunan Anda untuk tahun {$year} tidak mencukupi (Maksimal 12 hari per tahun. Cuti terpakai/pending: {$usedCutiCount} hari, sisa kuota: {$sisa} hari, durasi yang diajukan: {$duration} hari)."])->withInput();
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
                'date' => $startDate->format('Y-m-d'),
                'end_date' => $request->end_date ? $endDate->format('Y-m-d') : null,
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
