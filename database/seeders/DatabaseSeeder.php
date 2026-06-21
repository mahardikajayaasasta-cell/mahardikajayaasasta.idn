<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        try {
            // Admin
            User::updateOrCreate(
                ['email' => 'admin@absensi.app'],
                [
                    'name'        => 'Admin Sistem',
                    'password'    => Hash::make('password'),
                    'role'        => 'admin',
                    'employee_id' => 'ADM001',
                    'department'  => 'IT',
                    'position'    => 'System Administrator',
                    'is_active'   => true,
                ]
            );
        } catch (\Exception $e) {}

        try {
            // Sample Karyawan
            User::updateOrCreate(
                ['email' => 'budi@absensi.app'],
                [
                    'name'        => 'Budi Santoso',
                    'password'    => Hash::make('password'),
                    'role'        => 'karyawan',
                    'employee_id' => 'KRY001',
                    'department'  => 'Keuangan',
                    'position'    => 'Staff Keuangan',
                    'phone'       => '08123456789',
                    'is_active'   => true,
                ]
            );
        } catch (\Exception $e) {}

        try {
            User::updateOrCreate(
                ['email' => 'siti@absensi.app'],
                [
                    'name'        => 'Siti Rahayu',
                    'password'    => Hash::make('password'),
                    'role'        => 'karyawan',
                    'employee_id' => 'KRY002',
                    'department'  => 'HR',
                    'position'    => 'HR Officer',
                    'phone'       => '08123456780',
                    'is_active'   => true,
                ]
            );
        } catch (\Exception $e) {}

        try {
            // Lokasi Pertama: Kantor Pusat (Pondok Kacang)
            Location::updateOrCreate(
                ['name' => 'Kantor Pusat PT MJA (Pd. Kacang)'],
                [
                    'address'    => 'Jl. Pd. Kacang, RT.001/RW003/RW.No.75, Pd. Kacang Bar., Kec. Pd. Aren, Kota Tangerang Selatan, Banten',
                    'latitude'   => -6.265000, // Koordinat Pondok Kacang Barat
                    'longitude'  => 106.716000, 
                    'radius'     => 150,       // Radius toleransi 150 meter
                    'work_start' => '08:00:00',
                    'work_end'   => '17:00:00',
                    'late_after' => '08:30:00',
                    'is_active'  => true,
                ]
            );
        } catch (\Exception $e) {}

        try {
            // Lokasi Kedua: Cabang / Pendidikan (Universitas Pamulang)
            Location::updateOrCreate(
                ['name' => 'Cabang Universitas Pamulang'],
                [
                    'address'    => 'Jl. Surya Kencana No.1, Pamulang Barat, Kec. Pamulang, Kota Tangerang Selatan',
                    'latitude'   => -6.342110,  // Koordinat Universitas Pamulang (Pusat)
                    'longitude'  => 106.741005,
                    'radius'     => 300,        // Radius toleransi diperbesar untuk area kampus luas (300 meter)
                    'work_start' => '08:00:00',
                    'work_end'   => '17:00:00',
                    'late_after' => '08:30:00',
                    'is_active'  => true,
                ]
            );
        } catch (\Exception $e) {}
        try {
            $dummyNames = ['Agus Setiawan', 'Dewi Lestari', 'Reza Pramana'];
            foreach ($dummyNames as $index => $name) {
                User::updateOrCreate(
                    ['email' => 'karyawan' . ($index + 3) . '@absensi.app'],
                    [
                        'name'        => $name,
                        'password'    => Hash::make('password'),
                        'role'        => 'karyawan',
                        'employee_id' => 'KRY00' . ($index + 3),
                        'department'  => 'Operasional',
                        'position'    => 'Staff',
                        'is_active'   => true,
                    ]
                );
            }
        } catch (\Exception $e) {}

        // Inject Dummy Attendance Data (Past 14 Hari)
        try {
            $locations = Location::all();
            $users = User::karyawan()->get();
            $today = \Illuminate\Support\Carbon::today();

            if ($locations->isNotEmpty() && $users->isNotEmpty()) {
                foreach ($users as $u) {
                    for ($i = 1; $i <= 14; $i++) {
                        $date = $today->copy()->subDays($i);
                        // Skip hari minggu (Biarkan kosong agar realistis)
                        if ($date->isSunday()) continue;

                        $hasAtt = \App\Models\Attendance::where('user_id', $u->id)->whereDate('date', $date)->exists();
                        if (!$hasAtt) {
                            $isLate = rand(0, 100) > 80; // 20% probabilitas telat
                            $isMangkir = rand(0, 100) > 95; // 5% probabilitas mangkir
                            
                            $loc = $locations->random();
                            
                            if ($isMangkir) {
                                \App\Models\Attendance::create([
                                    'user_id' => $u->id,
                                    'date' => $date,
                                    'status' => 'Mangkir',
                                    'notes' => 'Tidak hadir tanpa keterangan (Data Dummy)',
                                ]);
                            } else {
                                $clockInTime = $isLate 
                                    ? $date->copy()->setTime(rand(8, 9), rand(31, 59), 0) // Telat
                                    : $date->copy()->setTime(rand(7, 8), rand(0, 29), 0); // On time
                                    
                                $clockOutTime = $clockInTime->copy()->addHours(rand(8, 10))->addMinutes(rand(0, 59));

                                \App\Models\Attendance::create([
                                    'user_id' => $u->id,
                                    'location_id' => $loc->id,
                                    'date' => $date, // Tanggal Absensi
                                    'clock_in' => $clockInTime,
                                    'clock_in_latitude' => $loc->latitude + (rand(-100, 100) / 1000000),
                                    'clock_in_longitude' => $loc->longitude + (rand(-100, 100) / 1000000),
                                    'clock_in_distance' => rand(5, $loc->radius - 5),
                                    'clock_in_photo' => 'https://ui-avatars.com/api/?name='.urlencode($u->name).'&background=random&color=fff&size=600',
                                    'clock_out' => $clockOutTime,
                                    'clock_out_latitude' => $loc->latitude + (rand(-100, 100) / 1000000),
                                    'clock_out_longitude' => $loc->longitude + (rand(-100, 100) / 1000000),
                                    'clock_out_distance' => rand(5, $loc->radius - 5),
                                    'clock_out_photo' => 'https://ui-avatars.com/api/?name='.urlencode($u->name).'&background=random&color=fff&size=600',
                                    'status' => $isLate ? 'Telat' : 'Hadir',
                                ]);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Dummy Seeder Error: ' . $e->getMessage());
        }
    }
}
