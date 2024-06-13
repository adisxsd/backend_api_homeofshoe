<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal_transaksi',
        'alamat_delivery',
        'size',
        'merk',
        'status_id'
    ];
    protected $table = 'transaksis';

    
    public function user()
    {
        return $this->belongsTo(User::class, 'id'); 
    }

    // Definisikan relasi ke model Status
    public function status()
    {
        return $this->belongsTo(StatusPesanan::class, 'status_id'); 
    }
}

