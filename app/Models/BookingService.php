<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingService extends Model
{
    use HasFactory;

    /**
     * Nama tabel didefinisikan secara eksplisit jika tidak mengikuti 
     * aturan penamaan jamak Laravel (optional).
     */
    protected $table = 'booking_services';

    /**
     * Kolom yang dapat diisi.
     */
    protected $fillable = [
        'booking_id',
        'service_id',
    ];

    /**
     * Relasi ke Booking.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Relasi ke Service.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}