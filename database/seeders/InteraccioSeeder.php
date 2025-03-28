<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Interaccio;
use App\Models\Publicacio;
use App\Models\TipusInteraccio;
use App\Models\Usuari;

class InteraccioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtenemos las publicaciones, tipos de interacciones y usuarios
        $publicacions = Publicacio::all();
        $tipusInteraccions = TipusInteraccio::all();
        $usuaris = Usuari::all();
        
        if ($publicacions->isEmpty() || $tipusInteraccions->isEmpty() || $usuaris->isEmpty()) {
            $this->command->error('No hay suficientes datos para crear interacciones. Ejecuta los seeders de usuarios, publicaciones y tipos de interacción primero.');
            return;
        }
        
        // Acciones posibles para interacciones
        $acciones = [
            'ha hecho clic',
            'ha respondido',
            'ha compartido',
            'ha comentado',
            'ha solicitado información',
            'ha reportado',
            'ha guardado',
            'ha destacado',
            'ha reaccionado a',
            'ha donado para'
        ];
        
        // Detalles posibles para interacciones
        $detalles = [
            'Me encantaría adoptar este animal, ¿cuál es el procedimiento?',
            'Gracias por la información, me ha sido muy útil.',
            'Evento compartido en mis redes sociales. ¡Mucha suerte!',
            'Estaré allí para ayudar, ¿a qué hora empezamos?',
            'Es admirable la labor que hacéis con los animales.',
            'Qué buena iniciativa, ojalá se apunte mucha gente.',
            'Lucas es precioso, espero que encuentre un hogar pronto.',
            '¿Puedo llevar a mis hijos al evento?',
            'Donación realizada, espero que ayude a mejorar las instalaciones.',
            'Maravillosa labor, sois un ejemplo a seguir.'
        ];
        
        // Para cada publicación, creamos 5 interacciones diferentes
        foreach ($publicacions as $publicacio) {
            // Cogemos 5 tipos de interacción aleatorios sin repetir
            $tiposSeleccionados = $tipusInteraccions->random(5);
            
            foreach ($tiposSeleccionados as $index => $tipus) {
                Interaccio::create([
                    'usuari_id' => $usuaris->random()->id,
                    'accio' => $acciones[$index % count($acciones)],
                    'tipus_interaccio_id' => $tipus->id,
                    'publicacio_id' => $publicacio->id,
                    'data' => now()->subDays(rand(0, 5))->subHours(rand(1, 23)),
                    'detalls' => $detalles[$index % count($detalles)]
                ]);
            }
        }
    }
}