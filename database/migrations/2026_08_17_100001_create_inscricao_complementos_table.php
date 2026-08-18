<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * O que a inscrição tem e o DRHFlow não comporta.
 *
 * `EN_CANDIDATO_VAGA_EMPREGO` não tem campo para carta de apresentação, para o
 * detalhamento do conflito de interesse nem para identificar qual versão de
 * currículo estava vigente no envio — e o versionamento de currículo é do
 * portal, não do DRHFlow.
 *
 * Fica de fora, deliberadamente, tudo que o DRHFlow já guarda: nome, e-mail,
 * telefone, endereço, aceite do código de conduta, data/hora/local de entrevista
 * e notas. Uma cópia divergiria em silêncio, e a capacidade
 * `candidatura-vinculada-a-conta` proíbe manter cópia do que tem campo na origem.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscricao_complementos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('candidato_id')
                ->constrained('candidatos')
                ->cascadeOnDelete();

            // Par de correlação com a origem — a mesma chave de
            // EN_CANDIDATO_VAGA_EMPREGO.
            $table->char('cpf', 11);
            $table->unsignedInteger('cd_vaga_emprego');

            $table->text('carta_apresentacao')->nullable();
            $table->boolean('conflito_interesse')->default(false);
            $table->text('conflito_interesse_detalhe')->nullable();

            // Qual PDF o processo recebeu. Se o candidato trocar o currículo
            // depois, esta continua apontando para o que foi enviado.
            $table->foreignId('curriculo_id_vigente')
                ->nullable()
                ->constrained('candidato_curriculos')
                ->nullOnDelete();

            $table->timestamp('enviada_em');
            $table->timestamps();

            // Espelha a chave primária da origem: um complemento por CPF + vaga.
            $table->unique(['cpf', 'cd_vaga_emprego']);
            $table->index('candidato_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscricao_complementos');
    }
};
