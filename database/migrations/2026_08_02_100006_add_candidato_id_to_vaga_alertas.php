<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * O alerta deixa de ser chaveado por um e-mail solto e passa a pertencer a
     * uma conta. Nullable neste passo: os alertas existentes só são vinculados
     * (ou desativados) na migração de dados do grupo 11.
     */
    public function up(): void
    {
        Schema::table('vaga_alertas', function (Blueprint $table) {
            $table->foreignId('candidato_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('candidatos')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vaga_alertas', function (Blueprint $table) {
            $table->dropForeign(['candidato_id']);
            $table->dropColumn('candidato_id');
        });
    }
};
