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
        Schema::create('pembimbingan_kp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Mahasiswa
            $table->foreignId('koordinator_id')->nullable()->constrained('users')->onDelete('set null'); // Koordinator
            $table->foreignId('dosen_pembimbing_id')->nullable()->constrained('users')->onDelete('set null'); // Dosen Pembimbing
            $table->string('mentor_perusahaan')->nullable(); // Nama mentor di perusahaan
            $table->string('nama_perusahaan')->nullable(); // Nama perusahaan tempat KP
            $table->text('catatan')->nullable(); // Catatan dari koordinator/pembimbing
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembimbingan_kp');
    }
};
