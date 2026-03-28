<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aduan extends Model
{
    use HasFactory;
    protected $table = 'aduans';

    protected $fillable = [
        'nama_spa', 'divisi_id', 'amanah', 'lokasi_pengaduan', 'jenis_pengaduan', 'kerusakan', 'rincian_pengaduan', 'nomor_telp','status'
    ];

    public const LOKASI_PENGADUAN = [
        'Rumah HUM', 'Rumah RAM', 'BMI Pusat', 'Baituzzakat'
    ];

    protected $guarded = ['id'];
    
    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
}