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
        Schema::table('peminjaman_barangs', function (Blueprint $table) {
            // nomor_surat diisi secara manual/fisik, tidak di-generate oleh sistem
            $table->string('nomor_surat')->nullable()->after('nama_kegiatan');
            // path file .docx yang di-generate oleh WordService
            $table->string('file_docx')->nullable()->after('nomor_surat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman_barangs', function (Blueprint $table) {
            $table->dropColumn(['nomor_surat', 'file_docx']);
        });
    }
};
