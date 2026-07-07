<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $table = 'school';

    protected $primaryKey = 'id_school';

    protected $fillable = [
        'nombre',
        'direccion',
        'email',
        'foto',
        'latitud',
        'longitud',
        'id_user',
    ];

    protected function casts(): array
    {
        return [
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_user', 'id_user');
    }

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_school', 'id_school');
    }
}
