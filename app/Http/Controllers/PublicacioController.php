<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publicacio;
use App\Models\Animal;
use App\Models\Protectora;
use App\Models\Usuari;

class PublicacioController extends Controller
{
    /**
     * Obtener todas las publicaciones
     */
    public function getPublicacions()
    {
        $publicacions = Publicacio::with(['usuari', 'animal', 'interaccions'])->get();

        foreach ($publicacions as $publicacio) {
            // Añadimos datos del usuario si existe
            if ($publicacio->usuari) {
                $publicacio->username = $publicacio->usuari->nom;
                $publicacio->usuari->makeHidden(['contrasenya']);
            }
        }

        return response()->json($publicacions);
    }

    /**
     * Obtener una publicación específica por ID
     */
    public function getPublicacio($id)
    {
        $publicacio = Publicacio::with(['usuari', 'animal', 'interaccions'])->find($id);
                
        if (!$publicacio) {
            return response()->json(['error' => 'Publicació no trobada'], 404);
        }
        
        // Añadimos datos del usuario si existe
        if ($publicacio->usuari) {
            $publicacio->username = $publicacio->usuari->nom;
            $publicacio->usuari->makeHidden(['contrasenya']);
        }

        return response()->json($publicacio);
    }

    /**
     * Crear una nueva publicación
     */
    public function createPublicacio(Request $request)
    {
        $validatedData = $request->validate([
            'tipus' => 'required|string|max:255',
            'detalls' => 'required|string',
            'data' => 'required|date',
            'usuari_id' => 'required|integer|exists:usuaris,id',
        ]);

        $publicacio = new Publicacio;
        $publicacio->tipus = $validatedData['tipus'];
        $publicacio->detalls = $validatedData['detalls'];
        $publicacio->data = $validatedData['data'];
        $publicacio->usuari_id = $validatedData['usuari_id'];

        $publicacio->save();

        // Si hay un animal asociado (se envió en la petición pero no es parte del modelo como fillable)
        if ($request->has('animal_id')) {
            $animal = Animal::find($request->animal_id);
            if ($animal) {
                $animal->publicacio_id = $publicacio->id;
                $animal->save();
            }
        }

        return response()->json($publicacio, 201);
    }

    /**
     * Actualizar una publicación existente
     */
    public function updatePublicacio(Request $request, $id)
    {
        $publicacio = Publicacio::find($id);
        
        if (!$publicacio) {
            return response()->json(['error' => 'Publicació no trobada'], 404);
        }

        $validatedData = $request->validate([
            'tipus' => 'sometimes|string|max:255',
            'detalls' => 'sometimes|string',
            'data' => 'sometimes|date',
            'usuari_id' => 'sometimes|integer|exists:usuaris,id',
        ]);

        // Actualizar campos básicos
        if (isset($validatedData['tipus'])) $publicacio->tipus = $validatedData['tipus'];
        if (isset($validatedData['detalls'])) $publicacio->detalls = $validatedData['detalls'];
        if (isset($validatedData['data'])) $publicacio->data = $validatedData['data'];
        if (isset($validatedData['usuari_id'])) $publicacio->usuari_id = $validatedData['usuari_id'];

        $publicacio->save();

        // Si se envió un animal_id en la petición
        if ($request->has('animal_id')) {
            // Actualizar/eliminar referencias anteriores
            $animalActual = $publicacio->animals;
            if ($animalActual && $animalActual->id != $request->animal_id) {
                $animalActual->publicacio_id = null;
                $animalActual->save();
            }
            
            // Establecer el nuevo animal
            if ($request->animal_id) {
                $animal = Animal::find($request->animal_id);
                if ($animal) {
                    $animal->publicacio_id = $publicacio->id;
                    $animal->save();
                }
            }
        }

        return response()->json($publicacio);
    }

    /**
     * Eliminar una publicación
     */
    public function deletePublicacio($id)
    {
        $publicacio = Publicacio::find($id);
        
        if (!$publicacio) {
            return response()->json(['error' => 'Publicació no trobada'], 404);
        }

        // Eliminar la referencia en el animal relacionado
        $animal = $publicacio->animals;
        if ($animal) {
            $animal->publicacio_id = null;
            $animal->save();
        }

        $publicacio->delete();

        return response()->json(['message' => 'Publicació eliminada correctament']);
    }
}