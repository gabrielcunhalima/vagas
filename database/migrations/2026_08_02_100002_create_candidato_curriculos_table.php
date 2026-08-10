<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Currículo versionado: cada envio cria uma versão nova e nenhum arquivo é
     * sobrescrito. Sem isso, um evento de decisão apontaria para um arquivo que
     * já foi substituído — o registro existiria sem significar nada.
     */
    public function up(): void
    {
        Schema::create('candidato_curriculos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('candidato_id')
                  ->constrained('candidatos')
                  ->cascadeOnDelete();

            $table->string('path');
            $table->string('nome_original');
            $table->timestamp('enviado_em');

            $table->timestamps();

            $table->index(['candidato_id', 'enviado_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidato_curriculos');
    }
};
