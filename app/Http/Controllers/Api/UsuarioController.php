<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{

    public function index(Request $request)
    {
        if (! $request->user()->isAdministrador()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return response()->json(Usuario::orderBy('nombre')->get());
    }


    public function show(Request $request, Usuario $usuario)
    {
        if (! $request->user()->isAdministrador()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return response()->json($usuario);
    }

    /**
     * Crear un nuevo usuario (solo Administrador).
     */
    public function store(Request $request)
    {
        if (! $request->user()->isAdministrador()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:255'],
            'usuario' => ['required', 'string', 'max:255', 'unique:usuarios,usuario'],
            'password' => ['required', 'string', 'min:6'],
            'tipo' => ['required', 'in:Administrador,Usuario'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'usuario' => $request->usuario,
            'password' => Hash::make($request->password),
            'tipo' => $request->tipo,
        ]);

        return response()->json($usuario, 201);
    }


    public function update(Request $request, Usuario $usuario)
    {
        if (! $request->user()->isAdministrador()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $rules = [
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'usuario' => ['sometimes', 'required', 'string', 'max:255', 'unique:usuarios,usuario,' . $usuario->id_user . ',id_user'],
            'password' => ['sometimes', 'required', 'string', 'min:6'],
            'tipo' => ['sometimes', 'required', 'in:Administrador,Usuario'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->only(['nombre', 'usuario', 'tipo']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return response()->json($usuario);
    }


    public function destroy(Request $request, Usuario $usuario)
    {
        if (! $request->user()->isAdministrador()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($usuario->id_user === $request->user()->id_user) {
            return response()->json(['message' => 'No puedes eliminar tu propio usuario'], 403);
        }

        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado']);
    }


    public function getUsuariosSimples(Request $request)
    {
        if (! $request->user()->isAdministrador()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return response()->json(
            Usuario::where('tipo', 'Usuario')
                ->orderBy('nombre')
                ->get(['id_user', 'nombre', 'usuario'])
        );
    }
}