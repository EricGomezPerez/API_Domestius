<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrador extends Usuari
{
    use HasFactory;

    // Relación con protectoras verificadas
    public function protectoresVerificados()
    {
        return $this->hasMany(Protectora::class);
    }
}
