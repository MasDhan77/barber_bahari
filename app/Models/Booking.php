<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi.
     * booking_code sangat penting untuk validasi saat pelanggan datang.
     */
    protected $fillable = [
        'user_id',
        'barber_id',
        'booking_code',
        'booking_date',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    /**
     * Relasi ke Pelanggan (User).
     * Satu booking dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Barber.
     * Satu booking ditangani oleh satu barber.
     */
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    /**
     * Relasi ke Services (Many-to-Many).
     * Menggunakan tabel pivot 'booking_services' yang kita buat tadi.
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'booking_services');
    }

    /**
     * Relasi ke Review.
     * Satu booking hanya bisa menghasilkan satu review.
     */
    public function review()
    {
        return $this->hasOne(Review::class);
    }
}