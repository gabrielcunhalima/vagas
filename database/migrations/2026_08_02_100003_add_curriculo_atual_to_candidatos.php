<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * O perfil aponta para exatamente uma versão vigente — a que todo consumidor
     * dos dados do candidato lê.
     *
     * nullOnDelete: apagar a versão vigente deixa o perfil sem currículo (e,
     * portanto, incompleto), em vez de apagar o candidato junto.
     */
    public function up(): void
    {
        Schema::table('candidatos', function (Blueprint $table) {
            $table->foreignId('curriculo_atual_id')
                  ->nullable()
                  ->after('curriculo_nome_original')
                  ->constrained('candidato_curriculos')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('candidatos', function (Blueprint $table) {
            $table->dropForeign(['curriculo_atual_id']);
            $table->dropColumn('curriculo_atual_id');
        });
    }
};
