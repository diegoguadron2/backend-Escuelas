<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $usuario = Usuario::where('usuario', $request->usuario)->first();

        if (! $usuario || ! Hash::check($request->password, $usuario->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $usuario->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'usuario' => $usuario,
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada']);
    }


    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
