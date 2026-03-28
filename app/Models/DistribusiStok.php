<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class DistribusiStok extends Model
{
    use HasFactory;
    protected $fillable = ['stok_pusat_id', 'divisi_id', 'jumlah_distribusi', 'tanggal_distribusi'];

    public function stokPusat()
    {
        return $this->belongsTo(StokPusat::class);
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
}