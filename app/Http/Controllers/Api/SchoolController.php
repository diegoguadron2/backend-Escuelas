<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();

        $query = School::query();

        if (! $usuario->isAdministrador()) {
            $query->where('id_user', $usuario->id_user);
        }

        return response()->json($query->orderBy('nombre')->get());
    }

    public function show(Request $request, School $school)
    {
        $this->authorizeSchool($request, $school);

        return response()->json($school);
    }

    public function store(Request $request)
    {
        if (! $request->user()->isAdministrador()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'foto' => ['nullable', 'string'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'id_user' => ['required', 'exists:usuarios,id_user'],
        ]);

        $school = School::create($data);

        return response()->json($school, 201);
    }

    public function update(Request $request, School $school)
    {
        $this->authorizeSchool($request, $school);

        $data = $request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'foto' => ['nullable', 'string'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'id_user' => ['sometimes', 'required', 'exists:usuarios,id_user'],
        ]);

        if (isset($data['id_user']) && ! $request->user()->isAdministrador()) {
            unset($data['id_user']);
        }

        $school->update($data);

        return response()->json($school);
    }

    public function destroy(Request $request, School $school)
    {
        if (! $request->user()->isAdministrador()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $school->delete();

        return response()->json(['message' => 'Escuela eliminada']);
    }

    private function authorizeSchool(Request $request, School $school): void
    {
        $usuario = $request->user();

        if (! $usuario->isAdministrador() && (int) $school->id_user !== (int) $usuario->id_user) {
            abort(403, 'No autorizado');
        }
    }
}
