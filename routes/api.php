<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*

GET para obtener todos los libros

PUT para actualizar un libro

POST para crear un libro

DELETE para borrar un libro

*/


Route::get('animales', [ApiController::class, 'getAnimales']);

Route::get('animal/get/{id}', [ApiController::class, 'getAnimal']);

Route::post('animal/{id}', [ApiController::class, 'updateAnimal']);

Route::post('animal/create', [ApiController::class, 'createAnimal']);

Route::delete('animal/delete/{id}', [ApiController::class, 'deleteAnimal']); 

Route::get('animal/imatge/{id}', [ApiController::class, 'getAnimalImatge']);


Route::get('protectoras', [ApiController::class, 'getProtectoras']);

Route::get('protectora/get/{id}', [ApiController::class, 'getProtectora']); 

Route::post('protectora/create', [ApiController::class, 'createProtectora']); 

Route::post('protectora/{id}', [ApiController::class, 'updateProtectora']); 

Route::delete('protectora/delete/{id}', [ApiController::class, 'deleteProtectora']);


