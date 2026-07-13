<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidatos', function (Blueprint $table) {
            $table->id();

            // Autenticação
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();

            // Dados pessoais persistentes
            $table->string('nome');
            $table->string('cpf', 14)->unique();
            $table->string('telefone', 20)->nullable();
            $table->string('linkedin')->nullable();

            // Dados acadêmicos persistentes
            $table->string('curso')->nullable();
            $table->string('instituicao')->nullable();
            $table->string('semestre', 10)->nullable();
            $table->date('previsao_conclusao')->nullable();

            // Endereço persistente
            $table->string('cep', 9)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();
            $table->string('pais', 50)->default('Brasil');

            // Preferências de candidatura
            $table->decimal('pretensao_salarial', 10, 2)->nullable();
            $table->string('disponibilidade', 50)->nullable();
            $table->boolean('pcd')->default(false);
            $table->string('pcd_tipo', 100)->nullable();

            // Currículo padrão
            $table->string('curriculo_path')->nullable();
            $table->string('curriculo_nome_original')->nullable();

            // LGPD
            $table->boolean('lgpd_consentimento')->default(false);
            $table->timestamp('lgpd_consentimento_em')->nullable();
            $table->boolean('ativo')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index('cpf');
        });

        // Liga candidaturas ao candidato (nullable — candidaturas antigas permanecem sem vínculo)
        Schema::table('candidaturas', function (Blueprint $table) {
            $table->foreignId('candidato_id')
                  ->nullable()
                  ->after('vaga_id')
                  ->constrained('candidatos')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('candidaturas', function (Blueprint $table) {
            $table->dropForeign(['candidato_id']);
            $table->dropColumn('candidato_id');
        });

        Schema::dropIfExists('candidatos');
    }
};
