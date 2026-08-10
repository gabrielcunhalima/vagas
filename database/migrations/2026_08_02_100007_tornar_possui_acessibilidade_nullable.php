<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Com `default(false)` não-nulo não há como distinguir "respondeu não" de
     * "ainda não respondeu" — e uma conta recém-criada pelo cadastro mínimo
     * apareceria como tendo respondido. A completude do perfil precisa dos três
     * estados, então nulo passa a significar "sem resposta".
     */
    public function up(): void
    {
        Schema::table('candidatos', function (Blueprint $table) {
            $table->boolean('possui_acessibilidade')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('candidatos', function (Blueprint $table) {
            $table->boolean('possui_acessibilidade')->default(false)->nullable(false)->change();
        });
    }
};
