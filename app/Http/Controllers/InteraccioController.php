<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Interaccio;
use App\Models\TipusInteraccio;
use App\Models\Publicacio;
use App\Models\Usuari;

class InteraccioController extends Controller
{
    /**
     * Obtener todas las interacciones
     */
    public function getInteraccions()
    {
        $interaccions = Interaccio::with(['usuari', 'publicacio', 'tipusInteraccio'])->get();
        
        foreach ($interaccions as $interaccio) {
            if ($interaccio->usuari) {
                $interaccio->usuari->makeHidden(['contrasenya']);
            }
        }
        
        return response()->json($interaccions);
    }
    
    /**
     * Obtener una interacción específica
     */
    public function getInteraccio($id)
    {
        $interaccio = Interaccio::with(['usuari', 'publicacio', 'tipusInteraccio'])->find($id);
        
        if (!$interaccio) {
            return response()->json(['error' => 'Interacción no encontrada'], 404);
        }
        
        if ($interaccio->usuari) {
            $interaccio->usuari->makeHidden(['contrasenya']);
        }
        
        return response()->json($interaccio);
    }
    
    /**
     * Obtener interacciones por publicación
     */
    public function getInteraccionsByPublicacio($publicacioId)
    {
        $publicacio = Publicacio::find($publicacioId);
        
        if (!$publicacio) {
            return response()->json(['error' => 'Publicación no encontrada'], 404);
        }
        
        $interaccions = Interaccio::with(['usuari', 'tipusInteraccio'])
            ->where('publicacio_id', $publicacioId)
            ->get();
            
        foreach ($interaccions as $interaccio) {
            if ($interaccio->usuari) {
                $interaccio->usuari->makeHidden(['contrasenya']);
            }
        }
        
        return response()->json($interaccions);
    }
    
    /**
     * Crear una nueva interacción
     */
    public function createInteraccio(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'usuari_id' => 'required|integer|exists:usuaris,id',
                'accio' => 'required|string',
                'tipus_interaccio_id' => 'required|integer|exists:tipus_interaccions,id',
                'publicacio_id' => 'required|integer|exists:publicacions,id',
                'data' => 'nullable|date',
                'detalls' => 'nullable|string'
            ]);
            
            // Si no se proporciona fecha, usar la actual
            if (!isset($validatedData['data'])) {
                $validatedData['data'] = now();
            }
            
            $interaccio = Interaccio::create($validatedData);
            
            return response()->json($interaccio, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear la interacción: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Actualizar una interacción existente
     */
    public function updateInteraccio(Request $request, $id)
    {
        $interaccio = Interaccio::find($id);
        
        if (!$interaccio) {
            return response()->json(['error' => 'Interacción no encontrada'], 404);
        }
        
        $validatedData = $request->validate([
            'accio' => 'sometimes|string',
            'tipus_interaccio_id' => 'sometimes|integer|exists:tipus_interaccions,id',
            'detalls' => 'nullable|string'
        ]);
        
        if (isset($validatedData['accio'])) $interaccio->accio = $validatedData['accio'];
        if (isset($validatedData['tipus_interaccio_id'])) $interaccio->tipus_interaccio_id = $validatedData['tipus_interaccio_id'];
        if (isset($validatedData['detalls'])) $interaccio->detalls = $validatedData['detalls'];
        
        $interaccio->save();
        
        return response()->json($interaccio);
    }
    
    /**
     * Eliminar una interacción
     */
    public function deleteInteraccio($id)
    {
        $interaccio = Interaccio::find($id);
        
        if (!$interaccio) {
            return response()->json(['error' => 'Interacción no encontrada'], 404);
        }
        
        $interaccio->delete();
        
        return response()->json(['message' => 'Interacción eliminada correctamente']);
    }
    
    /**
     * Obtener tipos de interacción disponibles
     */
    public function getTipusInteraccions()
    {
        $tipusInteraccions = TipusInteraccio::all();
        return response()->json($tipusInteraccions);
    }
}