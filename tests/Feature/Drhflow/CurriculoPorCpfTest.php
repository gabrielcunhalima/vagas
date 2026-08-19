<?php

namespace Tests\Feature\Drhflow;

use App\Models\Candidato;
use App\Models\CandidatoFormacao;
use App\Models\InscricaoComplemento;
use App\Services\AnonimizacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\UsaDrhflowFalso;
use Tests\TestCase;

/**
 * O armazenamento de currículos em pasta por CPF e o que a exclusão de conta faz
 * com eles.
 */
class CurriculoPorCpfTest extends TestCase
{
    use RefreshDatabase, UsaDrhflowFalso;

    private const CPF = '00001594923';

    protected function setUp(): void
    {
        parent::setUp();
        $this->configurarDrhflowFalso();
        Storage::fake(Candidato::DISCO_CURRICULOS);
    }

    private function candidato(): Candidato
    {
        $candidato = Candidato::create([
            'nome' => 'Maria da Silva',
            'email' => 'maria@example.com',
            'password' => 'segredo-de-teste',
            'cpf' => '000.015.949-23',
            'telefone' => '48999990000',
            'nacionalidade' => 'Brasileira',
            'possui_acessibilidade' => false,
            'ativo' => true,
        ]);

        $candidato->forceFill(['email_verified_at' => now()])->save();

        CandidatoFormacao::create([
            'candidato_id' => $candidato->id,
            'nivel_escolaridade' => 'graduacao',
            'situacao_curso' => 'concluido',
            'curso' => 'Ciência da Computação',
            'instituicao' => 'UFSC',
            'previsao_conclusao' => now()->subYear(),
        ]);

        return $candidato;
    }

    private function disco()
    {
        return Storage::disk(Candidato::DISCO_CURRICULOS);
    }

    // ── Gravação ──────────────────────────────────────────────────────────────

    public function test_primeiro_curriculo_vai_para_a_pasta_do_cpf(): void
    {
        $candidato = $this->candidato();

        $versao = $candidato->adicionarCurriculo(
            UploadedFile::fake()->create('meu-cv.pdf', 40, 'application/pdf')
        );

        $this->assertStringStartsWith(self::CPF.'/', $versao->path);
        $this->disco()->assertExists($versao->path);
        $this->assertTrue($this->disco()->directoryExists(self::CPF));
    }

    public function test_nome_do_arquivo_nao_e_o_enviado_pelo_candidato(): void
    {
        $candidato = $this->candidato();

        $versao = $candidato->adicionarCurriculo(
            UploadedFile::fake()->create('Maria da Silva - Curriculo.pdf', 40, 'application/pdf')
        );

        // O nome enviado costuma conter o nome da pessoa e não deve virar parte
        // de um caminho adivinhável — mas continua guardado para o download.
        $this->assertStringNotContainsString('Maria', $versao->path);
        $this->assertMatchesRegularExpression('#^\d{11}/[0-9a-f-]{36}\.pdf$#', $versao->path);
        $this->assertSame('Maria da Silva - Curriculo.pdf', $versao->nome_original);
    }

    public function test_versoes_convivem_na_mesma_pasta(): void
    {
        $candidato = $this->candidato();

        $primeira = $candidato->adicionarCurriculo(UploadedFile::fake()->create('v1.pdf', 40, 'application/pdf'));
        $segunda = $candidato->adicionarCurriculo(UploadedFile::fake()->create('v2.pdf', 40, 'application/pdf'));

        $this->assertNotSame($primeira->path, $segunda->path);
        $this->disco()->assertExists($primeira->path);
        $this->disco()->assertExists($segunda->path);
        $this->assertCount(2, $this->disco()->files(self::CPF));
        $this->assertSame($segunda->id, $candidato->fresh()->curriculo_atual_id);
    }

    public function test_raiz_do_disco_fica_fora_do_diretorio_servido_pela_web(): void
    {
        $raiz = str_replace('\\', '/', (string) config('filesystems.disks.curriculos.root'));
        $publico = str_replace('\\', '/', public_path());

        $this->assertStringStartsNotWith($publico, $raiz, 'A raiz dos currículos está dentro de public/.');
        $this->assertSame('private', config('filesystems.disks.curriculos.visibility'));
        $this->assertTrue(config('filesystems.disks.curriculos.throw'));
        $this->assertFalse(config('filesystems.disks.curriculos.serve'));
    }

    // ── Download ──────────────────────────────────────────────────────────────

    public function test_download_do_perfil_responde_ao_dono(): void
    {
        $candidato = $this->candidato();
        $candidato->adicionarCurriculo(UploadedFile::fake()->create('cv.pdf', 40, 'application/pdf'));

        $this->actingAs($candidato->fresh(), 'candidato')
            ->get('/minha-conta/meus-dados/curriculo')
            ->assertOk()
            ->assertDownload('cv.pdf');
    }

    public function test_download_exige_autenticacao(): void
    {
        $candidato = $this->candidato();
        $candidato->adicionarCurriculo(UploadedFile::fake()->create('cv.pdf', 40, 'application/pdf'));

        $this->get('/minha-conta/meus-dados/curriculo')->assertRedirect();
    }

