<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Geolocalitzacio;
use App\Models\Animal;
use Illuminate\Support\Facades\DB;

class GeolocalitzacioController extends Controller
{
    /**
     * Obtener las últimas ubicaciones de todos los animales para mostrar en el mapa
     */
    public function getLatestLocations()
    {
        // Obtenemos los animales con sus geolocalizaciones
        $animalsWithLocations = Animal::with('geolocalitzacio')
            ->whereNotNull('geolocalitzacio_id')
            // Si queremos filtrar por animales perdidos:
            ->where('estat', 'Perdido')
            ->get();
            
        // Preparamos los datos para el mapa
        $mapData = $animalsWithLocations->map(function($animal) {
            if ($animal->geolocalitzacio) {
                return [
                    'id' => $animal->id,
                    'nom' => $animal->nom,
                    'especie' => $animal->especie,
                    'raça' => $animal->raça,
                    'descripcio' => $animal->descripcio,
                    'estat' => $animal->estat,
                    'imatge' => url('/api/animal/imatge/' . $animal->id),
                    'ubicacio' => $animal->geolocalitzacio->nombre,
                    'latitud' => (float) $animal->geolocalitzacio->latitud,
                    'longitud' => (float) $animal->geolocalitzacio->longitud,
                    'ultima_actualizacion' => $animal->geolocalitzacio->updated_at
                ];
            }
            return null;
        })->filter(); // Eliminar elementos nulos
        
        return response()->json($mapData->values());
    }
    

    /**
     * Obtener la ubicación de un animal específico
     */
    public function getAnimalLocation($animalId)
    {
        $animal = Animal::with('geolocalitzacio')->find($animalId);
        
        if (!$animal || !$animal->geolocalitzacio) {
            return response()->json(['error' => 'Ubicación no encontrada'], 404);
        }
        
        return response()->json([
            'id' => $animal->id,
            'nom' => $animal->nom,
            'ubicacio' => $animal->geolocalitzacio->nombre,
            'latitud' => (float) $animal->geolocalitzacio->latitud,
            'longitud' => (float) $animal->geolocalitzacio->longitud,
            'ultima_actualizacion' => $animal->geolocalitzacio->updated_at
        ]);
    }
}