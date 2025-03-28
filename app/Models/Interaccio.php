<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interaccio extends Model
{
    use HasFactory;
    protected $table = 'interaccions';

    protected $fillable = [
        'usuari_id', 'accio', 'tipus_interaccio_id', 'publicacio_id', 'data', 'detalls'
    ];

    // Relación con el usuario
    public function usuari()
    {
        return $this->belongsTo(Usuari::class);
    }

    // Relación con la publicación
    public function publicacio()
    {
        return $this->belongsTo(Publicacio::class);
    }

    // Relación con el tipo de interacción

    public function tipusInteraccio()
    {
        return $this->belongsTo(TipusInteraccio::class);
    }
}
