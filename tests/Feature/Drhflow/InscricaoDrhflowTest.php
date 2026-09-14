<?php

namespace Tests\Feature\Drhflow;

use App\Models\Candidato;
use App\Models\CandidatoFormacao;
use App\Models\InscricaoComplemento;
use App\Support\Drhflow\InscricaoDrhflowRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\UsaDrhflowFalso;
use Tests\TestCase;

/** O envio da inscrição e o que ele grava de cada lado. */
class InscricaoDrhflowTest extends TestCase
{
    use RefreshDatabase, UsaDrhflowFalso;

    private const CPF = '00001594923';

    protected function setUp(): void
    {
        parent::setUp();
        $this->configurarDrhflowFalso();
        Storage::fake(Candidato::DISCO_CURRICULOS);
        Mail::fake();
    }

    private function candidato(array $atributos = []): Candidato
    {
        $candidato = Candidato::create(array_merge([
            'nome' => 'Maria da Silva',
            'nome_social' => 'Maria',
            'email' => 'maria@example.com',
            'password' => 'segredo-de-teste',
            // Com máscara de propósito: o DRHFlow guarda só dígitos.
            'cpf' => '000.015.949-23',
            'telefone' => '48999990000',
            'nacionalidade' => 'Brasileira',
            'cep' => '88040-400',
            'logradouro' => 'Rua Lauro Linhares',
            'numero' => '100',
            'bairro' => 'Trindade',
            'cidade' => 'Florianópolis',
            'estado' => 'SC',
            'pais' => 'Brasil',
            'outras_formacoes_mec' => 'Curso técnico em informática',
            'outros_cursos' => 'Inglês avançado',
            'pcd' => false,
            'possui_acessibilidade' => false,
            'ativo' => true,
        ], $atributos));

        $candidato->forceFill(['email_verified_at' => now()])->save();

        CandidatoFormacao::create([
            'candidato_id' => $candidato->id,
            'nivel_escolaridade' => 'graduacao',
            'situacao_curso' => 'concluido',
            'curso' => 'Ciência da Computação',
            'instituicao' => 'UFSC',
            'previsao_conclusao' => now()->subYear(),
        ]);

        CandidatoFormacao::create([
            'candidato_id' => $candidato->id,
            'nivel_escolaridade' => 'mestrado',
            'situacao_curso' => 'cursando',
            'curso' => 'Engenharia Civil',
            'instituicao' => 'UFSC',
            'semestre' => '2',
            'previsao_conclusao' => now()->addYear(),
        ]);

        $candidato->adicionarCurriculo(UploadedFile::fake()->create('cv.pdf', 40, 'application/pdf'));

        return $candidato->fresh(['formacoes', 'curriculos']);
    }

    private function enviar(Candidato $candidato, int $codigo, array $dados = [])
    {
        return $this->actingAs($candidato, 'candidato')->post("/candidatura/{$codigo}", array_merge([
            '_honeypot' => '',
            'carta_apresentacao' => 'Tenho grande interesse nesta vaga.',
            'conflito_interesse' => 0,
            'politica_privacidade_aceite' => 1,
        ], $dados));
    }

    private function linha(int $codigo): ?object
    {
        return $this->drhflow()->table('EN_CANDIDATO_VAGA_EMPREGO')
            ->where('CD_VAGA_EMPREGO', $codigo)
            ->first();
    }

    // ── Gravação ──────────────────────────────────────────────────────────────

    public function test_inscricao_cria_a_linha_no_drhflow(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = $this->candidato();

        $this->enviar($candidato, $codigo)->assertRedirect(route('candidato.candidaturas.index'));

        $linha = $this->linha($codigo);

        $this->assertNotNull($linha);
        $this->assertSame('Maria da Silva', $linha->NM_CANDIDATO);
        $this->assertSame('Maria', $linha->NM_SOCIAL);
        $this->assertSame('maria@example.com', $linha->DE_EMAIL);
        $this->assertSame('48999990000', $linha->NU_TELEFONE_CELULAR);
        $this->assertSame('Rua Lauro Linhares', $linha->NM_LOGRADOURO);
        $this->assertSame('SC', $linha->CD_UF_ENDERECO);
    }

    public function test_cpf_e_gravado_sem_mascara(): void
    {
        $codigo = $this->vagaDrhflow();

        $this->enviar($this->candidato(), $codigo);

        $this->assertSame(self::CPF, $this->linha($codigo)->NU_CPF);
    }

    public function test_cep_e_telefone_sao_gravados_sem_pontuacao(): void
    {
        $codigo = $this->vagaDrhflow();

        // O perfil guarda o CEP formatado; as 4469 linhas que o DRHFlow já
        // tinha usam só dígitos, e o portal não pode ser a exceção da tabela.
        $this->enviar($this->candidato(['cep' => '88040-400', 'telefone' => '(48) 99999-0000']), $codigo);

        $linha = $this->linha($codigo);

        $this->assertSame('88040400', $linha->NU_CEP);
        $this->assertSame('48999990000', $linha->NU_TELEFONE_CELULAR);
    }

