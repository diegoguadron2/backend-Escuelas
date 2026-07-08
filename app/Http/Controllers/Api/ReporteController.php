<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\School;
use Illuminate\Http\Request;

class ReporteController extends Controller
{

    public function escuelas(Request $request)
    {
        $usuario = $request->user();

        $query = School::with('usuario')->withCount('alumnos');

        if (! $usuario->isAdministrador()) {
            $query->where('id_user', $usuario->id_user);
        }

        return response()->json($query->orderBy('nombre')->get());
    }


    public function alumnos(Request $request)
    {
        $usuario = $request->user();

        $query = Alumno::with(['school', 'padres']);

        if (! $usuario->isAdministrador()) {
            $schoolIds = School::where('id_user', $usuario->id_user)->pluck('id_school');
            $query->whereIn('id_school', $schoolIds);
        }

        return response()->json($query->orderBy('nombre_completo')->get());
    }
}
