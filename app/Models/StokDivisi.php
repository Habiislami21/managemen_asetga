<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class StokDivisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'divisi_id', 
        'stok_pusat_id', 
        'stok_ideal',
        'sisa_stok',
        'stok_fisik_cek',
        'status_cek_bulanan',
        'tgl_cek_bulanan',
        'dicek_oleh',
        'keterangan_cek'
    ];

    protected $casts = [
        'tgl_cek_bulanan' => 'datetime',
        'stok_ideal' => 'integer',
        'sisa_stok' => 'integer',
        'stok_fisik_cek' => 'integer'
    ];

    public const SATUAN = [
        'bulan','pcs','pack','kg','rim','kotak','bungkus','botol','dus','lusin','set'
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

    // Accessor untuk menghitung selisih
    public function getSelisihAttribute()
    {
        // Jika stok_fisik_cek belum pernah diisi (null), maka selisih juga null.
        if ($this->stok_fisik_cek === null) {
            return null;
        }
        
        // Jika sudah diisi, hitung selisihnya.
        return (int)$this->stok_fisik_cek - (int)$this->sisa_stok;
    }
    

    // Accessor untuk label status cek
    public function getStatusCekLabelAttribute()
    {
        switch ($this->status_cek_bulanan) {
            case 'sesuai':
                return 'Sesuai';
            case 'tidak_sesuai':
                return 'Tidak Sesuai';
            default:
                return 'Belum Dicek';
        }
    }

    // Accessor untuk badge class status cek
    public function getStatusCekBadgeClassAttribute()
    {
        switch ($this->status_cek_bulanan) {
            case 'sesuai':
                return 'bg-success';
            case 'tidak_sesuai':
                return 'bg-danger';
            default:
                return 'bg-warning';
        }
    }

    // Accessor untuk status icon
    public function getStatusCekIconAttribute()
    {
        switch ($this->status_cek_bulanan) {
            case 'sesuai':
                return 'bi-check-circle';
            case 'tidak_sesuai':
                return 'bi-exclamation-triangle';
            default:
                return 'bi-clock';
        }
    }

    // Accessor untuk format tanggal cek
    public function getTglCekFormattedAttribute()
    {
        return $this->tgl_cek_bulanan ? $this->tgl_cek_bulanan->format('d/m/Y H:i') : null;
    }

    // Accessor untuk menentukan persentase akurasi stok
    public function getAccuracyPercentageAttribute()
    {
        if ($this->sisa_stok == 0) return 100;
        
        $stokFisik = $this->stok_fisik_cek ?? $this->sisa_stok;
        $selisih = abs($stokFisik - $this->sisa_stok);
        
        return max(0, 100 - (($selisih / $this->sisa_stok) * 100));
    }

    // Accessor untuk status prioritas cek
    public function getCheckPriorityAttribute()
    {
        // Prioritas tinggi jika belum dicek lebih dari 30 hari
        if (!$this->tgl_cek_bulanan) {
            return $this->created_at->diffInDays(now()) > 30 ? 'high' : 'medium';
        }
        
        // Prioritas tinggi jika terakhir dicek lebih dari 30 hari yang lalu
        if ($this->tgl_cek_bulanan->diffInDays(now()) > 30) {
            return 'high';
        }
        
        // Prioritas medium jika status tidak sesuai
        if ($this->status_cek_bulanan === 'tidak_sesuai') {
            return 'medium';
        }
        
        return 'low';
    }

    // Accessor untuk warna border card berdasarkan status
    public function getCardBorderColorAttribute()
    {
        switch ($this->status_cek_bulanan) {
            case 'sesuai':
                return '#198754'; // success color
            case 'tidak_sesuai':
                return '#dc3545'; // danger color
            default:
                return '#ffc107'; // warning color
        }
    }

    // Scopes
    public function scopeBelumDicek($query)
    {
        return $query->whereNull('status_cek_bulanan');
    }

    public function scopeSudahDicek($query)
    {
        return $query->whereNotNull('status_cek_bulanan');
    }

    public function scopeSesuai($query)
    {
        return $query->where('status_cek_bulanan', 'sesuai');
    }

    public function scopeTidakSesuai($query)
    {
        return $query->where('status_cek_bulanan', 'tidak_sesuai');
    }

    public function scopePeriodeBulan($query, $bulan, $tahun)
    {
        return $query->where(function($q) use ($bulan, $tahun) {
            $q->whereNull('tgl_cek_bulanan')
              ->orWhere(function($subQ) use ($bulan, $tahun) {
                  $subQ->whereYear('tgl_cek_bulanan', $tahun)
                       ->whereMonth('tgl_cek_bulanan', $bulan);
              });
        });
    }

    public function scopeByDivisi($query, $divisiId)
    {
        return $query->where('divisi_id', $divisiId);
    }

    public function scopeAdaStok($query)
    {
        return $query->where('sisa_stok', '>', 0);
    }

    public function scopeNonAssetGA($query)
    {
        return $query->whereHas('divisi', function($q) {
            $q->where('divisi', '!=', 'Asset & GA');
        });
    }

    public function scopePriorityHigh($query)
    {
        return $query->where(function($q) {
            $q->whereNull('tgl_cek_bulanan')
              ->orWhere('tgl_cek_bulanan', '<', now()->subDays(30))
              ->orWhere('status_cek_bulanan', 'tidak_sesuai');
        });
    }

    // Static methods untuk statistik
    public static function getStatsBulanIni()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');
        
        $query = static::nonAssetGA()->adaStok()->periodeBulan($currentMonth, $currentYear);
        
        $total = $query->count();
        $sudahDicek = $query->sudahDicek()->count();
        $sesuai = $query->sesuai()->count();
        $tidakSesuai = $query->tidakSesuai()->count();
        $belumDicek = $total - $sudahDicek;
        
        return [
            'total' => $total,
            'sudah_dicek' => $sudahDicek,
            'belum_dicek' => $belumDicek,
            'sesuai' => $sesuai,
            'tidak_sesuai' => $tidakSesuai,
            'progress_percentage' => $total > 0 ? round(($sudahDicek / $total) * 100) : 0
        ];
    }

    public static function getStatsByDivisi($bulan = null, $tahun = null)
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');
        
        return static::with(['divisi'])
                    ->nonAssetGA()
                    ->adaStok()
                    ->periodeBulan($bulan, $tahun)
                    ->get()
                    ->groupBy('divisi.divisi')
                    ->map(function($items, $divisi) {
                        $total = $items->count();
                        $sudahDicek = $items->whereNotNull('status_cek_bulanan')->count();
                        $sesuai = $items->where('status_cek_bulanan', 'sesuai')->count();
                        $tidakSesuai = $items->where('status_cek_bulanan', 'tidak_sesuai')->count();
                        
                        return [
                            'divisi' => $divisi,
                            'total' => $total,
                            'sudah_dicek' => $sudahDicek,
                            'belum_dicek' => $total - $sudahDicek,
                            'sesuai' => $sesuai,
                            'tidak_sesuai' => $tidakSesuai,
                            'progress_percentage' => $total > 0 ? round(($sudahDicek / $total) * 100) : 0
                        ];
                    });
    }

    // Method untuk mendapatkan items yang perlu prioritas pengecekan
    public static function getItemsPriorityCheck($limit = 10)
    {
        return static::with(['divisi', 'stokPusat'])
                    ->nonAssetGA()
                    ->adaStok()
                    ->priorityHigh()
                    ->orderByRaw('CASE 
                        WHEN tgl_cek_bulanan IS NULL THEN 1
                        WHEN status_cek_bulanan = "tidak_sesuai" THEN 2
                        ELSE 3
                    END')
                    ->orderBy('tgl_cek_bulanan', 'asc')
                    ->limit($limit)
                    ->get();
    }

    // Method untuk validasi data sebelum save
    public function validateStokFisik($stokFisik)
    {
        $stokFisik = (int) $stokFisik;
        
        if ($stokFisik < 0) {
            throw new \Exception('Stok fisik tidak boleh kurang dari 0');
        }
        
        // Warning jika selisih terlalu besar (lebih dari 50% dari stok sistem)
        $selisih = abs($stokFisik - $this->sisa_stok);
        $threshold = max(1, round($this->sisa_stok * 0.5));
        
        if ($selisih > $threshold) {
            Log::warning("Selisih stok besar terdeteksi", [
                'id' => $this->id,
                'divisi' => $this->divisi->divisi ?? 'Unknown',
                'barang' => $this->stokPusat->nama_barang ?? 'Unknown',
                'stok_sistem' => $this->sisa_stok,
                'stok_fisik' => $stokFisik,
                'selisih' => $selisih,
                'threshold' => $threshold
            ]);
        }
        
        return true;
    }

    // Method untuk auto-determine status
    public function determineStatus($stokFisik)
    {
        $stokFisik = (int) $stokFisik;
        $stokSistem = (int) $this->sisa_stok;
        
        return $stokFisik == $stokSistem ? 'sesuai' : 'tidak_sesuai';
    }

    // Event listeners
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            // Ensure integers - hanya casting, jangan ubah nilai
            if ($model->isDirty('sisa_stok')) {
                $model->sisa_stok = (int) $model->sisa_stok;
            }
            
            if ($model->isDirty('stok_ideal')) {
                $model->stok_ideal = (int) $model->stok_ideal;
            }
            
            // PENTING: Jangan auto-set stok_fisik_cek atau status
            // Biarkan controller yang mengatur ini
            if ($model->isDirty('stok_fisik_cek') && $model->stok_fisik_cek !== null) {
                $model->stok_fisik_cek = (int) $model->stok_fisik_cek;
                
                // Hanya validasi, jangan mengubah nilai
                try {
                    $model->validateStokFisik($model->stok_fisik_cek);
                } catch (\Exception $e) {
                    Log::error('Validation failed for stok fisik: ' . $e->getMessage(), [
                        'model_id' => $model->id,
                        'stok_fisik' => $model->stok_fisik_cek,
                        'stok_sistem' => $model->sisa_stok
                    ]);
                }
            }
        });
        
        // Log changes after save
        static::saved(function ($model) {
            if ($model->wasChanged(['status_cek_bulanan', 'stok_fisik_cek'])) {
                Log::info('StokDivisi updated', [
                    'id' => $model->id,
                    'divisi' => $model->divisi->divisi ?? 'Unknown',
                    'barang' => $model->stokPusat->nama_barang ?? 'Unknown',
                    'changes' => $model->getChanges(),
                    'stok_fisik_final' => $model->stok_fisik_cek,
                    'selisih' => $model->selisih
                ]);
            }
        });
    }
}