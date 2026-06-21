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
    }
}
