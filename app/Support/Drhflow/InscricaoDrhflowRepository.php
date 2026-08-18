<?php

namespace App\Support\Drhflow;

use App\Models\Candidato;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * A escrita do portal no DRHFlow — a única que existe.
 *
 * Toda operação daqui atinge `EN_CANDIDATO_VAGA_EMPREGO` e apenas a linha do par
 * CPF + vaga informado. Não há `DELETE` em lugar nenhum: registros de processo
 * pertencem ao RH, e a capacidade `vagas-drhflow` proíbe removê-los.
 *
 * O `UPDATE` nunca é da linha inteira. O RH grava datas de entrevista e notas
 * nessa mesma linha, e um update completo do portal apagaria o trabalho dele —
 * por isso toda atualização parte de uma lista explícita de colunas do candidato.
 */
class InscricaoDrhflowRepository
{
    public function __construct(
        private readonly MapeadorInscricao $mapeador = new MapeadorInscricao,
        private readonly ?string $conexao = null,
    ) {}

    /** Já existe inscrição deste CPF nesta vaga — inclusive feita fora do portal. */
    public function existe(string $cpf, int $cdVagaEmprego): bool
    {
        return $this->protegido('verificação de inscrição', function () use ($cpf, $cdVagaEmprego) {
            return $this->tabela()
                ->where('NU_CPF', $cpf)
                ->where('CD_VAGA_EMPREGO', $cdVagaEmprego)
                ->exists();
        });
    }

    /**
     * Grava a inscrição.
     *
     * Devolve `true` quando a linha foi criada e `false` quando já existia — o
     * segundo caso não é falha: ele é a resposta a "já inscrito".
     *
     * A verificação prévia resolve o caso comum; a captura de chave duplicada
     * resolve a corrida entre dois envios simultâneos, em que os dois passariam
     * pela verificação e só um pode criar a linha.
     *
     * @throws DrhflowIndisponivelException
     */
    public function criar(Candidato $candidato, int $cdVagaEmprego, bool $aceitouCodigoConduta): bool
    {
        $cpf = MapeadorInscricao::cpf($candidato);

        if ($this->existe($cpf, $cdVagaEmprego)) {
            return false;
        }

        $linha = $this->mapeador->linhaDeInscricao($candidato, $cdVagaEmprego, $aceitouCodigoConduta);

        try {
            // Lista explícita de colunas: o array do mapeador é o contrato, e
            // nenhuma coluna de entrevista, avaliação ou nota está nele.
            $this->tabela()->insert($linha);

            return true;
        } catch (QueryException $e) {
            if ($this->ehChaveDuplicada($e)) {
                // Corrida: o outro envio chegou primeiro. A linha existe, que é
                // o resultado desejado — não é erro.
                Log::info('Envio concorrente de inscrição resolvido como já inscrito.', [
                    'cd_vaga_emprego' => $cdVagaEmprego,
                ]);

                return false;
            }

            throw DrhflowIndisponivelException::aoConsultar('gravação da inscrição', $e);
        } catch (Throwable $e) {
            Log::error('Falha ao gravar inscrição no DRHFlow.', [
                'cd_vaga_emprego' => $cdVagaEmprego,
                'erro' => $e->getMessage(),
            ]);

            throw DrhflowIndisponivelException::aoConsultar('gravação da inscrição', $e);
        }
    }

    /**
     * Reenvio: atualiza só os dados do candidato na linha existente.
     *
     * O `WHERE` é o par completo, e as colunas são exatamente as de
     * `MapeadorInscricao::colunasDoCandidato()`. Data de entrevista, local,
     * notas e média não estão lá e não podem ser alcançadas por este caminho.
     *
     * @throws DrhflowIndisponivelException
     */
    public function atualizar(Candidato $candidato, int $cdVagaEmprego): int
    {
        return $this->protegido('atualização da inscrição', function () use ($candidato, $cdVagaEmprego) {
            $colunas = $this->mapeador->colunasDoCandidato($candidato);

            $colunas['DT_ALTERACAO'] = now();
            $colunas['ID_USUARIO_ALT'] = mb_substr((string) config('drhflow.id_usuario_cad', 'PORTALVAGAS'), 0, 25);

            return $this->tabela()
                ->where('NU_CPF', MapeadorInscricao::cpf($candidato))
                ->where('CD_VAGA_EMPREGO', $cdVagaEmprego)
                ->update($colunas);
        });
    }

