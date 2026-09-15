<?php

namespace App\Support\Drhflow;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Leitura das vagas do DRHFlow — a única fronteira do portal com
 * `EN_VAGA_EMPREGO`.
 *
 * A consulta é a que o RH forneceu (design D3), acrescida de `DE_ATIVIDADES` e
 * `CD_TIPO_ADMISSAO`, que a tela de cadastro do documento descreve mas o SELECT
 * omitia — sem a primeira o candidato veria uma vaga sem texto.
 *
 * Escrita: nenhuma. Este repositório só faz SELECT.
 */
class VagaDrhflowRepository
{
    /** `EN_SITUACAO_VAGA_EMPREGO`: 1 = Vaga em Aberto, 2 = Finalizada, 3 = Cancelada. */
    public const SITUACAO_ABERTA = 1;

    public function __construct(
        private readonly DominioDrhflowRepository $dominios = new DominioDrhflowRepository,
        private readonly ?string $conexao = null,
    ) {}

    /**
     * Página de vagas disponíveis ao candidato.
     *
     * @param  array<string, mixed>  $filtros
     *
     * @throws DrhflowIndisponivelException
     */
    public function paginar(array $filtros = [], int $porPagina = 12, int $pagina = 1): LengthAwarePaginator
    {
        return $this->protegido('listagem de vagas', function () use ($filtros, $porPagina, $pagina) {
            $consulta = $this->aplicarFiltros($this->consultaBase(), $filtros);

            $total = (clone $consulta)->count();

            // OFFSET/FETCH NEXT no SQL Server: o `orderBy` da consulta base é o
            // que o SQL Server exige para paginar, e já está lá.
            $linhas = $consulta
                ->forPage(max(1, $pagina), $porPagina)
                ->get();

            $vagas = $this->montar($linhas);

            return new LengthAwarePaginator($vagas, $total, $porPagina, $pagina, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
            ]);
        });
    }

    /**
     * Uma vaga pelo código do DRHFlow, sujeita ao mesmo critério de
     * disponibilidade da listagem.
     *
     * Devolve nulo tanto para código inexistente quanto para vaga fechada ou
     * fora do prazo — de fora, os dois casos são "vaga não encontrada", e
     * distingui-los revelaria a existência de vagas que o candidato não pode ver.
     *
     * @throws DrhflowIndisponivelException
     */
    public function buscarPorCodigo(int $cdVagaEmprego): ?VagaDrhflow
    {
        return $this->protegido('detalhe da vaga', function () use ($cdVagaEmprego) {
            $linha = $this->consultaBase()
                ->where('v.CD_VAGA_EMPREGO', $cdVagaEmprego)
                ->first();

            return $linha === null ? null : $this->montar(collect([$linha]))->first();
        });
    }

    /**
     * Quantas vagas atendem ao critério, sem paginar.
     *
     * @param  array<string, mixed>  $filtros
     *
     * @throws DrhflowIndisponivelException
     */
    public function contar(array $filtros = []): int
    {
        return $this->protegido('contagem de vagas', function () use ($filtros) {
            return $this->aplicarFiltros($this->consultaBase(), $filtros)->count();
        });
    }

    /**
     * Registra quantas vagas abertas o `inner join` com `VW_FUNCAO_5ANOS`
     * descartou.
     *
     * O join é o comportamento definido pelo RH e é mantido, mas ele faz uma
     * vaga com função fora da view **sumir sem aviso**. Sem este log, "minha
     * vaga não aparece no portal" não teria resposta.
     */
    public function registrarVagasOcultasPorFuncao(): int
    {
        try {
            $bruto = $this->conexao()->table('EN_VAGA_EMPREGO as v')
                ->where('v.CD_SITUACAO', self::SITUACAO_ABERTA)
                ->where('v.DT_LIMITE_PARA_INSCRICAO', '>=', $this->limiteInscricao())
                ->count();

            $filtrado = $this->consultaBase()->count();
            $ocultas = $bruto - $filtrado;

            if ($ocultas > 0) {
                Log::warning('Vagas abertas ocultadas pelo join com VW_FUNCAO_5ANOS.', [
                    'abertas_no_prazo' => $bruto,
                    'apresentadas' => $filtrado,
                    'ocultas' => $ocultas,
                ]);
            }

            return $ocultas;
        } catch (Throwable $e) {
            // Diagnóstico não pode derrubar a listagem que ele observa.
            Log::warning('Não foi possível medir as vagas ocultas pelo join de função.', [
                'erro' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * A consulta do documento: situação aberta e prazo de inscrição dentro da
     * tolerância de um dia que o próprio DRHFlow usa (`GETDATE() - 1`).
     */
    private function consultaBase(): Builder
    {
        return $this->conexao()->table('EN_VAGA_EMPREGO as v')
            ->leftJoin('VW_PROJETO as p', 'v.CD_PROJETO', '=', 'p.ano_codigo')
            ->join('VW_FUNCAO_5ANOS as f', 'v.CD_FUNCAO', '=', 'f.CODIGO')
            ->leftJoin('EN_MUNICIPIO as m', 'v.CD_MUNICIPIO', '=', 'm.CD_MUNICIPIO')
            ->where('v.CD_SITUACAO', self::SITUACAO_ABERTA)
            ->where('v.DT_LIMITE_PARA_INSCRICAO', '>=', $this->limiteInscricao())
            ->orderBy('f.NOME_E_CBO')
            // Desempate estável: sem ele, duas vagas de mesmo cargo podem trocar
            // de página entre requisições e uma delas nunca ser vista.
            ->orderBy('v.CD_VAGA_EMPREGO')
            ->select([
                'v.CD_VAGA_EMPREGO', 'v.CD_SITUACAO', 'v.FG_ATIVA',
                'v.CD_CENTRO', 'v.CD_DEPARTAMENTO', 'v.CD_PROJETO', 'v.CD_FUNCAO',
                'v.CD_ESCOLARIDADE_EXIGIDA', 'v.CD_TIPO_EXPERIENCIA', 'v.CD_HORARIO',
                'v.CD_TIPO_ADMISSAO', 'v.DE_ATIVIDADES',
                'v.DE_BENEFICIOS', 'v.DE_CARGA_HORARIA', 'v.DE_DOCUMENTACAO_NECESSARIA',
                'v.DE_REQUISITOS_EXIGIDOS', 'v.VL_SALARIO',
                'v.DT_CADASTRO', 'v.DT_ALTERACAO', 'v.DT_LIMITE_PARA_INSCRICAO',
                'v.CD_UF', 'v.CD_MUNICIPIO', 'm.NM_MUNICIPIO',
                'p.projeto_rubrica_nome', 'f.NOME_E_CBO',
            ]);
    }

    /**
     * `DT_LIMITE_PARA_INSCRICAO >= GETDATE() - 1`, calculado na aplicação.
     *
     * Como o limite é gravado à meia-noite, isso mantém a vaga disponível
     * durante todo o seu último dia — o cenário "vaga no último dia do prazo"
     * da spec.
     */
    private function limiteInscricao(): Carbon
    {
        return Carbon::now()->subDay();
    }

    /**
     * Filtros de D4 — apenas os que a origem sustenta. Área, modalidade e curso
     * desejado não têm coluna equivalente em `EN_VAGA_EMPREGO` e saíram.
     *
     * @param  array<string, mixed>  $filtros
     */
    private function aplicarFiltros(Builder $consulta, array $filtros): Builder
    {
        // O código é o que o RH e o candidato trocam entre si para falar de uma
        // vaga. Máscara ou espaço digitados junto não podem virar "nenhuma vaga".
        $codigo = Normalizador::digitos((string) ($filtros['codigo'] ?? ''));

        if ($codigo !== '') {
            $consulta->where('v.CD_VAGA_EMPREGO', (int) $codigo);
        }

        if (filled($filtros['busca'] ?? null)) {
            $termo = '%'.str_replace(['%', '_'], ['[%]', '[_]'], trim((string) $filtros['busca'])).'%';

            $consulta->where(function (Builder $q) use ($termo) {
                $q->where('f.NOME_E_CBO', 'like', $termo)
                    ->orWhere('v.DE_ATIVIDADES', 'like', $termo);
            });
        }

        if (filled($filtros['tipo'] ?? null)) {
            $consulta->where('v.CD_TIPO_ADMISSAO', $filtros['tipo']);
        }

        if (filled($filtros['escolaridade'] ?? null)) {
            $consulta->where('v.CD_ESCOLARIDADE_EXIGIDA', $filtros['escolaridade']);
        }

        if (filled($filtros['cidade'] ?? null)) {
            $consulta->where('m.NM_MUNICIPIO', 'like', '%'.trim((string) $filtros['cidade']).'%');
        }

        if (filled($filtros['estado'] ?? null)) {
            $consulta->where('v.CD_UF', $filtros['estado']);
        }

        if (filled($filtros['projeto'] ?? null)) {
            $consulta->where('v.CD_PROJETO', $filtros['projeto']);
        }

        // `VL_SALARIO` é uma coluna só — a faixa vira comparação simples. Vagas
        // com salário zero ("não informado") ficam fora de qualquer faixa, que é
        // o comportamento esperado de quem filtrou por valor.
        if (filled($filtros['salario_min'] ?? null)) {
            $consulta->where('v.VL_SALARIO', '>=', (float) $filtros['salario_min']);
        }

        if (filled($filtros['salario_max'] ?? null)) {
            $consulta->where('v.VL_SALARIO', '<=', (float) $filtros['salario_max'])
                ->where('v.VL_SALARIO', '>', 0);
        }

        return $consulta;
    }

    /**
     * Traduz os códigos contra os domínios e devolve os DTOs.
     *
     * @param  Collection<int, object>  $linhas
     * @return Collection<int, VagaDrhflow>
     */
    private function montar(Collection $linhas): Collection
    {
        if ($linhas->isEmpty()) {
            return collect();
        }

        $tipos = $this->dominios->tiposAdmissao();
        $escolaridades = $this->dominios->grausInstrucao();
        $experiencias = $this->dominios->tiposExperiencia();
        $horarios = $this->dominios->horarios();

        return $linhas->map(
            fn (object $linha) => VagaDrhflow::daLinha($linha, $tipos, $escolaridades, $experiencias, $horarios)
        )->values();
    }

    /**
     * Traduz qualquer falha de banco em `DrhflowIndisponivelException`, para que
     * a indisponibilidade chegue à tela como estado explícito e não como
     * resultado vazio.
     */
    private function protegido(string $operacao, callable $consulta): mixed
    {
        try {
            return $consulta();
        } catch (DrhflowIndisponivelException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error("DRHFlow indisponível na {$operacao}.", ['erro' => $e->getMessage()]);

            throw DrhflowIndisponivelException::aoConsultar($operacao, $e);
        }
    }

    private function conexao(): ConnectionInterface
    {
        return DB::connection($this->conexao ?? config('drhflow.conexao', 'drhflow'));
    }
}
