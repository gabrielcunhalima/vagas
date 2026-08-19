<?php

namespace App\Support\Drhflow;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * As tabelas de domínio do DRHFlow — o que traduz código em rótulo.
 *
 * Servem a dois propósitos: preencher os selects dos filtros e traduzir os
 * códigos das vagas antes de chegarem à tela (a spec proíbe código bruto).
 *
 * Tudo aqui é memorizado por algumas horas: são listas de dezenas de linhas que
 * mudam uma vez por ano, e a listagem pública é a rota mais visitada do portal.
 */
class DominioDrhflowRepository
{
    public function __construct(private readonly ?string $conexao = null) {}

    /**
     * Tipos de admissão, indexados pelo valor que aparece na vaga.
     *
     * A chave de `EN_TIPO_ADMISSAO` (`A`/`N`/`O`/`T`) **não** é o que
     * `EN_VAGA_EMPREGO.CD_TIPO_ADMISSAO` guarda: bolsista é `O` na tabela de
     * domínio e `U` na vaga. `CD_RM` é a coluna que faz a ponte.
     *
     * @return array<string, string>
     */
    public function tiposAdmissao(): array
    {
        return $this->memorizar('tipos_admissao', function (): array {
            return $this->tabela('EN_TIPO_ADMISSAO')
                ->whereNotNull('CD_RM')
                ->orderBy('NM_TIPO_ADMISSAO')
                ->pluck('NM_TIPO_ADMISSAO', 'CD_RM')
                ->map(fn ($nome) => $this->capitalizar((string) $nome))
                ->all();
        });
    }

    /**
     * Graus de instrução do RM, que servem tanto à escolaridade exigida pela
     * vaga quanto ao grau do candidato.
     *
     * Lê `VW_GRAU_INSTRUCAO_RM`, dentro do próprio `DB_DRHFLOW_TESTE`, e não
     * `CorporeRM.dbo.PCODINSTRUCAO`: a view tem os mesmos 17 registros e
     * dispensa permissão cross-database.
     *
     * @return array<string, string>
     */
    public function grausInstrucao(): array
    {
        return $this->memorizar('graus_instrucao', function (): array {
            return $this->tabela('VW_GRAU_INSTRUCAO_RM')
                ->orderBy('CODCLIENTE')
                ->pluck('DESCRICAO', 'CODCLIENTE')
                ->map(fn ($d) => trim((string) $d))
                ->all();
        });
    }

    /** @return array<string, string> */
    public function tiposExperiencia(): array
    {
        return $this->memorizar('tipos_experiencia', function (): array {
            return $this->tabela('EN_TIPO_EXPERIENCIA')
                ->orderBy('CD_TIPO_EXPERIENCIA')
                ->pluck('NM_TIPO_EXPERIENCIA', 'CD_TIPO_EXPERIENCIA')
                ->map(fn ($n) => $this->capitalizar((string) $n))
                ->all();
        });
    }

    /** Descrição do horário de trabalho, por código. @return array<string, string> */
    public function horarios(): array
    {
        return $this->memorizar('horarios', function (): array {
            return $this->tabela('VW_HORARIO_REQUISICAO')
                ->orderBy('CODIGO')
                ->get(['CODIGO', 'CARGA_SEMANAL', 'DESCRICAO'])
                ->mapWithKeys(fn ($h) => [(string) $h->CODIGO => trim((string) $h->DESCRICAO)])
                ->all();
        });
    }

    /** @return array<string, string> */
    public function ufs(): array
    {
        return $this->memorizar('ufs', function (): array {
            return $this->tabela('EN_UF')
                ->orderBy('CD_UF')
                ->pluck('NM_UF', 'CD_UF')
                ->all();
        });
    }

