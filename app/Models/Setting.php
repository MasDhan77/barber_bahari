<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi.
     * key: Nama pengaturannya (misal: 'shop_name')
     * value: Isi pengaturannya (misal: 'Barber Bahari')
     */
    protected $fillable = [
        'key',
        'value',
    ];
}