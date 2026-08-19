<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Você tem vínculo com alguém da equipe DESTA vaga?" tem resposta diferente
     * a cada inscrição, e o aceite do código de conduta é ato do processo
     * seletivo. Ambos saem da conta e passam a ser respondidos por candidatura.
     *
     * As colunas correspondentes em `candidatos` só caem no passo 4 do plano de
     * migração, depois que nada mais as lê.
     */
    public function up(): void
    {
        Schema::table('candidaturas', function (Blueprint $table) {
            $table->boolean('conflito_interesse')->default(false)->after('carta_apresentacao');
            $table->text('conflito_interesse_detalhe')->nullable()->after('conflito_interesse');
            $table->timestamp('codigo_conduta_aceito_em')->nullable()->after('conflito_interesse_detalhe');
        });
    }

    public function down(): void
    {
        Schema::table('candidaturas', function (Blueprint $table) {
            $table->dropColumn([
                'conflito_interesse',
                'conflito_interesse_detalhe',
                'codigo_conduta_aceito_em',
            ]);
        });
    }
};
