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
        // Admin
        User::create([
            'name'        => 'Admin Sistem',
            'email'       => 'admin@absensi.app',
            'password'    => Hash::make('password'),
            'role'        => 'admin',
            'employee_id' => 'ADM001',
            'department'  => 'IT',
            'position'    => 'System Administrator',
            'is_active'   => true,
        ]);

        // Sample Karyawan
        User::create([
            'name'        => 'Budi Santoso',
            'email'       => 'budi@absensi.app',
            'password'    => Hash::make('password'),
            'role'        => 'karyawan',
            'employee_id' => 'KRY001',
            'department'  => 'Keuangan',
            'position'    => 'Staff Keuangan',
            'phone'       => '08123456789',
            'is_active'   => true,
        ]);

        User::create([
            'name'        => 'Siti Rahayu',
            'email'       => 'siti@absensi.app',
            'password'    => Hash::make('password'),
            'role'        => 'karyawan',
            'employee_id' => 'KRY002',
            'department'  => 'HR',
            'position'    => 'HR Officer',
            'phone'       => '08198765432',
            'is_active'   => true,
        ]);

        // Sample Lokasi Kantor (Jakarta Pusat - Monas area)
        Location::create([
            'name'       => 'Kantor Pusat',
            'address'    => 'Jl. Medan Merdeka Barat, Jakarta Pusat',
            'latitude'   => -6.1751,
            'longitude'  => 106.8272,
            'radius'     => 100,
            'work_start' => '08:00:00',
            'work_end'   => '17:00:00',
            'late_after' => '08:30:00',
            'is_active'  => true,
        ]);

        Location::create([
            'name'       => 'Kantor Cabang Selatan',
            'address'    => 'Jl. TB Simatupang, Jakarta Selatan',
            'latitude'   => -6.2908,
            'longitude'  => 106.7794,
            'radius'     => 150,
            'work_start' => '08:00:00',
            'work_end'   => '17:00:00',
            'late_after' => '08:30:00',
            'is_active'  => true,
        ]);
    }
}
