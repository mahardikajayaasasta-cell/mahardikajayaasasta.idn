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
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        private string $dateFrom,
        private string $dateTo,
        private ?int $userId = null
    ) {}

    public function title(): string
    {
        return 'Rekap Absensi MJA';
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
            'NO',
            'NIP',
            'NAMA KARYAWAN',
            'DEPARTEMEN',
            'JABATAN',
            'TANGGAL',
            'HARI',
            'JAM MASUK',
            'JAM PULANG',
            'DURASI KERJA',
            'LOKASI MABES',
            'STATUS KEHADIRAN',
            'JARAK ABSEN (m)',
            'FOTO MASUK',
            'FOTO PULANG',
            'KETERANGAN / CATATAN',
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
            $attendance->clock_in ? $attendance->clock_in->format('H:i') : '-',
            $attendance->clock_out ? $attendance->clock_out->format('H:i') : '-',
            $attendance->work_duration ?? '-',
            $attendance->location->name ?? '-',
            $attendance->status,
            $attendance->clock_in_distance ? number_format($attendance->clock_in_distance, 0) : '-',
            $attendance->clock_in_photo ?? '-',
            $attendance->clock_out_photo ?? '-',
            $attendance->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        // 1. Style untuk Header (Baris 1)
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1E3A8A'], // Warna Biru Gelap Korporat (Tailwind Blue-900)
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        $sheet->getRowDimension(1)->setRowHeight(25);

        // 2. Style Borders & Alignment untuk semua cell data
        if ($lastRow > 1) {
            $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCBD5E1'], // Abu-abu muda
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            
            // Agar lebih estetik, bagian angka/waktu dibuat rata tengah
            $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // NO
            $sheet->getStyle("F2:J{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Tanggal-Durasi
            $sheet->getStyle("L2:M{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status & Jarak
        }

        return [];
    }
}
