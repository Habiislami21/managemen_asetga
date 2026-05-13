<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';

    protected $fillable = [
        'nama_peminjam',
        'nomor_hp',
        'kendaraan_id',
        'tanggal_pinjam',
        'jam_pinjam',
        'jam_kembali',
        'keperluan',
        'alamat_tujuan',
        'status',
        'approval_token',
        'approved_by',
        'approved_at',
        'catatan_admin',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }
}
