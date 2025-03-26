<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class ProtectorasSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('es_ES');
        
        // Nombres realistas de protectoras en España
        $nombres = [
            'Asociación Protectora de Animales Madrid',
            'Refugio Canino Barcelona',
            'SOS Animales Valencia',
            'Centro de Adopción Sevilla',
            'Protectora Amigos Peludos',
            'Refugio Patitas Felices',
            'Asociación Vida Animal'
        ];
        
        // Array para almacenar los IDs de las protectoras creadas
        $protectoraIds = [];
        
        // Crear usuarios y luego protectoras
        foreach ($nombres as $nombre) {
            // 1. Crear usuario
            $usuariId = DB::table('usuaris')->insertGetId([
                'nom' => $nombre,
                'email' => $faker->unique()->safeEmail,
                'contrasenya' => Hash::make('password123'), // Contraseña segura para pruebas
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // 2. Crear protectora asociada al usuario
            $protectoraId = DB::table('protectores')->insertGetId([
                'usuari_id' => $usuariId,
                'direccion' => $faker->address,
                'telefono' => $faker->phoneNumber,
                'verificada' => $faker->boolean(70), // 70% de probabilidad de estar verificada
                'imatge' => 'perro2.jpg', // Imagen por defecto
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $protectoraIds[] = $protectoraId;
        }
        
        // Actualizamos los animales existentes para asignarles una protectora aleatoria
        $animales = DB::table('animals')->get();
        
        foreach ($animales as $animal) {
            DB::table('animals')
                ->where('id', $animal->id)
                ->update([
                    'protectora_id' => $faker->randomElement($protectoraIds)
                ]);
        }
    }
}