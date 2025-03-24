<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Animal;
use App\Models\Protectora;

class ApiController extends Controller
{
    function getAnimales()
    {
        $animales = Animal::with('protectora')->get();

        foreach ($animales as $animal) {
            $animal->imatge = url( ('/api/animal/imatge/') . $animal->id);
        }

        return response()->json($animales);
    }

    function updateAnimal(Request $request, $id)
    {
        $animal = Animal::find($id);
        

        if ($request->file('imatge')) {
            $file = $request->file('imatge');
            $extension = $file->getClientOriginalExtension();
            $filename = strtolower($animal->nombre . '_' . $animal->raza . '_' . uniqid() . '.' . $extension);
            $file->move(public_path(env('RUTA_IMATGES')), $filename);
            $animal->imatge = $filename;
        }

        $animal->save();

        return $animal;
    }

    function updateProtectora(Request $request, $id)
    {
        $protectora = Protectora::find($id);
        $protectora->update($request->all());

        return $protectora;
    }

    public function getAnimal($id)
    {
        return Animal::find($id);
    }

    public function getProtectoras()
    {
        $protectoras = Protectora::all();
        return response()->json($protectoras);
    }

    public function getProtectora($id)
    {
        return Protectora::find($id);
    }

    public function createAnimal(Request $request)
    {

        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'raza' => 'required|string|max:255',
            'imatge' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
            'protectora_id' => 'required|integer',
        ]);

        $animal = new Animal;
        $animal->nombre = $validatedData['nombre'];
        $animal->raza = $validatedData['raza'];
        $animal->protectora_id = $validatedData['protectora_id'];
    
        if ($request->file('imatge')) {
            $file = $request->file('imatge');
            $extension = $file->getClientOriginalExtension();
            $filename = strtolower($animal->nombre . '_' . $animal->raza . '_' . uniqid() . '.' . $extension);
            $file->move(public_path(env('RUTA_IMATGES')), $filename);
            $animal->imatge = $filename;
        }
    
        $animal->save();
    
        return $animal;

    }

    public function createProtectora(Request $request)
{
    $validatedData = $request->validate([
        'nombre' => 'required|string|max:255',
        'direccion' => 'required|string|max:255',
        'telefono' => 'required|string|max:20',
    ]);

    $protectora = Protectora::create($validatedData);

    return response()->json($protectora);
}

    public function deleteAnimal($id)
    {
        $animal = Animal::find($id);
        $animal->delete();

        return response()->json('Animal deleted successfully');
    }

    public function deleteProtectora($id)
    {
        $protectora = Protectora::find($id);
        $protectora->delete();

        return response()->json('Protectora deleted successfully');
    }

    public function getAnimalImatge($id)
    {
        $animal = Animal::find($id);
    
        if (!$animal || !$animal->imatge) {
            return response()->json(['error' => 'Imatge no trobada'], 404);
        }
    
        $pathToFile = public_path(env('RUTA_IMATGES') . '/' . $animal->imatge);
        $headers = ['Content-Type' => 'image/jpeg'];
    
        return response()->file($pathToFile, $headers);
    }


}
