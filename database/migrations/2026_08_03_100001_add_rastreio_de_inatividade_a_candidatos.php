<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sustenta a política de retenção de contas inativas.
 *
 * `ultimo_acesso_em` registra o login. Não é a única forma de atividade — editar o
 * perfil e candidatar-se também contam —, mas é a única que nenhuma outra coluna
 * já capturava.
 *
 * `aviso_inatividade_em` marca quando o aviso prévio foi enviado. Sem isso não há
 * como respeitar a carência de 30 dias nem evitar reenviar o aviso todo dia.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidatos', function (Blueprint $table) {
            $table->timestamp('ultimo_acesso_em')->nullable()->after('ativo');
            $table->timestamp('aviso_inatividade_em')->nullable()->after('ultimo_acesso_em');
        });

        // Contas existentes nunca "acessaram" pelo novo rastreio; a criação serve
        // de piso para elas não entrarem na primeira varredura como inativas de anos.
        Schema::getConnection()
            ->table('candidatos')
            ->whereNull('ultimo_acesso_em')
            ->update(['ultimo_acesso_em' => now()]);
    }

    public function down(): void
    {
        Schema::table('candidatos', function (Blueprint $table) {
            $table->dropColumn(['ultimo_acesso_em', 'aviso_inatividade_em']);
        });
    }
};
