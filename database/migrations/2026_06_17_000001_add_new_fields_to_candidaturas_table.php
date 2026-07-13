<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidaturas', function (Blueprint $table) {
            $table->string('linkedin', 255)->nullable()->after('carta_apresentacao');
            $table->decimal('pretensao_salarial', 10, 2)->nullable()->after('linkedin');
            $table->string('disponibilidade', 50)->nullable()->after('pretensao_salarial');
            $table->boolean('pcd')->default(false)->after('disponibilidade');
            $table->string('pcd_tipo', 100)->nullable()->after('pcd');
        });
    }

    public function down(): void
    {
        Schema::table('candidaturas', function (Blueprint $table) {
            $table->dropColumn(['linkedin', 'pretensao_salarial', 'disponibilidade', 'pcd', 'pcd_tipo']);
        });
    }
};
