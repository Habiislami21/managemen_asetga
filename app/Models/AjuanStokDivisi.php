<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjuanStokDivisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'divisi_id',
        'stok_pusat_id',
        'pengaju_id',
        'jumlah_diminta',
        'jumlah_diberikan',
        'status',
        'approved_by_ga',
        'approved_at_ga',
        'keterangan_ga',
        'approved_by_kabag',
        'approved_at_kabag',
        'keterangan_kabag',
        'processed_by_admin',
        'processed_at_admin',
        'keterangan_admin',
        'reapproved_by_kabag',
        'reapproved_at_kabag',
        'keterangan_kabag_2',
        'rejected_by',
        'rejected_at',
        'alasan_reject',
        'completed_at',
        'keterangan'
    ];

    protected $casts = [
        'approved_at_ga' => 'datetime',
        'approved_at_kabag' => 'datetime',
        'processed_at_admin' => 'datetime',
        'reapproved_at_kabag' => 'datetime',
        'rejected_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function stokPusat()
    {
        return $this->belongsTo(StokPusat::class);
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'pengaju_id');
    }

    public function approvedByGA()
    {
        return $this->belongsTo(User::class, 'approved_by_ga');
    }

    public function approvedByKabag()
    {
        return $this->belongsTo(User::class, 'approved_by_kabag');
    }

    public function processedByAdmin()
    {
        return $this->belongsTo(User::class, 'processed_by_admin');
    }

    public function reapprovedByKabag()
    {
        return $this->belongsTo(User::class, 'reapproved_by_kabag');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Status helpers
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCheckedByKabag()
    {
        return $this->status === 'checked_kabag';
    }

    public function isCheckedByGA()
    {
        return $this->status === 'checked_ga';
    }

    public function isProcessedByAdmin()
    {
        return $this->status === 'processed_admin';
    }

    public function isReapprovedByKabag()
    {
        return $this->status === 'reapproved_kabag';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDivisi($query, $divisiId)
    {
        return $query->where('divisi_id', $divisiId);
    }

    public function scopePendingKabag($query, $divisiId = null)
    {
        $query = $query->where('status', 'pending');
        
        if ($divisiId) {
            $query->where('divisi_id', $divisiId);
        }
        
        return $query;
    }

    public function scopePendingGA($query)
    {
        return $query->where('status', 'checked_kabag');
    }

    public function scopePendingAdmin($query)
    {
        return $query->where('status', 'checked_ga');
    }

    public function scopePendingKabag2($query, $divisiId = null)
    {
        $query = $query->where('status', 'processed_admin');
        if ($divisiId) {
            $query->where('divisi_id', $divisiId);
        }
        return $query;
    }

    public function scopePendingGAFinal($query)
    {
        return $query->where('status', 'reapproved_kabag');
    }

    // Auto-generate nomor ajuan
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ajuan) {
            $ajuan->nomor_ajuan = self::generateNomorAjuan();
        });
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu Kabag 1',
            'checked_kabag' => 'Menunggu Rapat GA', 
            'checked_ga' => 'Menunggu Input Admin',
            'processed_admin' => 'Menunggu Kabag 2',
            'reapproved_kabag' => 'Menunggu Finalisasi GA',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak'
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    private static function generateNomorAjuan()
    {
        $prefix = 'ADV';
        $date = date('Ymd');
        $lastAjuan = self::whereDate('created_at', today())
                        ->orderBy('id', 'desc')
                        ->first();
        
        $sequence = $lastAjuan ? (int)substr($lastAjuan->nomor_ajuan, -3) + 1 : 1;
        
        return $prefix . $date . sprintf('%03d', $sequence);
    }

    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'pending' => 'bg-warning',
            'checked_kabag' => 'bg-info',
            'checked_ga' => 'bg-primary',
            'processed_admin' => 'bg-secondary',
            'reapproved_kabag' => 'bg-dark',
            'completed' => 'bg-success',
            'rejected' => 'bg-danger'
        ];

        return $classes[$this->status] ?? 'bg-secondary';
    }

}