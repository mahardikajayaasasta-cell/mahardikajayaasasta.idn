<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date')->comment('Tanggal absensi');
            
            // Clock In
            $table->timestamp('clock_in')->nullable()->comment('Waktu masuk (server time)');
            $table->decimal('clock_in_latitude', 10, 8)->nullable();
            $table->decimal('clock_in_longitude', 11, 8)->nullable();
            $table->string('clock_in_photo')->nullable()->comment('Cloudinary URL foto masuk');
            $table->decimal('clock_in_distance', 8, 2)->nullable()->comment('Jarak dari kantor saat masuk (meter)');
            
            // Clock Out
            $table->timestamp('clock_out')->nullable()->comment('Waktu pulang (server time)');
            $table->decimal('clock_out_latitude', 10, 8)->nullable();
            $table->decimal('clock_out_longitude', 11, 8)->nullable();
            $table->string('clock_out_photo')->nullable()->comment('Cloudinary URL foto pulang');
            $table->decimal('clock_out_distance', 8, 2)->nullable()->comment('Jarak dari kantor saat pulang (meter)');
            
            // Status
            $table->enum('status', ['Hadir', 'Telat', 'Mangkir'])->default('Hadir');
            $table->text('notes')->nullable()->comment('Catatan admin atau sistem');
            
            $table->timestamps();
            
            // Unique constraint: satu user satu absensi per hari
            $table->unique(['user_id', 'date']);
            $table->index(['date', 'status']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
