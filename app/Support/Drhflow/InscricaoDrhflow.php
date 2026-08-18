<?php

namespace App\Support\Drhflow;

use Illuminate\Support\Carbon;

/**
 * Uma inscrição do candidato, como o DRHFlow a guarda.
 *
 * O andamento é **derivado** das colunas que o RH preenche — o portal não tem
 * campo de status próprio. Enquanto o DRHFlow não expuser uma coluna que
 * determine o desfecho, aprovação e reprovação não são apresentadas: `FG_SEL`
 * existe e é usado (39 linhas com '1', 73 com '0'), mas nenhuma fonte documenta
 * o que significa, e adivinhar significaria dizer a alguém que foi reprovado.
 */
class InscricaoDrhflow
{
    public const RECEBIDA = 'recebida';

    public const ENTREVISTA_MARCADA = 'entrevista_marcada';

    public const AVALIACAO_CONCLUIDA = 'avaliacao_concluida';

    public const ROTULOS = [
        self::RECEBIDA => 'Inscrição recebida',
        self::ENTREVISTA_MARCADA => 'Entrevista marcada',
        self::AVALIACAO_CONCLUIDA => 'Avaliação concluída',
    ];

    public function __construct(
        public readonly string $cpf,
        public readonly int $cdVagaEmprego,
        public readonly ?Carbon $enviadaEm,
        public readonly ?Carbon $entrevistaData,
        public readonly ?string $entrevistaHora,
        public readonly ?string $entrevistaLocal,
        public readonly bool $avaliacaoConcluida,
        public readonly ?VagaDrhflow $vaga = null,
    ) {}

    public static function daLinha(object $linha, ?VagaDrhflow $vaga = null): self
    {
        return new self(
            cpf: trim((string) $linha->NU_CPF),
            cdVagaEmprego: (int) $linha->CD_VAGA_EMPREGO,
            enviadaEm: self::data($linha->DT_CADASTRO ?? null),
            entrevistaData: self::data($linha->DT_ENTREVISTA ?? null),
            entrevistaHora: self::texto($linha->HR_ENTREVISTA ?? null),
            entrevistaLocal: self::texto($linha->DE_LOCAL_ENTREVISTA ?? null),
            // Média preenchida é o único sinal documentado de avaliação feita.
            avaliacaoConcluida: ($linha->VL_MEDIA_AVALIACAO ?? null) !== null,
            vaga: $vaga,
        );
    }

    /** Com a vaga anexada — usado depois de resolver o cargo no repositório. */
    public function comVaga(?VagaDrhflow $vaga): self
    {
        return new self(
            $this->cpf, $this->cdVagaEmprego, $this->enviadaEm,
            $this->entrevistaData, $this->entrevistaHora, $this->entrevistaLocal,
            $this->avaliacaoConcluida, $vaga,
        );
    }

    /**
     * O andamento que o candidato vê, derivado do que o RH registrou.
     *
     * A ordem importa: avaliação concluída vence entrevista marcada, que vence
     * inscrição recebida.
     */
    public function andamento(): string
    {
        if ($this->avaliacaoConcluida) {
            return self::AVALIACAO_CONCLUIDA;
        }

        if ($this->entrevistaData !== null) {
            return self::ENTREVISTA_MARCADA;
        }

        return self::RECEBIDA;
    }

    public function andamentoRotulo(): string
    {
        return self::ROTULOS[$this->andamento()];
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'cd_vaga_emprego' => $this->cdVagaEmprego,
            'andamento' => $this->andamento(),
            'andamento_rotulo' => $this->andamentoRotulo(),
            'enviada_em' => $this->enviadaEm?->toIso8601String(),
            'entrevista_data' => $this->entrevistaData?->toIso8601String(),
            'entrevista_hora' => $this->entrevistaHora,
            'entrevista_local' => $this->entrevistaLocal,
            'vaga' => $this->vaga?->toArray(),
        ];
    }

    private static function texto(mixed $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $limpo = trim((string) $valor);

        return $limpo === '' ? null : $limpo;
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
