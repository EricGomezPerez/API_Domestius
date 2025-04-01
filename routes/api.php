<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\ProtectoraController;
use App\Http\Controllers\PublicacioController;


Route::get('animales', [AnimalController::class, 'getAnimales']);

Route::get('animal/get/{id}', [AnimalController::class, 'getAnimal']);

Route::post('animal/create', [AnimalController::class, 'createAnimal']);

Route::post('animal/{id}', [AnimalController::class, 'updateAnimal']);

Route::delete('animal/delete/{id}', [AnimalController::class, 'deleteAnimal']); 

Route::get('animal/imatge/{id}', [AnimalController::class, 'getAnimalImatge']);


Route::get('protectoras', [ProtectoraController::class, 'getProtectoras']);

Route::get('protectora/get/{id}', [ProtectoraController::class, 'getProtectora']); 

Route::post('protectora/create', [ProtectoraController::class, 'createProtectora']); 

Route::post('protectora/{id}', [ProtectoraController::class, 'updateProtectora']); 

Route::delete('protectora/delete/{id}', [ProtectoraController::class, 'deleteProtectora']);

Route::get('protectora/imatge/{id}', [ProtectoraController::class, 'getProtectoraImatge']);


Route::get('publicacions', [PublicacioController::class, 'getPublicacions']);

Route::get('publicacio/{id}', [PublicacioController::class, 'getPublicacio']);

Route::post('publicacio/create', [PublicacioController::class, 'createPublicacio']);

Route::put('publicacio/update/{id}', [PublicacioController::class, 'updatePublicacio']);

Route::delete('publicacio/delete/{id}', [PublicacioController::class, 'deletePublicacio']);
