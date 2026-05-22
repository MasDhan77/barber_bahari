<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarberSchedule extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi.
     * day_of_week: 0 (Minggu) sampai 6 (Sabtu)
     */
    protected $fillable = [
        'barber_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_off',
    ];

    /**
     * Relasi ke Barber.
     * Satu jadwal kerja dimiliki oleh satu barber.
     */
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }
}