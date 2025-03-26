<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Geolocalitzacio extends Model
{

    protected $table = 'geolocalitzacions';
    
    use HasFactory;

    protected $fillable = [
        'nombre', 'latitud', 'longitud'
    ];
}
