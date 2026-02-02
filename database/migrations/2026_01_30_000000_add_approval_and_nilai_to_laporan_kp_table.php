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
            // Status persetujuan laporan: null (belum diputuskan), 'disetujui', 'ditolak'
            $table->string('status_approve')->nullable()->after('file_laporan')->comment('Status persetujuan: disetujui, ditolak');
            
            // Nilai laporan KP dari dosen pembimbing (0-100)
            $table->decimal('nilai', 5, 2)->nullable()->after('status_approve')->comment('Nilai KP (0-100)');
            
            // Catatan dari dosen pembimbing saat approve/reject
            $table->text('catatan_dosen')->nullable()->after('nilai')->comment('Catatan dari dosen pembimbing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_kp', function (Blueprint $table) {
            $table->dropColumn(['status_approve', 'nilai', 'catatan_dosen']);
        });
    }
};
