<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Formação deixa de ser um conjunto fixo de colunas em `candidatos` e passa a
 * ser uma lista (1:N): um candidato pode ter zero, uma ou várias formações.
 * A formação já cadastrada por candidatos existentes é migrada como a
 * primeira (e única) entrada da lista de cada um.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidato_formacoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('candidato_id')
                ->constrained('candidatos')
                ->cascadeOnDelete();

            $table->string('nivel_escolaridade', 50)->nullable();
            $table->enum('situacao_curso', ['cursando', 'concluido'])->nullable();
            $table->string('curso')->nullable();
            $table->string('instituicao')->nullable();
            $table->string('semestre', 10)->nullable();
            $table->date('previsao_conclusao')->nullable();

            $table->timestamps();
        });

        $this->migrarFormacoesExistentes();

        Schema::table('candidatos', function (Blueprint $table) {
            $table->text('outras_formacoes_mec')->nullable()->after('acessibilidade_detalhe');
            $table->text('outros_cursos')->nullable()->after('outras_formacoes_mec');
        });

        Schema::table('candidatos', function (Blueprint $table) {
            $table->dropColumn([
                'curso',
                'instituicao',
                'nivel_escolaridade',
                'situacao_curso',
                'semestre',
                'previsao_conclusao',
            ]);
        });
    }

    private function migrarFormacoesExistentes(): void
    {
        DB::table('candidatos')
            ->where(function ($q) {
                $q->whereNotNull('curso')->orWhereNotNull('instituicao');
            })
            ->orderBy('id')
            ->get(['id', 'nivel_escolaridade', 'situacao_curso', 'curso', 'instituicao', 'semestre', 'previsao_conclusao'])
            ->each(function ($candidato) {
                DB::table('candidato_formacoes')->insert([
                    'candidato_id' => $candidato->id,
                    'nivel_escolaridade' => $candidato->nivel_escolaridade,
                    'situacao_curso' => $candidato->situacao_curso,
                    'curso' => $candidato->curso,
                    'instituicao' => $candidato->instituicao,
                    'semestre' => $candidato->semestre,
                    'previsao_conclusao' => $candidato->previsao_conclusao,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('candidatos', function (Blueprint $table) {
            $table->string('curso')->nullable();
            $table->string('instituicao')->nullable();
            $table->string('nivel_escolaridade', 50)->nullable();
            $table->enum('situacao_curso', ['cursando', 'concluido'])->nullable();
            $table->string('semestre', 10)->nullable();
            $table->date('previsao_conclusao')->nullable();
        });

        // Só a primeira formação (por ordem de cadastro) de cada candidato volta
        // para as colunas escalares — quaisquer outras se perdem no rollback.
        DB::table('candidato_formacoes')
            ->orderBy('id')
            ->get()
            ->groupBy('candidato_id')
            ->each(function ($formacoes, $candidatoId) {
                $primeira = $formacoes->first();

                DB::table('candidatos')->where('id', $candidatoId)->update([
                    'curso' => $primeira->curso,
                    'instituicao' => $primeira->instituicao,
                    'nivel_escolaridade' => $primeira->nivel_escolaridade,
                    'situacao_curso' => $primeira->situacao_curso,
                    'semestre' => $primeira->semestre,
                    'previsao_conclusao' => $primeira->previsao_conclusao,
                ]);
            });

        Schema::table('candidatos', function (Blueprint $table) {
            $table->dropColumn(['outras_formacoes_mec', 'outros_cursos']);
        });

        Schema::dropIfExists('candidato_formacoes');
    }
};
