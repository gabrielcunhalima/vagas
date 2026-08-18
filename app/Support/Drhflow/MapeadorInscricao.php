<?php

namespace App\Support\Drhflow;

use App\Models\Candidato;

/**
 * Traduz o perfil do candidato para as colunas de `EN_CANDIDATO_VAGA_EMPREGO`.
 *
 * Só o que o perfil realmente coleta é mapeado. As demais colunas — RG, CTPS,
 * PIS/PASEP, naturalidade, cor/raça, tipos de deficiência, parentesco, dívida —
 * ficam **ausentes do array**, e portanto nulas na origem. Nenhum valor é
 * inventado para preencher coluna: a capacidade `vagas-drhflow` exige isso, e um
 * valor inventado é indistinguível de um informado.
 *
 * As colunas de entrevista, avaliação e nota pertencem ao RH e nunca aparecem
 * aqui, em nenhuma operação.
 */
class MapeadorInscricao
{
    public function __construct(
        private readonly DominioDrhflowRepository $dominios = new DominioDrhflowRepository,
    ) {}

    /**
     * Colunas de dados do candidato — as únicas que o portal escreve.
     *
     * Esta lista é o contrato do reenvio: um `UPDATE` só pode tocar chaves que
     * estejam aqui. Ver `InscricaoDrhflowRepository::atualizar()`.
     *
     * @return array<string, mixed>
     */
    public function colunasDoCandidato(Candidato $candidato): array
    {
        $candidato->loadMissing('formacoes');

        return [
            'NM_CANDIDATO' => $this->limitar($candidato->nome, 150),
            'NM_SOCIAL' => $this->limitar($candidato->nome_social, 150),
            'DE_EMAIL' => $this->limitar($candidato->email, 150),
            'NU_TELEFONE_CELULAR' => $this->limitar(Normalizador::digitos($candidato->telefone) ?: null, 25),

            // Sem máscara, como as 4469 linhas que o DRHFlow já tinha. O perfil
            // guarda o CEP formatado ("88040-400"); gravá-lo assim aqui criaria
            // o único registro pontuado da tabela.
            'NU_CEP' => $this->limitar(Normalizador::digitos($candidato->cep) ?: null, 15),
            'NM_LOGRADOURO' => $this->limitar($candidato->logradouro, 100),
            'NU_LOGRADOURO' => $this->limitar($candidato->numero, 25),
            'NM_COMPLEMENTO_LOGRADOURO' => $this->limitar($candidato->complemento, 100),
            'NM_BAIRRO' => $this->limitar($candidato->bairro, 100),
            'CD_UF_ENDERECO' => $this->limitar($candidato->estado, 2),
            'CD_MUNICIPIO_ENDERECO' => $this->dominios->codigoMunicipioRm($candidato->cidade, $candidato->estado),
            'CD_PAIS' => $this->dominios->codigoPais($candidato->pais ?: 'Brasil'),

            'CD_GRAU_INSTRUCAO' => GrauInstrucao::maiorGrau($candidato->formacoes),
            'DE_OUTRAS_FORMACOES' => $candidato->outras_formacoes_mec,
            'DE_OUTROS_CURSOS' => $candidato->outros_cursos,

            // Flags do DRHFlow são 'S'/'N' em varchar(1), não booleano.
            'FG_PCD' => $candidato->pcd ? 'S' : 'N',
        ] + $this->cursosSuperiores($candidato);
    }

    /**
     * A linha completa de uma inscrição nova.
     *
     * @return array<string, mixed>
     */
    public function linhaDeInscricao(Candidato $candidato, int $cdVagaEmprego, bool $aceitouCodigoConduta): array
    {
        $identificador = $this->limitar((string) config('drhflow.id_usuario_cad', 'PORTALVAGAS'), 25);

        return array_merge($this->colunasDoCandidato($candidato), [
            'NU_CPF' => self::cpf($candidato),
            'CD_VAGA_EMPREGO' => $cdVagaEmprego,

            'FG_LEU_CODIGO_CONDUTA_FAPEU' => $aceitouCodigoConduta ? 'S' : 'N',

            'DT_CADASTRO' => now(),
            'ID_USUARIO_CAD' => $identificador,
        ]);
    }

    /**
     * CPF como a origem guarda: onze dígitos, sem máscara.
     *
     * A coluna é `varchar(25)` e os registros existentes usam zeros à esquerda
     * (`00001594923`), então o preenchimento à esquerda é obrigatório — sem ele,
     * um CPF que começa com zero viraria uma chave diferente da que o DRHFlow já
     * usa para a mesma pessoa.
     */
    public static function cpf(Candidato $candidato): string
    {
        return str_pad(Normalizador::digitos($candidato->cpf), 11, '0', STR_PAD_LEFT);
    }

    /**
     * Até três cursos superiores, na ordem de cadastro das formações.
     *
     * Curso sem correspondência em `EN_CURSO_SUPERIOR` é ignorado aqui e
     * continua legível em `DE_OUTROS_CURSOS` — o candidato não perde a
     * informação, o RH só não a recebe codificada.
     *
     * @return array<string, int|null>
     */
    private function cursosSuperiores(Candidato $candidato): array
    {
        $nomes = $candidato->formacoes
            ->pluck('curso')
            ->filter(fn ($c) => filled($c))
            ->values()
            ->all();

        $codigos = $this->dominios->codigosCursoSuperior($nomes);

        return [
            'CD_CURSO_SUPERIOR1' => $codigos[0] ?? null,
            'CD_CURSO_SUPERIOR2' => $codigos[1] ?? null,
            'CD_CURSO_SUPERIOR3' => $codigos[2] ?? null,
        ];
    }

    /**
     * Trunca ao limite da coluna. As colunas da origem são estreitas
     * (`NU_TELEFONE_CELULAR` é varchar(25)) e um estouro derrubaria a inscrição
     * inteira por causa de um campo secundário.
     */
    private function limitar(?string $valor, int $tamanho): ?string
    {
        if (blank($valor)) {
            return null;
        }

        return mb_substr(trim($valor), 0, $tamanho);
    }
}
