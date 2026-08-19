<?php

namespace Tests\Feature\Drhflow;

use App\Support\Drhflow\DrhflowIndisponivelException;
use App\Support\Drhflow\VagaDrhflow;
use App\Support\Drhflow\VagaDrhflowRepository;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\UsaDrhflowFalso;
use Tests\TestCase;

/** O critério de vaga disponível ao candidato, da capacidade `vagas-drhflow`. */
class VagaDrhflowRepositoryTest extends TestCase
{
    use UsaDrhflowFalso;

    private VagaDrhflowRepository $repositorio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configurarDrhflowFalso();
        $this->repositorio = new VagaDrhflowRepository;
    }

    /** @return list<int> */
    private function codigosListados(array $filtros = []): array
    {
        return collect($this->repositorio->paginar($filtros, 50)->items())
            ->map(fn (VagaDrhflow $v) => $v->codigo)
            ->all();
    }

    public function test_vaga_aberta_dentro_do_prazo_aparece(): void
    {
        $codigo = $this->vagaDrhflow(['DT_LIMITE_PARA_INSCRICAO' => now()->addDays(10)->startOfDay()]);

        $this->assertSame([$codigo], $this->codigosListados());
    }

    public function test_vaga_com_situacao_diferente_de_aberta_nao_aparece(): void
    {
        $finalizada = $this->vagaDrhflow(['CD_SITUACAO' => 2]);
        $cancelada = $this->vagaDrhflow(['CD_SITUACAO' => 3]);

        $this->assertSame([], $this->codigosListados());
        $this->assertNull($this->repositorio->buscarPorCodigo($finalizada));
        $this->assertNull($this->repositorio->buscarPorCodigo($cancelada));
    }

    public function test_vaga_com_prazo_vencido_nao_aparece(): void
    {
        $vencida = $this->vagaDrhflow(['DT_LIMITE_PARA_INSCRICAO' => now()->subDays(3)->startOfDay()]);

        $this->assertSame([], $this->codigosListados());
        $this->assertNull($this->repositorio->buscarPorCodigo($vencida));
    }

    public function test_vaga_no_ultimo_dia_do_prazo_continua_disponivel(): void
    {
        $hoje = $this->vagaDrhflow(['DT_LIMITE_PARA_INSCRICAO' => now()->startOfDay()]);

        $this->assertSame([$hoje], $this->codigosListados());
        $this->assertNotNull($this->repositorio->buscarPorCodigo($hoje));
    }

    public function test_vaga_sem_projeto_e_sem_municipio_nao_quebra(): void
    {
        $codigo = $this->vagaDrhflow([
            'CD_PROJETO' => null,
            'CD_MUNICIPIO' => null,
            'CD_UF' => 'SC',
        ]);

        $vaga = $this->repositorio->buscarPorCodigo($codigo);

        $this->assertNotNull($vaga);
        $this->assertNull($vaga->projetoNome);
        $this->assertNull($vaga->cidade);
        $this->assertSame('SC', $vaga->localizacao());
    }

    public function test_vaga_com_funcao_fora_da_view_desaparece_e_e_registrada(): void
    {
        $this->vagaDrhflow(['CD_FUNCAO' => '9999']);
        $this->vagaDrhflow(['CD_FUNCAO' => '0993']);

        $this->assertCount(1, $this->codigosListados());
        $this->assertSame(1, $this->repositorio->registrarVagasOcultasPorFuncao());
    }

    public function test_codigo_inexistente_devolve_nulo(): void
    {
        $this->assertNull($this->repositorio->buscarPorCodigo(999999));
    }

    public function test_codigos_chegam_traduzidos_a_apresentacao(): void
    {
        $codigo = $this->vagaDrhflow([
            'CD_TIPO_ADMISSAO' => 'N',
            'CD_ESCOLARIDADE_EXIGIDA' => '9',
            'CD_TIPO_EXPERIENCIA' => 4,
        ]);

        $vaga = $this->repositorio->buscarPorCodigo($codigo);

        $this->assertSame('Celetista', $vaga->tipo);
        $this->assertSame('Educação superior completo', $vaga->escolaridade);
        $this->assertSame('2 Anos', $vaga->experiencia);
    }

    public function test_bolsista_e_traduzido_pelo_codigo_do_rm(): void
    {
        // 'U' na vaga corresponde a 'O' na chave de EN_TIPO_ADMISSAO.
        $codigo = $this->vagaDrhflow(['CD_TIPO_ADMISSAO' => 'U']);

        $this->assertSame('Bolsista', $this->repositorio->buscarPorCodigo($codigo)->tipo);
    }

    public function test_salario_zero_e_ausencia_e_nao_valor(): void
    {
        $codigo = $this->vagaDrhflow(['VL_SALARIO' => 0]);

        $vaga = $this->repositorio->buscarPorCodigo($codigo);

        $this->assertNull($vaga->remuneracao);
        $this->assertNull($vaga->remuneracaoFormatada());
    }

    public function test_ordenacao_e_por_cargo(): void
    {
        $programador = $this->vagaDrhflow(['CD_FUNCAO' => '0373']);
        $bolsista = $this->vagaDrhflow(['CD_FUNCAO' => '0993']);

        $this->assertSame([$bolsista, $programador], $this->codigosListados());
    }

    public function test_paginacao_devolve_o_total_e_a_fatia(): void
    {
        foreach (range(1, 7) as $i) {
            $this->vagaDrhflow();
        }

        $pagina = $this->repositorio->paginar([], porPagina: 3, pagina: 2);

        $this->assertSame(7, $pagina->total());
        $this->assertCount(3, $pagina->items());
        $this->assertSame(3, $pagina->lastPage());
    }

    public function test_busca_cobre_cargo_e_atividades(): void
    {
        $porCargo = $this->vagaDrhflow(['CD_FUNCAO' => '0373', 'DE_ATIVIDADES' => 'Nada relacionado.']);
        $porAtividade = $this->vagaDrhflow(['CD_FUNCAO' => '0993', 'DE_ATIVIDADES' => 'Manutenção de sistemas legados.']);

        $this->assertSame([$porCargo], $this->codigosListados(['busca' => 'PROGRAMADOR']));
        $this->assertSame([$porAtividade], $this->codigosListados(['busca' => 'legados']));
    }

    public function test_filtro_por_tipo_de_contratacao(): void
    {
        $celetista = $this->vagaDrhflow(['CD_TIPO_ADMISSAO' => 'N']);
        $this->vagaDrhflow(['CD_TIPO_ADMISSAO' => 'U']);

        $this->assertSame([$celetista], $this->codigosListados(['tipo' => 'N']));
    }

    public function test_filtro_por_municipio(): void
    {
        $floripa = $this->vagaDrhflow(['CD_MUNICIPIO' => 1653]);
        $this->vagaDrhflow(['CD_MUNICIPIO' => 4204]);

        $this->assertSame([$floripa], $this->codigosListados(['cidade' => 'Florian']));
    }

    public function test_filtro_por_escolaridade_uf_projeto_e_faixa_salarial(): void
    {
        $alvo = $this->vagaDrhflow([
            'CD_ESCOLARIDADE_EXIGIDA' => '9',
            'CD_UF' => 'SC',
            'CD_PROJETO' => '2024.011',
            'VL_SALARIO' => 3000,
        ]);
        $this->vagaDrhflow([
            'CD_ESCOLARIDADE_EXIGIDA' => '7',
            'CD_UF' => 'SP',
            'CD_PROJETO' => '2025.114',
            'VL_SALARIO' => 900,
        ]);

        $this->assertSame([$alvo], $this->codigosListados(['escolaridade' => '9']));
        $this->assertSame([$alvo], $this->codigosListados(['estado' => 'SC']));
        $this->assertSame([$alvo], $this->codigosListados(['projeto' => '2024.011']));
        $this->assertSame([$alvo], $this->codigosListados(['salario_min' => 2000]));
        $this->assertSame([$alvo], $this->codigosListados(['salario_max' => 3500, 'salario_min' => 1000]));
    }

    public function test_vaga_sem_salario_fica_fora_do_filtro_de_faixa(): void
    {
        $this->vagaDrhflow(['VL_SALARIO' => 0]);

        $this->assertSame([], $this->codigosListados(['salario_max' => 5000]));
    }

    public function test_falha_de_conexao_vira_indisponibilidade_e_nao_lista_vazia(): void
    {
        DB::purge('drhflow');
        config(['database.connections.drhflow' => [
            'driver' => 'sqlite',
            'database' => '/caminho/que/nao/existe/drhflow.sqlite',
            'prefix' => '',
        ]]);

        $this->expectException(DrhflowIndisponivelException::class);

        $this->repositorio->paginar();
    }
}
