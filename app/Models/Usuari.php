<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class Usuari extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'usuaris';

    protected $fillable = [
        'nom', 'email', 'contrasenya'
    ];

    protected $hidden = [
        'contrasenya',
    ];

    // Indica a Laravel que use el campo 'contrasenya' como contraseña
    public function getAuthPassword()
    {
        return $this->contrasenya;
    }

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
