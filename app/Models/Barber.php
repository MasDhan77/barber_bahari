<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barber extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara massal.
     */
    protected $fillable = [
        'user_id',
        'name',
        'bio',
        'photo',
        'status',
    ];

    /**
     * Relasi ke User (Jika barber memiliki akun login)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Jadwal Kerja
     */
    public function schedules()
    {
        return $this->hasMany(BarberSchedule::class);
    }

    /**
     * Relasi ke Data Booking
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Relasi ke Data Absensi (Ijin/Sakit)
     */
    public function absences()
    {
        return $this->hasMany(BarberAbsence::class);
    }

    /**
     * Relasi ke Galeri Foto Portofolio
     */
    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    // Menghubungkan Barber ke Review lewat bantuan tabel Bookings
    public function reviews()
    {
        return $this->hasMany(Review::class, 'barber_id');
    }
}