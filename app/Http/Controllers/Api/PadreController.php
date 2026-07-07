<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Padre;
use Illuminate\Http\Request;

class PadreController extends Controller
{
    public function index()
    {
        return response()->json(Padre::orderBy('nombre')->get());
    }

    public function show(Padre $padre)
    {
        return response()->json($padre->load('alumnos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
        ]);

        $padre = Padre::create($data);

        return response()->json($padre, 201);
    }

    public function update(Request $request, Padre $padre)
    {
        $data = $request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
        ]);

        $padre->update($data);

        return response()->json($padre);
    }

    public function destroy(Padre $padre)
    {
        $padre->delete();

        return response()->json(['message' => 'Padre eliminado']);
    }
}
