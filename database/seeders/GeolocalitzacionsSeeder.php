<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class GeolocalitzacionsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('es_ES');
        
        // Obtenemos los IDs de los animales existentes
        $animalIds = DB::table('animals')->pluck('id')->toArray();
        
        // Definimos 4 ubicaciones con coordenadas reales en España
        $ubicaciones = [
            [
                'nombre' => 'Madrid',
                'latitud' => 40.4167,
                'longitud' => -3.7033
            ],
            [
                'nombre' => 'Barcelona',
                'latitud' => 41.3851,
                'longitud' => 2.1734
            ],
            [
                'nombre' => 'Valencia',
                'latitud' => 39.4699,
                'longitud' => -0.3763
            ],
            [
                'nombre' => 'Sevilla',
                'latitud' => 37.3891,
                'longitud' => -5.9845
            ]
        ];
        
        // Para cada animal existente, le asignamos aleatoriamente una ubicación
        foreach ($animalIds as $animalId) {
            // Elegimos una ubicación aleatoria
            $ubicacion = $faker->randomElement($ubicaciones);
            
            // Añadimos un poco de variación a las coordenadas para que no sean exactamente iguales
            $latitudVariacion = $faker->randomFloat(6, -0.01, 0.01);
            $longitudVariacion = $faker->randomFloat(6, -0.01, 0.01);
            
            // Insertamos la geolocalización y obtenemos su ID
            $geolocalitzacioId = DB::table('geolocalitzacions')->insertGetId([
                'nombre' => $ubicacion['nombre'],
                'latitud' => $ubicacion['latitud'] + $latitudVariacion,
                'longitud' => $ubicacion['longitud'] + $longitudVariacion,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Actualizamos la tabla animals con la ubicación y la referencia a geolocalización
            DB::table('animals')
                ->where('id', $animalId)
                ->update([
                    'geolocalitzacio_id' => $geolocalitzacioId
                ]);
        }
    }
}


                