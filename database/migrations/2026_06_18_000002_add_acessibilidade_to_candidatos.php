<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidatos', function (Blueprint $table) {
            $table->boolean('possui_acessibilidade')->default(false)->after('pcd_tipo');
            $table->text('acessibilidade_detalhe')->nullable()->after('possui_acessibilidade');
        });
    }

    public function down(): void
    {
        Schema::table('candidatos', function (Blueprint $table) {
            $table->dropColumn(['possui_acessibilidade', 'acessibilidade_detalhe']);
        });
    }
};
