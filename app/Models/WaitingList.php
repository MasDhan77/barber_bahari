<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitingList extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'user_id',
        'barber_id',
        'preferred_date',
        'status',
    ];

    /**
     * Relasi ke User (Pelanggan yang mengantre).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Barber yang ditunggu.
     */
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }
}