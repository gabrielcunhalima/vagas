<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->enum('perfil', ['coordenador', 'gestor', 'admin'])->default('coordenador');
                $table->boolean('ativo')->default(true);
                $table->string('cpf', 14)->nullable()->index();
                $table->rememberToken();
                $table->timestamps();
            });
        } elseif (!Schema::hasColumn('users', 'cpf')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('cpf', 14)->nullable()->after('ativo')->index();
            });
        }

        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // Volta coordenador_id/gestor_id para bigint, recria FKs.
        // Reason: estavam como int unsigned apontando para IDPessoaFisicaJuridica do MANAGER;
        // agora voltam a referenciar users.id (bigint) como originalmente.
        Schema::table('vagas', function (Blueprint $table) {
            $table->unsignedBigInteger('coordenador_id')->nullable()->change();
            $table->unsignedBigInteger('gestor_id')->nullable()->change();
        });

        if (!$this->foreignExists('vagas', 'vagas_coordenador_id_foreign')) {
            Schema::table('vagas', function (Blueprint $table) {
                $table->foreign('coordenador_id')->references('id')->on('users')->nullOnDelete();
            });
        }
        if (!$this->foreignExists('vagas', 'vagas_gestor_id_foreign')) {
            Schema::table('vagas', function (Blueprint $table) {
                $table->foreign('gestor_id')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Sem rollback completo — usuários cadastrados seriam perdidos.
    }

    private function foreignExists(string $table, string $name): bool
    {
        // information_schema não existe no SQLite (usado em testes); nesse caso nunca há FKs a verificar
        if (DB::getDriverName() === 'sqlite') {
            return false;
        }

        $row = DB::selectOne(
            "SELECT 1 AS x FROM information_schema.TABLE_CONSTRAINTS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?",
            [$table, $name]
        );
        return (bool) $row;
    }
};
