<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Protectora extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'direccion', 'telefono'];

    public function animales()
    {
        return $this->hasMany(Animal::class);
    }
}