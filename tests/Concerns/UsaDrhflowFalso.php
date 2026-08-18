<?php

namespace Tests\Concerns;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Um DRHFlow de mentira, em SQLite na memória.
 *
 * O DRHFlow é um SQL Server compartilhado com o RH, que este projeto acessa com
 * credencial de escrita. Nenhum teste pode encostar nele: um teste que apaga uma
 * linha lá apaga o trabalho de alguém.
 *
 * A troca é possível porque os repositórios usam o query builder em vez de SQL
 * cru de SQL Server. Os nomes de tabela e coluna são os reais, conferidos contra
 * o banco (ver `openspec/changes/.../reconhecimento-banco.md`) — um teste que
 * passa aqui exercita os mesmos identificadores que a produção usa.
 */
trait UsaDrhflowFalso
{
    protected function configurarDrhflowFalso(): void
    {
        config([
            'database.connections.drhflow' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => false,
            ],
        ]);

        DB::purge('drhflow');
        Cache::flush();

        $this->criarEsquemaDrhflow();
        $this->semearDominiosDrhflow();
    }

    protected function drhflow(): Connection
    {
        return DB::connection('drhflow');
    }

    /**
     * Insere uma vaga aberta, com o mínimo necessário para aparecer na listagem.
     *
     * @param  array<string, mixed>  $atributos
     */
    protected function vagaDrhflow(array $atributos = []): int
    {
        $codigo = $atributos['CD_VAGA_EMPREGO'] ?? (($this->drhflow()->table('EN_VAGA_EMPREGO')->max('CD_VAGA_EMPREGO') ?? 0) + 1);

        $this->drhflow()->table('EN_VAGA_EMPREGO')->insert(array_merge([
            'CD_VAGA_EMPREGO' => $codigo,
            'CD_SITUACAO' => 1,
            'FG_ATIVA' => 'S',
            'CD_FUNCAO' => '0993',
            'CD_PROJETO' => '2025.114',
            'CD_TIPO_ADMISSAO' => 'U',
            'CD_ESCOLARIDADE_EXIGIDA' => '8',
            'CD_TIPO_EXPERIENCIA' => 1,
            'CD_HORARIO' => '0021',
            'DE_ATIVIDADES' => 'Apoio às atividades do projeto.',
            'DE_REQUISITOS_EXIGIDOS' => 'Estar matriculado em curso de graduação.',
            'DE_BENEFICIOS' => 'Auxílio transporte.',
            'DE_DOCUMENTACAO_NECESSARIA' => 'RG, CPF e comprovante de matrícula.',
            'DE_CARGA_HORARIA' => '20:00',
            'VL_SALARIO' => 1050.00,
            'CD_UF' => 'SC',
            'CD_MUNICIPIO' => 1653,
            'DT_CADASTRO' => now()->subDays(2)->toDateTimeString(),
            'DT_LIMITE_PARA_INSCRICAO' => now()->addDays(30)->startOfDay()->toDateTimeString(),
        ], $atributos));

        return (int) $codigo;
    }

    /** @param array<string, mixed> $atributos */
    protected function inscricaoDrhflow(string $cpf, int $cdVaga, array $atributos = []): void
    {
        $this->drhflow()->table('EN_CANDIDATO_VAGA_EMPREGO')->insert(array_merge([
            'NU_CPF' => $cpf,
            'CD_VAGA_EMPREGO' => $cdVaga,
            'NM_CANDIDATO' => 'Candidato de Teste',
            'DT_CADASTRO' => now()->toDateTimeString(),
        ], $atributos));
    }

    private function criarEsquemaDrhflow(): void
    {
        $esquema = $this->drhflow()->getSchemaBuilder();

        $esquema->create('EN_VAGA_EMPREGO', function (Blueprint $t) {
            $t->integer('CD_VAGA_EMPREGO')->primary();
            $t->string('ID_USUARIO_CAD', 25)->nullable();
            $t->string('ID_USUARIO_ALT', 25)->nullable();
            $t->dateTime('DT_CADASTRO')->nullable();
            $t->dateTime('DT_ALTERACAO')->nullable();
            $t->string('CD_FUNCAO', 10)->nullable();
            $t->string('DE_CARGA_HORARIA', 25)->nullable();
            $t->string('CD_ESCOLARIDADE_EXIGIDA', 10)->nullable();
            $t->string('CD_PROJETO', 25)->nullable();
            $t->decimal('VL_SALARIO', 15, 2)->nullable();
            $t->string('FG_ATIVA', 1)->nullable();
            $t->text('DE_REQUISITOS_EXIGIDOS')->nullable();
            $t->smallInteger('CD_SITUACAO')->nullable();
            $t->integer('CD_TIPO_EXPERIENCIA')->nullable();
            $t->string('CD_CENTRO', 35)->nullable();
            $t->string('CD_DEPARTAMENTO', 35)->nullable();
            $t->text('DE_BENEFICIOS')->nullable();
            $t->text('DE_DOCUMENTACAO_NECESSARIA')->nullable();
            $t->dateTime('DT_LIMITE_PARA_INSCRICAO')->nullable();
            $t->string('CD_HORARIO', 25)->nullable();
            $t->string('CD_UF', 2)->nullable();
            $t->integer('CD_MUNICIPIO')->nullable();
            $t->text('DE_ATIVIDADES')->nullable();
            $t->string('CD_TIPO_ADMISSAO', 1)->nullable();
        });

        $esquema->create('EN_CANDIDATO_VAGA_EMPREGO', function (Blueprint $t) {
            $t->string('NU_CPF', 25);
            $t->integer('CD_VAGA_EMPREGO');
            $t->string('ID_USUARIO_CAD', 25)->nullable();
            $t->string('ID_USUARIO_ALT', 25)->nullable();
            $t->dateTime('DT_CADASTRO')->nullable();
            $t->dateTime('DT_ALTERACAO')->nullable();
            $t->string('NM_CANDIDATO', 150)->nullable();
            $t->string('NM_SOCIAL', 150)->nullable();
            $t->string('DE_EMAIL', 150)->nullable();
            $t->string('CD_GRAU_INSTRUCAO', 5)->nullable();
            $t->integer('CD_CURSO_SUPERIOR1')->nullable();
            $t->integer('CD_CURSO_SUPERIOR2')->nullable();
            $t->integer('CD_CURSO_SUPERIOR3')->nullable();
            $t->dateTime('DT_NASCIMENTO')->nullable();
            $t->string('FG_SEXO', 1)->nullable();
            $t->string('CD_ESTADO_CIVIL', 5)->nullable();
            $t->string('NU_PISPASEP', 25)->nullable();
            $t->string('NU_CTPS', 25)->nullable();
            $t->string('UF_CTPS', 2)->nullable();
            $t->string('NU_SERIE_CTPS', 25)->nullable();
            $t->integer('CD_NACIONALIDADE')->nullable();
            $t->integer('CD_ESTADO_NATAL')->nullable();
            $t->integer('CD_NATURALIDADE')->nullable();
            $t->smallInteger('CD_COR_RACA')->nullable();
            $t->string('NU_RG', 25)->nullable();
            $t->string('NM_EMISSOR_RG', 50)->nullable();
            $t->string('UF_RG', 2)->nullable();
            $t->dateTime('DT_EMISSAO_RG')->nullable();
            $t->string('NU_REGISTRO_PROFISSIONAL', 50)->nullable();
            $t->string('NM_EMISSOR_REGISTRO_PROFISSIONAL', 50)->nullable();
            $t->dateTime('DT_EMISSAO_REGISTRO_PROFISSIONAL')->nullable();
            foreach (['FISICO', 'AUDITIVO', 'FALA', 'VISUAL', 'MENTAL', 'REABILITADO', 'INTELECTUAL', 'AUTISMO'] as $tipo) {
                $t->string("FG_DEFICIENTE_{$tipo}", 1)->nullable();
            }
            $t->string('NU_CEP', 15)->nullable();
            $t->string('CD_UF_ENDERECO', 2)->nullable();
            $t->string('CD_MUNICIPIO_ENDERECO', 15)->nullable();
            $t->smallInteger('CD_TIPO_RUA')->nullable();
            $t->smallInteger('CD_TIPO_BAIRRO')->nullable();
            $t->string('NM_LOGRADOURO', 100)->nullable();
            $t->string('NM_COMPLEMENTO_LOGRADOURO', 100)->nullable();
            $t->string('NU_LOGRADOURO', 25)->nullable();
            $t->string('NM_BAIRRO', 100)->nullable();
            $t->string('NU_TELEFONE_FIXO', 25)->nullable();
            $t->string('NU_TELEFONE_CELULAR', 25)->nullable();
            $t->text('DE_OUTROS_CURSOS')->nullable();
            $t->text('DE_OUTRAS_FORMACOES')->nullable();
            $t->integer('CD_PAIS')->nullable();
            foreach (['SERVIDOR', 'DIRIGENTE', 'DIRETOR', 'COORDENADOR_PROJETO', 'FISCAL_CONTRATO'] as $tipo) {
                $t->string("FG_PARENTE_{$tipo}", 1)->nullable();
            }
            $t->string('FG_LEU_CODIGO_CONDUTA_FAPEU', 1)->nullable();
            $t->string('FG_TEM_DIVIDA_BANCO', 1)->nullable();
            $t->string('FG_SEL', 1)->nullable();
            $t->dateTime('DT_ENTREVISTA')->nullable();
            $t->string('HR_ENTREVISTA', 5)->nullable();
            $t->string('DE_LOCAL_ENTREVISTA', 255)->nullable();
            $t->string('ID_USUARIO_MARCOU_ENTREVISTA', 25)->nullable();
            $t->dateTime('DT_MARCACAO_ENTREVISTA')->nullable();
            $t->dateTime('DT_ENVIO_EMAIL')->nullable();
            $t->string('ID_USUARIO_ENVIO_EMAIL', 25)->nullable();
            $t->decimal('VL_NOTA_ESCRITA', 10, 2)->nullable();
            $t->decimal('VL_NOTA_PRATICA', 10, 2)->nullable();
            $t->decimal('VL_NOTA_PSICOLOGICO', 10, 2)->nullable();
            $t->decimal('VL_NOTA_ENTREVISTA', 10, 2)->nullable();
            $t->decimal('VL_MEDIA_AVALIACAO', 10, 2)->nullable();
            $t->decimal('VL_NOTA_AVALIACAO_CURRICULO', 10, 2)->nullable();
            $t->string('FG_PCD', 1)->nullable();

            // A mesma chave composta da origem — é ela que torna a gravação
            // idempotente por CPF + vaga (design D8).
            $t->primary(['NU_CPF', 'CD_VAGA_EMPREGO']);
        });

        $esquema->create('VW_FUNCAO_5ANOS', function (Blueprint $t) {
            $t->string('CODIGO', 10);
            $t->string('NOME', 100)->nullable();
            $t->string('CBO2002', 10)->nullable();
            $t->string('NOME_E_CBO', 100)->nullable();
            $t->text('DESCRICAO')->nullable();
        });

        $esquema->create('VW_PROJETO', function (Blueprint $t) {
            $t->string('codccusto', 8)->nullable();
            $t->string('ano_codigo', 8);
            $t->string('codigo_ano', 8)->nullable();
            $t->string('ano', 4)->nullable();
            $t->string('codigo', 3)->nullable();
            $t->string('projeto_rubrica_nome', 72)->nullable();
        });

        $esquema->create('EN_MUNICIPIO', function (Blueprint $t) {
            $t->integer('CD_MUNICIPIO');
            $t->string('CD_UF', 3)->nullable();
            $t->string('NM_MUNICIPIO', 100)->nullable();
            $t->integer('CD_PAIS_IBGE')->nullable();
            $t->integer('CD_UF_INT')->nullable();
        });

        $esquema->create('EN_UF', function (Blueprint $t) {
            $t->string('CD_UF', 2);
            $t->string('NM_UF', 50)->nullable();
        });

        $esquema->create('EN_TIPO_ADMISSAO', function (Blueprint $t) {
            $t->string('CD_TIPO_ADMISSAO', 3);
            $t->string('NM_TIPO_ADMISSAO', 50)->nullable();
            $t->string('CD_RM', 3)->nullable();
        });

        $esquema->create('EN_TIPO_EXPERIENCIA', function (Blueprint $t) {
            $t->integer('CD_TIPO_EXPERIENCIA');
            $t->string('NM_TIPO_EXPERIENCIA', 255)->nullable();
        });

        $esquema->create('VW_GRAU_INSTRUCAO_RM', function (Blueprint $t) {
            $t->string('CODCLIENTE', 3);
            $t->string('DESCRICAO', 255)->nullable();
        });

        $esquema->create('VW_MUNICIPIO_RM', function (Blueprint $t) {
            $t->string('CODMUNICIPIO', 5);
            $t->string('CODETDMUNICIPIO', 2)->nullable();
            $t->string('NOMEMUNICIPIO', 100)->nullable();
        });

        $esquema->create('VW_HORARIO_REQUISICAO', function (Blueprint $t) {
            $t->string('CODIGO', 10);
            $t->string('CARGA_SEMANAL', 10)->nullable();
            $t->string('DESCRICAO', 255)->nullable();
        });

        $esquema->create('EN_CURSO_SUPERIOR', function (Blueprint $t) {
            $t->integer('CD_CURSO_SUPERIOR');
            $t->string('NM_AREA_CURSO', 100)->nullable();
            $t->string('NM_CURSO_SUPERIOR', 150)->nullable();
        });

        $esquema->create('EN_PAIS_IBGE', function (Blueprint $t) {
            $t->integer('CD_PAIS');
            $t->string('NM_PAIS', 100)->nullable();
            $t->string('CD_ALFA3', 3)->nullable();
        });
    }

    /** Domínios com os valores reais do banco, para que as traduções sejam as de produção. */
    private function semearDominiosDrhflow(): void
    {
        $db = $this->drhflow();

        $db->table('VW_FUNCAO_5ANOS')->insert([
            ['CODIGO' => '0993', 'NOME' => 'BOLSISTA ALUNO DE GRADUAÇÃO', 'CBO2002' => '', 'NOME_E_CBO' => 'BOLSISTA ALUNO DE GRADUAÇÃO'],
            ['CODIGO' => '0373', 'NOME' => 'PROGRAMADOR', 'CBO2002' => '317210', 'NOME_E_CBO' => 'PROGRAMADOR'],
        ]);

        $db->table('VW_PROJETO')->insert([
            ['ano_codigo' => '2025.114', 'ano' => '2025', 'codigo' => '114', 'projeto_rubrica_nome' => '114.2025 - UFSC 200/2025 - ELABORAÇÃO DE ESTUDOS'],
            ['ano_codigo' => '2024.011', 'ano' => '2024', 'codigo' => '011', 'projeto_rubrica_nome' => '011.2024 - APOIO TÉCNICO NO PLANEJAMENTO'],
        ]);

        $db->table('EN_MUNICIPIO')->insert([
            ['CD_MUNICIPIO' => 1653, 'CD_UF' => 'SC', 'NM_MUNICIPIO' => 'Florianópolis', 'CD_PAIS_IBGE' => 76, 'CD_UF_INT' => 24],
            ['CD_MUNICIPIO' => 5407, 'CD_UF' => 'MG', 'NM_MUNICIPIO' => 'Santo Antônio do Amparo', 'CD_PAIS_IBGE' => 76, 'CD_UF_INT' => 11],
            ['CD_MUNICIPIO' => 4204, 'CD_UF' => 'SC', 'NM_MUNICIPIO' => 'Joinville', 'CD_PAIS_IBGE' => 76, 'CD_UF_INT' => 24],
        ]);

        $db->table('EN_UF')->insert([
            ['CD_UF' => 'SC', 'NM_UF' => 'Santa Catarina'],
            ['CD_UF' => 'SP', 'NM_UF' => 'São Paulo'],
            ['CD_UF' => 'MG', 'NM_UF' => 'Minas Gerais'],
        ]);

        // Bolsista é 'O' na chave e 'U' na vaga — o de-para real da origem.
        $db->table('EN_TIPO_ADMISSAO')->insert([
            ['CD_TIPO_ADMISSAO' => 'A', 'NM_TIPO_ADMISSAO' => 'AUTÔNOMO', 'CD_RM' => 'A'],
            ['CD_TIPO_ADMISSAO' => 'N', 'NM_TIPO_ADMISSAO' => 'CELETISTA', 'CD_RM' => 'N'],
            ['CD_TIPO_ADMISSAO' => 'O', 'NM_TIPO_ADMISSAO' => 'BOLSISTA', 'CD_RM' => 'U'],
            ['CD_TIPO_ADMISSAO' => 'T', 'NM_TIPO_ADMISSAO' => 'ESTAGIÁRIO', 'CD_RM' => 'T'],
        ]);

        $db->table('EN_TIPO_EXPERIENCIA')->insert([
            ['CD_TIPO_EXPERIENCIA' => 1, 'NM_TIPO_EXPERIENCIA' => 'NÃO'],
            ['CD_TIPO_EXPERIENCIA' => 3, 'NM_TIPO_EXPERIENCIA' => '1 ANO'],
            ['CD_TIPO_EXPERIENCIA' => 4, 'NM_TIPO_EXPERIENCIA' => '2 ANOS'],
        ]);

        $db->table('VW_GRAU_INSTRUCAO_RM')->insert([
            ['CODCLIENTE' => '1', 'DESCRICAO' => 'Analfabeto'],
            ['CODCLIENTE' => '2', 'DESCRICAO' => 'Até o 5º ano incompleto do ensino fundamental'],
            ['CODCLIENTE' => '3', 'DESCRICAO' => '5º ano completo do ensino fundamental'],
            ['CODCLIENTE' => '4', 'DESCRICAO' => 'Do 6º ao 9º ano do ensino fundamental'],
            ['CODCLIENTE' => '5', 'DESCRICAO' => 'Ensino fundamental completo'],
            ['CODCLIENTE' => '6', 'DESCRICAO' => 'Ensino médio incompleto'],
            ['CODCLIENTE' => '7', 'DESCRICAO' => 'Ensino médio completo'],
            ['CODCLIENTE' => '8', 'DESCRICAO' => 'Educação superior incompleto'],
            ['CODCLIENTE' => '9', 'DESCRICAO' => 'Educação superior completo'],
            ['CODCLIENTE' => 'A', 'DESCRICAO' => 'Pós Grad. incompleto'],
            ['CODCLIENTE' => 'B', 'DESCRICAO' => 'Pós Grad. completo'],
            ['CODCLIENTE' => 'C', 'DESCRICAO' => 'Mestrado incompleto'],
            ['CODCLIENTE' => 'D', 'DESCRICAO' => 'Mestrado completo'],
            ['CODCLIENTE' => 'E', 'DESCRICAO' => 'Doutorado incompleto'],
            ['CODCLIENTE' => 'F', 'DESCRICAO' => 'Doutorado completo'],
            ['CODCLIENTE' => 'G', 'DESCRICAO' => 'Pós Dout.incompleto'],
            ['CODCLIENTE' => 'H', 'DESCRICAO' => 'Pós Dout.completo'],
        ]);

        // O mesmo código em duas UFs — o caso que obriga a busca a casar UF.
        $db->table('VW_MUNICIPIO_RM')->insert([
            ['CODMUNICIPIO' => '05407', 'CODETDMUNICIPIO' => 'SC', 'NOMEMUNICIPIO' => 'Florianópolis'],
            ['CODMUNICIPIO' => '05407', 'CODETDMUNICIPIO' => 'CE', 'NOMEMUNICIPIO' => 'Icó'],
            ['CODMUNICIPIO' => '16602', 'CODETDMUNICIPIO' => 'SC', 'NOMEMUNICIPIO' => 'São José'],
            ['CODMUNICIPIO' => '50308', 'CODETDMUNICIPIO' => 'SP', 'NOMEMUNICIPIO' => 'São Paulo'],
        ]);

        $db->table('VW_HORARIO_REQUISICAO')->insert([
            ['CODIGO' => '0021', 'CARGA_SEMANAL' => '20:00', 'DESCRICAO' => 'DAS 08:00 AS 12:00'],
            ['CODIGO' => '0118', 'CARGA_SEMANAL' => '40:00', 'DESCRICAO' => 'DAS 08:00 AS 18:00'],
        ]);

        $db->table('EN_CURSO_SUPERIOR')->insert([
            ['CD_CURSO_SUPERIOR' => 1, 'NM_AREA_CURSO' => 'Ciências biológicas e da Saúde', 'NM_CURSO_SUPERIOR' => 'Bacharel em Biologias (licenciatura e bacharelado)'],
            ['CD_CURSO_SUPERIOR' => 120, 'NM_AREA_CURSO' => 'Exatas', 'NM_CURSO_SUPERIOR' => 'Ciência da Computação'],
            ['CD_CURSO_SUPERIOR' => 121, 'NM_AREA_CURSO' => 'Exatas', 'NM_CURSO_SUPERIOR' => 'Engenharia Civil'],
            ['CD_CURSO_SUPERIOR' => 122, 'NM_AREA_CURSO' => 'Humanas', 'NM_CURSO_SUPERIOR' => 'Administração'],
        ]);

        $db->table('EN_PAIS_IBGE')->insert([
            ['CD_PAIS' => 76, 'NM_PAIS' => 'Brasil', 'CD_ALFA3' => 'BRA'],
            ['CD_PAIS' => 249, 'NM_PAIS' => 'Estados Unidos', 'CD_ALFA3' => 'USA'],
        ]);
    }
}
