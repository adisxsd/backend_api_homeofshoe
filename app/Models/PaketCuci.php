<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketCuci extends Model
{
    use HasFactory;
    protected $table = 'paket_cucis';

    protected $primaryKey = 'paket_cuci_id';

    public $timestamps = false;

    protected $fillable = [
        'nama_paket',
        'deskripsi',
        'harga',
    ];
    
    public function transactions()
    {
        return $this->hasMany(Transaksi::class, 'paket_cuci_id');
    }
}

