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
        Schema::create('penerimaan_kps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pengajuan_kp_id')->constrained('pengajuan_kp')->onDelete('cascade');
            $table->string('nama_perusahaan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('file_surat_penerimaan')->nullable();
            $table->enum('status', ['menunggu', 'diterima', 'ditolak'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerimaan_k_p_s');
    }
};
