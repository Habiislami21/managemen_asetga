<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;
    protected $fillable = ['divisi', 'is_pusat'];
    protected $table = 'divisis';
    protected $guarded = ['id'];
    
    public function ajuans()
    {
        return $this->hasMany(Ajuan::class);
    }
    
    public function aduans()
    {
        return $this->hasMany(Aduan::class);
    }

    public function ajuanRutins()
    {
        return $this->hasMany(AjuanRutin::class);
    }

    public function stokDivisi()
    {
        return $this->hasMany(StokDivisi::class);
    }

    /**
     * Relationship ke User
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get kabag for this divisi
     */
    public function kabag()
    {
        return $this->hasOne(User::class)->where('role', 'kabag');
    }

    public static function getDivisiPusat()
    {
        return self::where('is_pusat', true)->first(); // Mengambil divisi yang berperan sebagai pusat
    }
} 