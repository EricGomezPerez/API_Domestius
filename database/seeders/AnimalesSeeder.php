<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AnimalesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('es_ES');
        
        // Especies comunes de mascotas
        $especies = ['Perro', 'Gato', 'Conejo', 'Hámster', 'Pájaro', 'Tortuga'];
        
        // Razas por especie
        $razas = [
            'Perro' => ['Labrador', 'Pastor Alemán', 'Bulldog', 'Beagle', 'Chihuahua', 'Golden Retriever'],
            'Gato' => ['Siamés', 'Persa', 'Bengalí', 'Maine Coon', 'Sphynx', 'Ragdoll'],
            'Conejo' => ['Belier', 'Cabeza de León', 'Angora', 'Holandés', 'Rex', 'Gigante de Flandes'],
            'Hámster' => ['Sirio', 'Ruso', 'Roborovski', 'Chino', 'Campbell'],
            'Pájaro' => ['Canario', 'Periquito', 'Agapornis', 'Jilguero', 'Diamante Mandarín'],
            'Tortuga' => ['Rusa', 'Mediterránea', 'Mapa', 'Pintada', 'Orejas Rojas']
        ];
        
        // Estados posibles para un animal
        $estados = ['Disponible', 'En Adopción', 'Adoptado', 'En Tratamiento', 'Reservado'];
        
        // Obtenemos los IDs de las protectoras existentes
        $usuariIds = DB::table('usuaris')->pluck('id')->toArray();
        
        for ($i = 0; $i < 6; $i++) {
            // Elegimos una especie aleatoria
            $especie = $faker->randomElement($especies);
            
            // Elegimos una raza de esa especie
            $raza = $faker->randomElement($razas[$especie]);
            
            DB::table('animals')->insert([
                'nom' => $faker->firstName,
                'edat' => $faker->numberBetween(1, 12),
                'especie' => $especie,
                'raça' => $raza,
                'descripcio' => $faker->paragraph,
                'estat' => $faker->randomElement($estados),
                'imatge' => 'perro1.jpg',
                'usuari_id' => $faker->randomElement($usuariIds),
                'publicacio_id' => null, // Asumimos que inicialmente no tienen publicación
                'geolocalitzacio_id' => null, // Asumimos que inicialmente no tienen geolocalización
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}