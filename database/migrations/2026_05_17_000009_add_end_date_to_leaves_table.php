<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Drop unique constraint
            $table->dropUnique(['user_id', 'date']);
            
            // Add end_date column
            $table->date('end_date')->nullable()->after('date')->comment('Tanggal akhir pengajuan');
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn('end_date');
            $table->unique(['user_id', 'date']);
        });
    }
};
