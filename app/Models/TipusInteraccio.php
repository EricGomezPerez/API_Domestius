<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipusInteraccio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom'
    ];

    // Relación con interacciones
    public function interaccions()
    {
        return $this->hasMany(Interaccio::class);
    }
}
