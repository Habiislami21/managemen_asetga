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
        Schema::create('distribusi_stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_pusat_id')->constrained('stok_pusats')->onDelete('cascade');
            $table->foreignId('divisi_id')->constrained()->onDelete('cascade');
            $table->integer('jumlah_distribusi');
            $table->timestamp('tanggal_distribusi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribusi_stoks');
    }
};