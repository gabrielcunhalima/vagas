<?php

namespace App\Providers;

use App\Models\Vagas\Candidatura;
use App\Policies\CandidaturaPolicy;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(Vite $vite): void
    {
        Schema::defaultStringLength(191);

        Gate::policy(Candidatura::class, CandidaturaPolicy::class);

        $this->redirecionarEmailsDeTeste();

        $vite->prefetch(concurrency: 3);
    }

    /**
     * Em ambiente de teste e homologação, todo e-mail vai para um único
     * destinatário.
     *
     * O banco de homologação tem 4 mil e-mails de candidatos reais. Sem esta
     * trava, um teste de inscrição ou a rotina de aviso de inatividade escreve
     * para pessoas de verdade — e não há como desfazer.
     *
     * Vazio em produção, onde os e-mails devem chegar a quem se destinam.
     */
    private function redirecionarEmailsDeTeste(): void
    {
        $destino = config('mail.always_to');

        if (filled($destino)) {
            Mail::alwaysTo($destino);
        }
    }
}
