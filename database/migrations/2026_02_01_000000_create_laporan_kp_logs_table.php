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
        Schema::create('laporan_kp_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('laporan_kp_id')->comment('ID laporan KP');
            $table->unsignedBigInteger('dosen_id')->comment('ID dosen yang melakukan aksi');
            $table->enum('aksi', ['approve', 'reject'])->comment('Aksi yang dilakukan');
            $table->text('catatan')->nullable()->comment('Catatan dari dosen');
            $table->timestamp('created_at')->useCurrent()->comment('Waktu aksi dilakukan');

            // Foreign keys
            $table->foreign('laporan_kp_id')->references('id')->on('laporan_kp')->onDelete('cascade');
            $table->foreign('dosen_id')->references('id')->on('users')->onDelete('cascade');

            // Index
            $table->index('laporan_kp_id');
            $table->index('dosen_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_kp_logs');
    }
};
