<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interaccio extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuari_id', 'accio'
    ];

    // Relación con el usuario
    public function usuari()
    {
        return $this->belongsTo(Usuari::class);
    }
}
