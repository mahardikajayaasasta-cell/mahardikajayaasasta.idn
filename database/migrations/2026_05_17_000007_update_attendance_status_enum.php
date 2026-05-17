<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum status di tabel attendances untuk mendukung Izin dan Sakit
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', ['Hadir', 'Telat', 'Mangkir', 'Izin', 'Sakit'])->default('Hadir')->change();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', ['Hadir', 'Telat', 'Mangkir'])->default('Hadir')->change();
        });
    }
};
