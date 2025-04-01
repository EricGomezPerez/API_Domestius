<?php

namespace Database\Seeders;

use App\Models\Animal;
use Illuminate\Database\Seeder;
use App\Models\Publicacio;
use App\Models\Usuari;

class PublicacioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos usuarios para asociar con las publicaciones
        $usuaris = Usuari::all();
        
        if ($usuaris->isEmpty()) {
            // Si no hay usuarios, crear uno básico
            $usuari = Usuari::create([
                'nom' => 'Usuario Semilla',
                'email' => 'seed@example.com',
                'contrasenya' => bcrypt('password'),
            ]);
            $usuaris = [$usuari];
        }

        $animals = Animal::all();
        if ($animals->isEmpty()) {
            // Si no hay animales, crear uno básico
            $animal = Animal::create([
                'nom' => 'Perro Semilla',
                'tipus' => 'Perro',
                'edat' => 2,
                'raca' => 'Mestizo',
                'descripcio' => 'Un perro muy cariñoso y juguetón.',
                'imatge' => null,
                'protectora_id' => 1, // Asignar a una protectora existente
            ]);
            $animals = [$animal];
        }

        $publicaciones = [
            [
                'tipus' => 'Adopción',
                'detalls' => 'Buscamos hogar para Lucas, un perro mestizo de 2 años muy cariñoso y juguetón. Ya está vacunado y desparasitado. Ideal para familias con niños.',
                'data' => now()->subDays(5),
                'usuari_id' => $usuaris->random()->id,
                'animal_id' => $animals->random()->id
            ],
            [
                'tipus' => 'Ayuda',
                'detalls' => 'Necesitamos voluntarios este fin de semana para limpiar las instalaciones de la protectora. Cualquier ayuda es bienvenida, aunque sea por unas horas.',
                'data' => now()->subDays(3),
                'usuari_id' => $usuaris->random()->id,
                'animal_id' => $animals->random()->id
            ],
            [
                'tipus' => 'Evento',
                'detalls' => 'Este sábado organizamos una jornada de puertas abiertas en nuestra protectora. Ven a conocer a nuestros animales y las instalaciones. Habrá actividades para los más pequeños.',
                'data' => now()->subDays(2),
                'usuari_id' => $usuaris->random()->id,
                'animal_id' => $animals->random()->id
            ],
            [
                'tipus' => 'Información',
                'detalls' => 'Recordamos que todas nuestras adopciones incluyen vacunación, desparasitación, microchip y esterilización. Además, hacemos seguimiento posterior a la adopción.',
                'data' => now()->subDay(),
                'usuari_id' => $usuaris->random()->id,
                'animal_id' => $animals->random()->id
            ]
        ];

        foreach ($publicaciones as $pub) {
            Publicacio::create($pub);
        }
    }
}