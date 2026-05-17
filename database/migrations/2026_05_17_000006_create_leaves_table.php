<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['izin', 'sakit'])->comment('Tipe pengajuan');
            $table->date('date')->comment('Tanggal pengajuan izin/sakit');
            $table->text('reason')->comment('Alasan pengajuan');
            $table->string('attachment')->nullable()->comment('Cloudinary URL surat izin/sakit');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('Status verifikasi');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->comment('Admin yang memverifikasi');
            $table->timestamps();

            // Unique constraint: satu user satu izin/sakit per hari
            $table->unique(['user_id', 'date']);
            $table->index(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
