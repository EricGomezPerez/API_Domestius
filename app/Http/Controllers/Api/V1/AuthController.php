<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\RegisterRequest;
use App\Models\User;
use App\Models\Usuari;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use GetStream\StreamChat\Client;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->validated())) {
            return response()->json([
                'errors' => 'Credenciales incorrectas.'
            ], Response::HTTP_UNAUTHORIZED);
        }
    
        $user = $request->user();
        $userToken = $user->createToken('AppToken')->plainTextToken;
    
        
        $client = new Client(env('STREAM_API_KEY'), env('STREAM_API_SECRET'));
        $client->upsertUser([
            'id' => (string) $user->id,
            'name' => $user->nom,
        ]);
    
        return response()->json([
            'message' => 'Se ha iniciado sesión correctamente.',
            'token' => $userToken,
            'user' => $user,
        ], Response::HTTP_OK);
    }
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = Usuari::create($request->validated());
    
        
        $client = new Client(env('STREAM_API_KEY'), env('STREAM_API_SECRET'));
        $client->upsertUser([
            'id' => (string) $user->id, 
            'name' => $user->nom, 
        ]);
    
        return response()->json([
            'message' => 'Usuario registrado exitosamente.',
            'id' => $user->id,
        ], Response::HTTP_CREATED);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Se ha cerrado sesión correctamente.'
        ], Response::HTTP_OK);
    }
    public function sendPasswordResetLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:usuaris,email',
        ]);
    
        
        $token = Str::random(60);
    
        
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );
    
        
        return response()->json([
            'message' => 'Token generado con éxito.',
            'token' => $token, 
        ], Response::HTTP_OK);
    }
public function resetPassword(Request $request): JsonResponse
{
    $request->validate([
        'email' => 'required|email|exists:usuaris,email',
        'token' => 'required',
        'password' => 'required|confirmed|min:6',
    ]);

 
    $resetRecord = DB::table('password_resets')
        ->where('email', $request->email)
        ->where('token', $request->token)
        ->first();

    if (!$resetRecord) {
        return response()->json([
            'message' => 'Token inválido o expirado.',
        ], Response::HTTP_BAD_REQUEST);
    }

    $user = Usuari::where('email', $request->email)->first();
    $user->password = Hash::make($request->password);
    $user->save();


    DB::table('password_resets')->where('email', $request->email)->delete();

    return response()->json([
        'message' => 'Contraseña restablecida con éxito.',
    ], Response::HTTP_OK);
}
}
