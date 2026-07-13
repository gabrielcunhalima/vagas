<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Vagas\AlertaVaga;
use App\Models\Vagas\Vaga;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AlertaVagaModelTest extends TestCase
{
    use RefreshDatabase;

    private function makeAlerta(array $attrs = []): AlertaVaga
    {
        return AlertaVaga::create(array_merge([
            'email'      => 'alerta@email.com',
            'areas'      => [],
            'modalidades'=> [],
            'tipos'      => [],
            'ativo'      => true,
        ], $attrs));
    }

    private function makeVaga(array $attrs = []): Vaga
    {
        $coord = User::factory()->create(['perfil' => 'coordenador', 'ativo' => true]);
        return Vaga::create(array_merge([
            'titulo'            => 'Vaga Teste Alerta',
            'descricao'         => 'Descrição da vaga para testes de alerta de vagas.',
            'requisitos'        => 'Requisitos mínimos da vaga de teste de alerta.',
            'tipo'              => 'estagio',
            'area'              => 'Tecnologia da Informação',
            'modalidade'        => 'presencial',
            'cidade'            => 'Florianópolis',
            'estado'            => 'SC',
            'pais'              => 'Brasil',
            'data_encerramento' => now()->addDays(30)->toDateString(),
            'status'            => 'ativa',
            'coordenador_id'    => $coord->id,
            'notificar_email'   => true,
        ], $attrs));
    }

    // ── Token gerado automaticamente ──────────────────────────────────────────

    public function test_token_gerado_ao_criar(): void
    {
        $alerta = $this->makeAlerta();
        $this->assertNotNull($alerta->token);
        $this->assertEquals(64, strlen($alerta->token));
    }

    public function test_tokens_unicos_por_alerta(): void
    {
        $a1 = $this->makeAlerta(['email' => 'a1@email.com']);
        $a2 = $this->makeAlerta(['email' => 'a2@email.com']);
        $this->assertNotEquals($a1->token, $a2->token);
    }

    // ── Scope ativos ──────────────────────────────────────────────────────────

    public function test_scope_ativos(): void
    {
        $this->makeAlerta(['email' => 'ativo@email.com', 'ativo' => true]);
        $this->makeAlerta(['email' => 'inativo@email.com', 'ativo' => false]);

        $ativos = AlertaVaga::ativos()->get();
        $this->assertCount(1, $ativos);
        $this->assertTrue($ativos->first()->ativo);
    }

    // ── compativel() ─────────────────────────────────────────────────────────

    public function test_alerta_sem_filtros_compativel_com_qualquer_vaga(): void
    {
        $alerta = $this->makeAlerta(['areas' => [], 'modalidades' => [], 'tipos' => []]);
        $vaga = $this->makeVaga(['area' => 'Administração', 'modalidade' => 'remoto', 'tipo' => 'emprego']);
        $this->assertTrue($alerta->compativel($vaga));
    }

    public function test_alerta_filtrado_por_area_compativel(): void
    {
        $alerta = $this->makeAlerta(['areas' => ['Tecnologia da Informação']]);
        $vaga = $this->makeVaga(['area' => 'Tecnologia da Informação']);
        $this->assertTrue($alerta->compativel($vaga));
    }

    public function test_alerta_filtrado_por_area_incompativel(): void
    {
        $alerta = $this->makeAlerta(['areas' => ['Administração']]);
        $vaga = $this->makeVaga(['area' => 'Tecnologia da Informação']);
        $this->assertFalse($alerta->compativel($vaga));
    }

    public function test_alerta_filtrado_por_modalidade_compativel(): void
    {
        $alerta = $this->makeAlerta(['modalidades' => ['remoto']]);
        $vaga = $this->makeVaga(['modalidade' => 'remoto']);
        $this->assertTrue($alerta->compativel($vaga));
    }

    public function test_alerta_filtrado_por_modalidade_incompativel(): void
    {
        $alerta = $this->makeAlerta(['modalidades' => ['remoto']]);
        $vaga = $this->makeVaga(['modalidade' => 'presencial']);
        $this->assertFalse($alerta->compativel($vaga));
    }

    public function test_alerta_filtrado_por_tipo_estagio_compativel(): void
    {
        $alerta = $this->makeAlerta(['tipos' => ['estagio']]);
        $vaga = $this->makeVaga(['tipo' => 'estagio']);
        $this->assertTrue($alerta->compativel($vaga));
    }

    public function test_alerta_filtrado_por_tipo_incompativel(): void
    {
        $alerta = $this->makeAlerta(['tipos' => ['bolsa']]);
        $vaga = $this->makeVaga(['tipo' => 'estagio']);
        $this->assertFalse($alerta->compativel($vaga));
    }

    public function test_alerta_multiplos_filtros_todos_compatíveis(): void
    {
        $alerta = $this->makeAlerta([
            'areas'      => ['Tecnologia da Informação'],
            'modalidades'=> ['remoto'],
            'tipos'      => ['estagio'],
        ]);
        $vaga = $this->makeVaga([
            'area'      => 'Tecnologia da Informação',
            'modalidade'=> 'remoto',
            'tipo'      => 'estagio',
        ]);
        $this->assertTrue($alerta->compativel($vaga));
    }

    public function test_alerta_multiplos_filtros_um_incompativel(): void
    {
        $alerta = $this->makeAlerta([
            'areas'      => ['Tecnologia da Informação'],
            'modalidades'=> ['remoto'],
            'tipos'      => ['estagio'],
        ]);
        $vaga = $this->makeVaga([
            'area'      => 'Tecnologia da Informação',
            'modalidade'=> 'presencial', // incompatível
            'tipo'      => 'estagio',
        ]);
        $this->assertFalse($alerta->compativel($vaga));
    }

    // ── Casts ────────────────────────────────────────────────────────────────

    public function test_campos_json_cast_array(): void
    {
        $alerta = $this->makeAlerta([
            'areas'      => ['Administração', 'Saúde'],
            'modalidades'=> ['presencial'],
            'tipos'      => ['emprego', 'bolsa'],
        ]);
        $this->assertIsArray($alerta->areas);
        $this->assertIsArray($alerta->modalidades);
        $this->assertIsArray($alerta->tipos);
    }

    public function test_ativo_cast_boolean(): void
    {
        $alerta = $this->makeAlerta(['ativo' => true]);
        $this->assertIsBool($alerta->ativo);
        $this->assertTrue($alerta->ativo);
    }
}
