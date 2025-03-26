<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuari extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'email', 'contrasenya'
    ];

    // Relación con publicaciones
    public function publicacions()
    {
        return $this->hasMany(Publicacio::class);
    }

    // Relación con mensajes
    public function missatges()
    {
        return $this->hasMany(Missatge::class);
    }

    // Relación con notificaciones
    public function notificacions()
    {
        return $this->hasMany(Notificacio::class);
    }

    // Relación con interacciones
    public function interaccions()
    {
        return $this->hasMany(Interaccio::class);
    }
}
