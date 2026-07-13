<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidatos', function (Blueprint $table) {
            $table->string('nome_social')->nullable()->after('nome');
            $table->string('nacionalidade')->nullable()->default('Brasileira')->after('nome_social');
            $table->string('nivel_escolaridade', 50)->nullable()->after('instituicao');
            $table->enum('situacao_curso', ['cursando', 'concluido'])->nullable()->after('nivel_escolaridade');
            $table->boolean('conflito_interesse')->default(false)->after('lgpd_consentimento_em');
            $table->text('conflito_interesse_detalhe')->nullable()->after('conflito_interesse');
            $table->timestamp('codigo_conduta_aceito_em')->nullable()->after('conflito_interesse_detalhe');
        });
    }

    public function down(): void
    {
        Schema::table('candidatos', function (Blueprint $table) {
            $table->dropColumn([
                'nome_social',
                'nacionalidade',
                'nivel_escolaridade',
                'situacao_curso',
                'conflito_interesse',
                'conflito_interesse_detalhe',
                'codigo_conduta_aceito_em',
            ]);
        });
    }
};
