<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Gestor — perfil que autoriza (confirma) ou recusa as vagas criadas pelos
 * coordenadores. Ver User::podeAutorizarVagas().
 *
 * Credenciais: gestor@fapeu.org.br / password
 */
class GestorSeeder extends Seeder
{
    private const EMAIL = 'gestor@fapeu.org.br';
    private const SENHA = 'password';

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => self::EMAIL],
            [
                'name'              => 'Roberto Nunes Machado',
                'password'          => Hash::make(self::SENHA),
                'perfil'            => 'gestor',
                'cpf'               => '82715684770',
                'ativo'             => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command?->info('Gestor (autoriza vagas): ' . self::EMAIL . ' / ' . self::SENHA);
    }
}
