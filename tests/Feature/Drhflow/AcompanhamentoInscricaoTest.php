<?php

namespace Tests\Feature\Drhflow;

use App\Models\Candidato;
use App\Models\CandidatoFormacao;
use App\Models\InscricaoComplemento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\UsaDrhflowFalso;
use Tests\TestCase;

/**
 * "Minhas candidaturas": o andamento derivado do DRHFlow.
 *
 * O portal não tem campo de status próprio — o que o candidato vê é função
 * direta das colunas que o RH preenche.
 */
class AcompanhamentoInscricaoTest extends TestCase
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
            'cidade' => 'Florianópolis',
            'estado' => 'SC',
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

        $candidato->adicionarCurriculo(UploadedFile::fake()->create('cv.pdf', 40, 'application/pdf'));

        return $candidato->fresh(['formacoes', 'curriculos']);
    }

    // ── Andamento ─────────────────────────────────────────────────────────────

    public function test_inscricao_recem_enviada_aparece_como_recebida(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = $this->candidato();
        $this->inscricaoDrhflow(self::CPF, $codigo);

        $props = $this->propsInertia(
            $this->actingAs($candidato, 'candidato')->get('/minha-conta/candidaturas')
        );

        $this->assertCount(1, $props['candidaturas']);
        $this->assertSame('recebida', $props['candidaturas'][0]['andamento']);
        $this->assertSame('Inscrição recebida', $props['candidaturas'][0]['andamento_rotulo']);
    }

    public function test_entrevista_preenchida_pelo_rh_aparece_para_o_candidato(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = $this->candidato();

        // O RH preenche direto no DRHFlow, sem nenhuma ação do portal.
        $this->inscricaoDrhflow(self::CPF, $codigo, [
            'DT_ENTREVISTA' => now()->addWeek()->startOfDay(),
            'HR_ENTREVISTA' => '14:30',
            'DE_LOCAL_ENTREVISTA' => 'Sede da FAPEU, sala 3',
        ]);

        $props = $this->propsInertia(
            $this->actingAs($candidato, 'candidato')->get("/minha-conta/candidaturas/{$codigo}")
        );

        $candidatura = $props['candidatura'];

        $this->assertSame('entrevista_marcada', $candidatura['andamento']);
        $this->assertSame('14:30', $candidatura['entrevista_hora']);
        $this->assertSame('Sede da FAPEU, sala 3', $candidatura['entrevista_local']);
        $this->assertNotNull($candidatura['entrevista_data']);
    }

    public function test_avaliacao_concluida_nao_afirma_aprovacao_nem_reprovacao(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = $this->candidato();
        $this->inscricaoDrhflow(self::CPF, $codigo, [
            'DT_ENTREVISTA' => now()->subWeek(),
            'VL_MEDIA_AVALIACAO' => 8.5,
        ]);

        $resposta = $this->actingAs($candidato, 'candidato')->get("/minha-conta/candidaturas/{$codigo}");

        $this->assertSame('avaliacao_concluida', $this->propsInertia($resposta)['candidatura']['andamento']);
        $this->assertNaoVeInertia($resposta, 'aprovado');
        $this->assertNaoVeInertia($resposta, 'reprovado');
        $this->assertNaoVeInertia($resposta, 'Aprovado');
        $this->assertNaoVeInertia($resposta, 'Reprovado');
    }

    public function test_a_nota_do_rh_nunca_chega_ao_candidato(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = $this->candidato();
        $this->inscricaoDrhflow(self::CPF, $codigo, [
            'VL_MEDIA_AVALIACAO' => 8.5,
            'VL_NOTA_ENTREVISTA' => 9.75,
        ]);

        $resposta = $this->actingAs($candidato, 'candidato')->get("/minha-conta/candidaturas/{$codigo}");

        $this->assertNaoVeInertia($resposta, '8.5');
        $this->assertNaoVeInertia($resposta, '9.75');
    }

    // ── Reunião das duas origens ──────────────────────────────────────────────

    public function test_consulta_reune_o_drhflow_e_os_dados_do_portal(): void
    {
        $codigo = $this->vagaDrhflow(['CD_FUNCAO' => '0373']);
        $candidato = $this->candidato();
        $this->inscricaoDrhflow(self::CPF, $codigo);

        InscricaoComplemento::create([
            'candidato_id' => $candidato->id,
            'cpf' => self::CPF,
            'cd_vaga_emprego' => $codigo,
            'carta_apresentacao' => 'Trabalho com testes há três anos.',
            'conflito_interesse' => false,
            'curriculo_id_vigente' => $candidato->curriculo_atual_id,
            'enviada_em' => now(),
        ]);

        $candidatura = $this->propsInertia(
            $this->actingAs($candidato, 'candidato')->get("/minha-conta/candidaturas/{$codigo}")
        )['candidatura'];

        // Do DRHFlow
        $this->assertSame('recebida', $candidatura['andamento']);
        $this->assertSame('PROGRAMADOR', $candidatura['vaga']['titulo']);
        // Do portal
        $this->assertSame('Trabalho com testes há três anos.', $candidatura['carta_apresentacao']);
        $this->assertSame('cv.pdf', $candidatura['curriculo_nome']);
    }

    public function test_inscricao_de_vaga_ja_encerrada_continua_listada(): void
    {
        $codigo = $this->vagaDrhflow(['CD_SITUACAO' => 2]);
        $candidato = $this->candidato();
        $this->inscricaoDrhflow(self::CPF, $codigo);

        $props = $this->propsInertia(
            $this->actingAs($candidato, 'candidato')->get('/minha-conta/candidaturas')
        );

        $this->assertCount(1, $props['candidaturas']);
        // A vaga saiu do critério de disponibilidade, mas a inscrição existe.
        $this->assertNull($props['candidaturas'][0]['vaga']);
    }

    // ── Isolamento entre candidatos ───────────────────────────────────────────

    public function test_candidato_nao_ve_inscricao_de_outro_cpf(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = $this->candidato();
        $this->inscricaoDrhflow('99988877766', $codigo, ['NM_CANDIDATO' => 'Outra Pessoa']);

        $this->actingAs($candidato, 'candidato')
            ->get("/minha-conta/candidaturas/{$codigo}")
            ->assertStatus(404);

        $props = $this->propsInertia(
            $this->actingAs($candidato, 'candidato')->get('/minha-conta/candidaturas')
        );

        $this->assertCount(0, $props['candidaturas']);
    }

    public function test_download_do_curriculo_so_responde_ao_dono(): void
    {
        $codigo = $this->vagaDrhflow();
        $dono = $this->candidato();
        $this->inscricaoDrhflow(self::CPF, $codigo);

        InscricaoComplemento::create([
            'candidato_id' => $dono->id,
            'cpf' => self::CPF,
            'cd_vaga_emprego' => $codigo,
            'conflito_interesse' => false,
            'curriculo_id_vigente' => $dono->curriculo_atual_id,
            'enviada_em' => now(),
        ]);

        $this->actingAs($dono, 'candidato')
            ->get("/minha-conta/candidaturas/{$codigo}/curriculo")
            ->assertOk();

        $intruso = Candidato::create([
            'nome' => 'Outro Candidato',
            'email' => 'outro@example.com',
            'password' => 'segredo-de-teste',
            'cpf' => '99988877766',
            'telefone' => '48988887777',
            'ativo' => true,
        ]);
        $intruso->forceFill(['email_verified_at' => now()])->save();

        // Sem currículo próprio e sem complemento para este par: não há o que baixar.
        $this->actingAs($intruso, 'candidato')
            ->get("/minha-conta/candidaturas/{$codigo}/curriculo")
            ->assertStatus(404);
    }

    // ── Indisponibilidade ─────────────────────────────────────────────────────

    public function test_drhflow_indisponivel_nao_vira_lista_vazia(): void
    {
        $candidato = $this->candidato();

        DB::purge('drhflow');
        config(['database.connections.drhflow' => [
            'driver' => 'sqlite',
            'database' => '/caminho/inexistente/drhflow.sqlite',
            'prefix' => '',
        ]]);

        $props = $this->propsInertia(
            $this->actingAs($candidato, 'candidato')->get('/minha-conta/candidaturas')
        );

        $this->assertTrue($props['indisponivel']);
    }
}
