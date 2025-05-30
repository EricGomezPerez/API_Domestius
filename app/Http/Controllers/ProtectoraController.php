<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Protectora;
use Illuminate\Support\Facades\Hash;
use App\Models\Animal;

class ProtectoraController extends Controller
{
    /**
     * Obtener todas las protectoras (sin incluir contraseñas)
     */
    public function getProtectoras()
    {
        $protectoras = Protectora::with(['usuari'])->get();
        
        
        foreach ($protectoras as $protectora) {
            // Excluir el campo 'password' de la respuesta
            $protectora->makeHidden(['password']);
            // Añadimos datos del usuario si existe
            if ($protectora->usuari) {
                $protectora->username = $protectora->usuari->nom;
                $protectora->user_email = $protectora->usuari->email;
                $protectora->user_id = $protectora->usuari->id;
                
                // Excluir el campo 'password' del usuario
                $protectora->usuari->makeHidden(['contrasenya']);
            }
            // Retornar la URL de la imagen
            if ($protectora->imatge) {
                $protectora->imatge = url('/api/protectora/imatge/' . $protectora->id);
            }
        }
        
        return response()->json($protectoras);
    }

    /**
     * Obtener una protectora específica por ID
     */
    public function getProtectora($id)
    {
        $protectora = Protectora::with('usuari')->find($id);

        
        if (!$protectora) {
            return response()->json(['error' => 'Protectora no encontrada'], 404);
        }
        
        $protectora->makeHidden(['password']);
        
        if ($protectora->imatge) {
            $protectora->imatge = url('/api/protectora/imatge/' . $protectora->id);
        }
        
        if ($protectora->user) {
            $protectora->username = $protectora->user->name;
            $protectora->user_email = $protectora->user->email;
            $protectora->user_id = $protectora->user->id;
            
            $protectora->user->makeHidden(['password']);
        }
        
        return response()->json($protectora);
    }

    /**
 * Crear una nueva protectora
 */
public function createProtectora(Request $request)
{
    try {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'usuari_id' => 'required|exists:usuaris,id', // Validar que el usuario exista
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'imatge' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
            'horario_apertura' => 'nullable|date_format:H:i',
            'horario_cierre' => 'nullable|date_format:H:i',
        ]);

        // Crear la protectora
        $protectora = new Protectora();
        $protectora->usuari_id = $validatedData['usuari_id']; // Asociar al usuario
        $protectora->direccion = $validatedData['direccion'];
        $protectora->telefono = $validatedData['telefono'];
        $protectora->horario_apertura = $validatedData['horario_apertura'] ?? null;
        $protectora->horario_cierre = $validatedData['horario_cierre'] ?? null;

        // Procesar la imagen si existe
        if ($request->file('imatge')) {
            $file = $request->file('imatge');
            $extension = $file->getClientOriginalExtension();
            $filename = strtolower('protectora_' . uniqid() . '.' . $extension);
            $ruta = $request->file('imatge')->storeAs('uploads/imatges', $filename, 'public');
            $protectora->imatge = $ruta;
        }

        $protectora->save();

        // Excluir el campo 'password' de la respuesta
        $protectora->makeHidden(['password']);

        // Retornar la URL de la imagen
        if ($protectora->imatge) {
            $protectora->imatge = url('/api/protectora/imatge/' . $protectora->id);
        }

