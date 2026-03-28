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
        Schema::create('ajuan_finals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ajuan_rutin_id')->constrained('ajuan_rutins')->onDelete('cascade');
            $table->string('nama_spa');
            $table->foreignId('divisi_id')->constrained('divisis')->onDelete('cascade');
            $table->string('barang_ajuan');
            $table->enum('kategori_barang', ['RTK', 'ATK']);
            $table->integer('banyak_barang');
            $table->string('satuan');
            $table->integer('harga');
            $table->integer('total');
            $table->string('nomor_telp');
            $table->string('keterangan')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamp('modified_at')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajuan_finals');
    }
};