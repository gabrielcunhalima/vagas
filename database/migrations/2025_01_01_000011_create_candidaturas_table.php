<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidaturas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vaga_id')
                  ->constrained('vagas')
                  ->cascadeOnDelete();

            $table->string('nome');
            $table->string('email');
            $table->string('cpf', 14);
            $table->string('telefone', 20)->nullable();
            $table->string('curso');
            $table->string('instituicao');
            $table->string('semestre', 10)->nullable();
            $table->date('previsao_conclusao')->nullable();
            $table->text('carta_apresentacao')->nullable();

            $table->string('cep', 9)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();
            $table->string('pais', 50)->default('Brasil');

            $table->string('curriculo_path')->nullable();
            $table->string('curriculo_nome_original')->nullable();

            $table->enum('status', [
                'recebida',
                'em_analise',
                'entrevista',
                'aprovado',
                'reprovado',
            ])->default('recebida');

            $table->dateTime('entrevista_data')->nullable();
            $table->string('entrevista_local')->nullable();
            $table->text('entrevista_observacoes')->nullable();
            $table->text('observacoes_internas')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['vaga_id', 'cpf']);
            $table->index('status');
            $table->index('email');
            $table->index('cpf');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidaturas');
    }
};
