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
        Schema::create('aduans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_spa');
            $table->foreignId('divisi_id')->constrained('divisis')->onDelete('cascade');
            $table->string('amanah');
            $table->string('lokasi_pengaduan');
            $table->enum('jenis_pengaduan',['Aset','GA']);
            $table->string('kerusakan');
            $table->text('rincian_pengaduan');
            $table->string('nomor_telp');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aduans');
    }
};