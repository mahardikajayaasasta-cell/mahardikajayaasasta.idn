<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
                    'position'    => 'Pimpinan/Administrator',
                    'is_active'   => true,
                ]
            );
        } catch (\Exception $e) {}

        try {
            // Sample Karyawan Asli
            User::updateOrCreate(
                ['email' => 'karyawan@absensi.app'],
                [
                    'name'        => 'Budi Ahmad (Karyawan Tes)',
                    'password'    => Hash::make('password'),
                    'role'        => 'karyawan',
                    'employee_id' => 'KRY001',
                    'department'  => 'Operasional',
                    'position'    => 'Staff',
                    'phone'       => '08123456789',
                    'is_active'   => true,
                ]
            );
        } catch (\Exception $e) {}

        try {
            // Lokasi Utama: Kantor MJA Baru
            Location::updateOrCreate(
                ['name' => 'Kantor MJA (Pondok Kacang)'],
                [
                    'address'    => 'Jl. Pd. Kacang Raya No.14B, Pondok Kacang Barat, Kec. Pondok Aren, Kota Tangerang Selatan',
                    // Koordinat pendekatan daerah Pondok Kacang Barat
                    'latitude'   => -6.265000, 
                    'longitude'  => 106.716000, 
                    'radius'     => 150,       
                    'work_start' => '08:00:00',
                    'work_end'   => '17:00:00',
                    'late_after' => '08:30:00',
                    'is_active'  => true,
                ]
            );
        } catch (\Exception $e) {}

        // TIDAK ADA LAGI DUMMY DATA KEHADIRAN / MANUSIA LAINNYA.
    }
}
