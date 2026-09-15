<?php

namespace App\Console\Commands;

use App\Models\Candidato;
use App\Models\CandidatoCurriculo;
use App\Support\Drhflow\CurriculoDrhflowRepository;
use App\Support\Drhflow\DrhflowIndisponivelException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Retenção de currículos: 6 meses sem uso e o PDF sai do sistema.
 *
 * "Uso" é o envio do arquivo ou uma candidatura feita com ele — cada candidatura
 * renova `renovado_em` para o dia em que foi enviada. Vale para todas as
 * versões, não só a vigente: uma versão antiga deixa de ser usada no dia em que
 * é substituída, e vence 6 meses depois da última candidatura que a levou.
 *
 * Só apaga o que o portal gravou. A pasta do CPF no servidor de arquivos também
 * guarda PDFs colocados pelo DRHFlow, com outros nomes, e esses não são deste
 * sistema — por isso a remoção é arquivo por arquivo, pelo caminho registrado,
 * e nunca da pasta.
 */
class RemoverCurriculosExpirados extends Command
{
    protected $signature = 'vagas:remover-curriculos-expirados
                            {--dry-run : Apenas relata o que seria removido}';

    protected $description = 'Remove currículos sem envio nem candidatura há mais de '.CandidatoCurriculo::RETENCAO_MESES.' meses';

    public function handle(): int
    {
        $simular = (bool) $this->option('dry-run');
        $disco = Storage::disk(Candidato::DISCO_CURRICULOS);

        $removidos = 0;
        $falhas = 0;

        CandidatoCurriculo::query()
            ->expirados()
            ->chunkById(200, function ($versoes) use ($simular, $disco, &$removidos, &$falhas) {
                foreach ($versoes as $versao) {
                    if ($simular) {
                        $ultimoUso = ($versao->renovado_em ?? $versao->enviado_em)->format('d/m/Y');
                        $this->line("  removeria: #{$versao->id} {$versao->path} (sem uso desde {$ultimoUso})");
                        $removidos++;

                        continue;
                    }

                    // O arquivo primeiro: se o servidor de arquivos falhar, a
                    // versão continua registrada e a rotina tenta de novo amanhã,
                    // em vez de deixar um PDF órfão que ninguém mais apagaria.
                    try {
                        if ($disco->exists($versao->path)) {
                            $disco->delete($versao->path);
                        }
                    } catch (\Throwable $e) {
                        $falhas++;
                        Log::error('Não foi possível remover o arquivo de um currículo expirado.', [
                            'curriculo_id' => $versao->id,
                            'candidato_id' => $versao->candidato_id,
                            'erro' => $e->getMessage(),
                        ]);

                        continue;
                    }

                    $this->esvaziarNoDrhflow($versao);

                    DB::transaction(function () use ($versao) {
                        // O perfil fica sem currículo e volta a pedir um novo
                        // antes da próxima candidatura.
                        Candidato::withTrashed()
                            ->whereKey($versao->candidato_id)
                            ->where('curriculo_atual_id', $versao->id)
                            ->update(['curriculo_atual_id' => null]);

                        $versao->delete();
                    });

                    $removidos++;
                }
            });

        if (! $simular && ($removidos > 0 || $falhas > 0)) {
            Log::info('Retenção de currículos executada.', ['removidos' => $removidos, 'falhas' => $falhas]);
        }

        $this->info(($simular ? '[simulação] ' : '')."{$removidos} currículo(s) removido(s), {$falhas} falha(s).");

        return $falhas > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Tira de `EN_UPLOAD_CURRICULO` a referência ao PDF que acabou de sair — só
     * se a linha ainda apontar para ele. Falha aqui não segura a retenção: o
     * arquivo já foi removido, e uma referência órfã é menos grave que reter.
     */
    private function esvaziarNoDrhflow(CandidatoCurriculo $versao): void
    {
        if (! preg_match('#^(\d{11})/([^/]+)$#', $versao->path, $partes)) {
            return;
        }

        try {
            app(CurriculoDrhflowRepository::class)->esvaziar($partes[1], somenteSeFor: $partes[2]);
        } catch (DrhflowIndisponivelException) {
            Log::warning('Currículo expirado removido, mas a referência no DRHFlow ficou.', [
                'curriculo_id' => $versao->id,
            ]);
        }
    }
}
