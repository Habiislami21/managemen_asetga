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
        Schema::create('ajuans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_spa');
            $table->foreignId('divisi_id')->constrained('divisis')->onDelete('cascade');
            $table->string('barang_ajuan');
            $table->enum('kategori_barang', ['RTK','ATK']);
            $table->integer('banyak_barang');
            $table->string('satuan');
            $table->integer('harga');
            $table->integer('total');
            $table->string('nomor_telp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajuans');
    }
};