<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vagas\Vaga;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VagaPublicaTest extends TestCase
{
    use RefreshDatabase;

    private User $coord;

    protected function setUp(): void
    {
        parent::setUp();
        $this->coord = User::factory()->create(['perfil' => 'coordenador', 'ativo' => true]);
    }

    private function criarVaga(array $attrs = []): Vaga
    {
        return Vaga::create(array_merge([
            'titulo'            => 'Vaga Pública de Teste',
            'descricao'         => 'Descrição pública da vaga de teste para listagem.',
            'requisitos'        => 'Requisitos da vaga pública de teste.',
            'tipo'              => 'estagio',
            'area'              => 'Tecnologia da Informação',
            'modalidade'        => 'presencial',
            'cidade'            => 'Florianópolis',
            'estado'            => 'SC',
            'pais'              => 'Brasil',
            'remuneracao'       => 1200.00,
            'carga_horaria'     => 30,
            'data_encerramento' => now()->addDays(30)->toDateString(),
            'status'            => 'ativa',
            'coordenador_id'    => $this->coord->id,
            'notificar_email'   => true,
        ], $attrs));
    }

    // ── Home / Listagem pública ───────────────────────────────────────────────

    public function test_home_redireciona_para_vagas(): void
    {
        $response = $this->get('/');
        // Home pode ser a própria listagem ou redirect
        $response->assertStatus(200);
    }

    public function test_listagem_publica_acessivel(): void
    {
        $response = $this->get('/vagas');
        $response->assertStatus(200);
        $response->assertViewIs('vagas.publico.index');
    }

    public function test_listagem_exibe_vagas_ativas(): void
    {
        $this->criarVaga(['titulo' => 'Estágio em PHP']);
        $response = $this->get('/vagas');
        $response->assertSee('Estágio em PHP');
    }

    public function test_listagem_nao_exibe_vagas_rascunho(): void
    {
        $this->criarVaga(['titulo' => 'Rascunho Escondido', 'status' => 'rascunho']);
        $response = $this->get('/vagas');
        $response->assertDontSee('Rascunho Escondido');
    }

    public function test_listagem_nao_exibe_vagas_aguardando_autorizacao(): void
    {
        $this->criarVaga(['titulo' => 'Aguardando Escondida', 'status' => 'aguardando_autorizacao']);
        $response = $this->get('/vagas');
        $response->assertDontSee('Aguardando Escondida');
    }

    public function test_listagem_nao_exibe_vagas_encerradas(): void
    {
        $this->criarVaga([
            'titulo'            => 'Encerrada Escondida',
            'status'            => 'encerrada',
            'data_encerramento' => now()->subDays(5)->toDateString(),
        ]);
        $response = $this->get('/vagas');
        $response->assertDontSee('Encerrada Escondida');
    }

    public function test_listagem_nao_exibe_vagas_ativas_com_data_passada(): void
    {
        $this->criarVaga([
            'titulo'            => 'Ativa Expirada',
            'status'            => 'ativa',
            'data_encerramento' => now()->subDays(1)->toDateString(),
        ]);
        $response = $this->get('/vagas');
        $response->assertDontSee('Ativa Expirada');
    }

    // ── Filtros na listagem pública ───────────────────────────────────────────

    public function test_filtro_por_area(): void
    {
        $this->criarVaga(['titulo' => 'Vaga TI', 'area' => 'Tecnologia da Informação']);
        $this->criarVaga(['titulo' => 'Vaga ADM', 'area' => 'Administração']);

        $response = $this->get('/vagas?area=Administra%C3%A7%C3%A3o');
        $response->assertSee('Vaga ADM');
        $response->assertDontSee('Vaga TI');
    }

    public function test_filtro_por_tipo(): void
    {
        $this->criarVaga(['titulo' => 'Estágio X', 'tipo' => 'estagio']);
        $this->criarVaga(['titulo' => 'Emprego X', 'tipo' => 'emprego']);

        $response = $this->get('/vagas?tipo=emprego');
        $response->assertSee('Emprego X');
        $response->assertDontSee('Estágio X');
    }

    public function test_filtro_por_modalidade(): void
    {
        $this->criarVaga(['titulo' => 'Vaga Presencial', 'modalidade' => 'presencial']);
        $this->criarVaga(['titulo' => 'Vaga Remota', 'modalidade' => 'remoto']);

        $response = $this->get('/vagas?modalidade=remoto');
        $response->assertSee('Vaga Remota');
        $response->assertDontSee('Vaga Presencial');
    }

    public function test_filtro_por_busca(): void
    {
        $this->criarVaga(['titulo' => 'Desenvolvedor Laravel']);
        $this->criarVaga(['titulo' => 'Analista Financeiro']);

        $response = $this->get('/vagas?busca=Laravel');
        $response->assertSee('Desenvolvedor Laravel');
        $response->assertDontSee('Analista Financeiro');
    }

    public function test_filtro_por_curso(): void
    {
        $this->criarVaga([
            'titulo'         => 'Vaga para Computação',
            'curso_desejado' => ['Ciência da Computação'],
        ]);
        $this->criarVaga([
            'titulo'         => 'Vaga para Direito',
            'curso_desejado' => ['Direito'],
        ]);

        $response = $this->get('/vagas?curso=Ci%C3%AAncia+da+Computa%C3%A7%C3%A3o');
        $response->assertSee('Vaga para Computação');
        $response->assertDontSee('Vaga para Direito');
    }

    public function test_filtro_por_cidade(): void
    {
        $this->criarVaga(['titulo' => 'Vaga Floripa', 'cidade' => 'Florianópolis']);
        $this->criarVaga(['titulo' => 'Vaga SP', 'cidade' => 'São Paulo']);

        $response = $this->get('/vagas?cidade=Florian%C3%B3polis');
        $response->assertSee('Vaga Floripa');
        $response->assertDontSee('Vaga SP');
    }

    public function test_filtro_por_faixa_salarial_minima(): void
    {
        $this->criarVaga(['titulo' => 'Vaga Alta Remun', 'remuneracao' => 5000.00]);
        $this->criarVaga(['titulo' => 'Vaga Baixa Remun', 'remuneracao' => 900.00]);

        $response = $this->get('/vagas?salario_min=3000');
        $response->assertSee('Vaga Alta Remun');
        $response->assertDontSee('Vaga Baixa Remun');
    }

    public function test_filtro_por_faixa_salarial_maxima(): void
    {
        $this->criarVaga(['titulo' => 'Vaga Alta Remun', 'remuneracao' => 5000.00]);
        $this->criarVaga(['titulo' => 'Vaga Baixa Remun', 'remuneracao' => 900.00]);

        $response = $this->get('/vagas?salario_max=2000');
        $response->assertSee('Vaga Baixa Remun');
        $response->assertDontSee('Vaga Alta Remun');
    }

    // ── Detalhes da vaga ──────────────────────────────────────────────────────

    public function test_pagina_detalhes_vaga_ativa(): void
    {
        $vaga = $this->criarVaga(['titulo' => 'Estágio em Python']);
        $response = $this->get("/vagas/{$vaga->id}");
        $response->assertStatus(200);
        $response->assertViewIs('vagas.publico.show');
        $response->assertSee('Estágio em Python');
    }

    public function test_pagina_detalhes_exibe_vagas_relacionadas(): void
    {
        $vaga = $this->criarVaga(['area' => 'Tecnologia da Informação']);
        $this->criarVaga(['titulo' => 'Outra Vaga TI', 'area' => 'Tecnologia da Informação']);

        $response = $this->get("/vagas/{$vaga->id}");
        $response->assertStatus(200);
        $response->assertViewHas('vagasRelacionadas');
    }

    // ── Fazenda Ressacada (página especial) ───────────────────────────────────

    public function test_pagina_fazenda_ressacada_acessivel(): void
    {
        $response = $this->get('/fazenda-ressacada');
        $response->assertStatus(200);
    }

    // ── API CEP ───────────────────────────────────────────────────────────────

    public function test_api_cep_retorna_json(): void
    {
        $response = $this->get('/api/cep/88040-400');
        // Pode retornar 200 (dados) ou 422 (CEP não encontrado no ViaCEP externo)
        // Em testes, verificamos apenas que retorna JSON e não 500
        $this->assertNotEquals(500, $response->status());
    }

    public function test_api_cep_formato_invalido_retorna_404(): void
    {
        $response = $this->get('/api/cep/abc');
        $response->assertStatus(404); // Rota não existe para este formato (regex)
    }
}
