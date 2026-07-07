<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::updateOrCreate(
            ['usuario' => 'admin'],
            [
                'nombre' => 'Administrador General',
                'password' => Hash::make('password123'),
                'tipo' => 'Administrador',
            ]
        );
    }
}