    public function test_outro_candidato_nao_alcanca_o_curriculo_alheio(): void
    {
        $dono = $this->candidato();
        $dono->adicionarCurriculo(UploadedFile::fake()->create('cv.pdf', 40, 'application/pdf'));

        $intruso = Candidato::create([
            'nome' => 'Outro',
            'email' => 'outro@example.com',
            'password' => 'segredo-de-teste',
            'cpf' => '99988877766',
            'ativo' => true,
        ]);
        $intruso->forceFill(['email_verified_at' => now()])->save();

        // O download do perfil sempre resolve pelo autenticado: o intruso não
        // tem currículo e recebe 404, nunca o arquivo do dono.
        $this->actingAs($intruso, 'candidato')
            ->get('/minha-conta/meus-dados/curriculo')
            ->assertStatus(404);
    }

    // ── Exclusão de conta ─────────────────────────────────────────────────────

    public function test_exclusao_remove_todas_as_versoes_da_pasta_do_cpf(): void
    {
        $candidato = $this->candidato();
        $primeira = $candidato->adicionarCurriculo(UploadedFile::fake()->create('v1.pdf', 40, 'application/pdf'));
        $segunda = $candidato->adicionarCurriculo(UploadedFile::fake()->create('v2.pdf', 40, 'application/pdf'));

        app(AnonimizacaoService::class)->anonimizarCandidato($candidato->fresh('curriculos'));

        $this->disco()->assertMissing($primeira->path);
        $this->disco()->assertMissing($segunda->path);
        $this->assertFalse($this->disco()->directoryExists(self::CPF));
    }

    public function test_exclusao_anonimiza_a_linha_do_drhflow_sem_remove_la(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = $this->candidato();
        $candidato->adicionarCurriculo(UploadedFile::fake()->create('cv.pdf', 40, 'application/pdf'));

        $this->inscricaoDrhflow(self::CPF, $codigo, [
            'NM_CANDIDATO' => 'Maria da Silva',
            'DE_EMAIL' => 'maria@example.com',
            'NU_TELEFONE_CELULAR' => '48999990000',
            'NM_LOGRADOURO' => 'Rua Lauro Linhares',
            'CD_UF_ENDERECO' => 'SC',
            // Preenchidos pelo RH: precisam sobreviver.
            'DT_ENTREVISTA' => now()->subWeek()->startOfDay(),
            'DE_LOCAL_ENTREVISTA' => 'Sede da FAPEU',
            'VL_MEDIA_AVALIACAO' => 8.5,
        ]);

        app(AnonimizacaoService::class)->anonimizarCandidato($candidato->fresh('curriculos'));

        $linha = $this->drhflow()->table('EN_CANDIDATO_VAGA_EMPREGO')
            ->where('CD_VAGA_EMPREGO', $codigo)
            ->first();

        // A linha continua existindo.
        $this->assertNotNull($linha);
        $this->assertSame(self::CPF, $linha->NU_CPF);

        // A identidade saiu.
        $this->assertSame('Candidato excluído', $linha->NM_CANDIDATO);
        $this->assertStringContainsString('@removido.invalid', $linha->DE_EMAIL);
        $this->assertNull($linha->NU_TELEFONE_CELULAR);
        $this->assertNull($linha->NM_LOGRADOURO);
        $this->assertNull($linha->CD_UF_ENDERECO);

        // O trabalho do RH ficou intacto.
        $this->assertNotNull($linha->DT_ENTREVISTA);
        $this->assertSame('Sede da FAPEU', $linha->DE_LOCAL_ENTREVISTA);
        $this->assertEquals(8.5, $linha->VL_MEDIA_AVALIACAO);
    }

    public function test_exclusao_remove_o_complemento_local(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = $this->candidato();
        $candidato->adicionarCurriculo(UploadedFile::fake()->create('cv.pdf', 40, 'application/pdf'));

        InscricaoComplemento::create([
            'candidato_id' => $candidato->id,
            'cpf' => self::CPF,
            'cd_vaga_emprego' => $codigo,
            'carta_apresentacao' => 'Texto pessoal escrito pela titular.',
            'conflito_interesse' => false,
            'enviada_em' => now(),
        ]);

        app(AnonimizacaoService::class)->anonimizarCandidato($candidato->fresh('curriculos'));

        $this->assertDatabaseCount('inscricao_complementos', 0);
    }

    public function test_drhflow_fora_do_ar_nao_impede_a_exclusao_da_conta(): void
    {
        $candidato = $this->candidato();
        $candidato->adicionarCurriculo(UploadedFile::fake()->create('cv.pdf', 40, 'application/pdf'));

        DB::purge('drhflow');
        config(['database.connections.drhflow' => [
            'driver' => 'sqlite',
            'database' => '/caminho/inexistente/drhflow.sqlite',
            'prefix' => '',
        ]]);

        // O titular tem direito à exclusão, e o portal não controla a
        // disponibilidade do banco do RH.
        app(AnonimizacaoService::class)->anonimizarCandidato($candidato->fresh('curriculos'));

        $this->assertSoftDeleted('candidatos', ['id' => $candidato->id]);
    }
}
