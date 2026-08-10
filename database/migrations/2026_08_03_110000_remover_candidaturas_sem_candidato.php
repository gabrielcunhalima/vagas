<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Limpeza que precede o passo destrutivo: sem esta remoção, tornar
 * `candidato_id` NOT NULL falharia com as linhas que restarem sem vínculo.
 *
 * Toda candidatura nova já exige conta autenticada desde
 * `candidatura-vinculada-a-conta`; o que sobra aqui é herança de antes dessa
 * regra existir, e não há mais `cpf`/`email` física em `candidaturas` para
 * tentar reivindicá-la depois que a migration seguinte cair.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('candidaturas')->whereNull('candidato_id')->delete();
    }

    public function down(): void
    {
        // Sem volta: os registros apagados aqui não podem ser recriados.
    }
};
