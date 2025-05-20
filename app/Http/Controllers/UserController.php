<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuari;
use App\Models\Administrador;

class UserController extends Controller
{
    /**
     * Verifica si un usuario es administrador
     * 
     * @param int $id ID del usuario a verificar
     * @return \Illuminate\Http\JsonResponse
     */
    public function isAdmin($id)
    {
        // Verificar si la ID es válida
        if (!$id) {
            return response()->json([
                'error' => 'ID de usuario no proporcionada'
            ], 400);
        }
        
        // Verificar si el usuario existe
        $usuario = Usuari::find($id);
        if (!$usuario) {
            return response()->json([
                'error' => 'Usuario no encontrado'
            ], 404);
        }
        
        // Verificar si existe un registro en la tabla administradors
        $isAdmin = Administrador::where('usuari_id', $id)->exists();
        
        return response()->json([
            'isAdmin' => $isAdmin,
            'usuario_id' => $id,
            'nombre' => $usuario->nom
        ]);
    }

    public function getAllUsers()
{
    // Obtener solo los campos id y nom de todos los usuarios
    $usuarios = Usuari::select('id', 'nom', 'email')->get();
    
    return response()->json([
        'usuarios' => $usuarios
    ]);
}
}