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
        Schema::create('ajuan_stok_divisis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_ajuan')->unique(); // Auto-generated unique number
            $table->foreignId('divisi_id')->constrained()->onDelete('cascade');
            $table->foreignId('stok_pusat_id')->constrained('stok_pusats')->onDelete('cascade');
            $table->foreignId('pengaju_id')->constrained('users')->onDelete('cascade'); // PJ Divisi yang mengajukan
            $table->integer('jumlah_diminta');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'approved_ga', 'approved_kabag', 'completed', 'rejected'])->default('pending');
            $table->foreignId('approved_by_ga')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at_ga')->nullable();
            $table->text('keterangan_ga')->nullable();
            $table->foreignId('approved_by_kabag')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at_kabag')->nullable();
            $table->text('keterangan_kabag')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
            $table->text('alasan_reject')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('jumlah_diberikan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajuan_stok_divisis');
    }
};