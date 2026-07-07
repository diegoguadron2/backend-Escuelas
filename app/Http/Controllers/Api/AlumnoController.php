<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\School;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();

        $query = Alumno::with('padres');

        if (! $usuario->isAdministrador()) {
            $schoolIds = School::where('id_user', $usuario->id_user)->pluck('id_school');
            $query->whereIn('id_school', $schoolIds);
        }

        return response()->json($query->orderBy('nombre_completo')->get());
    }

    public function show(Request $request, Alumno $alumno)
    {
        $this->authorizeAlumno($request, $alumno);

        return response()->json($alumno->load('padres'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $this->authorizeSchoolId($request, (int) $data['id_school']);

        $alumno = Alumno::create(collect($data)->except('padres')->toArray());

        $this->syncPadres($alumno, $data['padres'] ?? null);

        return response()->json($alumno->load('padres'), 201);
    }

    public function update(Request $request, Alumno $alumno)
    {
        $this->authorizeAlumno($request, $alumno);

        $data = $this->validated($request, sometimes: true);

        if (isset($data['id_school'])) {
            $this->authorizeSchoolId($request, (int) $data['id_school']);
        }

        $alumno->update(collect($data)->except('padres')->toArray());

        if (array_key_exists('padres', $data)) {
            $this->syncPadres($alumno, $data['padres']);
        }

        return response()->json($alumno->load('padres'));
    }

    public function destroy(Request $request, Alumno $alumno)
    {
        $this->authorizeAlumno($request, $alumno);

        $alumno->delete();

        return response()->json(['message' => 'Alumno eliminado']);
    }

    private function validated(Request $request, bool $sometimes = false): array
    {
        $req = fn (string $rule) => $sometimes ? ['sometimes', 'required', $rule] : ['required', $rule];

        return $request->validate([
            'nombre_completo' => $sometimes ? ['sometimes', 'required', 'string', 'max:255'] : ['required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'foto' => ['nullable', 'string'],
            'genero' => ['nullable', 'in:M,F'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'id_grado' => ['nullable', 'integer'],
            'id_seccion' => ['nullable', 'integer'],
            'id_school' => $sometimes ? ['sometimes', 'required', 'exists:school,id_school'] : ['required', 'exists:school,id_school'],
            'padres' => ['nullable', 'array'],
            'padres.*.id_padre' => ['required_with:padres', 'exists:padres,id_padre'],
            'padres.*.parentesco' => ['nullable', 'string', 'max:100'],
        ]);
    }

    private function syncPadres(Alumno $alumno, ?array $padres): void
    {
        $sync = collect($padres ?? [])->mapWithKeys(fn ($p) => [
            $p['id_padre'] => ['parentesco' => $p['parentesco'] ?? null],
        ]);

        $alumno->padres()->sync($sync);
    }

    private function authorizeAlumno(Request $request, Alumno $alumno): void
    {
        $usuario = $request->user();

        if ($usuario->isAdministrador()) {
            return;
        }

        $ownsSchool = School::where('id_school', $alumno->id_school)
            ->where('id_user', $usuario->id_user)
            ->exists();

        abort_unless($ownsSchool, 403, 'No autorizado');
    }

    private function authorizeSchoolId(Request $request, int $idSchool): void
    {
        $usuario = $request->user();

        if ($usuario->isAdministrador()) {
            return;
        }

        $ownsSchool = School::where('id_school', $idSchool)
            ->where('id_user', $usuario->id_user)
            ->exists();

        abort_unless($ownsSchool, 403, 'No autorizado');
    }

    
}
