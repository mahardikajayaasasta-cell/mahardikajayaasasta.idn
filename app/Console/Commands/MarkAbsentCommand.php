<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Location;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class MarkAbsentCommand extends Command
{
    protected $signature = 'attendance:mark-absent {--date= : Date to process (Y-m-d), default: today}';
    protected $description = 'Tandai karyawan yang tidak absen sebagai Mangkir otomatis';

    public function handle(): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : now()->setTimezone(config('app.timezone', 'Asia/Jakarta'));

        // Lewati akhir pekan (Sabtu=6, Minggu=0)
        if ($date->isWeekend()) {
            $this->info("Weekend ({$date->format('l, d M Y')}). Skip.");
            return 0;
        }

        $this->info("Memproses absensi untuk: {$date->format('d M Y')}");

        // Dapatkan semua karyawan aktif
        $karyawans = User::karyawan()->active()->get();
        $count = 0;

        foreach ($karyawans as $karyawan) {
            // Skip jika sudah ada catatan absen hari ini
            $exists = Attendance::where('user_id', $karyawan->id)
                ->whereDate('date', $date->toDateString())
                ->exists();

            if (!$exists) {
                Attendance::create([
                    'user_id'  => $karyawan->id,
                    'date'     => $date->toDateString(),
                    'status'   => 'Mangkir',
                    'notes'    => 'Otomatis ditandai Mangkir oleh sistem.',
                ]);
                $count++;
                $this->line("  - {$karyawan->name} → Mangkir");
            }
        }

        $this->info("Selesai. Total {$count} karyawan ditandai Mangkir.");
        return 0;
    }
}
