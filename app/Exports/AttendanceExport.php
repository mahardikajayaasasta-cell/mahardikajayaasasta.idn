<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        private string $dateFrom,
        private string $dateTo,
        private ?int $userId = null
    ) {}

    public function title(): string
    {
        return 'Rekap Absensi';
    }

    public function query()
    {
        return Attendance::with(['user', 'location'])
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->when($this->userId, fn($q) => $q->where('user_id', $this->userId))
            ->orderBy('date', 'asc')
            ->orderBy('user_id', 'asc');
    }

    public function headings(): array
    {
        return [
            'No',
            'NIP/ID Karyawan',
            'Nama Karyawan',
            'Departemen',
            'Jabatan',
            'Tanggal',
            'Hari',
            'Jam Masuk',
            'Jam Pulang',
            'Durasi Kerja',
            'Lokasi',
            'Status',
            'Jarak Masuk (m)',
            'Foto Masuk (URL)',
            'Foto Pulang (URL)',
            'Catatan',
        ];
    }

    public function map($attendance): array
    {
        static $no = 1;

        return [
            $no++,
            $attendance->user->employee_id ?? '-',
            $attendance->user->name ?? '-',
            $attendance->user->department ?? '-',
            $attendance->user->position ?? '-',
            $attendance->date->format('d/m/Y'),
            $attendance->date->translatedFormat('l'),
            $attendance->clock_in ? $attendance->clock_in->format('H:i:s') : '-',
            $attendance->clock_out ? $attendance->clock_out->format('H:i:s') : '-',
            $attendance->work_duration ?? '-',
            $attendance->location->name ?? '-',
            $attendance->status,
            $attendance->clock_in_distance ?? '-',
            $attendance->clock_in_photo ?? '-',
            $attendance->clock_out_photo ?? '-',
            $attendance->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A5F'],
                ],
            ],
        ];
    }
}
