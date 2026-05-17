<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah enum type di tabel leaves untuk mendukung Cuti
        Schema::table('leaves', function (Blueprint $table) {
            $table->enum('type', ['izin', 'sakit', 'cuti'])->comment('Tipe pengajuan')->change();
        });

        // 2. Ubah enum status di tabel attendances untuk mendukung Cuti
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', ['Hadir', 'Telat', 'Mangkir', 'Izin', 'Sakit', 'Cuti'])->default('Hadir')->change();
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->enum('type', ['izin', 'sakit'])->comment('Tipe pengajuan')->change();
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', ['Hadir', 'Telat', 'Mangkir', 'Izin', 'Sakit'])->default('Hadir')->change();
        });
    }
};
