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
        // 1. Lokasi Utama: Kantor MJA (Dilakukan pertama agar Karyawan bisa terhubung ke lokasi ini)
        try {
            Location::updateOrCreate(
                ['name' => 'Kantor MJA (Pondok Kacang)'],
                [
                    'address'    => 'Jl. Pd. Kacang Raya No.14B, Pondok Kacang Barat, Kec. Pondok Aren, Kota Tangerang Selatan',
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

        // Mengambil ID Lokasi Kantor MJA untuk karyawan
        $kantorMJA = Location::where('name', 'Kantor MJA (Pondok Kacang)')->first();
        $locationId = $kantorMJA ? $kantorMJA->id : null;

        // 2. Data Karyawan MJA
        try {
            // 1. DIREKTUR : NADYA AN NUURA
            User::updateOrCreate(
                ['email' => 'nadya@absensi.app'],
                [
                    'name'        => 'Nadya An Nuura',
                    'password'    => Hash::make('password'),
                    'role'        => 'admin', // Direktur = Admin agar bisa lihat semua rekap
                    'employee_id' => 'MJA-01',
                    'department'  => 'Manajemen',
                    'position'    => 'Direktur',
                    'location_id' => $locationId,
                    'is_active'   => true,
                ]
            );

            // 2. ADMIN KEUANGAN : INDRIYANI
            User::updateOrCreate(
                ['email' => 'indriyani@absensi.app'],
                [
                    'name'        => 'Indriyani',
                    'password'    => Hash::make('password'),
                    'role'        => 'admin', // Admin Keuangan = Admin
                    'employee_id' => 'MJA-02',
                    'department'  => 'Keuangan',
                    'position'    => 'Admin Keuangan',
                    'location_id' => $locationId,
                    'is_active'   => true,
                ]
            );

            // 3. MARKETING ONLINE : VIRRA
            User::updateOrCreate(
                ['email' => 'virra@absensi.app'],
                [
                    'name'        => 'Virra',
                    'password'    => Hash::make('password'),
                    'role'        => 'karyawan', 
                    'employee_id' => 'MJA-03',
                    'department'  => 'Marketing',
                    'position'    => 'Marketing Online',
                    'location_id' => $locationId,
                    'is_active'   => true,
                ]
            );

            // 4. KURIR : AHMAD ZIDAN
            User::updateOrCreate(
                ['email' => 'zidan@absensi.app'],
                [
                    'name'        => 'Ahmad Zidan',
                    'password'    => Hash::make('password'),
                    'role'        => 'karyawan', 
                    'employee_id' => 'MJA-04',
                    'department'  => 'Operasional',
                    'position'    => 'Kurir',
                    'location_id' => $locationId,
                    'is_active'   => true,
                ]
            );

            // 5. KURIR : MISBAH
            User::updateOrCreate(
                ['email' => 'misbah@absensi.app'],
                [
                    'name'        => 'Misbah',
                    'password'    => Hash::make('password'),
                    'role'        => 'karyawan', 
                    'employee_id' => 'MJA-05',
                    'department'  => 'Operasional',
                    'position'    => 'Kurir',
                    'location_id' => $locationId,
                    'is_active'   => true,
                ]
            );
        } catch (\Exception $e) {}
    }
}
