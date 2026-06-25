<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanBarangItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'peminjaman_barang_id',
        'nama_barang',
        'jumlah',
    ];

    public function peminjamanBarang()
    {
        return $this->belongsTo(PeminjamanBarang::class);
    }
}
