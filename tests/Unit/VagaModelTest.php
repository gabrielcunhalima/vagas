<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Vagas\Vaga;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VagaModelTest extends TestCase
{
    use RefreshDatabase;

    private function makeVaga(array $attrs = []): Vaga
    {
        $coord = User::factory()->create(['perfil' => 'coordenador', 'ativo' => true]);

        return Vaga::create(array_merge([
            'titulo' => 'Vaga de Teste',
            'descricao' => 'Descrição completa da vaga de teste para validação.',
            'requisitos' => 'Requisitos básicos para a vaga de teste.',
            'tipo' => 'estagio',
            'area' => 'Tecnologia da Informação',
            'modalidade' => 'presencial',
            'cidade' => 'Florianópolis',
            'estado' => 'SC',
            'pais' => 'Brasil',
            'remuneracao' => 1200.00,
            'carga_horaria' => 30,
            'data_encerramento' => now()->addDays(30)->toDateString(),
            'status' => 'ativa',
            'coordenador_id' => $coord->id,
            'notificar_email' => true,
        ], $attrs));
    }

    // ── Accessors / Labels ────────────────────────────────────────────────────

    public function test_tipo_label_estagio(): void
    {
        $vaga = $this->makeVaga(['tipo' => 'estagio']);
        $this->assertEquals('Estágio', $vaga->tipo_label);
    }

    public function test_tipo_label_emprego(): void
    {
        $vaga = $this->makeVaga(['tipo' => 'emprego']);
        $this->assertEquals('CLT', $vaga->tipo_label);
    }

    public function test_tipo_label_bolsa(): void
    {
        $vaga = $this->makeVaga(['tipo' => 'bolsa']);
        $this->assertEquals('Bolsa', $vaga->tipo_label);
    }

    public function test_modalidade_label_presencial(): void
    {
        $vaga = $this->makeVaga(['modalidade' => 'presencial']);
        $this->assertEquals('Presencial', $vaga->modalidade_label);
    }

    public function test_modalidade_label_remoto(): void
    {
        $vaga = $this->makeVaga(['modalidade' => 'remoto']);
        $this->assertEquals('Remoto', $vaga->modalidade_label);
    }

    public function test_modalidade_label_hibrido(): void
    {
        $vaga = $this->makeVaga(['modalidade' => 'hibrido']);
        $this->assertEquals('Híbrido', $vaga->modalidade_label);
    }

    public function test_status_label_rascunho(): void
    {
        $vaga = $this->makeVaga(['status' => 'rascunho']);
        $this->assertEquals('Rascunho', $vaga->status_label);
    }

    public function test_status_label_aguardando_autorizacao(): void
    {
        $vaga = $this->makeVaga(['status' => 'aguardando_autorizacao']);
        $this->assertEquals('Aguardando Autorização', $vaga->status_label);
    }

    public function test_status_label_ativa(): void
    {
        $vaga = $this->makeVaga(['status' => 'ativa']);
        $this->assertEquals('Ativa', $vaga->status_label);
    }

    public function test_status_cor_ativa(): void
    {
        $vaga = $this->makeVaga(['status' => 'ativa']);
        $this->assertEquals('success', $vaga->status_cor);
    }

    public function test_status_cor_aguardando(): void
    {
        $vaga = $this->makeVaga(['status' => 'aguardando_autorizacao']);
        $this->assertEquals('warning', $vaga->status_cor);
    }

    public function test_status_cor_recusada(): void
    {
        $vaga = $this->makeVaga(['status' => 'recusada']);
        $this->assertEquals('danger', $vaga->status_cor);
    }

    // ── esta_aberta ───────────────────────────────────────────────────────────

    public function test_vaga_ativa_com_data_futura_esta_aberta(): void
    {
        $vaga = $this->makeVaga(['status' => 'ativa', 'data_encerramento' => now()->addDays(10)->toDateString()]);
        $this->assertTrue($vaga->esta_aberta);
    }

    public function test_vaga_ativa_com_data_passada_nao_esta_aberta(): void
    {
        $vaga = $this->makeVaga(['status' => 'ativa', 'data_encerramento' => now()->subDays(1)->toDateString()]);
        $this->assertFalse($vaga->esta_aberta);
    }

    public function test_vaga_rascunho_nao_esta_aberta(): void
    {
        $vaga = $this->makeVaga(['status' => 'rascunho', 'data_encerramento' => now()->addDays(10)->toDateString()]);
        $this->assertFalse($vaga->esta_aberta);
    }

    public function test_vaga_encerrada_nao_esta_aberta(): void
    {
        $vaga = $this->makeVaga(['status' => 'encerrada', 'data_encerramento' => now()->subDays(5)->toDateString()]);
        $this->assertFalse($vaga->esta_aberta);
    }

    // ── dias_restantes ────────────────────────────────────────────────────────

    public function test_dias_restantes_futuro(): void
    {
        $vaga = $this->makeVaga(['data_encerramento' => now()->addDays(15)->toDateString()]);
        $this->assertEquals(15, $vaga->dias_restantes);
    }

    public function test_dias_restantes_passado_retorna_zero(): void
    {
        $vaga = $this->makeVaga(['data_encerramento' => now()->subDays(5)->toDateString()]);
        $this->assertEquals(0, $vaga->dias_restantes);
    }

    // ── is_nova ───────────────────────────────────────────────────────────────

    public function test_vaga_criada_hoje_is_nova(): void
    {
        $vaga = $this->makeVaga();
        $this->assertTrue($vaga->is_nova);
    }

    // ── endereco_completo ─────────────────────────────────────────────────────

    public function test_endereco_completo_com_todos_campos(): void
    {
        $vaga = $this->makeVaga([
            'logradouro' => 'Av. Madre Benvenuta',
            'numero' => '2007',
            'bairro' => 'Santa Mônica',
            'cidade' => 'Florianópolis',
            'estado' => 'SC',
            'cep' => '88040-400',
        ]);
        $endereco = $vaga->endereco_completo;
        $this->assertStringContainsString('Av. Madre Benvenuta', $endereco);
        $this->assertStringContainsString('2007', $endereco);
        $this->assertStringContainsString('Florianópolis/SC', $endereco);
    }

    public function test_endereco_completo_sem_logradouro(): void
    {
        $vaga = $this->makeVaga(['cidade' => 'Florianópolis', 'estado' => 'SC']);
        $this->assertStringContainsString('Florianópolis/SC', $vaga->endereco_completo);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function test_scope_ativas_retorna_apenas_vagas_ativas_com_data_futura(): void
    {
        $this->makeVaga(['status' => 'ativa', 'data_encerramento' => now()->addDays(10)->toDateString()]);
        $this->makeVaga(['status' => 'ativa', 'data_encerramento' => now()->subDays(1)->toDateString()]);
        $this->makeVaga(['status' => 'rascunho', 'data_encerramento' => now()->addDays(10)->toDateString()]);

        $ativas = Vaga::ativas()->get();
        $this->assertCount(1, $ativas);
    }

    public function test_scope_aguardando_autorizacao(): void
    {
        $this->makeVaga(['status' => 'aguardando_autorizacao']);
        $this->makeVaga(['status' => 'ativa']);
        $this->makeVaga(['status' => 'rascunho']);

        $aguardando = Vaga::aguardandoAutorizacao()->get();
        $this->assertCount(1, $aguardando);
    }

    public function test_scope_por_area(): void
    {
        $this->makeVaga(['area' => 'Tecnologia da Informação']);
        $this->makeVaga(['area' => 'Administração']);

        $vagas = Vaga::porArea('Tecnologia da Informação')->get();
        $this->assertCount(1, $vagas);
        $this->assertEquals('Tecnologia da Informação', $vagas->first()->area);
    }

    public function test_scope_por_tipo(): void
    {
        $this->makeVaga(['tipo' => 'estagio']);
        $this->makeVaga(['tipo' => 'emprego']);
        $this->makeVaga(['tipo' => 'bolsa']);

        $estagios = Vaga::porTipo('estagio')->get();
        $this->assertCount(1, $estagios);
    }

    public function test_scope_por_modalidade(): void
    {
        $this->makeVaga(['modalidade' => 'presencial']);
        $this->makeVaga(['modalidade' => 'remoto']);
        $this->makeVaga(['modalidade' => 'hibrido']);

        $remotas = Vaga::porModalidade('remoto')->get();
        $this->assertCount(1, $remotas);
    }

    public function test_scope_busca_por_titulo(): void
    {
        $this->makeVaga(['titulo' => 'Estágio em PHP Laravel']);
        $this->makeVaga(['titulo' => 'Analista de Dados']);

        $resultado = Vaga::busca('Laravel')->get();
        $this->assertCount(1, $resultado);
        $this->assertStringContainsString('Laravel', $resultado->first()->titulo);
    }

    public function test_scope_por_curso(): void
    {
        $this->makeVaga(['curso_desejado' => ['Ciência da Computação', 'Sistemas de Informação']]);
        $this->makeVaga(['curso_desejado' => ['Administração']]);

        $resultado = Vaga::porCurso('Ciência da Computação')->get();
        $this->assertCount(1, $resultado);
    }

    // ── Relacionamentos ───────────────────────────────────────────────────────

    public function test_relacionamento_candidaturas(): void
    {
        $vaga = $this->makeVaga();
        $this->assertInstanceOf(HasMany::class, $vaga->candidaturas());
    }

    public function test_relacionamento_coordenador(): void
    {
        $vaga = $this->makeVaga();
        $this->assertNotNull($vaga->coordenador);
        $this->assertEquals('coordenador', $vaga->coordenador->perfil);
    }

    // ── Métodos de negócio ────────────────────────────────────────────────────

    public function test_total_candidaturas_zero_sem_candidaturas(): void
    {
        $vaga = $this->makeVaga();
        $this->assertEquals(0, $vaga->totalCandidaturas());
    }

    public function test_curso_desejado_cast_array(): void
    {
        $vaga = $this->makeVaga(['curso_desejado' => ['Ciência da Computação', 'Direito']]);
        $this->assertIsArray($vaga->curso_desejado);
        $this->assertContains('Ciência da Computação', $vaga->curso_desejado);
    }

    public function test_remuneracao_max_nullable(): void
    {
        $vaga = $this->makeVaga(['remuneracao_max' => null]);
        $this->assertNull($vaga->remuneracao_max);

        $vagaComMax = $this->makeVaga(['remuneracao_max' => 2500.00]);
        $this->assertEquals('2500.00', $vagaComMax->remuneracao_max);
    }

    public function test_soft_delete(): void
    {
        $vaga = $this->makeVaga();
        $id = $vaga->id;
        $vaga->delete();

        $this->assertNull(Vaga::find($id));
        $this->assertNotNull(Vaga::withTrashed()->find($id));
    }
}
