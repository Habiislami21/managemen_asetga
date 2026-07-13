<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanKendaraan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_peminjaman',
        'nomor_surat',
        'nama_peminjam',
        'divisi',
        'jabatan',
        'nomor_hp',
        'jenis_kendaraan',
        'nama_kendaraan',
        'nomor_plat',
        'tanggal_pemakaian',
        'tanggal_kembali',
        'peruntukan',
        'lokasi_tujuan',
        'nama_kegiatan',
        'file_docx',
    ];

    protected $casts = [
        'tanggal_pemakaian' => 'date',
        'tanggal_kembali'   => 'date',
    ];
}
