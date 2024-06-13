<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPesanan extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'status_pesanans';

    // Primary key
    protected $primaryKey = 'status_id';

    // Enable incrementing for primary key
    public $incrementing = true;

    // Data type for primary key
    protected $keyType = 'int';

    // Mass assignable attributes
    protected $fillable = [
        'nama_status',
    ];

    // Timestamps
    public $timestamps = true;
    public function transactions()
    {
        return $this->hasMany(Transaksi::class, 'status_id');
    }
}