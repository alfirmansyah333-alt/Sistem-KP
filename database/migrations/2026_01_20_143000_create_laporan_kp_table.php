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
        Schema::create('laporan_kp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Mahasiswa yang upload laporan');
            $table->string('judul_kp_final')->comment('Judul KP final');
            $table->string('file_laporan')->comment('Path file PDF laporan');
            $table->timestamp('tanggal_upload')->nullable()->comment('Tanggal upload');
            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Index
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_kp');
    }
};
