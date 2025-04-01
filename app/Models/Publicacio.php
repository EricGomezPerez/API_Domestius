<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publicacio extends Model
{
    use HasFactory;
    protected $table = 'publicacions';

    protected $fillable = [
        'tipus',
        'data',
        'detalls',
        'usuari_id',
        'animal_id'
    ];

    // Relación con el usuario (quien crea la publicación)
    public function usuari()
    {
        return $this->belongsTo(Usuari::class);
    }

    // Relación con los animales en la publicación
    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    // Relacion con las interacciones
    public function interaccions()
    {
        return $this->hasMany(Interaccio::class);
    }
}
