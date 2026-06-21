<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi {{ $dateFrom }} s/d {{ $dateTo }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; padding: 30px; }

        /* KOP SURAT */
        .kop-surat { width: 100%; border-bottom: 3px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 25px; }
        .kop-surat table { width: 100%; border-collapse: collapse; }
        .kop-surat h1 { font-size: 22px; font-weight: 800; color: #1e3a8a; text-transform: uppercase; margin-bottom: 4px; letter-spacing: 1px; }
        .kop-surat p { font-size: 11px; color: #475569; line-height: 1.4; }

        /* LAPORAN TITLE */
        .report-title { text-align: center; margin-bottom: 25px; }
        .report-title h2 { font-size: 16px; font-weight: 700; text-decoration: underline; margin-bottom: 5px; text-transform: uppercase; }
        .report-title p { font-size: 11px; color: #64748b; }

        /* SUMMARY / STATISTICS */
        .summary-container { width: 100%; margin-bottom: 20px; }
        .summary-table { width: 60%; border-collapse: collapse; margin: 0 auto; }
        .summary-table th, .summary-table td { padding: 8px 12px; font-size: 11px; border: 1px solid #cbd5e1; text-align: center; }
        .summary-table th { background-color: #f1f5f9; font-weight: bold; color: #334155; }
        
        .badge-hadir { color: #166534; font-weight:bold; }
        .badge-telat { color: #9a3412; font-weight:bold; }
        .badge-mangkir { color: #991b1b; font-weight:bold; }

        /* DATA TABLE */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .data-table thead { background-color: #1e3a8a; color: #ffffff; }
        .data-table th { padding: 9px 6px; text-align: center; font-size: 10px; font-weight: bold; border: 1px solid #94a3b8; text-transform: uppercase;}
        .data-table td { padding: 8px 6px; border: 1px solid #cbd5e1; font-size: 10px; text-align: center; vertical-align: middle; }
        .data-table tbody tr:nth-child(even) { background-color: #f8fafc; }
        .data-table tbody tr:nth-child(odd) { background-color: #ffffff; }

        .text-left { text-align: left !important; }

        /* TTD / SIGNATURE AREA */
        .signature-area { width: 100%; margin-top: 40px; }
        .signature-box { float: right; width: 250px; text-align: center; }
        .signature-box p { margin-bottom: 70px; font-size: 12px; }
        .signature-box strong { font-size: 12px; text-decoration: underline; display: block; margin-bottom: 3px; }
        .signature-box span { font-size: 11px; color: #475569; }
        
        /* FOOTER */
        .footer { font-size: 9px; color: #94a3b8; text-align: left; margin-top: 60px; border-top: 1px solid #cbd5e1; padding-top: 10px; clear: both;}
    </style>
</head>
<body>
    @php
        $logoPath = public_path('logo-mja.jpg');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoBase64 = 'data:image/jpeg;base64,' . $logoData;
        }
    @endphp

    <div class="kop-surat">
        <table>
            <tr>
                <td style="width: 15%; text-align: left; vertical-align: middle;">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" style="max-height: 70px; width: auto;">
                    @endif
                </td>
                <td style="width: 85%; text-align: center; vertical-align: middle; padding-right: 15%;">
                    <h1>PT MAHARDIKA JAYA ASASTA</h1>
                    <p>Gedung Mahardika Lt.3, Jl. Surya Kencana No. 1, Pamulang, Tangerang Selatan, Banten<br>
                    Telp: (021) 12345678 | Email: hrd@mahardikajayaasasta.com | Web: www.mahardikajayaasasta.com</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="report-title">
        <h2>REKAPITULASI ABSENSI KARYAWAN</h2>
        <p>Periode: <strong>{{ \Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y') }}</strong> s.d <strong>{{ \Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') }}</strong></p>
    </div>

    @php
        $hadir = $attendances->where('status', 'Hadir')->count();
        $telat = $attendances->where('status', 'Telat')->count();
        $mangkir = $attendances->where('status', 'Mangkir')->count();
        $total = $attendances->count();
    @endphp

    <div class="summary-container">
        <table class="summary-table">
            <thead>
                <tr>
                    <th>TOTAL HADIR</th>
                    <th>TOTAL TERLAMBAT</th>
                    <th>TOTAL MANGKIR</th>
                    <th>TOTAL DATA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge-hadir">{{ $hadir }}</span></td>
                    <td><span class="badge-telat">{{ $telat }}</span></td>
                    <td><span class="badge-mangkir">{{ $mangkir }}</span></td>
                    <td><strong>{{ $total }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="4%">NO</th>
                <th width="12%">TANGGAL</th>
                <th width="12%">NIP</th>
                <th width="18%" class="text-left">NAMA LENGKAP</th>
                <th width="10%">JAM MASUK</th>
                <th width="10%">JAM PULANG</th>
                <th width="14%">TITIK LOKASI</th>
                <th width="10%">JARAK (m)</th>
                <th width="10%">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $i => $att)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $att->date->format('d/m/Y') }}</td>
                <td>{{ $att->user->employee_id ?? '-' }}</td>
                <td class="text-left"><strong>{{ $att->user->name }}</strong></td>
                <td>{{ $att->clock_in?->format('H:i') ?? '-' }}</td>
                <td>{{ $att->clock_out?->format('H:i') ?? '-' }}</td>
                <td>{{ $att->location?->name ?? 'Luar Kantor' }}</td>
                <td>{{ $att->clock_in_distance ? number_format($att->clock_in_distance, 0) : '-' }}</td>
                <td>
                    <span class="badge-{{ strtolower($att->status) }}">{{ strtoupper($att->status) }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center; padding:20px; color:#64748b;">Tidak ada data absensi untuk periode yang dipilih.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-area">
        <div class="signature-box">
            <p>Pamulang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>Mengetahui,</p>
            <strong>Manager HRD PT MJA</strong>
            <span>( .................................................. )</span>
        </div>
    </div>

    <div class="footer">
        Dicetak secara otomatis oleh <strong>Sistem Absensi MJA (Cloud Server)</strong> | Oleh: {{ auth()->user()->name }} | Waktu Cetak: {{ $printedAt }} WIB
    </div>

</body>
</html>
