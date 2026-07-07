<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PadreAlumno extends Pivot
{
    protected $table = 'padres_alumnos';

    protected $primaryKey = 'id_padre_alumno';

    public $incrementing = true;

    protected $fillable = [
        'id_alumno',
        'id_padre',
        'parentesco',
    ];
}
