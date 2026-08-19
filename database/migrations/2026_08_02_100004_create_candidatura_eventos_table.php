<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registra a DECISÃO, não a IDENTIDADE.
     *
     * Nenhum campo pessoal do candidato entra aqui — nome, CPF, e-mail e endereço
     * nunca são copiados. Por isso a tabela não precisa entrar na rotina de
     * anonimização: ela responde quem decidiu o quê, quando, e sobre qual versão
     * de currículo. É o que substitui o snapshot que `candidaturas` guardava.
     */
    public function up(): void
    {
        Schema::create('candidatura_eventos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('candidatura_id')
                ->constrained('candidaturas')
                ->cascadeOnDelete();

            // 'submissao' | 'transicao_status'
            $table->string('tipo', 30);

            $table->string('status_anterior', 20)->nullable();
            $table->string('status_novo', 20)->nullable();

            // Coordenador que realizou a transição; nulo quando o autor é o candidato.
            $table->foreignId('autor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('curriculo_id_vigente')
                ->nullable()
                ->constrained('candidato_curriculos')
                ->nullOnDelete();

            $table->text('observacao')->nullable();
            $table->timestamp('ocorrido_em');

            $table->timestamps();

            $table->index(['candidatura_id', 'ocorrido_em']);
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidatura_eventos');
    }
};
