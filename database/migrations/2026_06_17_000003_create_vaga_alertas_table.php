<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaga_alertas', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->json('areas')->nullable();
            $table->json('modalidades')->nullable();
            $table->json('tipos')->nullable();
            $table->boolean('ativo')->default(true);
            $table->string('token', 64)->unique();
            $table->timestamps();

            $table->index('email');
            $table->index('ativo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaga_alertas');
    }
};
