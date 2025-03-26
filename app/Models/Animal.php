<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'edat', 'especie', 'raça', 'descripcio', 'estat', 'imatge', 'protectora_id', 'publicacio_id', 'geolocalitzacio_id'
    ];

    public function geolocalitzacio()
    {
        return $this->belongsTo(Geolocalitzacio::class);
    }

    public function protectora()
    {
        return $this->belongsTo(Protectora::class);
    }

    public function publicacio()
    {
        return $this->belongsTo(Publicacio::class);
    }
}
