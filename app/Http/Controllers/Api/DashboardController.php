<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Alumno;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $usuario = $request->user();

        // Obtener la escuela del usuario o todas si es Admin
        if ($usuario->isAdministrador()) {
  
            $escuelas = School::with(['alumnos' => function($query) {
                $query->select('id_alumno', 'nombre_completo', 'latitud', 'longitud', 'foto', 'id_school');
            }])
            ->select('id_school', 'nombre', 'direccion', 'latitud', 'longitud', 'foto')
            ->get();

            $dashboardData = $escuelas->map(function($escuela) {
                return [
                    'escuela' => [
                        'id' => $escuela->id_school,
                        'nombre' => $escuela->nombre,
                        'direccion' => $escuela->direccion,
                        'latitud' => $escuela->latitud,
                        'longitud' => $escuela->longitud,
                        'foto' => $escuela->foto,
                        'tipo' => 'escuela', 
                    ],
                    'alumnos' => $escuela->alumnos->map(function($alumno) {
                        return [
                            'id' => $alumno->id_alumno,
                            'nombre' => $alumno->nombre_completo,
                            'latitud' => $alumno->latitud,
                            'longitud' => $alumno->longitud,
                            'foto' => $alumno->foto,
                            'tipo' => 'alumno', 
                        ];
                    }),
                    'total_alumnos' => $escuela->alumnos->count()
                ];
            });

            return response()->json([
                'data' => $dashboardData,
                'total_escuelas' => $escuelas->count(),
                'total_alumnos' => $escuelas->sum('alumnos.count'),
            ]);
        }

        // Usuario normal: solo su escuela
        $escuela = School::with(['alumnos' => function($query) {
            $query->select('id_alumno', 'nombre_completo', 'latitud', 'longitud', 'foto', 'id_school');
        }])
        ->where('id_user', $usuario->id_user)
        ->select('id_school', 'nombre', 'direccion', 'latitud', 'longitud', 'foto')
        ->first();

        if (!$escuela) {
            return response()->json([
                'message' => 'El usuario no tiene una escuela asignada',
            ], 404);
        }

        return response()->json([
            'escuela' => [
                'id' => $escuela->id_school,
                'nombre' => $escuela->nombre,
                'direccion' => $escuela->direccion,
                'latitud' => $escuela->latitud,
                'longitud' => $escuela->longitud,
                'foto' => $escuela->foto,
                'tipo' => 'escuela',
            ],
            'alumnos' => $escuela->alumnos->map(function($alumno) {
                return [
                    'id' => $alumno->id_alumno,
                    'nombre' => $alumno->nombre_completo,
                    'latitud' => $alumno->latitud,
                    'longitud' => $alumno->longitud,
                    'foto' => $alumno->foto,
                    'tipo' => 'alumno',
                ];
            }),
            'total_alumnos' => $escuela->alumnos->count(),
        ]);
    }


    public function markers(Request $request)
    {
        $usuario = $request->user();

        $data = [
            'escuelas' => [],
            'alumnos' => [],
        ];

        // Obtener escuelas
        if ($usuario->isAdministrador()) {
            $escuelas = School::select('id_school', 'nombre', 'latitud', 'longitud', 'foto')
                ->whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->get();

            $data['escuelas'] = $escuelas->map(function($escuela) {
                return [
                    'id' => $escuela->id_school,
                    'nombre' => $escuela->nombre,
                    'latitud' => (float) $escuela->latitud,
                    'longitud' => (float) $escuela->longitud,
                    'foto' => $escuela->foto,
                ];
            });

            // Todos los alumnos
            $alumnos = Alumno::select('id_alumno', 'nombre_completo', 'latitud', 'longitud', 'foto', 'id_school')
                ->whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->get();

            $data['alumnos'] = $alumnos->map(function($alumno) {
                return [
                    'id' => $alumno->id_alumno,
                    'nombre' => $alumno->nombre_completo,
                    'latitud' => (float) $alumno->latitud,
                    'longitud' => (float) $alumno->longitud,
                    'foto' => $alumno->foto,
                    'id_school' => $alumno->id_school,
                ];
            });
        } else {

            $escuela = School::select('id_school', 'nombre', 'latitud', 'longitud', 'foto')
                ->where('id_user', $usuario->id_user)
                ->whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->first();

            if ($escuela) {
                $data['escuelas'] = [
                    [
                        'id' => $escuela->id_school,
                        'nombre' => $escuela->nombre,
                        'latitud' => (float) $escuela->latitud,
                        'longitud' => (float) $escuela->longitud,
                        'foto' => $escuela->foto,
                    ]
                ];

                $alumnos = Alumno::select('id_alumno', 'nombre_completo', 'latitud', 'longitud', 'foto', 'id_school')
                    ->where('id_school', $escuela->id_school)
                    ->whereNotNull('latitud')
                    ->whereNotNull('longitud')
                    ->get();

                $data['alumnos'] = $alumnos->map(function($alumno) {
                    return [
                        'id' => $alumno->id_alumno,
                        'nombre' => $alumno->nombre_completo,
                        'latitud' => (float) $alumno->latitud,
                        'longitud' => (float) $alumno->longitud,
                        'foto' => $alumno->foto,
                        'id_school' => $alumno->id_school,
                    ];
                });
            }
        }

        return response()->json($data);
    }
}