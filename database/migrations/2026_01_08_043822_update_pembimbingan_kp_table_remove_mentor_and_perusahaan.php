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
        Schema::table('pembimbingan_kp', function (Blueprint $table) {
            $table->dropColumn(['mentor_perusahaan', 'nama_perusahaan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembimbingan_kp', function (Blueprint $table) {
            $table->string('mentor_perusahaan')->nullable();
            $table->string('nama_perusahaan')->nullable();
        });
    }
};
