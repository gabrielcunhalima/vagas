<?php

namespace App\Support\Drhflow;

use Illuminate\Support\Carbon;

/**
 * Uma vaga do DRHFlow, já traduzida para o que o candidato vê.
 *
 * É um objeto simples, não um model: `EN_VAGA_EMPREGO` pertence ao RH e o portal
 * só lê. Um model Eloquent traria `save()`, `delete()` e `truncate()` para perto
 * de uma tabela onde nada disso pode acontecer (design D2).
 *
 * A tradução de códigos (tipo de admissão, escolaridade, experiência) acontece
 * no repositório, contra os domínios do próprio DRHFlow — aqui só chegam
 * rótulos legíveis, porque a spec proíbe código bruto na tela.
 */
class VagaDrhflow
{
    public function __construct(
        public readonly int $codigo,
        public readonly ?string $titulo,
        public readonly ?string $descricao,
        public readonly ?string $tipoCodigo,
        public readonly ?string $tipo,
        public readonly ?string $requisitos,
        public readonly ?string $beneficios,
        public readonly ?string $documentacao,
        public readonly ?float $remuneracao,
        public readonly ?string $cargaHoraria,
        public readonly ?string $horario,
        public readonly ?string $escolaridade,
        public readonly ?string $experiencia,
        public readonly ?string $cidade,
        public readonly ?string $estado,
        public readonly ?string $projetoNome,
        public readonly ?string $projetoCodigo,
        public readonly ?Carbon $dataEncerramento,
        public readonly ?Carbon $cadastradaEm,
    ) {}

    /**
     * Monta a partir de uma linha da consulta de vagas.
     *
     * @param  array<string, string|null>  $tiposAdmissao  código do RM => rótulo
     * @param  array<string, string|null>  $escolaridades  código => rótulo
     * @param  array<string, string|null>  $experiencias  código => rótulo
     * @param  array<string, string|null>  $horarios  código => descrição
     */
    public static function daLinha(
        object $linha,
        array $tiposAdmissao = [],
        array $escolaridades = [],
        array $experiencias = [],
        array $horarios = [],
    ): self {
        $tipoCodigo = self::texto($linha->CD_TIPO_ADMISSAO ?? null);
        $escolaridade = self::texto($linha->CD_ESCOLARIDADE_EXIGIDA ?? null);
        $experiencia = self::texto($linha->CD_TIPO_EXPERIENCIA ?? null);
        $horario = self::texto($linha->CD_HORARIO ?? null);

        return new self(
            codigo: (int) $linha->CD_VAGA_EMPREGO,
            titulo: self::texto($linha->NOME_E_CBO ?? null),
            descricao: self::texto($linha->DE_ATIVIDADES ?? null),
            tipoCodigo: $tipoCodigo,
            tipo: $tipoCodigo === null ? null : ($tiposAdmissao[$tipoCodigo] ?? null),
            requisitos: self::texto($linha->DE_REQUISITOS_EXIGIDOS ?? null),
            beneficios: self::texto($linha->DE_BENEFICIOS ?? null),
            documentacao: self::texto($linha->DE_DOCUMENTACAO_NECESSARIA ?? null),
            remuneracao: self::salario($linha->VL_SALARIO ?? null),
            cargaHoraria: self::texto($linha->DE_CARGA_HORARIA ?? null),
            horario: $horario === null ? null : ($horarios[$horario] ?? null),
            escolaridade: $escolaridade === null ? null : ($escolaridades[$escolaridade] ?? null),
            experiencia: $experiencia === null ? null : ($experiencias[$experiencia] ?? null),
            cidade: self::texto($linha->NM_MUNICIPIO ?? null),
            estado: self::texto($linha->CD_UF ?? null),
            projetoNome: self::texto($linha->projeto_rubrica_nome ?? null),
            projetoCodigo: self::texto($linha->CD_PROJETO ?? null),
            dataEncerramento: self::data($linha->DT_LIMITE_PARA_INSCRICAO ?? null),
            cadastradaEm: self::data($linha->DT_CADASTRO ?? null),
        );
    }

    /** Cidade/UF como o candidato lê, com o que houver. */
    public function localizacao(): ?string
    {
        if ($this->cidade && $this->estado) {
            return "{$this->cidade}/{$this->estado}";
        }

        return $this->cidade ?: $this->estado ?: null;
    }

    /** Dias até o fim das inscrições, saturado em zero. */
    public function diasRestantes(): ?int
    {
        if (! $this->dataEncerramento) {
            return null;
        }

        return max(0, (int) Carbon::today()->diffInDays($this->dataEncerramento->copy()->startOfDay(), false));
    }

    public function remuneracaoFormatada(): ?string
    {
        if ($this->remuneracao === null) {
            return null;
        }

        return 'R$ '.number_format($this->remuneracao, 2, ',', '.');
    }

    /**
     * Forma consumida pelo React.
     *
     * As chaves `id`, `titulo`, `descricao`, `tipo`, `remuneracao`, `cidade`,
     * `estado`, `carga_horaria`, `data_encerramento`, `projeto_nome` e
     * `created_at` são as que os componentes de listagem e detalhe já esperam —
     * a troca da origem não deve vazar para eles.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->codigo,
            'codigo' => $this->codigo,
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'tipo' => $this->tipo,
            'tipo_codigo' => $this->tipoCodigo,
            'requisitos' => $this->requisitos,
            'beneficios' => $this->beneficios,
            'documentacao' => $this->documentacao,
            'remuneracao' => $this->remuneracao,
            'remuneracao_formatada' => $this->remuneracaoFormatada(),
            'carga_horaria' => $this->cargaHoraria,
            'horario' => $this->horario,
            'escolaridade' => $this->escolaridade,
            'experiencia' => $this->experiencia,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
            'localizacao' => $this->localizacao(),
            'projeto_nome' => $this->projetoNome,
            'projeto_codigo' => $this->projetoCodigo,
            'data_encerramento' => $this->dataEncerramento?->toIso8601String(),
            'dias_restantes' => $this->diasRestantes(),
            'created_at' => $this->cadastradaEm?->toIso8601String(),
        ];
    }

    /** Texto da origem sem espaço em volta; string vazia é ausência, não valor. */
    private static function texto(mixed $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $limpo = trim((string) $valor);

        return $limpo === '' ? null : $limpo;
    }

    /**
     * `VL_SALARIO` vem `0` em vagas sem remuneração definida (117 delas no banco
     * de testes). Zero ali significa "não informado", e apresentá-lo como
     * "R$ 0,00" seria uma afirmação falsa — vira ausência.
     */
    private static function salario(mixed $valor): ?float
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        $numero = (float) $valor;

        return $numero > 0 ? $numero : null;
    }

    private static function data(mixed $valor): ?Carbon
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        try {
            return Carbon::parse($valor);
        } catch (\Throwable) {
            return null;
        }
    }
}
