<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Coordenador — cria vagas e as envia para autorização.
 * Gestor e admin têm seeders próprios: GestorSeeder e SuperAdminSeeder.
 *
 * Credenciais: coordenador@fapeu.org.br / password
 */
class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'coordenador@fapeu.org.br'],
            [
                'name' => 'Coordenador Teste',
                'password' => Hash::make('password'),
                'perfil' => 'coordenador',
                'ativo' => true,
            ]
        );
    }
}
