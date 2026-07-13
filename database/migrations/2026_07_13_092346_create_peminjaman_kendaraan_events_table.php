<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_kendaraan_events', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_peminjaman')->unique();
            $table->string('nomor_surat')->nullable();
            $table->string('nama_peminjam');
            $table->string('divisi');
            $table->string('jabatan');
            $table->string('nomor_hp');
            $table->string('jenis_kendaraan');
            $table->string('nama_kendaraan');
            $table->string('nomor_plat');
            $table->date('tanggal_pemakaian');
            $table->date('tanggal_kembali');
            $table->string('peruntukan');
            $table->string('lokasi_tujuan');
            $table->string('nama_kegiatan');
            $table->string('file_docx')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_kendaraan_events');
    }
};
