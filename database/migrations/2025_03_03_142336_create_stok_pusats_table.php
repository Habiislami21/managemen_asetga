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
        Schema::create('stok_pusats', function (Blueprint $table) {
            $table->id();
            $table->integer('kode_barang')->unique();
            $table->string('nama_barang');
            $table->integer('sisa_stok');
            $table->integer('stok_ideal');
            $table->string('satuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_pusats');
    }
};