<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vaga_alertas', function (Blueprint $table) {
            $table->boolean('lgpd_consentimento')->default(false)->after('token');
            $table->timestamp('lgpd_consentimento_em')->nullable()->after('lgpd_consentimento');
        });
    }

    public function down(): void
    {
        Schema::table('vaga_alertas', function (Blueprint $table) {
            $table->dropColumn(['lgpd_consentimento', 'lgpd_consentimento_em']);
        });
    }
};
