<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `conflito_interesse` e o aceite do código de conduta migraram para
 * `candidaturas` em `add_aceites_por_vaga_to_candidaturas` — são respondidos
 * por vaga, não pela conta. Nenhum código lê ou escreve mais nestas colunas
 * em `candidatos`; são peso morto.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidatos', function (Blueprint $table) {
            $table->dropColumn([
                'conflito_interesse',
                'conflito_interesse_detalhe',
                'codigo_conduta_aceito_em',
            ]);
        });
    }

    public function down(): void
    {
        // Sem volta: já não há leitura ou escrita destas colunas para restaurar.
    }
};
