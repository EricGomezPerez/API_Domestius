<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Animal;
use App\Models\Protectora;
use App\Models\Geolocalitzacio;
use App\Models\Publicacio;
use App\Models\Usuari;
use App\Models\Interaccio;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


class AnimalController extends Controller
{
    public function getAnimales()
    {
        $animales = Animal::with(['protectora', 'usuari', 'geolocalitzacio', 'publicacio'])->get();

        foreach ($animales as $animal) {
            $animal->imatge = url('/api/animal/imatge/' . $animal->id);

            
            // Añadir información del propietario
            if ($animal->protectora_id) {
                $animal->propietario_tipo = 'protectora';
                $animal->propietario_nombre = $animal->protectora->nom;
            } else {
                $animal->propietario_tipo = 'usuario';
                $animal->propietario_nombre = $animal->usuari->nom;
            }
        }

        return response()->json($animales);
    }

    public function getAnimal($id)
    {
        $animal = Animal::with(['protectora', 'usuari', 'geolocalitzacio', 'publicacio'])->find($id);
                
        if (!$animal) {
            return response()->json(['error' => 'Animal no encontrado'], 404);
        }
        
        $animal->imatge = url('/api/animal/imatge/' . $animal->id);

        
        // Añadir información del propietario
        if ($animal->protectora_id) {
            $animal->propietario_tipo = 'protectora';
            $animal->propietario_nombre = $animal->protectora->nom;
        } else {
            $animal->propietario_tipo = 'usuario';
            $animal->propietario_nombre = $animal->usuari->nom;
        }
        
        return response()->json($animal);
    }
    
