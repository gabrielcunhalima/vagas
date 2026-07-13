<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vagas', function (Blueprint $table) {
            $table->id();

            $table->string('titulo');
            $table->text('descricao');
            $table->text('requisitos');
            $table->text('requisitos_desejaveis')->nullable();
            $table->text('beneficios')->nullable();

            $table->enum('tipo', ['estagio', 'emprego', 'bolsa'])->default('estagio');
            $table->string('area');
            $table->json('curso_desejado')->nullable();

            $table->decimal('remuneracao', 10, 2)->nullable();
            $table->integer('carga_horaria')->nullable();
            $table->enum('modalidade', ['presencial', 'remoto', 'hibrido'])->default('presencial');

            $table->string('cep', 9)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();
            $table->string('pais', 50)->default('Brasil');
            $table->string('local_trabalho')->nullable();

            $table->date('data_encerramento');
            $table->boolean('notificar_email')->default(true);

            $table->string('projeto_nome')->nullable();
            $table->string('projeto_codigo')->nullable();
            $table->unsignedBigInteger('coordenador_id')->nullable();
            $table->unsignedBigInteger('gestor_id')->nullable();

            $table->enum('status', [
                'rascunho',
                'aguardando_autorizacao',
                'ativa',
                'encerrada',
                'recusada',
                'inativa',
            ])->default('rascunho');

            $table->text('motivo_recusa')->nullable();
            $table->timestamp('autorizada_em')->nullable();
            $table->timestamp('encerrada_em')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('coordenador_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('gestor_id')->references('id')->on('users')->nullOnDelete();

            $table->index('status');
            $table->index('area');
            $table->index('tipo');
            $table->index('data_encerramento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vagas');
    }
};
