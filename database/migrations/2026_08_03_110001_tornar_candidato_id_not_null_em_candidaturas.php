<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A garantia contra dupla inscrição passa a se apoiar na conta, não em uma
 * cópia de CPF que está saindo — ver a próxima migration, que troca a
 * unicidade. Depende da limpeza anterior já ter rodado.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidaturas', function (Blueprint $table) {
            $table->foreignId('candidato_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        // Sem volta a partir daqui: a migration seguinte troca a unicidade
        // sobre esta coluna e as próximas derrubam `cpf`, que o rollback
        // precisaria de volta para repor a constraint antiga.
    }
};