    public function test_municipio_do_endereco_usa_o_codigo_do_rm(): void
    {
        $codigo = $this->vagaDrhflow();

        $this->enviar($this->candidato(), $codigo);

        // Florianópolis é 05407 no RM e 1653 em EN_MUNICIPIO. Gravar 1653 aqui
        // apontaria para outro município.
        $this->assertSame('05407', $this->linha($codigo)->CD_MUNICIPIO_ENDERECO);
    }

    public function test_pais_e_traduzido_para_o_codigo_ibge(): void
    {
        $codigo = $this->vagaDrhflow();

        $this->enviar($this->candidato(), $codigo);

        $this->assertSame(76, (int) $this->linha($codigo)->CD_PAIS);
    }

    public function test_grau_de_instrucao_e_o_da_formacao_mais_alta(): void
    {
        $codigo = $this->vagaDrhflow();

        $this->enviar($this->candidato(), $codigo);

        // Graduação concluída ('9') e mestrado cursando ('C') — vale o mestrado.
        $this->assertSame('C', $this->linha($codigo)->CD_GRAU_INSTRUCAO);
    }

    public function test_cursos_superiores_sao_traduzidos_na_ordem_de_cadastro(): void
    {
        $codigo = $this->vagaDrhflow();

        $this->enviar($this->candidato(), $codigo);

        $linha = $this->linha($codigo);

        $this->assertSame(120, (int) $linha->CD_CURSO_SUPERIOR1);
        $this->assertSame(121, (int) $linha->CD_CURSO_SUPERIOR2);
        $this->assertNull($linha->CD_CURSO_SUPERIOR3);
    }

    public function test_flags_usam_s_e_n_como_a_origem(): void
    {
        $codigo = $this->vagaDrhflow();

        $this->enviar($this->candidato(), $codigo);

        $linha = $this->linha($codigo);

        $this->assertSame('N', $linha->FG_PCD);
        $this->assertSame('S', $linha->FG_LEU_CODIGO_CONDUTA_FAPEU);
    }

    public function test_colunas_nao_coletadas_pelo_perfil_ficam_nulas(): void
    {
        $codigo = $this->vagaDrhflow();

        $this->enviar($this->candidato(), $codigo);

        $linha = $this->linha($codigo);

        foreach ([
            'DT_NASCIMENTO', 'FG_SEXO', 'CD_ESTADO_CIVIL', 'NU_PISPASEP', 'NU_CTPS',
            'UF_CTPS', 'NU_SERIE_CTPS', 'CD_NACIONALIDADE', 'CD_ESTADO_NATAL',
            'CD_NATURALIDADE', 'CD_COR_RACA', 'NU_RG', 'NM_EMISSOR_RG', 'UF_RG',
            'NU_TELEFONE_FIXO', 'CD_TIPO_RUA', 'CD_TIPO_BAIRRO', 'FG_TEM_DIVIDA_BANCO',
            'FG_PARENTE_SERVIDOR', 'FG_PARENTE_DIRIGENTE', 'FG_PARENTE_DIRETOR',
            'FG_PARENTE_COORDENADOR_PROJETO', 'FG_PARENTE_FISCAL_CONTRATO',
            'FG_DEFICIENTE_FISICO', 'FG_DEFICIENTE_VISUAL', 'FG_SEL',
        ] as $coluna) {
            $this->assertNull($linha->{$coluna}, "{$coluna} deveria ficar nula — o perfil não a coleta.");
        }
    }

    public function test_colunas_do_rh_nunca_sao_tocadas(): void
    {
        $codigo = $this->vagaDrhflow();

        $this->enviar($this->candidato(), $codigo);

        $linha = $this->linha($codigo);

        foreach ([
            'DT_ENTREVISTA', 'HR_ENTREVISTA', 'DE_LOCAL_ENTREVISTA',
            'ID_USUARIO_MARCOU_ENTREVISTA', 'DT_MARCACAO_ENTREVISTA',
            'VL_NOTA_ESCRITA', 'VL_NOTA_PRATICA', 'VL_NOTA_PSICOLOGICO',
            'VL_NOTA_ENTREVISTA', 'VL_MEDIA_AVALIACAO', 'VL_NOTA_AVALIACAO_CURRICULO',
        ] as $coluna) {
            $this->assertNull($linha->{$coluna}, "{$coluna} pertence ao RH e não pode ser escrita pelo portal.");
        }
    }

    public function test_o_portal_se_identifica_na_auditoria(): void
    {
        $codigo = $this->vagaDrhflow();

        $this->enviar($this->candidato(), $codigo);

        $linha = $this->linha($codigo);

        $this->assertSame(config('drhflow.id_usuario_cad'), $linha->ID_USUARIO_CAD);
        $this->assertNotNull($linha->DT_CADASTRO);
    }

