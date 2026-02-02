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
        Schema::table('pengajuan_kp', function (Blueprint $table) {
            $table->string('file_surat_penerimaan')->nullable(); // Path to uploaded acceptance letter
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_kp', function (Blueprint $table) {
            $table->dropColumn('file_surat_penerimaan');
        });
    }
};
