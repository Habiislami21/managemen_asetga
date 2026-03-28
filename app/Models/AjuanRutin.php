<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjuanRutin extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_spa', 'divisi_id', 'barang_ajuan', 'kategori_barang', 
        'banyak_barang', 'satuan', 'harga', 'total', 'nomor_telp', 
        'status', 'keterangan', 'approved_by', 'approved_at'
    ];

    public const SATUAN = [
        'bulan','pcs','pack','kg','rim','kotak','bungkus','botol','dus','lusin','set'
    ];
    
    protected $table = 'ajuan_rutins';

    
    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Relasi ke AjuanFinal (satu ajuan rutin bisa punya satu ajuan final)
    public function ajuanFinal()
    {
        return $this->hasOne(AjuanFinal::class, 'ajuan_rutin_id');
    }
}