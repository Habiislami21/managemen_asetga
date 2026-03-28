<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjuanFinal extends Model
{
    use HasFactory;

    protected $fillable = [
        'ajuan_rutin_id', 'nama_spa', 'divisi_id', 'barang_ajuan', 
        'kategori_barang', 'banyak_barang', 'satuan', 'harga', 'total', 
        'nomor_telp', 'keterangan', 'approved_by', 'approved_at',
        'modified_by', 'modified_at'
    ];

    protected $table = 'ajuan_finals';

    protected $casts = [
        'approved_at' => 'datetime',
        'modified_at' => 'datetime',
    ];

    // Konstanta yang sama dengan AjuanRutin untuk konsistensi
    public const SATUAN = [
        'bulan','pcs','pack','kg','rim','kotak','bungkus','botol','dus','lusin','set'
    ];

    public const KATEGORI_BARANG = ['RTK', 'ATK'];

    // Relasi ke AjuanRutin (induk)
    public function ajuanRutin()
    {
        return $this->belongsTo(AjuanRutin::class, 'ajuan_rutin_id');
    }

    // Relasi ke Divisi
    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    // Relasi ke User yang approve (dari ajuan rutin)
    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // Relasi ke User yang modify (di ajuan final)
    public function modifiedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'modified_by');
    }

    // Scope untuk kemudahan query
    public function scopeByDivisi($query, $divisiId)
    {
        return $query->where('divisi_id', $divisiId);
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori_barang', $kategori);
    }

    // Accessor untuk format harga
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }
}