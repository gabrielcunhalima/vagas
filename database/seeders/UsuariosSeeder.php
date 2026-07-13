<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'coordenador@fapeu.org.br'],
            [
                'name'     => 'Coordenador Teste',
                'password' => Hash::make('password'),
                'perfil'   => 'coordenador',
                'ativo'    => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'gestor@fapeu.org.br'],
            [
                'name'     => 'Gestor Teste',
                'password' => Hash::make('password'),
                'perfil'   => 'gestor',
                'ativo'    => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@fapeu.org.br'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('password'),
                'perfil'   => 'admin',
                'ativo'    => true,
            ]
        );
    }
}
