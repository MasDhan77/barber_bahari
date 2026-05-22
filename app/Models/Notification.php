<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal.
     */
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'is_read',
    ];

    /**
     * Casting kolom is_read menjadi boolean agar mudah dicek (true/false).
     */
    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Relasi ke User.
     * Setiap notifikasi ditujukan untuk satu user tertentu.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}