    public function createAnimal(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nom' => 'required|string|max:255',
                'edat' => 'required|integer',
                'especie' => 'required|string|max:255',
                'raça' => 'required|string|max:255',
                'descripcio' => 'nullable|string',
                'estat' => 'required|string',
                'imatge' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
                'usuari_id' => 'nullable|nullable|integer|exists:usuaris,id',
                'protectora_id' => 'nullable|nullable|integer|exists:protectores,id',
                'publicacio_id' => 'nullable|integer|exists:publicacions,id',
                'latitud' => 'required|string',
                'longitud' => 'required|string',
                'nombre' => 'required|string|max:255',
            ]);

            // Crear el animal
            $animal = new Animal;
            $animal->nom = $validatedData['nom'];
            $animal->edat = $validatedData['edat'];
            $animal->especie = $validatedData['especie'];
            $animal->raça = $validatedData['raça'];
            $animal->descripcio = $validatedData['descripcio'] ?? null;
            $animal->estat = $validatedData['estat'];
            $animal->usuari_id = $validatedData['usuari_id'] ?? null;
            $animal->protectora_id = $validatedData['protectora_id'] ?? null;
            $animal->publicacio_id = $validatedData['publicacio_id'] ?? null;

            if ($request->file('imatge')) {
                $file = $request->file('imatge');
                $extension = $file->getClientOriginalExtension();
                $filename = strtolower($animal->nom . '_' . $animal->raça . '_' . uniqid() . '.' . $extension);
                $ruta = $request->file('imatge')->storeAs('uploads/imatges', $filename, 'public');
                $animal->imatge = $ruta;
            }

            $animal->save();

            // Crear la geolocalización y asociarla al animal
            $geolocalizacion = new Geolocalitzacio([
                'latitud' => $validatedData['latitud'],
                'longitud' => $validatedData['longitud'],
                'nombre' => $validatedData['nombre'],
            ]);
            $geolocalizacion->save();

            // Asociar el ID de la geolocalización al animal
            $animal->geolocalitzacio_id = $geolocalizacion->id;
            $animal->save();

            return response()->json($animal, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear el animal: ' . $e->getMessage()], 500);
        }
    }
    
    public function updateAnimal(Request $request, $id)
    {
        $animal = Animal::find($id);
        
        if (!$animal) {
            return response()->json(['error' => 'Animal no encontrado'], 404);
        }
        
        $validatedData = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'edat' => 'sometimes|required|integer',
            'especie' => 'sometimes|required|string|max:255',
            'raça' => 'sometimes|required|string|max:255',
            'descripcio' => 'nullable|string',
            'ubicacio' => 'nullable|string',
            'estat' => 'sometimes|required|string',
            'imatge' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
            'usuari_id' => 'nullable|integer|exists:usuaris,id',
            'protectora_id' => 'nullable|integer|exists:protectores,id',
            'publicacio_id' => 'nullable|integer|exists:publicacions,id',
        ]);
        
        // Actualizar campos básicos
        if (isset($validatedData['nom'])) $animal->nom = $validatedData['nom'];
        if (isset($validatedData['edat'])) $animal->edat = $validatedData['edat'];
        if (isset($validatedData['especie'])) $animal->especie = $validatedData['especie'];
        if (isset($validatedData['raça'])) $animal->raça = $validatedData['raça'];
        if (isset($validatedData['descripcio'])) $animal->descripcio = $validatedData['descripcio'];
        if (isset($validatedData['ubicacio'])) $animal->ubicacio = $validatedData['ubicacio'];
        if (isset($validatedData['estat'])) $animal->estat = $validatedData['estat'];
        if (isset($validatedData['publicacio_id'])) $animal->publicacio_id = $validatedData['publicacio_id'];
        
        // Actualizar el propietario (protectora o usuario)
        if (isset($validatedData['protectora_id'])) {
            $animal->protectora_id = $validatedData['protectora_id'];
            // Si se establece una protectora, eliminar la referencia al usuario
            $animal->usuari_id = null;
        }
        
        if (isset($validatedData['usuari_id'])) {
            $animal->usuari_id = $validatedData['usuari_id'];
            // Si se establece un usuario, eliminar la referencia a la protectora
            $animal->protectora_id = null;
        }
        
        if ($request->file('imatge')) {
            $file = $request->file('imatge');
            $extension = $file->getClientOriginalExtension();
            $filename = strtolower($animal->nom . '_' . $animal->raça . '_' . uniqid() . '.' . $extension);
            $ruta = $request->file('imatge')->storeAs('uploads/imatges', $filename, 'public');
            $animal->imatge = $ruta;
        }

        $animal->save();
        
        // Actualizar geolocalización si se proporcionan los datos
        if ($request->has('latitud') && $request->has('longitud')) {
            $geolocalizacion = Geolocalitzacio::firstOrNew(['animal_id' => $animal->id]);
            $geolocalizacion->latitud = $request->input('latitud');
            $geolocalizacion->longitud = $request->input('longitud');
            if ($request->has('nombre')) {
                $geolocalizacion->nombre = $request->input('nombre');
            }
            $geolocalizacion->save();
        }

        return response()->json($animal);
    }
    
    public function deleteAnimal($id)
    {
        $animal = Animal::find($id);
        
        if (!$animal) {
            return response()->json(['error' => 'Animal no encontrado'], 404);
        }
        
        // Eliminar geolocalización asociada si existe
        if ($animal->geolocalitzacio) {
            $animal->geolocalitzacio->delete();
        }
        
        $animal->delete();
        
        return response()->json(['message' => 'Animal eliminado correctamente']);
    }
    
    public function getAnimalImatge($id)
    {
        $animal = Animal::find($id);
        
        if (!$animal || !$animal->imatge) {
            return response()->json(['error' => 'Imagen no encontrada'], 404);
        }
        
        $path = storage_path('app/public/' . $animal->imatge);
        
        if (file_exists($path)) {
            $headers = ['Content-Type' => 'image/jpeg'];
            return response()->file($path, $headers);
        }
        
        return response()->json(['error' => 'Imagen no encontrada'], 404);
    }

    public function getAnimalesByUsuario($userId)
{
    // Verificar si el usuario existe
    $usuari = Usuari::find($userId);
    
    if (!$usuari) {
        return response()->json(['error' => 'Usuario no encontrado'], 404);
    }
    
    // Obtener IDs de las publicaciones del usuario
    $publicacionesIds = Publicacio::where('usuari_id', $userId)->pluck('id')->toArray();
    
    // Obtener publicaciones con las que el usuario ha interactuado
    $interaccionesPublicacionesIds = Interaccio::where('usuari_id', $userId)
        ->pluck('publicacio_id')
        ->unique()
        ->toArray();
    
    // Combinar ambos arrays de IDs de publicaciones
    $allPublicacionesIds = array_unique(array_merge($publicacionesIds, $interaccionesPublicacionesIds));
    
    // Obtener animales de las publicaciones propias
    $animalesPropios = Animal::whereIn('publicacio_id', $allPublicacionesIds)
        ->orWhereIn('id', function($query) use ($allPublicacionesIds) {
            $query->select('animal_id')
                  ->from('publicacions')
                  ->whereIn('id', $allPublicacionesIds);
        })
        ->with(['protectora', 'geolocalitzacio', 'publicacio'])
        ->get();
    
    // Agregar URL de la imagen
    foreach ($animalesPropios as $animal) {
        $animal->imatge = url('/api/animal/imatge/' . $animal->id);

    }
    
    return response()->json($animalesPropios);
}

public function getAnimalesPropiosByUsuario($usuariId)
{
    // Verificar si el usuario existe
    $usuari = Usuari::find($usuariId);
    
    if (!$usuari) {
        return response()->json(['error' => 'Usuario no encontrado'], 404);
    }
    
    // Obtener los animales que pertenecen directamente al usuario
    $animales = Animal::with(['geolocalitzacio', 'publicacio'])
        ->where('usuari_id', $usuariId)
        ->get();
    
    // Agregar URL de la imagen y más información
    foreach ($animales as $animal) {
        $animal->imatge = url('/api/animal/imatge/' . $animal->id);

        $animal->propietario_tipo = 'usuario';
        $animal->propietario_nombre = $usuari->nom;
        
        // Añadir información de la publicación si existe
        if ($animal->publicacio) {
            $animal->tiene_publicacion = true;
            $animal->publicacion_tipo = $animal->publicacio->tipus;
            $animal->publicacion_fecha = $animal->publicacio->data;
        } else {
            $animal->tiene_publicacion = false;
        }
    }
    
    return response()->json($animales);
}
}