<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * O cadastro passa a exigir apenas e-mail, senha, CPF e consentimento.
     * O nome deixa de existir no momento da criação da conta.
     */
    public function up(): void
    {
        Schema::table('candidatos', function (Blueprint $table) {
            $table->string('nome')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('candidatos', function (Blueprint $table) {
            $table->string('nome')->nullable(false)->change();
        });
    }
};