        return response()->json($protectora, 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Capturar errores de validación
        return response()->json([
            'error' => 'Error de validación',
            'detalles' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        // Capturar cualquier otro error
        return response()->json([
            'error' => 'Error al crear la protectora',
            'mensaje' => $e->getMessage()
        ], 500);
    }
}
/**
 * Actualizar una protectora existente
 */
public function updateProtectora(Request $request, $id)
{
    $protectora = Protectora::find($id);
    
    if (!$protectora) {
        return response()->json(['error' => 'Protectora no encontrada'], 404);
    }

    $validatedData = $request->validate([
        'nombre' => 'sometimes|string|max:255',
        'email' => 'sometimes|string|email|max:255|unique:users,email,'.$protectora->id,
        'direccion' => 'sometimes|string|max:255',
        'telefono' => 'sometimes|string|max:20',
        'imatge' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
        'horario_apertura' => 'nullable|date_format:H:i',
        'horario_cierre' => 'nullable|date_format:H:i',
    ]);

    // Actualizar campos básicos
    if ($request->has('nombre')) $protectora->nombre = $validatedData['nombre'];
    if ($request->has('email')) $protectora->email = $validatedData['email'];
    if ($request->has('direccion')) $protectora->direccion = $validatedData['direccion'];
    if ($request->has('telefono')) $protectora->telefono = $validatedData['telefono'];
    if ($request->has('horario_apertura')) $protectora->horario_apertura = $validatedData['horario_apertura'];
    if ($request->has('horario_cierre')) $protectora->horario_cierre = $validatedData['horario_cierre'];
    
        // Procesar la imagen si existe
        if ($request->file('imatge')) {
            $file = $request->file('imatge');
            $extension = $file->getClientOriginalExtension();
            $filename = strtolower($protectora->nombre ?? 'protectora' . '_' . uniqid() . '.' . $extension);
            $ruta = $request->file('imatge')->storeAs('uploads/imatges', $filename, 'public');
            $protectora->imatge = $ruta;
        }
        
        $protectora->save();

        // Excluir el campo 'password' de la respuesta
        $protectora->makeHidden(['password']);
        
        // Retornar la URL de la imagen
        if ($protectora->imatge) {
            $protectora->imatge = url('/api/protectora/imatge/' . $protectora->id);
        }

        return response()->json($protectora);
    }

    /**
     * Eliminar una protectora
     */
    public function deleteProtectora($id)
    {
        $protectora = Protectora::find($id);
        
        if (!$protectora) {
            return response()->json(['error' => 'Protectora no encontrada'], 404);
        }

        // Eliminar imagen si existe
        if ($protectora->imatge) {
            $path = public_path(env('RUTA_IMATGES') . '/' . $protectora->imatge);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $protectora->delete();

        return response()->json(['message' => 'Protectora eliminada correctamente']);
    }
    
    /**
     * Obtener la imagen de una protectora
     */
    public function getProtectoraImatge($id)
{
    $protectora = Protectora::find($id);
    
    if (!$protectora || !$protectora->imatge) {
        return response()->json(['error' => 'Imagen no encontrada'], 404);
    }
    
    // Redirigir a la URL pública de la imagen
    $imageUrl = url('/storage/' . $protectora->imatge);
    return redirect($imageUrl);
}

    /**
 * Obtener protectora por ID de usuario
 */
public function getProtectoraByUsuario($usuarioId)
{
    $protectora = Protectora::where('usuari_id', $usuarioId)->first();
    
    if (!$protectora) {
        return response()->json(['error' => 'Protectora no encontrada para este usuario'], 404);
    }
    
    // Excluir el campo 'password' de la respuesta
    $protectora->makeHidden(['password']);
    
    // Retornar la URL de la imagen
    if ($protectora->imatge) {
        $protectora->imatge = url('/api/protectora/imatge/' . $protectora->id);
    }
    
    return response()->json($protectora);
}

public function getAnimalesByProtectora($protectoraId)
{
    // Verificar si la protectora existe
    $protectora = Protectora::find($protectoraId);
    
    if (!$protectora) {
        return response()->json(['error' => 'Protectora no encontrada'], 404);
    }
    
    // Obtener los animales de la protectora
    $animales = Animal::with(['geolocalitzacio', 'publicacio'])
        ->where('protectora_id', $protectoraId)
        ->get();
    
    // Añadir URL de la imagen
    foreach ($animales as $animal) {
        $animal->imatge = url('/api/animal/imatge/' . $animal->id);

    }
    
    return response()->json($animales);
}
}