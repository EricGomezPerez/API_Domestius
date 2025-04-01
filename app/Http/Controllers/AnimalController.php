<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Animal;
use App\Models\Protectora;
use App\Models\Geolocalitzacio;
use App\Models\Publicacio;

class AnimalController extends Controller
{
    public function getAnimales()
{
    $animales = Animal::with(['protectora', 'geolocalitzacio', 'publicacio'])->get();

    foreach ($animales as $animal) {
        $animal->imatge = url('/api/animal/imatge/' . $animal->id);
    }

    return response()->json($animales);
}

    public function getAnimal($id)
    {
        $animal = Animal::with(['protectora', 'geolocalitzacio', 'publicacio'])->find($id);
                
        if (!$animal) {
            return response()->json(['error' => 'Animal no encontrado'], 404);
        }
        
        $animal->imatge = url('/api/animal/imatge/' . $animal->id);
        return response()->json($animal);
    }
    
    public function createAnimal(Request $request)
    {
        try{
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'edat' => 'required|integer',
            'especie' => 'required|string|max:255',
            'raça' => 'required|string|max:255',
            'descripcio' => 'nullable|string',
            'estat' => 'required|string',
            'imatge' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
            'protectora_id' => 'required|integer|exists:protectores,id',
            'publicacio_id' => 'nullable|integer|exists:publicacions,id',
            'geolocalitzacio_id' => 'nullable|integer|exists:geolocalitzacions,id',
        ]);

        $animal = new Animal;
        $animal->nom = $validatedData['nom'];
        $animal->edat = $validatedData['edat'];
        $animal->especie = $validatedData['especie'];
        $animal->raça = $validatedData['raça'];
        $animal->descripcio = $validatedData['descripcio'] ?? null;
        $animal->estat = $validatedData['estat'];
        $animal->protectora_id = $validatedData['protectora_id'];
        $animal->publicacio_id = $validatedData['publicacio_id'] ?? null;
        $animal->geolocalitzacio_id = $validatedData['geolocalitzacio_id'] ?? null;
        
        if ($request->file('imatge')) {
            $file = $request->file('imatge');
            $extension = $file->getClientOriginalExtension();
            $filename = strtolower($animal->nom . '_' . $animal->raça . '_' . uniqid() . '.' . $extension);
            $file->move(public_path(env('RUTA_IMATGES')), $filename);
            $animal->imatge = $filename;
        }
        
        $animal->save();
        
        // Si se proporcionan datos de geolocalización, guardarlos
        if ($request->has('latitud') && $request->has('longitud')) {
            $geolocalizacion = new Geolocalitzacio([
                'animal_id' => $animal->id,
                'latitud' => $request->input('latitud'),
                'longitud' => $request->input('longitud')
            ]);
            $geolocalizacion->save();
        }
        
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
            'protectora_id' => 'sometimes|required|integer|exists:protectoras,id',
            'publicacio_id' => 'nullable|integer|exists:publicacios,id',
        ]);
        
        if (isset($validatedData['nom'])) {
            $animal->nom = $validatedData['nom'];
        }
        
        if (isset($validatedData['edat'])) {
            $animal->edat = $validatedData['edat'];
        }
        
        if (isset($validatedData['especie'])) {
            $animal->especie = $validatedData['especie'];
        }
        
        if (isset($validatedData['raça'])) {
            $animal->raça = $validatedData['raça'];
        }
        
        if (isset($validatedData['descripcio'])) {
            $animal->descripcio = $validatedData['descripcio'];
        }
        
        if (isset($validatedData['ubicacio'])) {
            $animal->ubicacio = $validatedData['ubicacio'];
        }
        
        if (isset($validatedData['estat'])) {
            $animal->estat = $validatedData['estat'];
        }
        
        if (isset($validatedData['protectora_id'])) {
            $animal->protectora_id = $validatedData['protectora_id'];
        }
        
        if (isset($validatedData['publicacio_id'])) {
            $animal->publicacio_id = $validatedData['publicacio_id'];
        }
        
        if ($request->file('imatge')) {
            $file = $request->file('imatge');
            $extension = $file->getClientOriginalExtension();
            $filename = strtolower($animal->nom . '_' . $animal->raça . '_' . uniqid() . '.' . $extension);
            $file->move(public_path(env('RUTA_IMATGES')), $filename);
            $animal->imatge = $filename;
        }

        $animal->save();
        
        // Actualizar geolocalización si se proporcionan los datos
        if ($request->has('latitud') && $request->has('longitud')) {
            $geolocalizacion = Geolocalitzacio::firstOrNew(['animal_id' => $animal->id]);
            $geolocalizacion->latitud = $request->input('latitud');
            $geolocalizacion->longitud = $request->input('longitud');
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
        
        $path = public_path(env('RUTA_IMATGES') . '/' . $animal->imatge);
        
        if (file_exists($path)) {
            $headers = ['Content-Type' => 'image/jpeg'];
            return response()->file($path, $headers);
        }
        
        return response()->json(['error' => 'Imagen no encontrada'], 404);
    }
}