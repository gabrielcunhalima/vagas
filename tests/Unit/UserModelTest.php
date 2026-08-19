<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'perfil' => 'coordenador',
            'ativo' => true,
        ], $attrs));
    }

    public function test_coordenador_identificado_corretamente(): void
    {
        $user = $this->makeUser(['perfil' => 'coordenador']);
        $this->assertTrue($user->isCoordenador());
        $this->assertFalse($user->isGestor());
        $this->assertFalse($user->isAdmin());
    }

    public function test_gestor_identificado_corretamente(): void
    {
        $user = $this->makeUser(['perfil' => 'gestor']);
        $this->assertFalse($user->isCoordenador());
        $this->assertTrue($user->isGestor());
        $this->assertFalse($user->isAdmin());
    }

    public function test_admin_identificado_corretamente(): void
    {
        $user = $this->makeUser(['perfil' => 'admin']);
        $this->assertFalse($user->isCoordenador());
        $this->assertFalse($user->isGestor());
        $this->assertTrue($user->isAdmin());
    }

    public function test_pode_autorizar_vagas_gestor(): void
    {
        $gestor = $this->makeUser(['perfil' => 'gestor']);
        $this->assertTrue($gestor->podeAutorizarVagas());
    }

    public function test_pode_autorizar_vagas_admin(): void
    {
        $admin = $this->makeUser(['perfil' => 'admin']);
        $this->assertTrue($admin->podeAutorizarVagas());
    }

    public function test_coordenador_nao_pode_autorizar_vagas(): void
    {
        $coord = $this->makeUser(['perfil' => 'coordenador']);
        $this->assertFalse($coord->podeAutorizarVagas());
    }

    public function test_pode_criar_vagas_coordenador(): void
    {
        $coord = $this->makeUser(['perfil' => 'coordenador']);
        $this->assertTrue($coord->podeCriarVagas());
    }

    public function test_pode_criar_vagas_admin(): void
    {
        $admin = $this->makeUser(['perfil' => 'admin']);
        $this->assertTrue($admin->podeCriarVagas());
    }

    public function test_gestor_nao_pode_criar_vagas(): void
    {
        $gestor = $this->makeUser(['perfil' => 'gestor']);
        $this->assertFalse($gestor->podeCriarVagas());
    }

    public function test_campo_ativo_cast_boolean(): void
    {
        $user = $this->makeUser(['ativo' => true]);
        $this->assertIsBool($user->ativo);
        $this->assertTrue($user->ativo);

        $inativo = $this->makeUser(['ativo' => false]);
        $this->assertIsBool($inativo->ativo);
        $this->assertFalse($inativo->ativo);
    }

    public function test_relacionamento_vagas_criadas(): void
    {
        $coord = $this->makeUser(['perfil' => 'coordenador']);
        $this->assertInstanceOf(HasMany::class, $coord->vagasCriadas());
    }

    public function test_relacionamento_vagas_autorizadas(): void
    {
        $gestor = $this->makeUser(['perfil' => 'gestor']);
        $this->assertInstanceOf(HasMany::class, $gestor->vagasAutorizadas());
    }
}
