<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ajuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_spa', 'divisi_id', 'barang_ajuan', 'kategori_barang', 'banyak_barang', 'satuan', 'harga', 'total', 'nomor_telp', 'status'
    ];
    // protected $fillable = ['divisi'];

    public const SATUAN = [
        'bulan','pcs','pack','kg','rim','kotak','bungkus','botol','dus','lusin','set'
    ];
    protected $table = 'ajuans';

    protected $guarded = ['id'];
    
    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
}