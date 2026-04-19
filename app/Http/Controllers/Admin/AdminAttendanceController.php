<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Location;
use App\Exports\AttendanceExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class AdminAttendanceController
{
    /**
     * Dashboard Admin
     */
    public function dashboard()
    {
        $today = today();

        $stats = [
            'total_karyawan' => User::karyawan()->active()->count(),
            'hadir_hari_ini' => Attendance::whereDate('date', $today)->where('status', 'Hadir')->count(),
            'telat_hari_ini' => Attendance::whereDate('date', $today)->where('status', 'Telat')->count(),
            'mangkir_hari_ini' => Attendance::whereDate('date', $today)->where('status', 'Mangkir')->count(),
            'belum_absen' => User::karyawan()->active()->count() -
                Attendance::whereDate('date', $today)->whereNotNull('clock_in')->count(),
        ];

        $recentAttendances = Attendance::with('user', 'location')
            ->whereDate('date', $today)
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentAttendances', 'today'));
    }

    /**
     * Rekap absensi dengan filter
     */
    public function rekap(Request $request)
    {
        $dateFrom = $request->get('date_from', today()->format('Y-m-d'));
        $dateTo   = $request->get('date_to', today()->format('Y-m-d'));
        $status   = $request->get('status', '');
        $userId   = $request->get('user_id', '');

        $query = Attendance::with(['user', 'location'])
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $attendances = $query->paginate(20)->withQueryString();
        $karyawanList = User::karyawan()->active()->orderBy('name')->get();

        return view('admin.rekap', compact('attendances', 'karyawanList', 'dateFrom', 'dateTo', 'status', 'userId'));
    }

    /**
     * Ekspor Excel
     */
    public function exportExcel(Request $request)
    {
        $dateFrom = $request->get('date_from', today()->format('Y-m-d'));
        $dateTo   = $request->get('date_to', today()->format('Y-m-d'));
        $userId   = $request->get('user_id', null);

        $fileName = "rekap-absensi_{$dateFrom}_sd_{$dateTo}.xlsx";

        return Excel::download(
            new AttendanceExport($dateFrom, $dateTo, $userId),
            $fileName
        );
    }

    /**
     * Ekspor PDF
     */
    public function exportPdf(Request $request)
    {
        $dateFrom = $request->get('date_from', today()->format('Y-m-d'));
        $dateTo   = $request->get('date_to', today()->format('Y-m-d'));
        $userId   = $request->get('user_id', null);

        $query = Attendance::with(['user', 'location'])
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date', 'asc');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $attendances = $query->get();

        $pdf = Pdf::loadView('admin.pdf.rekap-absensi', [
            'attendances' => $attendances,
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
            'printedAt'   => now()->format('d/m/Y H:i'),
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download("rekap-absensi_{$dateFrom}_sd_{$dateTo}.pdf");
    }

    /**
     * Detail absensi karyawan
     */
    public function show(Attendance $attendance)
    {
        $attendance->load('user', 'location');
        return view('admin.absensi-detail', compact('attendance'));
    }

    /**
     * Update status absensi manual (admin)
     */
    public function updateStatus(Request $request, Attendance $attendance)
    {
        $request->validate([
            'status' => 'required|in:Hadir,Telat,Mangkir',
            'notes'  => 'nullable|string|max:500',
        ]);

        $attendance->update([
            'status' => $request->status,
            'notes'  => $request->notes,
        ]);

        return back()->with('success', 'Status absensi berhasil diperbarui.');
    }
}
