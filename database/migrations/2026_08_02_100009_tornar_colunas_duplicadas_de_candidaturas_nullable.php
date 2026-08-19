<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ainda no passo 1 (aditivo): as colunas que vão sair passam a aceitar nulo.
 *
 * Sem isto o expand/contract não fecha — a aplicação já parou de escrever nestes
 * campos, mas o schema continuaria exigindo-os, e nenhuma candidatura nova seria
 * inserida até o passo destrutivo rodar. Tornar nulável é o que permite os dois
 * deploys serem independentes.
 *
 * As colunas continuam existindo e populadas: quem as derruba é o passo 4.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidaturas', function (Blueprint $table) {
            $table->string('nome')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('cpf', 14)->nullable()->change();
            $table->string('curso')->nullable()->change();
            $table->string('instituicao')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('candidaturas', function (Blueprint $table) {
            $table->string('nome')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('cpf', 14)->nullable(false)->change();
            $table->string('curso')->nullable(false)->change();
            $table->string('instituicao')->nullable(false)->change();
        });
    }
};
