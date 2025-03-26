<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Protectora extends Usuari
{
    use HasFactory;
    protected $table = 'protectores';

    protected $fillable = [
        'verificada' , 'direccion', 'telefono', 'imatge'
    ];

    // Relación con animales
    public function animals()
    {
        return $this->hasMany(Animal::class);
    }
}
