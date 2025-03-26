<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Missatge extends Model
{
    use HasFactory;

    protected $fillable = [
        'remitent_id', 'destinatari_id', 'contingut', 'data'
    ];

    // Relación con el remitente
    public function remitent()
    {
        return $this->belongsTo(Usuari::class, 'remitent_id');
    }

    // Relación con el destinatario
    public function destinatari()
    {
        return $this->belongsTo(Usuari::class, 'destinatari_id');
    }
}
