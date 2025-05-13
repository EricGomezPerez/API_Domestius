<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    use HasFactory;
    
    protected $table = 'administradors';
    
    protected $fillable = ['usuari_id'];
    
    /**
     * El usuario que es administrador
     */
    public function usuari()
    {
        return $this->belongsTo(Usuari::class);
    }

    public function protectoresVerificados()
    {
        return $this->hasMany(Protectora::class);
    }
}