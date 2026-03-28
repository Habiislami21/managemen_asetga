<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah enum menjadi string untuk memperbolehkan status baru
        DB::statement("ALTER TABLE ajuan_stok_divisis CHANGE COLUMN status status VARCHAR(255) DEFAULT 'pending'");

        Schema::table('ajuan_stok_divisis', function (Blueprint $table) {
            // Tahapan Admin
            $table->foreignId('processed_by_admin')->nullable()->after('jumlah_diberikan')->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at_admin')->nullable()->after('processed_by_admin');
            $table->text('keterangan_admin')->nullable()->after('processed_at_admin');

            // Tahapan Re-approve Kabag
            $table->foreignId('reapproved_by_kabag')->nullable()->after('keterangan_admin')->constrained('users')->onDelete('set null');
            $table->timestamp('reapproved_at_kabag')->nullable()->after('reapproved_by_kabag');
            $table->text('keterangan_kabag_2')->nullable()->after('reapproved_at_kabag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ajuan_stok_divisis', function (Blueprint $table) {
            $table->dropForeign(['processed_by_admin']);
            $table->dropForeign(['reapproved_by_kabag']);
            
            $table->dropColumn([
                'processed_by_admin',
                'processed_at_admin',
                'keterangan_admin',
                'reapproved_by_kabag',
                'reapproved_at_kabag',
                'keterangan_kabag_2'
            ]);
        });
        
        // Kembalikan ke enum sebelumnya secara paksa jika ada status yang sama,
        // namun biasanya untuk down, enum varchar tidak dikembalikan error-nya jika terlanjur string
        // DB::statement("ALTER TABLE ajuan_stok_divisis CHANGE COLUMN status status ENUM('pending', 'approved_ga', 'approved_kabag', 'completed', 'rejected') DEFAULT 'pending'");
    }
};
