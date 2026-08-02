<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Super admin — perfil 'admin', acumula as permissões de coordenador e gestor
 * (pode criar E autorizar vagas). Ver User::podeCriarVagas()/podeAutorizarVagas().
 *
 * Credenciais: admin@fapeu.org.br / password
 */
class SuperAdminSeeder extends Seeder
{
    private const EMAIL = 'admin@fapeu.org.br';
    private const SENHA = 'password';

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => self::EMAIL],
            [
                'name'              => 'Patrícia Gomes Ribeiro',
                'password'          => Hash::make(self::SENHA),
                'perfil'            => 'admin',
                'cpf'               => '64712345896',
                'ativo'             => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command?->info('Super admin: ' . self::EMAIL . ' / ' . self::SENHA);
    }
}
