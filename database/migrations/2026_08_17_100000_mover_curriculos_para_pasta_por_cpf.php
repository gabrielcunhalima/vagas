<?php

use App\Models\Candidato;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Move os currículos já enviados de `candidatos/curriculos` (disco `local`) para
 * a pasta do CPF no novo disco `curriculos`, reescrevendo `candidato_curriculos.path`.
 *
 * Nada é apagado. Um registro cujo arquivo não seja encontrado fica exatamente
 * como está e a ocorrência é registrada — o `path` antigo continua sendo a
 * melhor pista para recuperá-lo depois.
 */
return new class extends Migration
{
    private const CAMINHO_ANTIGO = 'candidatos/curriculos';

    public function up(): void
    {
        $origem = Storage::disk('local');
        $destino = Storage::disk(Candidato::DISCO_CURRICULOS);

        $naoEncontrados = [];
        $movidos = 0;

        DB::table('candidato_curriculos')
            ->join('candidatos', 'candidatos.id', '=', 'candidato_curriculos.candidato_id')
            ->select([
                'candidato_curriculos.id',
                'candidato_curriculos.path',
                'candidatos.cpf',
                'candidatos.id as candidato_id',
            ])
            ->orderBy('candidato_curriculos.id')
            ->chunk(200, function ($versoes) use ($origem, $destino, &$naoEncontrados, &$movidos) {
                foreach ($versoes as $versao) {
                    // Só o que ainda está no layout antigo.
                    if (! Str::startsWith($versao->path, self::CAMINHO_ANTIGO)) {
                        continue;
                    }

                    if (! $origem->exists($versao->path)) {
                        $naoEncontrados[] = ['id' => $versao->id, 'path' => $versao->path];

                        continue;
                    }

                    $pasta = preg_replace('/\D/', '', (string) $versao->cpf) ?: (string) $versao->candidato_id;
                    $novoCaminho = $pasta.'/'.Str::uuid().'.pdf';

                    $destino->put($novoCaminho, $origem->get($versao->path));

                    DB::table('candidato_curriculos')
                        ->where('id', $versao->id)
                        ->update(['path' => $novoCaminho]);

                    // O original permanece: se algo der errado no meio da
                    // migração, os arquivos ainda estão inteiros no lugar antigo.
                    $movidos++;
                }
            });

        if ($naoEncontrados !== []) {
            Log::warning('Currículos sem arquivo correspondente na migração de pastas por CPF.', [
                'quantidade' => count($naoEncontrados),
                'registros' => $naoEncontrados,
            ]);
        }

        Log::info('Migração de currículos para pasta por CPF concluída.', [
            'movidos' => $movidos,
            'sem_arquivo' => count($naoEncontrados),
        ]);
    }

    public function down(): void
    {
        $antigo = Storage::disk('local');
        $atual = Storage::disk(Candidato::DISCO_CURRICULOS);

        DB::table('candidato_curriculos')
            ->select(['id', 'path', 'nome_original'])
            ->orderBy('id')
            ->chunk(200, function ($versoes) use ($antigo, $atual) {
                foreach ($versoes as $versao) {
                    // Já estava no layout antigo antes desta migração.
                    if (Str::startsWith($versao->path, self::CAMINHO_ANTIGO)) {
                        continue;
                    }

                    if (! $atual->exists($versao->path)) {
                        continue;
                    }

                    $caminhoAntigo = self::CAMINHO_ANTIGO.'/'.basename($versao->path);

                    $antigo->put($caminhoAntigo, $atual->get($versao->path));

                    DB::table('candidato_curriculos')
                        ->where('id', $versao->id)
                        ->update(['path' => $caminhoAntigo]);
                }
            });
    }
};
