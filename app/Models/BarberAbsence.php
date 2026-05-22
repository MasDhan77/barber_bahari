<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarberAbsence extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal.
     */
    protected $fillable = [
        'barber_id',
        'date',
        'reason',
    ];

    /**
     * Memastikan kolom 'date' diperlakukan sebagai objek Carbon/tanggal.
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relasi kembali ke Barber.
     * Absensi ini dimiliki oleh seorang barber tertentu.
     */
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }
}