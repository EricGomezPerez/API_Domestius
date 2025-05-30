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
        // Incluir la relación 'geolocalitzacio' a través de 'animal' y también 'tipusInteraccio' en interacciones
        $publicacions = Publicacio::with(['usuari', 'animal.geolocalitzacio', 'interaccions.tipusInteraccio'])->get();
    
        foreach ($publicacions as $publicacio) {
            // Añadimos datos del usuario si existe
            if ($publicacio->usuari) {
                $publicacio->username = $publicacio->usuari->nom;
                $publicacio->usuari->makeHidden(['contrasenya']);
            }
            
            // Añadir el slug a cada interacción
            if ($publicacio->interaccions) {
                foreach ($publicacio->interaccions as $interaccio) {
                    if ($interaccio->tipusInteraccio) {
                        $interaccio->tipus_interaccio_slug = $interaccio->tipusInteraccio->slug;
                    }
                }
            }
        }
    
        return response()->json($publicacions);
    }

    /**
     * Obtener una publicación específica por ID
     */
    public function getPublicacio($id)
{
    $publicacio = Publicacio::with(['usuari', 'animal', 'interaccions.tipusInteraccio'])->find($id);

    if (!$publicacio) {
        return response()->json(['error' => 'Publicació no trobada'], 404);
    }

    // Añadimos datos del usuario si existe
    if ($publicacio->usuari) {
        $publicacio->username = $publicacio->usuari->nom;
        $publicacio->usuari->makeHidden(['contrasenya']);
    }
    
    // Añadir el slug a cada interacción y asegurarse que hora_creacio está disponible
    if ($publicacio->interaccions) {
        foreach ($publicacio->interaccions as $interaccio) {
            if ($interaccio->tipusInteraccio) {
                $interaccio->tipus_interaccio_slug = $interaccio->tipusInteraccio->slug;
            }
            // La hora ya estará incluida por el accessor 'hora_creacio'
        }
    }

    return response()->json($publicacio);
}

    /**
     * Crear una nueva publicación
     */
    public function createPublicacio(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tipus' => 'required|string|max:255',
                'detalls' => 'required|string',
                'data' => 'required|date',
                'usuari_id' => 'required|integer|exists:usuaris,id',
                'animal_id' => 'nullable|integer|exists:animals,id',
            ]);

            $publicacio = new Publicacio;
            $publicacio->tipus = $validatedData['tipus'];
            $publicacio->detalls = $validatedData['detalls'];
            $publicacio->data = $validatedData['data'];
            $publicacio->usuari_id = $validatedData['usuari_id'];

            if (isset($validatedData['animal_id'])) {
                $publicacio->animal_id = $validatedData['animal_id'];
            }

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
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear la publicació: ' . $e->getMessage()], 500);
        }
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

    public function getPublicacionsByAnimal($animalId)
    {
        // Verificar si el animal existe
        $animal = Animal::find($animalId);
        
        if (!$animal) {
            return response()->json(['error' => 'Animal no encontrado'], 404);
        }
        
        // Obtener las publicaciones relacionadas con este animal
        $publicacions = Publicacio::with(['usuari', 'interaccions.tipusInteraccio'])
            ->where('animal_id', $animalId)
            ->get();
        
        // Añadir información del usuario y ocultar la contraseña
        foreach ($publicacions as $publicacio) {
            if ($publicacio->usuari) {
                $publicacio->username = $publicacio->usuari->nom;
                $publicacio->usuari->makeHidden(['contrasenya']);
            }
            
            // También podemos añadir información básica del animal
            $publicacio->nombre_animal = $animal->nom;
            $publicacio->especie_animal = $animal->especie;
            
            // Añadir el slug a cada interacción
            if ($publicacio->interaccions) {
                foreach ($publicacio->interaccions as $interaccio) {
                    if ($interaccio->tipusInteraccio) {
                        $interaccio->tipus_interaccio_slug = $interaccio->tipusInteraccio->slug;
                    }
                }
            }
        }
        
        return response()->json($publicacions);
    }

    public function getPublicacionsByUsuari($usuariId)
    {
        // Verificar si el usuario existe
        $usuari = Usuari::find($usuariId);
        
        if (!$usuari) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        
        // Obtener las publicaciones relacionadas con este usuario
        $publicacions = Publicacio::with(['animal', 'interaccions.tipusInteraccio'])
            ->where('usuari_id', $usuariId)
            ->get();
        
        // Añadir información del usuario y ocultar la contraseña
        foreach ($publicacions as $publicacio) {
            if ($publicacio->usuari) {
                $publicacio->username = $publicacio->usuari->nom;
                $publicacio->usuari->makeHidden(['contrasenya']);
            }
            
            // También podemos añadir información básica del animal
            if ($publicacio->animal) {
                $publicacio->nombre_animal = $publicacio->animal->nom;
                $publicacio->especie_animal = $publicacio->animal->especie;
            }
            
            // Añadir el slug a cada interacción
            if ($publicacio->interaccions) {
                foreach ($publicacio->interaccions as $interaccio) {
                    if ($interaccio->tipusInteraccio) {
                        $interaccio->tipus_interaccio_slug = $interaccio->tipusInteraccio->slug;
                    }
                }
            }
        }
        
        return response()->json($publicacions);
    }
}
