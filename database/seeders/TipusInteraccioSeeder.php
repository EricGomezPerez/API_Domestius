<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipusInteraccio;
use Illuminate\Support\Str;

class TipusInteraccioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipus = [
            ['nom' => 'Me gusta', 'descripcio' => 'Mostrar aprecio por una publicación'],
            ['nom' => 'Compartir', 'descripcio' => 'Compartir una publicación con otros usuarios'],
            ['nom' => 'Comentar', 'descripcio' => 'Dejar un comentario en una publicación'],
            ['nom' => 'Adoptar', 'descripcio' => 'Solicitar la adopción de un animal'],
            ['nom' => 'Donar', 'descripcio' => 'Hacer una donación a la protectora'],
            ['nom' => 'Preguntar', 'descripcio' => 'Realizar una pregunta sobre la publicación'],
            ['nom' => 'Reportar', 'descripcio' => 'Reportar contenido inapropiado'],
            ['nom' => 'Destacar', 'descripcio' => 'Marcar una publicación como destacada'],
            ['nom' => 'Guardar', 'descripcio' => 'Guardar publicación para verla más tarde'],
            ['nom' => 'Ofrecer ayuda', 'descripcio' => 'Ofrecer ayuda voluntaria a la protectora']
        ];

        foreach ($tipus as $tip) {
            // Generar el slug a partir del nombre
            $tip['slug'] = Str::slug($tip['nom']);
            TipusInteraccio::create($tip);
        }
    }
}