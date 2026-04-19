<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi {{ $dateFrom }} s/d {{ $dateTo }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e293b; }

        .header { background: linear-gradient(135deg, #1e3a5f, #2563eb); color: white; padding: 18px 20px; margin-bottom: 16px; border-radius: 4px; }
        .header h1 { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
        .header p { font-size: 9px; opacity: 0.8; }

        .meta { display: flex; justify-content: space-between; margin-bottom: 14px; font-size: 9px; color: #64748b; }

        table { width: 100%; border-collapse: collapse; }
        thead { background: #1e3a5f; color: white; }
        thead th { padding: 7px 8px; text-align: left; font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:hover { background: #eff6ff; }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 8px; font-weight: 700; }
        .badge-hadir { background: #d1fae5; color: #065f46; }
        .badge-telat { background: #fef3c7; color: #92400e; }
        .badge-mangkir { background: #fee2e2; color: #991b1b; }

        .footer { margin-top: 16px; text-align: right; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }

        .summary { display: flex; gap: 12px; margin-bottom: 14px; }
        .summary-card { flex: 1; padding: 10px 12px; border-radius: 4px; }
        .summary-card.green { background: #d1fae5; }
        .summary-card.yellow { background: #fef3c7; }
        .summary-card.red { background: #fee2e2; }
        .summary-card p.num { font-size: 18px; font-weight: 700; }
        .summary-card p.lbl { font-size: 8px; opacity: 0.7; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rekap Absensi Karyawan</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') }}</p>
    </div>

    <div class="meta">
        <span>Dicetak oleh: {{ auth()->user()->name }}</span>
        <span>Tanggal cetak: {{ $printedAt }}</span>
    </div>

    <!-- Summary -->
    @php
        $hadir = $attendances->where('status', 'Hadir')->count();
        $telat = $attendances->where('status', 'Telat')->count();
        $mangkir = $attendances->where('status', 'Mangkir')->count();
    @endphp
    <div class="summary">
        <div class="summary-card green">
            <p class="num">{{ $hadir }}</p>
            <p class="lbl">Hadir</p>
        </div>
        <div class="summary-card yellow">
            <p class="num">{{ $telat }}</p>
            <p class="lbl">Telat</p>
        </div>
        <div class="summary-card red">
            <p class="num">{{ $mangkir }}</p>
            <p class="lbl">Mangkir</p>
        </div>
        <div class="summary-card" style="background:#f1f5f9">
            <p class="num">{{ $attendances->count() }}</p>
            <p class="lbl">Total Record</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama Karyawan</th>
                <th>Departemen</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Jarak (m)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $i => $att)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $att->user->employee_id ?? '—' }}</td>
                <td>{{ $att->user->name }}</td>
                <td>{{ $att->user->department ?? '—' }}</td>
                <td>{{ $att->date->format('d/m/Y') }}</td>
                <td>{{ $att->clock_in?->format('H:i:s') ?? '—' }}</td>
                <td>{{ $att->clock_out?->format('H:i:s') ?? '—' }}</td>
                <td>{{ $att->location?->name ?? '—' }}</td>
                <td>
                    <span class="badge badge-{{ strtolower($att->status) }}">{{ $att->status }}</span>
                </td>
                <td>{{ $att->clock_in_distance ? number_format($att->clock_in_distance, 0) : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        AbsensiApp — Sistem Absensi Berbasis GPS & Kamera | {{ $printedAt }}
    </div>
</body>
</html>
