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
        $divisiZakat = Divisi::where('divisi', 'Zakat')->first();
        $divisiInfaq = Divisi::where('divisi', 'Infaq')->first();
        $divisiMultimedia = Divisi::where('divisi', 'Multimedia')->first();
        $divisiPenghimpunan = Divisi::where('divisi', 'Penghimpunan')->first();
        $divisiWakaf = Divisi::where('divisi', 'Wakaf')->first();
        $divisiBMIKKR = Divisi::where('divisi', 'BMI KKR')->first();
        $divisiBMIPontianak = Divisi::where('divisi', 'BMI Pontianak')->first();

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

        if ($divisiZakat) {
            User::create([
                'name' => 'Kabag Zakat',
                'email' => 'kabagzakat@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'kabag',
                'divisi_id' => $divisiZakat->id,
            ]);
        }

        if ($divisiInfaq) {
            User::create([
                'name' => 'Kabag Infaq',
                'email' => 'kabaginfaq@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'kabag',
                'divisi_id' => $divisiInfaq->id,
            ]);
        }

        if ($divisiMultimedia) {
            User::create([
                'name' => 'Kabag Multimedia',
                'email' => 'kabagmultimedia@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'kabag',
                'divisi_id' => $divisiMultimedia->id,
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

        if ($divisiBMIKKR) {
            User::create([
                'name' => 'Kabag BMI KKR',
                'email' => 'kabagbmikkr@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'kabag',
                'divisi_id' => $divisiBMIKKR->id,
            ]);
        }

        if ($divisiBMIPontianak) {
            User::create([
                'name' => 'Kabag BMI Pontianak',
                'email' => 'kabagbmipontianak@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'kabag',
                'divisi_id' => $divisiBMIPontianak->id,
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

        if ($divisiZakat) {
            User::create([
                'name' => 'PJ Zakat',
                'email' => 'pjzakat@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'pj_divisi',
                'divisi_id' => $divisiZakat->id,
            ]);
        }

        if ($divisiInfaq) {
            User::create([
                'name' => 'PJ Infaq',
                'email' => 'pjinfaq@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'pj_divisi',
                'divisi_id' => $divisiInfaq->id,
            ]);
        }

        if ($divisiMultimedia) {
            User::create([
                'name' => 'PJ Multimedia',
                'email' => 'pjmultimedia@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'pj_divisi',
                'divisi_id' => $divisiMultimedia->id,
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

        if ($divisiBMIKKR) {
            User::create([
                'name' => 'PJ BMI KKR',
                'email' => 'pjbmikkr@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'pj_divisi',
                'divisi_id' => $divisiBMIKKR->id,
            ]);
        }

        if ($divisiBMIPontianak) {
            User::create([
                'name' => 'PJ BMI Pontianak',
                'email' => 'pjbmipontianak@bmi.com',
                'password' => Hash::make('bmi123'),
                'role' => 'pj_divisi',
                'divisi_id' => $divisiBMIPontianak->id,
            ]);
        }
    }
}