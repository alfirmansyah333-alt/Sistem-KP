<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('laporan_kp', function (Blueprint $table) {
            $table->boolean('is_current')->default(true)->after('catatan_dosen')->comment('Apakah ini versi laporan terbaru');
            $table->integer('versi')->default(1)->after('is_current')->comment('Versi/revisi laporan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_kp', function (Blueprint $table) {
            $table->dropColumn(['is_current', 'versi']);
        });
    }
};