    /**
     * Municípios que efetivamente têm vaga — a lista inteira são 5.5 mil linhas
     * e um select com 5.5 mil opções não é um filtro, é um obstáculo.
     *
     * @return list<string>
     */
    public function municipiosComVaga(): array
    {
        return $this->memorizar('municipios_com_vaga', function (): array {
            return $this->tabela('EN_VAGA_EMPREGO as v')
                ->join('EN_MUNICIPIO as m', 'v.CD_MUNICIPIO', '=', 'm.CD_MUNICIPIO')
                ->where('v.CD_SITUACAO', VagaDrhflowRepository::SITUACAO_ABERTA)
                ->orderBy('m.NM_MUNICIPIO')
                ->distinct()
                ->pluck('m.NM_MUNICIPIO')
                ->map(fn ($n) => trim((string) $n))
                ->all();
        });
    }

    /**
     * Projetos com vaga aberta, como código => nome.
     *
     * @return array<string, string>
     */
    public function projetosComVaga(): array
    {
        return $this->memorizar('projetos_com_vaga', function (): array {
            return $this->tabela('EN_VAGA_EMPREGO as v')
                ->join('VW_PROJETO as p', 'v.CD_PROJETO', '=', 'p.ano_codigo')
                ->where('v.CD_SITUACAO', VagaDrhflowRepository::SITUACAO_ABERTA)
                ->orderBy('p.projeto_rubrica_nome')
                ->distinct()
                ->pluck('p.projeto_rubrica_nome', 'v.CD_PROJETO')
                ->map(fn ($n) => trim((string) $n))
                ->all();
        });
    }

    /**
     * Código do município no cadastro do RM, para gravar no endereço do
     * candidato. Casa nome **e** UF: `CODMUNICIPIO` sozinho não é único
     * (`05407` é Icó/CE e Florianópolis/SC).
     *
     * Este é um domínio diferente do de `EN_MUNICIPIO`, que serve à localização
     * da vaga. Trocar um pelo outro grava o município errado.
     */
    public function codigoMunicipioRm(?string $cidade, ?string $uf): ?string
    {
        if (blank($cidade) || blank($uf)) {
            return null;
        }

        $procurado = Normalizador::chave($cidade);
        $ufNormalizada = strtoupper(trim($uf));

        foreach ($this->municipiosRm() as $municipio) {
            if ($municipio['uf'] === $ufNormalizada && $municipio['chave'] === $procurado) {
                return $municipio['codigo'];
            }
        }

        return null;
    }

    /** Código IBGE do país, por nome. Sem correspondência devolve nulo. */
    public function codigoPais(?string $pais): ?int
    {
        if (blank($pais)) {
            return null;
        }

        $procurado = Normalizador::chave($pais);

        foreach ($this->paises() as $codigo => $chave) {
            if ($chave === $procurado) {
                return (int) $codigo;
            }
        }

        return null;
    }

    /**
     * Códigos dos cursos superiores correspondentes aos nomes informados, na
     * ordem em que chegaram e sem repetição.
     *
     * @param  list<string|null>  $nomes
     * @return list<int>
     */
    public function codigosCursoSuperior(array $nomes): array
    {
        $indice = $this->cursosSuperiores();
        $encontrados = [];

        foreach ($nomes as $nome) {
            if (blank($nome)) {
                continue;
            }

            $codigo = $indice[Normalizador::chave($nome)] ?? null;

            if ($codigo !== null && ! in_array($codigo, $encontrados, true)) {
                $encontrados[] = $codigo;
            }
        }

        return $encontrados;
    }

    /** @return list<array{codigo: string, uf: string, chave: string}> */
    private function municipiosRm(): array
    {
        return $this->memorizar('municipios_rm', function (): array {
            return $this->tabela('VW_MUNICIPIO_RM')
                ->get(['CODMUNICIPIO', 'CODETDMUNICIPIO', 'NOMEMUNICIPIO'])
                ->map(fn ($m) => [
                    'codigo' => trim((string) $m->CODMUNICIPIO),
                    'uf' => strtoupper(trim((string) $m->CODETDMUNICIPIO)),
                    'chave' => Normalizador::chave((string) $m->NOMEMUNICIPIO),
                ])
                ->all();
        });
    }

