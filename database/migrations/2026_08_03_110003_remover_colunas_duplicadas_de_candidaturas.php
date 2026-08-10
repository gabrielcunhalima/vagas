<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `Candidatura::CAMPOS_DO_PERFIL` já delega toda leitura destes campos para
 * `candidato` desde a change `cadastro-minimo-perfil-unico` — as colunas
 * físicas eram peso morto, nuláveis desde a migration
 * `2026_08_02_100009_tornar_colunas_duplicadas_de_candidaturas_nullable`
 * justamente para permitir este corte em um deploy separado.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidaturas', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['cpf']);

            $table->dropColumn([
                'nome', 'email', 'cpf', 'telefone', 'linkedin',
                'curso', 'instituicao', 'semestre', 'previsao_conclusao',
                'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'pais',
                'pretensao_salarial', 'disponibilidade', 'pcd', 'pcd_tipo',
                'curriculo_path', 'curriculo_nome_original',
            ]);
        });
    }

    public function down(): void
    {
        // Sem volta: os dados que estas colunas guardavam não sobrevivem em
        // nenhum outro lugar do banco.
    }
};
