<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Retenção: avisa contas inativas há 2 anos e anonimiza 30 dias depois do aviso.
 * Substitui `vagas:anonimizar-candidaturas-antigas`, que perdeu a premissa quando
 * as candidaturas deixaram de guardar cópia de dados pessoais.
 */
Schedule::command('vagas:anonimizar-contas-inativas')->daily();

/*
 * Retenção de currículos: remove o PDF sem envio nem candidatura há 6 meses.
 * Ver CandidatoCurriculo::RETENCAO_MESES.
 */
Schedule::command('vagas:remover-curriculos-expirados')->dailyAt('03:00');
