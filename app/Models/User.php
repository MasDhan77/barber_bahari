<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Daftarkan kolom agar bisa diisi (Mass Assignment)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number', // Tambahkan ini
        'role',         // Tambahkan ini
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- Tambahkan Relasi di Bawah Ini ---

    /**
     * User sebagai pelanggan bisa punya banyak booking
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * User bisa memberikan banyak review
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * User bisa menerima banyak notifikasi
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}