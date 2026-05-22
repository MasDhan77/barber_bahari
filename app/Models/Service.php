<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi (sesuai migration kita tadi).
     */
    protected $fillable = [
        'name',
        'description',
        'duration_minutes',
        'price',
    ];

    /**
     * Relasi Many-to-Many ke Booking.
     * Karena satu layanan bisa ada di banyak booking, 
     * dan satu booking bisa berisi banyak layanan.
     */
    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_services');
    }
}