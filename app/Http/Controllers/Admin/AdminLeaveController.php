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

            $startDate = $leave->date;
            $endDate = $leave->end_date ?? $startDate;
            
            if ($status === 'approved') {
                // Loop through each day in the range and record attendance
                $currentDate = $startDate->copy();
                while ($currentDate->lte($endDate)) {
                    $dateStr = $currentDate->format('Y-m-d');
                    
                    Attendance::updateOrCreate(
                        [
                            'user_id' => $leave->user_id,
                            'date' => $dateStr,
                        ],
                        [
                            'status' => $leave->type === 'izin' ? 'Izin' : ($leave->type === 'sakit' ? 'Sakit' : 'Cuti'),
                            'notes' => 'Pengajuan ' . ucfirst($leave->type) . ' disetujui Admin. Alasan: ' . $leave->reason,
                        ]
                    );
                    
                    $currentDate->addDay();
                }
            } else {
                // Loop through each day in the range and remove attendance
                $currentDate = $startDate->copy();
                while ($currentDate->lte($endDate)) {
                    $dateStr = $currentDate->format('Y-m-d');
                    
                    $attendance = Attendance::where('user_id', $leave->user_id)
                        ->whereDate('date', $dateStr)
                        ->first();
                        
                    if ($attendance && in_array($attendance->status, ['Izin', 'Sakit', 'Cuti'])) {
                        $attendance->delete();
                    }
                    
                    $currentDate->addDay();
                }
            }

            return redirect()->route('admin.izin.index')->with('success', 'Pengajuan izin/sakit/cuti berhasil diverifikasi.');
        } catch (\Exception $e) {
            Log::error('Admin Leave Verification Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
