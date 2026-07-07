<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'usuarios';

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nombre',
        'usuario',
        'password',
        'tipo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function schools()
    {
        return $this->hasMany(School::class, 'id_user', 'id_user');
    }

    public function isAdministrador(): bool
    {
        return $this->tipo === 'Administrador';
    }
}
