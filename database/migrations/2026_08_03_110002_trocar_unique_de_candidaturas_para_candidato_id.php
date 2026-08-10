<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `candidatos.cpf` já é unique e `candidato_id` acabou de virar NOT NULL:
 * `unique(vaga_id, candidato_id)` preserva exatamente a mesma garantia que
 * `unique(vaga_id, cpf)` oferecia, só que passando pela conta em vez de por
 * um valor copiado — que a próxima migration derruba.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidaturas', function (Blueprint $table) {
            $table->dropUnique(['vaga_id', 'cpf']);
            $table->unique(['vaga_id', 'candidato_id']);
        });
    }

    public function down(): void
    {
        // Sem volta: a migration seguinte derruba `cpf`, que esta constraint
        // precisaria de volta para existir.
    }
};
