<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi massal.
     */
    protected $fillable = [
        'booking_id',
        'user_id',
        'barber_id',
        'rating',
        'comment',
    ];

    /**
     * Relasi ke Booking.
     * Satu ulasan merujuk pada satu sesi booking tertentu.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Relasi ke User.
     * Ulasan ini ditulis oleh seorang pelanggan (user).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class, 'barber_id');
    }
}