    /** @return array<int, string> código IBGE => nome normalizado */
    private function paises(): array
    {
        return $this->memorizar('paises', function (): array {
            return $this->tabela('EN_PAIS_IBGE')
                ->get(['CD_PAIS', 'NM_PAIS'])
                ->mapWithKeys(fn ($p) => [(int) $p->CD_PAIS => Normalizador::chave((string) $p->NM_PAIS)])
                ->all();
        });
    }

    /**
     * Índice de cursos superiores por nome normalizado.
     *
     * Os 242 nomes de `EN_CURSO_SUPERIOR` são todos prefixados por grau —
     * "Bacharel em Ciência da Computação", "Tecnólogo em Redes de Computadores".
     * O candidato digita o curso, não o grau ("Ciência da Computação"), então um
     * índice só pelo nome completo não casaria praticamente nunca.
     *
     * Por isso cada curso entra duas vezes: pelo nome inteiro e pelo nome sem o
     * prefixo de grau. O nome inteiro tem precedência, para que um curso cujo
     * nome sem prefixo colida com outro completo não roube a correspondência.
     *
     * @return array<string, int> nome normalizado => código
     */
    private function cursosSuperiores(): array
    {
        return $this->memorizar('cursos_superiores', function (): array {
            $porNomeCompleto = [];
            $semPrefixo = [];

            foreach ($this->tabela('EN_CURSO_SUPERIOR')->get(['CD_CURSO_SUPERIOR', 'NM_CURSO_SUPERIOR']) as $curso) {
                $codigo = (int) $curso->CD_CURSO_SUPERIOR;
                $chave = Normalizador::chave((string) $curso->NM_CURSO_SUPERIOR);

                if ($chave === '') {
                    continue;
                }

                $porNomeCompleto[$chave] = $codigo;

                $reduzida = preg_replace('/^(bacharel|licenciatura|licenciado|tecnologo|tecnologia) (em|de) /', '', $chave) ?? $chave;

                // Ambiguidade não vira palpite: dois cursos que reduzem ao mesmo
                // nome deixam a chave reduzida fora, e o casamento devolve nulo.
                if ($reduzida !== $chave && $reduzida !== '') {
                    $semPrefixo[$reduzida] = array_key_exists($reduzida, $semPrefixo) ? null : $codigo;
                }
            }

            return array_merge(array_filter($semPrefixo, fn ($c) => $c !== null), $porNomeCompleto);
        });
    }

    /**
     * Memoriza o domínio e traduz qualquer falha de conexão para a exceção do
     * módulo — os domínios só existem para servir uma tela, e uma tela sem
     * domínio precisa dizer que está indisponível, não fingir lista vazia.
     */
    private function memorizar(string $chave, callable $consulta): array
    {
        $segundos = (int) config('drhflow.cache_dominios_segundos', 21600);

        try {
            return Cache::remember("drhflow:dominio:{$chave}", $segundos, $consulta);
        } catch (DrhflowIndisponivelException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw DrhflowIndisponivelException::aoConsultar("domínio {$chave}", $e);
        }
    }

    private function tabela(string $tabela): Builder
    {
        return $this->conexao()->table($tabela);
    }

    private function conexao(): ConnectionInterface
    {
        return DB::connection($this->conexao ?? config('drhflow.conexao', 'drhflow'));
    }

    /** Os domínios vêm em caixa alta ("AUTÔNOMO"); a tela não grita. */
    private function capitalizar(string $texto): string
    {
        $limpo = trim($texto);

        if ($limpo === '' || $limpo !== mb_strtoupper($limpo, 'UTF-8')) {
            return $limpo;
        }

        return mb_convert_case($limpo, MB_CASE_TITLE, 'UTF-8');
    }
}
