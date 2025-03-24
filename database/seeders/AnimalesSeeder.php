<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AnimalesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 200; $i++) {
            DB::table('animals')->insert([
                'nombre' => $faker->firstName,
                'raza' => $faker->word,
                'imatge' => $faker->imageUrl(640, 480, 'animales', true),
                'protectora_id' => $faker->numberBetween(1, 4), // Asigna a una de las 4 protectoras
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}