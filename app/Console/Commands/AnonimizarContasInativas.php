<?php

namespace App\Console\Commands;

use App\Models\Candidato;
use App\Notifications\Candidato\AvisoInatividadeCandidato;
use App\Services\AnonimizacaoService;
use Illuminate\Console\Command;

/**
 * Retenção de contas inativas.
 *
 * Substitui `vagas:anonimizar-candidaturas-antigas`, que anonimizava a cópia de
 * dados pessoais guardada em cada candidatura. Sem essa cópia, o alvo passa a ser
 * a CONTA — e a régua deixa de ser "a vaga encerrou" (que nada diz sobre o
 * candidato, ainda concorrendo a outras) e passa a ser "esta pessoa parou de usar
 * o portal".
 *
 * Duas fases, porque anonimizar é irreversível e não pode surpreender ninguém:
 *
 *   inativa há 2 anos ──► aviso por e-mail ──► 30 dias ──► anonimização
 *                              │
 *                              └─ qualquer acesso cancela (o login zera o aviso)
 */
class AnonimizarContasInativas extends Command
{
    protected $signature = 'vagas:anonimizar-contas-inativas
                            {--anos=2 : Tempo sem atividade que caracteriza inatividade}
                            {--carencia=30 : Dias entre o aviso e a anonimização}
                            {--dry-run : Apenas relata o que seria feito}';

    protected $description = 'Avisa e anonimiza contas de candidatos sem atividade, conforme a política de retenção (LGPD)';

    public function handle(AnonimizacaoService $anonimizacao): int
    {
        $anos     = max(1, (int) $this->option('anos'));
        $carencia = max(1, (int) $this->option('carencia'));
        $simular  = (bool) $this->option('dry-run');

        $limiteInatividade = now()->subYears($anos);

        $avisadas    = $this->avisar($limiteInatividade, $anos, $carencia, $simular);
        $anonimizadas = $this->anonimizar($limiteInatividade, $carencia, $simular, $anonimizacao);

        $this->info(($simular ? '[simulação] ' : '')
            . "{$avisadas} conta(s) avisada(s), {$anonimizadas} anonimizada(s).");

        return self::SUCCESS;
    }

    /** Fase 1 — quem cruzou o limite e ainda não foi avisado. */
    private function avisar(\DateTimeInterface $limite, int $anos, int $carencia, bool $simular): int
    {
        $total = 0;

        foreach ($this->candidatosInativos($limite) as $candidato) {
            if ($candidato->aviso_inatividade_em !== null) {
                continue;
            }

            $total++;

            if ($simular) {
                $this->line("  avisaria: {$candidato->email}");
                continue;
            }

            $candidato->notify(new AvisoInatividadeCandidato($carencia, $anos));

            // Sem tocar em `updated_at`: o carimbo é escrita da rotina, não uso da
            // conta, e não pode ser confundido com atividade do titular.
            Candidato::withoutTimestamps(
                fn () => $candidato->forceFill(['aviso_inatividade_em' => now()])->save()
            );
        }

        return $total;
    }

    /** Fase 2 — avisados há mais que a carência e que não voltaram. */
    private function anonimizar(
        \DateTimeInterface $limite,
        int $carencia,
        bool $simular,
        AnonimizacaoService $anonimizacao,
    ): int {
        $prazoAviso = now()->subDays($carencia);
        $total      = 0;

        foreach ($this->candidatosInativos($limite) as $candidato) {
            // O login zera o aviso, então chegar aqui significa que não houve retorno.
            if ($candidato->aviso_inatividade_em === null || $candidato->aviso_inatividade_em->gt($prazoAviso)) {
                continue;
            }

            $total++;

            if ($simular) {
                $this->line("  anonimizaria: {$candidato->email}");
                continue;
            }

            $anonimizacao->anonimizarCandidato($candidato);
        }

        return $total;
    }

    /**
     * Contas candidatas às duas fases.
     *
     * A atividade não está numa coluna só — login, edição de perfil e candidatura
     * contam igualmente —, então a régua fina fica em `ultimaAtividadeEm()`. A
     * consulta abaixo apenas reduz o conjunto antes disso.
     *
     * @return \Generator<Candidato>
     */
    private function candidatosInativos(\DateTimeInterface $limite): \Generator
    {
        $possiveis = Candidato::query()
            ->where(fn ($q) => $q
                ->whereNull('ultimo_acesso_em')
                ->orWhere('ultimo_acesso_em', '<=', $limite))
            ->orderBy('id')
            ->cursor();

        foreach ($possiveis as $candidato) {
            // Ainda concorrendo: a conta serve a um processo em andamento.
            if ($candidato->temProcessoEmAberto()) {
                continue;
            }

            if ($candidato->ultimaAtividadeEm()?->gt($limite)) {
                continue;
            }

            yield $candidato;
        }
    }
}
