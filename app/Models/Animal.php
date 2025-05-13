<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    use HasFactory;

    protected $table = 'animals';
    
    protected $fillable = [
        'nom', 
        'edat', 
        'especie', 
        'raça', 
        'descripcio',
        'estat', 
        'imatge',
        'usuari_id',
        'protectora_id',
        'publicacio_id',
        'geolocalitzacio_id'
    ];
    
    // Relación con protectora (opcional)
    public function protectora()
    {
        return $this->belongsTo(Protectora::class);
    }
    
    // Relación con usuario (opcional)
    public function usuari()
    {
        return $this->belongsTo(Usuari::class);
    }
    
    // Relación con geolocalización
    public function geolocalitzacio()
    {
        return $this->belongsTo(Geolocalitzacio::class);
    }
    
    // Relación con publicación
    public function publicacio()
    {
        return $this->belongsTo(Publicacio::class);
    }
    
    // Método para obtener el propietario (sea protectora o usuario)
    public function getPropietari()
    {
        return $this->protectora_id ? $this->protectora : $this->usuari;
    }
    
    // Método para saber si el propietario es una protectora
    public function esPropietariProtectora()
    {
        return $this->protectora_id !== null;
    }
}