<?php

namespace App\Console\Commands;

use App\Models\Vagas\Candidatura;
use App\Services\AnonimizacaoService;
use Illuminate\Console\Command;

class AnonimizarCandidaturasAntigas extends Command
{
    protected $signature = 'vagas:anonimizar-candidaturas-antigas {--dias=180}';

    protected $description = 'Anonimiza dados pessoais de candidaturas de vagas encerradas há mais de N dias (LGPD, retenção)';

    public function handle(AnonimizacaoService $anonimizacao): int
    {
        $dias = (int) $this->option('dias');
        $limite = now()->subDays($dias);

        $candidaturas = Candidatura::whereHas('vaga', function ($query) use ($limite) {
            $query->where('status', 'encerrada')->where('encerrada_em', '<=', $limite);
        })
            ->where('email', 'not like', '%@removido.invalid')
            ->get();

        if ($candidaturas->isEmpty()) {
            $this->info('Nenhuma candidatura elegível para anonimização.');
            return self::SUCCESS;
        }

        foreach ($candidaturas as $candidatura) {
            $anonimizacao->anonimizarCandidatura($candidatura);
        }

        $this->info("{$candidaturas->count()} candidatura(s) anonimizada(s) (vagas encerradas há mais de {$dias} dias).");

        return self::SUCCESS;
    }
}
