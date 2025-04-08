<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'v1'], function() {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::delete('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->get('/v1/user-profile', function (Request $request) {
    return response()->json($request->user());
});

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\ProtectoraController;
use App\Http\Controllers\PublicacioController;
/* use App\Http\Controllers\Api\V1\AuthController;
 */use App\Http\Controllers\InteraccioController;



Route::get('animales', [AnimalController::class, 'getAnimales']);

Route::get('animal/get/{id}', [AnimalController::class, 'getAnimal']);

Route::post('animal/create', [AnimalController::class, 'createAnimal']);

Route::post('animal/{id}', [AnimalController::class, 'updateAnimal']);

Route::delete('animal/delete/{id}', [AnimalController::class, 'deleteAnimal']); 

Route::get('animal/imatge/{id}', [AnimalController::class, 'getAnimalImatge']);

Route::get('usuario/{id}/animales', [AnimalController::class, 'getAnimalesByUsuario']);


Route::get('protectoras', [ProtectoraController::class, 'getProtectoras']);

Route::get('protectora/get/{id}', [ProtectoraController::class, 'getProtectora']); 

Route::post('protectora/create', [ProtectoraController::class, 'createProtectora']); 

Route::post('protectora/{id}', [ProtectoraController::class, 'updateProtectora']); 

Route::delete('protectora/delete/{id}', [ProtectoraController::class, 'deleteProtectora']);

Route::get('protectora/imatge/{id}', [ProtectoraController::class, 'getProtectoraImatge']);

Route::get('protectora/usuario/{usuarioId}', [ProtectoraController::class, 'getProtectoraByUsuario']);


Route::get('publicacions', [PublicacioController::class, 'getPublicacions']);

Route::get('publicacio/{id}', [PublicacioController::class, 'getPublicacio']);

Route::post('publicacio/create', [PublicacioController::class, 'createPublicacio']);

Route::put('publicacio/update/{id}', [PublicacioController::class, 'updatePublicacio']);

Route::delete('publicacio/delete/{id}', [PublicacioController::class, 'deletePublicacio']);

Route::get('animal/{id}/publicacions', [PublicacioController::class, 'getPublicacionsByAnimal']);

Route::get('usuari/{id}/publicacions', [PublicacioController::class, 'getPublicacionsByUsuari']);

/* Route::group(['prefix' => 'v1'], function() {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::delete('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('')->get('/v1/user-profile', function (Request $request) {
    try {
        return response()->json($request->user());
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}); */

Route::get('interaccions', [InteraccioController::class, 'getInteraccions']);
Route::get('interaccio/{id}', [InteraccioController::class, 'getInteraccio']);
Route::get('publicacio/{id}/interaccions', [InteraccioController::class, 'getInteraccionsByPublicacio']);
Route::post('interaccio/create', [InteraccioController::class, 'createInteraccio']);
Route::put('interaccio/{id}', [InteraccioController::class, 'updateInteraccio']);
Route::delete('interaccio/{id}', [InteraccioController::class, 'deleteInteraccio']);
Route::get('tipus-interaccions', [InteraccioController::class, 'getTipusInteraccions']);