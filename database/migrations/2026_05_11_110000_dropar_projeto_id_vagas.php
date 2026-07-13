<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('vagas', 'projeto_id')) {
            Schema::table('vagas', function (Blueprint $table) {
                $table->dropIndex(['projeto_id']);
                $table->dropColumn('projeto_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('vagas', function (Blueprint $table) {
            $table->unsignedInteger('projeto_id')->nullable()->after('projeto_codigo');
            $table->index('projeto_id');
        });
    }
};