    // ── Duplicidade ───────────────────────────────────────────────────────────

    public function test_segunda_tentativa_na_mesma_vaga_e_recusada(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = $this->candidato();

        $this->enviar($candidato, $codigo);
        $this->enviar($candidato, $codigo)
            ->assertRedirect(route('candidato.candidaturas.index'))
            ->assertSessionHas('info', 'Você já se candidatou a esta vaga.');

        $this->assertSame(1, $this->drhflow()->table('EN_CANDIDATO_VAGA_EMPREGO')->count());
    }

    public function test_inscricao_preexistente_feita_fora_do_portal_bloqueia_o_envio(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = $this->candidato();

        // Cadastrada pelo RH, sem passar pelo portal.
        $this->inscricaoDrhflow(self::CPF, $codigo, ['NM_CANDIDATO' => 'Cadastro do RH']);

        $this->enviar($candidato, $codigo)->assertSessionHas('info');

        $this->assertSame(1, $this->drhflow()->table('EN_CANDIDATO_VAGA_EMPREGO')->count());
        $this->assertSame('Cadastro do RH', $this->linha($codigo)->NM_CANDIDATO);
    }

    public function test_envio_concorrente_cria_uma_linha_so(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = $this->candidato();
        $repositorio = new InscricaoDrhflowRepository;

        // Simula a corrida: o segundo `criar()` encontra a chave já ocupada e
        // trata a violação como "já inscrito", não como falha.
        $primeiro = $repositorio->criar($candidato, $codigo, true);

        $this->drhflow()->table('EN_CANDIDATO_VAGA_EMPREGO')->count();
        $segundo = $repositorio->criar($candidato, $codigo, true);

        $this->assertTrue($primeiro);
        $this->assertFalse($segundo);
        $this->assertSame(1, $this->drhflow()->table('EN_CANDIDATO_VAGA_EMPREGO')->count());
    }

    // ── Gates ─────────────────────────────────────────────────────────────────

    public function test_perfil_incompleto_bloqueia_antes_de_qualquer_escrita(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = $this->candidato();
        $candidato->formacoes()->delete();

        $this->enviar($candidato->fresh(), $codigo)
            ->assertRedirect(route('candidato.perfil.edit'));

        $this->assertSame(0, $this->drhflow()->table('EN_CANDIDATO_VAGA_EMPREGO')->count());
    }

    public function test_vaga_fora_do_prazo_nao_aceita_inscricao(): void
    {
        $codigo = $this->vagaDrhflow(['DT_LIMITE_PARA_INSCRICAO' => now()->subWeek()]);

        $this->enviar($this->candidato(), $codigo)->assertStatus(404);

        $this->assertSame(0, $this->drhflow()->table('EN_CANDIDATO_VAGA_EMPREGO')->count());
    }

    public function test_vaga_fechada_nao_aceita_inscricao(): void
    {
        $codigo = $this->vagaDrhflow(['CD_SITUACAO' => 3]);

        $this->enviar($this->candidato(), $codigo)->assertStatus(404);

        $this->assertSame(0, $this->drhflow()->table('EN_CANDIDATO_VAGA_EMPREGO')->count());
    }

    // ── Complemento local ─────────────────────────────────────────────────────

    public function test_complemento_guarda_o_que_o_drhflow_nao_comporta(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = $this->candidato();

        $this->enviar($candidato, $codigo, [
            'conflito_interesse' => 1,
            'conflito_interesse_detalhe' => 'Meu irmão trabalha no projeto.',
        ]);

        $complemento = InscricaoComplemento::where('cpf', self::CPF)
            ->where('cd_vaga_emprego', $codigo)
            ->first();

        $this->assertNotNull($complemento);
        $this->assertSame('Tenho grande interesse nesta vaga.', $complemento->carta_apresentacao);
        $this->assertTrue($complemento->conflito_interesse);
        $this->assertSame('Meu irmão trabalha no projeto.', $complemento->conflito_interesse_detalhe);
        $this->assertSame($candidato->curriculo_atual_id, $complemento->curriculo_id_vigente);
    }

    public function test_curriculo_trocado_depois_do_envio_nao_muda_a_versao_registrada(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = $this->candidato();
        $versaoEnviada = $candidato->curriculo_atual_id;

        $this->enviar($candidato, $codigo);

        $candidato->adicionarCurriculo(UploadedFile::fake()->create('cv-novo.pdf', 40, 'application/pdf'));

        $complemento = InscricaoComplemento::where('cd_vaga_emprego', $codigo)->first();

        $this->assertSame($versaoEnviada, $complemento->curriculo_id_vigente);
        $this->assertNotSame($candidato->fresh()->curriculo_atual_id, $complemento->curriculo_id_vigente);
    }
}
