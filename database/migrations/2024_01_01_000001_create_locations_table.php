<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama lokasi kerja');
            $table->string('address')->nullable()->comment('Alamat lengkap');
            $table->decimal('latitude', 10, 8)->comment('Koordinat latitude');
            $table->decimal('longitude', 11, 8)->comment('Koordinat longitude');
            $table->integer('radius')->default(100)->comment('Radius toleransi dalam meter');
            $table->time('work_start')->default('08:00:00')->comment('Jam mulai kerja');
            $table->time('work_end')->default('17:00:00')->comment('Jam selesai kerja');
            $table->time('late_after')->default('08:30:00')->comment('Batas jam dianggap telat');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