    /**
     * Uma inscrição do candidato, ou nulo.
     *
     * @throws DrhflowIndisponivelException
     */
    public function buscar(string $cpf, int $cdVagaEmprego): ?InscricaoDrhflow
    {
        return $this->protegido('consulta da inscrição', function () use ($cpf, $cdVagaEmprego) {
            $linha = $this->tabela()
                ->where('NU_CPF', $cpf)
                ->where('CD_VAGA_EMPREGO', $cdVagaEmprego)
                ->first($this->colunasDeLeitura());

            return $linha === null ? null : InscricaoDrhflow::daLinha($linha);
        });
    }

    /**
     * Todas as inscrições de um CPF, da mais recente para a mais antiga.
     *
     * @return Collection<int, InscricaoDrhflow>
     *
     * @throws DrhflowIndisponivelException
     */
    public function doCpf(string $cpf): Collection
    {
        return $this->protegido('inscrições do candidato', function () use ($cpf) {
            return $this->tabela()
                ->where('NU_CPF', $cpf)
                ->orderByDesc('DT_CADASTRO')
                ->get($this->colunasDeLeitura())
                ->map(fn (object $linha) => InscricaoDrhflow::daLinha($linha));
        });
    }

    /**
     * Anonimiza as linhas de um CPF, mantendo o registro do processo.
     *
     * O RH pediu que nada seja apagado do banco; a LGPD exige que a exclusão de
     * conta remova os dados pessoais. As duas se conciliam substituindo apenas
     * os campos de identificação por marcadores: `NU_CPF`, `CD_VAGA_EMPREGO` e
     * tudo que o RH preencheu permanecem intactos.
     *
     * @return int linhas anonimizadas
     */
    public function anonimizar(string $cpf, string $marcador): int
    {
        return $this->protegido('anonimização da inscrição', function () use ($cpf, $marcador) {
            return $this->tabela()
                ->where('NU_CPF', $cpf)
                ->update([
                    'NM_CANDIDATO' => 'Candidato excluído',
                    'NM_SOCIAL' => null,
                    'DE_EMAIL' => mb_substr($marcador.'@removido.invalid', 0, 150),
                    'NU_TELEFONE_CELULAR' => null,
                    'NU_TELEFONE_FIXO' => null,
                    'NU_CEP' => null,
                    'NM_LOGRADOURO' => null,
                    'NU_LOGRADOURO' => null,
                    'NM_COMPLEMENTO_LOGRADOURO' => null,
                    'NM_BAIRRO' => null,
                    'CD_UF_ENDERECO' => null,
                    'CD_MUNICIPIO_ENDERECO' => null,
                    'DE_OUTRAS_FORMACOES' => null,
                    'DE_OUTROS_CURSOS' => null,
                    'DT_ALTERACAO' => now(),
                    'ID_USUARIO_ALT' => mb_substr((string) config('drhflow.id_usuario_cad', 'PORTALVAGAS'), 0, 25),
                ]);
        });
    }

    /**
     * Só o que o candidato precisa ver. As notas individuais são do RH e não
     * saem daqui; da média sai apenas o fato de existir.
     *
     * @return list<string>
     */
    private function colunasDeLeitura(): array
    {
        return [
            'NU_CPF', 'CD_VAGA_EMPREGO', 'DT_CADASTRO',
            'DT_ENTREVISTA', 'HR_ENTREVISTA', 'DE_LOCAL_ENTREVISTA',
            'VL_MEDIA_AVALIACAO',
        ];
    }

    /** Violação de chave primária composta (`NU_CPF` + `CD_VAGA_EMPREGO`). */
    private function ehChaveDuplicada(QueryException $e): bool
    {
        // 23000 é o SQLSTATE de violação de integridade, comum a SQL Server e
        // SQLite; 2627/2601 são os códigos nativos do SQL Server para chave
        // primária e índice único duplicados.
        $codigos = ['23000', '23505'];
        $mensagem = $e->getMessage();

        return in_array((string) $e->getCode(), $codigos, true)
            || str_contains($mensagem, '2627')
            || str_contains($mensagem, '2601')
            || str_contains($mensagem, 'UNIQUE constraint failed')
            || str_contains($mensagem, 'Violation of PRIMARY KEY');
    }

    private function protegido(string $operacao, callable $consulta): mixed
    {
        try {
            return $consulta();
        } catch (DrhflowIndisponivelException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error("DRHFlow indisponível na {$operacao}.", ['erro' => $e->getMessage()]);

            throw DrhflowIndisponivelException::aoConsultar($operacao, $e);
        }
    }

    private function tabela(): Builder
    {
        return $this->conexao()->table(config('drhflow.tabela_inscricao', 'EN_CANDIDATO_VAGA_EMPREGO'));
    }

    private function conexao(): ConnectionInterface
    {
        return DB::connection($this->conexao ?? config('drhflow.conexao', 'drhflow'));
    }
}
