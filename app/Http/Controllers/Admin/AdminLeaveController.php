<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminLeaveController
{
    public function index(Request $request)
    {
        $status = $request->get('status', '');
        
        $query = Leave::with(['user', 'verifier'])
            ->orderBy('created_at', 'desc');
            
        if ($status) {
            $query->where('status', $status);
        }
        
        $leaves = $query->paginate(15)->withQueryString();
        
        return view('admin.izin.index', compact('leaves', 'status'));
    }

    public function verify(Request $request, Leave $leave)
    {
        try {
            $request->validate([
                'status' => 'required|in:approved,rejected',
                'notes'  => 'nullable|string|max:500',
            ]);

            $status = $request->status;
            
            // Simpan status verifikasi
            $leave->update([
                'status' => $status,
                'verified_by' => auth()->id(),
            ]);

            $dateStr = $leave->date->format('Y-m-d');

            if ($status === 'approved') {
                // Jika disetujui, buat atau update catatan kehadiran
                Attendance::updateOrCreate(
                    [
                        'user_id' => $leave->user_id,
                        'date' => $dateStr,
                    ],
                    [
                        'status' => $leave->type === 'izin' ? 'Izin' : 'Sakit',
                        'notes' => 'Pengajuan ' . ($leave->type === 'izin' ? 'Izin' : 'Sakit') . ' disetujui Admin. Alasan: ' . $leave->reason,
                    ]
                );
            } else {
                // Jika ditolak, hapus catatan kehadiran otomatis yang bertipe Izin/Sakit
                $attendance = Attendance::where('user_id', $leave->user_id)
                    ->whereDate('date', $dateStr)
                    ->first();
                    
                if ($attendance && in_array($attendance->status, ['Izin', 'Sakit'])) {
                    $attendance->delete();
                }
            }

            return redirect()->route('admin.izin.index')->with('success', 'Pengajuan izin/sakit berhasil diverifikasi.');
        } catch (\Exception $e) {
            Log::error('Admin Leave Verification Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
