<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\UsaDrhflowFalso;
use Tests\TestCase;

/**
 * A listagem e o detalhe públicos, agora servidos pelo DRHFlow.
 *
 * As vagas não vêm mais da tabela `vagas` do MySQL — que continua existindo para
 * o coordenador e o gestor, cobertos por `VagaCoordenadorTest` e `VagaGestorTest`.
 */
class VagaPublicaTest extends TestCase
{
    use RefreshDatabase, UsaDrhflowFalso;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configurarDrhflowFalso();
    }

    // ── Home / Listagem pública ───────────────────────────────────────────────

    public function test_home_responde(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_listagem_publica_acessivel(): void
    {
        $response = $this->get('/vagas');

        $response->assertStatus(200);
        $response->assertViewIs('publico.vagas.index');
    }

    public function test_listagem_exibe_vagas_abertas_do_drhflow(): void
    {
        $this->vagaDrhflow(['CD_FUNCAO' => '0373']);

        $this->get('/vagas')->assertSee('PROGRAMADOR');
    }

    public function test_listagem_nao_exibe_vaga_finalizada(): void
    {
        $this->vagaDrhflow(['CD_FUNCAO' => '0373', 'CD_SITUACAO' => 2]);

        $this->get('/vagas')->assertDontSee('PROGRAMADOR');
    }

    public function test_listagem_nao_exibe_vaga_cancelada(): void
    {
        $this->vagaDrhflow(['CD_FUNCAO' => '0373', 'CD_SITUACAO' => 3]);

        $this->get('/vagas')->assertDontSee('PROGRAMADOR');
    }

    public function test_listagem_nao_exibe_vaga_com_prazo_vencido(): void
    {
        $this->vagaDrhflow([
            'CD_FUNCAO' => '0373',
            'DT_LIMITE_PARA_INSCRICAO' => now()->subDays(3)->startOfDay(),
        ]);

        $this->get('/vagas')->assertDontSee('PROGRAMADOR');
    }

    public function test_listagem_exibe_vaga_no_ultimo_dia_do_prazo(): void
    {
        $this->vagaDrhflow([
            'CD_FUNCAO' => '0373',
            'DT_LIMITE_PARA_INSCRICAO' => now()->startOfDay(),
        ]);

        $this->get('/vagas')->assertSee('PROGRAMADOR');
    }

    // ── Filtros ───────────────────────────────────────────────────────────────

    public function test_filtro_por_tipo_de_contratacao(): void
    {
        $this->vagaDrhflow(['CD_FUNCAO' => '0373', 'CD_TIPO_ADMISSAO' => 'N']);
        $this->vagaDrhflow(['CD_FUNCAO' => '0993', 'CD_TIPO_ADMISSAO' => 'U']);

        $response = $this->get('/vagas?tipo=N');

        $response->assertSee('PROGRAMADOR');
        $response->assertDontSee('BOLSISTA');
    }

    public function test_filtro_por_busca_cobre_cargo_e_atividades(): void
    {
        $this->vagaDrhflow(['CD_FUNCAO' => '0373', 'DE_ATIVIDADES' => 'Nada a ver.']);
        $this->vagaDrhflow(['CD_FUNCAO' => '0993', 'DE_ATIVIDADES' => 'Levantamento topográfico.']);

        $porCargo = $this->get('/vagas?busca=PROGRAMADOR');
        $porCargo->assertSee('PROGRAMADOR');
        $porCargo->assertDontSee('BOLSISTA');

        $porAtividade = $this->get('/vagas?busca=topogr');
        $porAtividade->assertSee('BOLSISTA');
        $porAtividade->assertDontSee('PROGRAMADOR');
    }

    public function test_filtro_por_cidade(): void
    {
        $this->vagaDrhflow(['CD_FUNCAO' => '0373', 'CD_MUNICIPIO' => 1653]);
        $this->vagaDrhflow(['CD_FUNCAO' => '0993', 'CD_MUNICIPIO' => 4204]);

        $response = $this->get('/vagas?cidade=Florian%C3%B3polis');

        $response->assertSee('PROGRAMADOR');
        $response->assertDontSee('BOLSISTA');
    }

    public function test_filtro_por_escolaridade(): void
    {
        $this->vagaDrhflow(['CD_FUNCAO' => '0373', 'CD_ESCOLARIDADE_EXIGIDA' => '9']);
        $this->vagaDrhflow(['CD_FUNCAO' => '0993', 'CD_ESCOLARIDADE_EXIGIDA' => '8']);

        $response = $this->get('/vagas?escolaridade=9');

        $response->assertSee('PROGRAMADOR');
        $response->assertDontSee('BOLSISTA');
    }

    public function test_filtro_por_projeto(): void
    {
        $this->vagaDrhflow(['CD_FUNCAO' => '0373', 'CD_PROJETO' => '2024.011']);
        $this->vagaDrhflow(['CD_FUNCAO' => '0993', 'CD_PROJETO' => '2025.114']);

        $response = $this->get('/vagas?projeto=2024.011');

        $response->assertSee('PROGRAMADOR');
        $response->assertDontSee('BOLSISTA');
    }

    public function test_filtro_por_faixa_salarial(): void
    {
        $this->vagaDrhflow(['CD_FUNCAO' => '0373', 'VL_SALARIO' => 5000]);
        $this->vagaDrhflow(['CD_FUNCAO' => '0993', 'VL_SALARIO' => 900]);

        $minimo = $this->get('/vagas?salario_min=3000');
        $minimo->assertSee('PROGRAMADOR');
        $minimo->assertDontSee('BOLSISTA');

        $maximo = $this->get('/vagas?salario_max=2000');
        $maximo->assertSee('BOLSISTA');
        $maximo->assertDontSee('PROGRAMADOR');
    }

    public function test_nao_ha_filtro_por_area_modalidade_nem_curso(): void
    {
        $dados = $this->get('/vagas')->original->getData();

        $this->assertArrayNotHasKey('areas', $dados);
        $this->assertArrayNotHasKey('cursos', $dados);
        $this->assertArrayNotHasKey('modalidades', $dados);
    }

    public function test_opcoes_de_filtro_vem_dos_dominios_do_drhflow(): void
    {
        $this->vagaDrhflow();

        $dados = $this->get('/vagas')->original->getData();

        $this->assertSame('Bolsista', $dados['tipos']['U']);
        $this->assertSame('Educação superior completo', $dados['escolaridades']['9']);
        $this->assertContains('Florianópolis', $dados['municipios']);
        $this->assertArrayHasKey('SC', $dados['ufs']);
    }

    // ── Detalhe ───────────────────────────────────────────────────────────────

    public function test_detalhe_resolve_pelo_codigo_do_drhflow(): void
    {
        $codigo = $this->vagaDrhflow(['CD_FUNCAO' => '0373']);

        $response = $this->get("/vagas/{$codigo}");

        $response->assertStatus(200);
        $response->assertViewIs('publico.vagas.show');
        $response->assertSee('PROGRAMADOR');
    }

    public function test_detalhe_de_codigo_inexistente_responde_404(): void
    {
        $this->get('/vagas/999999')->assertStatus(404);
    }

    public function test_detalhe_de_vaga_fora_do_criterio_responde_404(): void
    {
        $finalizada = $this->vagaDrhflow(['CD_SITUACAO' => 2]);
        $vencida = $this->vagaDrhflow(['DT_LIMITE_PARA_INSCRICAO' => now()->subWeek()]);

        $this->get("/vagas/{$finalizada}")->assertStatus(404);
        $this->get("/vagas/{$vencida}")->assertStatus(404);
    }

    public function test_codigos_chegam_traduzidos_ao_detalhe(): void
    {
        $codigo = $this->vagaDrhflow([
            'CD_TIPO_ADMISSAO' => 'N',
            'CD_ESCOLARIDADE_EXIGIDA' => '9',
            'CD_TIPO_EXPERIENCIA' => 4,
        ]);

        $vaga = $this->get("/vagas/{$codigo}")->original->getData()['vaga'];

        $this->assertSame('Celetista', $vaga->tipo);
        $this->assertSame('Educação superior completo', $vaga->escolaridade);
        $this->assertSame('2 Anos', $vaga->experiencia);
    }

    // ── Indisponibilidade da origem ───────────────────────────────────────────

    public function test_drhflow_indisponivel_nao_vira_listagem_vazia(): void
    {
        DB::purge('drhflow');
        config(['database.connections.drhflow' => [
            'driver' => 'sqlite',
            'database' => '/caminho/inexistente/drhflow.sqlite',
            'prefix' => '',
        ]]);

        $response = $this->get('/vagas');

        $response->assertStatus(200);
        $this->assertTrue($response->original->getData()['indisponivel']);
    }

    // ── Páginas estáticas e API ───────────────────────────────────────────────

    public function test_pagina_fazenda_ressacada_acessivel(): void
    {
        $this->get('/fazenda-ressacada')->assertStatus(200);
    }

    public function test_api_cep_retorna_json(): void
    {
        // Pode ser 200 (dados) ou 422 (não encontrado no ViaCEP); o que importa
        // é não estourar.
        $this->assertNotEquals(500, $this->get('/api/cep/88040-400')->status());
    }

    public function test_api_cep_formato_invalido_retorna_404(): void
    {
        $this->get('/api/cep/abc')->assertStatus(404);
    }
}
