<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal.
     */
    protected $fillable = [
        'barber_id',
        'image_path',
        'caption',
        'type',
    ];

    /**
     * Relasi ke Barber.
     * Foto di galeri bisa dikaitkan dengan barber tertentu (portofolio) 
     * atau bersifat umum (fasilitas toko).
     */
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }
}