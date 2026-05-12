<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisiList = [
            ['divisi' => 'Asset & GA', 'is_pusat' => true],
            ['divisi' => 'KPK', 'is_pusat' => false],
            ['divisi' => 'Finance', 'is_pusat' => false],
            ['divisi' => 'Program', 'is_pusat' => false],
            ['divisi' => 'RSM', 'is_pusat' => false],
            ['divisi' => 'Penghimpunan', 'is_pusat' => false],
            ['divisi' => 'Wakaf', 'is_pusat' => false],
            ['divisi' => 'BMI KKR', 'is_pusat' => false],
            ['divisi' => 'BMI Pontianak', 'is_pusat' => false],
        ];

        foreach ($divisiList as $divisi) {
            DB::table('divisis')->insert([
                'divisi' => $divisi['divisi'],
                'is_pusat' => $divisi['is_pusat'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}