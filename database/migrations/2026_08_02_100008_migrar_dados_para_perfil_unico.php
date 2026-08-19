<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Passo 2 do plano de migração: move o que existe para a estrutura nova, sem
 * remover nada. As colunas antigas continuam de pé — quem as derruba é o passo 4.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->curriculosViramVersao1();
        $this->vincularAlertasAContas();
        $this->adotarCandidaturasOrfas();
    }

    /** O currículo único do perfil passa a ser a versão 1, e a vigente. */
    private function curriculosViramVersao1(): void
    {
        $comCurriculo = DB::table('candidatos')
            ->whereNotNull('curriculo_path')
            ->whereNull('curriculo_atual_id')
            ->get(['id', 'curriculo_path', 'curriculo_nome_original', 'created_at']);

        foreach ($comCurriculo as $candidato) {
            $versaoId = DB::table('candidato_curriculos')->insertGetId([
                'candidato_id' => $candidato->id,
                'path' => $candidato->curriculo_path,
                'nome_original' => $candidato->curriculo_nome_original ?? 'curriculo.pdf',
                'enviado_em' => $candidato->created_at ?? now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('candidatos')
                ->where('id', $candidato->id)
                ->update(['curriculo_atual_id' => $versaoId]);
        }
    }

    /**
     * Alertas cujo e-mail corresponde a uma conta passam a pertencer a ela. Os
     * demais são desativados: sem conta, não há mais como o destinatário
     * administrá-los, e o portal não pode seguir enviando o que ninguém controla.
     */
    private function vincularAlertasAContas(): void
    {
        DB::table('vaga_alertas')
            ->whereNull('candidato_id')
            ->orderBy('id')
            ->each(function ($alerta) {
                $candidatoId = DB::table('candidatos')
                    ->where('email', $alerta->email)
                    ->whereNull('deleted_at')
                    ->value('id');

                DB::table('vaga_alertas')
                    ->where('id', $alerta->id)
                    ->update($candidatoId
                        ? ['candidato_id' => $candidatoId]
                        : ['ativo' => false]);
            });
    }

    /**
     * Candidaturas anteriores sem conta cujo CPF **e** e-mail já batem com uma
     * conta existente são incorporadas a ela. As demais ficam como estão: enquanto
     * as colunas `cpf`/`email` existirem, quem se cadastrar depois ainda pode
     * adotá-las pelo caminho do cadastro.
     */
    private function adotarCandidaturasOrfas(): void
    {
        DB::table('candidaturas')
            ->whereNull('candidato_id')
            ->orderBy('id')
            ->each(function ($candidatura) {
                $candidatoId = DB::table('candidatos')
                    ->where('cpf', $candidatura->cpf)
                    ->where('email', $candidatura->email)
                    ->whereNull('deleted_at')
                    ->value('id');

                if ($candidatoId) {
                    DB::table('candidaturas')
                        ->where('id', $candidatura->id)
                        ->update(['candidato_id' => $candidatoId]);
                }
            });
    }

    /**
     * Reversível apenas no que é ponteiro. As versões criadas são descartadas e o
     * perfil volta a apontar para o currículo único das colunas antigas, que
     * seguem intactas neste passo.
     */
    public function down(): void
    {
        DB::table('candidatos')->update(['curriculo_atual_id' => null]);
        DB::table('candidato_curriculos')->delete();
    }
};
