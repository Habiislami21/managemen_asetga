<?php

namespace Database\Seeders;

use App\Models\Kendaraan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kendaraans = [
            ['nama' => 'Innova', 'kategori' => 'R4'],
            ['nama' => 'Veloz', 'kategori' => 'R4'],
            ['nama' => 'Ertiga', 'kategori' => 'R4'],
            ['nama' => 'Hilux', 'kategori' => 'R4'],
            ['nama' => 'Triton', 'kategori' => 'R4'],
            ['nama' => 'Traga', 'kategori' => 'R4'],
            ['nama' => 'X-Ride Wakaf', 'kategori' => 'R2'],
            ['nama' => 'CRF Wakaf', 'kategori' => 'R2'],
            ['nama' => 'CRF Paskas', 'kategori' => 'R2'],
            ['nama' => 'Revo Wakaf', 'kategori' => 'R2'],
        ];

        foreach ($kendaraans as $kendaraan) {
            Kendaraan::updateOrCreate(['nama' => $kendaraan['nama']], $kendaraan);
        }
    }
}
