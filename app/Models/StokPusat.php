<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokPusat extends Model
{
    use HasFactory;

    protected $fillable = ['kode_barang', 'nama_barang', 'sisa_stok', 'stok_ideal', 'satuan'];

    public const SATUAN = [
        'bulan','pcs','pack','kg','rim','kotak','bungkus','botol','dus','lusin','set'
    ];

    public function distribusi()
    {
        return $this->hasMany(DistribusiStok::class);
    }
    
}