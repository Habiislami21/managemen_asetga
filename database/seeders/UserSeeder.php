<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Divisi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil divisi yang akan digunakan
        $divisiKPK = Divisi::where('divisi', 'KPK')->first();
        $divisiFinance = Divisi::where('divisi', 'Finance')->first();
        $divisiProgram = Divisi::where('divisi', 'Program')->first();
        $divisiRSM = Divisi::where('divisi', 'RSM')->first();
        $divisiPenghimpunan = Divisi::where('divisi', 'Penghimpunan')->first();
        $divisiWakaf = Divisi::where('divisi', 'Wakaf')->first();

        // User dengan role admin, aset, ga tidak perlu divisi
        User::create([
            'name' => 'Admin',
            'email' => 'admin@bmi.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'divisi_id' => null,  // Admin tidak perlu divisi
        ]);

        User::create([
            'name' => 'Aset',
            'email' => 'aset@bmi.com',
            'password' => Hash::make('aset123'),
            'role' => 'aset',
            'divisi_id' => null,  // Aset tidak perlu divisi
        ]);

        User::create([
            'name' => 'GA',
            'email' => 'ga@bmi.com',
            'password' => Hash::make('ga123'),
            'role' => 'ga',
            'divisi_id' => null,  // GA tidak perlu divisi
        ]);

        // Kabag untuk setiap divisi
        if ($divisiKPK) {
            User::create([
                'name' => 'Kabag KPK',
                'email' => 'kabagkpk@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'kabag',
                'divisi_id' => $divisiKPK->id,
            ]);
        }

        if ($divisiFinance) {
            User::create([
                'name' => 'Kabag Finance',
                'email' => 'kabagfinance@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'kabag',
                'divisi_id' => $divisiFinance->id,
            ]);
        }

        if ($divisiProgram) {
            User::create([
                'name' => 'Kabag Program',
                'email' => 'kabagprogram@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'kabag',
                'divisi_id' => $divisiProgram->id,
            ]);
        }

        if ($divisiRSM) {
            User::create([
                'name' => 'Kabag RSM',
                'email' => 'kabagrsm@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'kabag',
                'divisi_id' => $divisiRSM->id,
            ]);
        }

        if ($divisiPenghimpunan) {
            User::create([
                'name' => 'Kabag Penghimpunan',
                'email' => 'kabagpenghimpunan@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'kabag',
                'divisi_id' => $divisiPenghimpunan->id,
            ]);
        }

        if ($divisiWakaf) {
            User::create([
                'name' => 'Kabag Wakaf',
                'email' => 'kabagwakaf@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'kabag',
                'divisi_id' => $divisiWakaf->id,
            ]);
        }




        // PJ untuk setiap divisi
        if ($divisiKPK) {
            User::create([
                'name' => 'PJ KPK',
                'email' => 'pjkpk@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'pj_divisi',
                'divisi_id' => $divisiKPK->id,
            ]);
        }

        if ($divisiFinance) {
            User::create([
                'name' => 'PJ Finance',
                'email' => 'pjfinance@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'pj_divisi',
                'divisi_id' => $divisiFinance->id,
            ]);
        }

        if ($divisiProgram) {
            User::create([
                'name' => 'PJ Program',
                'email' => 'pjprogram@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'pj_divisi',
                'divisi_id' => $divisiProgram->id,
            ]);
        }

        if ($divisiRSM) {
            User::create([
                'name' => 'PJ RSM',
                'email' => 'pjrsm@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'pj_divisi',
                'divisi_id' => $divisiRSM->id,
            ]);
        }

        if ($divisiPenghimpunan) {
            User::create([
                'name' => 'PJ Penghimpunan',
                'email' => 'pjpenghimpunan@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'pj_divisi',
                'divisi_id' => $divisiPenghimpunan->id,
            ]);
        }

        if ($divisiWakaf) {
            User::create([
                'name' => 'PJ Wakaf',
                'email' => 'pjwakaf@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'pj_divisi',
                'divisi_id' => $divisiWakaf->id,
            ]);
        }


    }
}