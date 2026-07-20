<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DivisiAmanahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisiList = [
            'Organ Kepengasuhan',
            'Pengurus Wilayah',
            'Idarah Centre',
            'Imarah Centre',
            'Riayah Centre',
            'Baitul Qur\'an',
            'Baitulmaal Munzalan Indonesia',
            'Baitul Wakaf Munzalan Indonesia',
            'Baituddakwah',
            'Baitul Mu\'amalah',
        ];

        $now = Carbon::now();

        foreach ($divisiList as $divisi) {
            DB::table('divisi_amanahs')->updateOrInsert(
                ['divisi' => $divisi],
                [
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            );
        }
    }
}
