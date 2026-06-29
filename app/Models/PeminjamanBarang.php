<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanBarang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_peminjaman',
        'nama_peminjam',
        'divisi',
        'nomor_hp',
        'tanggal_kegiatan',
        'tanggal_kembali',
        'tempat',
        'nama_kegiatan',
        'nomor_surat',
        'file_docx',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'tanggal_kembali' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(PeminjamanBarangItem::class);
    }
}
