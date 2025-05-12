<?php
namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use App\Events\ChatMessageEvent;
use App\Models\Usuari;
use Illuminate\Http\Request;
use GetStream\StreamChat\Client;

class ChatController extends Controller
{
    public function createToken(Request $request)
    {
        $userId = $request->input('user_id');
        $usuari = Usuari::find($userId);
        if (!$usuari) {
            return response()->json(['error' => 'Usuari no trobat'], 404)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }

        $role = 'user';
        if ($usuari->es_admin) {
            $role = 'admin';
        } elseif ($usuari->es_protectora) {
            $role = 'protectora';
        }

        $payload = [
            'user_id' => (string) $userId,
            'role' => $role,
            'iat' => time(),
            'exp' => time() + (60 * 60)
        ];
        $secretKey = env('STREAM_API_SECRET');
        $token = JWT::encode($payload, $secretKey, 'HS256');

        return response()->json(['token' => $token, 'role' => $role])
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    public function sendMessage(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:usuaris,id',
            'message' => 'required|string',
        ]);

        $userId = $validatedData['user_id'];
        $message = $validatedData['message'];

        $usuari = Usuari::find($userId);
        if (!$usuari) {
            return response()->json(['error' => 'Usuari no trobat'], 404)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }

        broadcast(new ChatMessageEvent($usuari, $message))->toOthers();

        return response()->json(['status' => 'Missatge enviat'])
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    public function searchUsers(Request $request)
    {
        try {
            $query = $request->input('query');
            if (!$query) {
                throw new \InvalidArgumentException('El paràmetre "query" és obligatori.');
            }

            $users = Usuari::where('nom', 'like', "%$query%")->get();
            if ($users->isEmpty()) {
                return response()->json(['error' => 'No s\'han trobat usuaris amb el noma proporcionat.'], 404);
            }

            return response()->json($users)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Error en la consulta a la base de dades: ' . $e->getMessage()], 500)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error intern del servidor: ' . $e->getMessage()], 500)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }
    }

    public function upsertUser(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|string',
            'name' => 'required|string',
        ]);

        try {
            $client = new Client(env('STREAM_API_KEY'), env('STREAM_API_SECRET'));

            $client->upsertUser([
                'id' => $validatedData['id'],
                'name' => $validatedData['name'],
            ]);

            return response()->json(['message' => 'Usuari sincronitzat correctament.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al sincronitzar l\'usuari: ' . $e->getMessage()], 500);
        }
    }
}