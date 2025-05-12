<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interaccio extends Model
{
    use HasFactory;
    
    protected $table = 'interaccions';
    
    protected $fillable = [
        'usuari_id',
        'publicacio_id',
        'tipus_interaccio_id',
        'accio',
        'detalls',
        'data' 
    ];
    
    protected $appends = ['hora_creacio'];
    
    /**
     * Get the formatted creation time
     *
     * @return string
     */
    public function getHoraCreacioAttribute()
    {
        return $this->created_at->format('H:i');
    }
    
    public function usuari()
    {
        return $this->belongsTo(Usuari::class);
    }
    
    public function publicacio()
    {
        return $this->belongsTo(Publicacio::class);
    }
    
    public function tipusInteraccio()
    {
        return $this->belongsTo(TipusInteraccio::class);
    }
}