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
        Schema::create('stok_divisis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('divisi_id')->constrained()->onDelete('cascade');
            $table->foreignId('stok_pusat_id')->constrained('stok_pusats')->onDelete('cascade');
            $table->integer('stok_ideal');
            $table->integer('sisa_stok');
            
            // Field untuk checking bulanan
            $table->integer('stok_fisik_cek')->nullable()->comment('Stok fisik hasil pengecekan');
            $table->enum('status_cek_bulanan', ['sesuai', 'tidak_sesuai'])->nullable()->comment('Status hasil pengecekan');
            $table->timestamp('tgl_cek_bulanan')->nullable()->comment('Tanggal pengecekan dilakukan');
            $table->string('dicek_oleh', 100)->nullable()->comment('Nama user yang melakukan pengecekan');
            $table->text('keterangan_cek')->nullable()->comment('Keterangan tambahan saat pengecekan');
            
            $table->timestamps();

            // Constraints dan indexes
            $table->unique(['divisi_id', 'stok_pusat_id']); // Mencegah duplikasi
            $table->index(['status_cek_bulanan', 'tgl_cek_bulanan'], 'idx_status_tgl_cek');
            $table->index(['divisi_id', 'status_cek_bulanan'], 'idx_divisi_status_cek');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_divisis');
    }
};