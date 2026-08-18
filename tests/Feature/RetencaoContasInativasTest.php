<?php

namespace Tests\Feature;

use App\Models\Candidato;
use App\Models\User;
use App\Models\Vagas\Vaga;
use App\Models\Vagas\Candidatura;
use App\Notifications\Candidato\AvisoInatividadeCandidato;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\UsaDrhflowFalso;

/**
 * A anonimização por inatividade é irreversível, então o que estes testes guardam
 * é sobretudo o que NÃO deve acontecer: apagar quem ainda usa o portal, quem ainda
 * concorre a uma vaga, ou quem voltou depois de avisado.
 */
class RetencaoContasInativasTest extends TestCase
{
    use RefreshDatabase, UsaDrhflowFalso;

    protected function setUp(): void
    {
        parent::setUp();

        // A rotina consulta o DRHFlow para saber se a conta ainda concorre a
        // alguma vaga. Sem a origem alcançável ela trata todas como em
        // andamento e não anonimiza nada — o que é o comportamento correto em
        // produção, mas deixaria estes testes sem objeto.
        $this->configurarDrhflowFalso();
    }

    /** Conta parada há N anos — sem login, sem edição de perfil, sem candidatura. */
    private function contaInativaHa(int $anos, array $over = []): Candidato
    {
        $candidato = Candidato::factory()->create($over);

        $candidato->forceFill(['ultimo_acesso_em' => now()->subYears($anos)->subMonth()])->save();

        return $candidato->fresh();
    }

    private function rodar(array $opcoes = []): void
    {
        $this->artisan('vagas:anonimizar-contas-inativas', $opcoes);
    }

    // ─── Fase 1: aviso ───────────────────────────────────────────────────────

    public function test_conta_inativa_ha_dois_anos_recebe_aviso(): void
    {
        Notification::fake();
        $candidato = $this->contaInativaHa(2);

        $this->rodar();

        Notification::assertSentTo($candidato, AvisoInatividadeCandidato::class);
        $this->assertNotNull($candidato->fresh()->aviso_inatividade_em);
    }

    public function test_conta_ativa_nao_recebe_aviso(): void
    {
        Notification::fake();
        $candidato = Candidato::factory()->create();

        $this->rodar();

        Notification::assertNothingSentTo($candidato);
        $this->assertNull($candidato->fresh()->aviso_inatividade_em);
    }

    public function test_aviso_nao_e_reenviado_a_cada_execucao(): void
    {
        Notification::fake();
        $candidato = $this->contaInativaHa(2);

        $this->rodar();
        $this->rodar();

        Notification::assertSentToTimes($candidato, AvisoInatividadeCandidato::class, 1);
    }

    public function test_candidatura_recente_conta_como_atividade(): void
    {
        Notification::fake();
        $candidato = $this->contaInativaHa(2);
        $this->candidaturaPara($candidato, 'reprovado');

        $this->rodar();

        Notification::assertNothingSentTo($candidato);
    }

    // ─── Fase 2: anonimização ────────────────────────────────────────────────

    public function test_anonimiza_somente_apos_a_carencia(): void
    {
        $candidato = $this->contaInativaHa(2);
        $candidato->forceFill(['aviso_inatividade_em' => now()->subDays(10)])->save();

        $this->rodar();

        $this->assertNull(Candidato::withTrashed()->find($candidato->id)->deleted_at);

        $candidato->forceFill(['aviso_inatividade_em' => now()->subDays(31)])->save();
        $this->rodar();

        $perfil = Candidato::withTrashed()->find($candidato->id);
        $this->assertNotNull($perfil->deleted_at);
        $this->assertSame('Candidato excluído', $perfil->nome);
    }

    public function test_nao_anonimiza_sem_aviso_previo(): void
    {
        $candidato = $this->contaInativaHa(5);

        $this->rodar();

        // A primeira execução apenas avisa, por mais antiga que a conta seja.
        $this->assertNull(Candidato::withTrashed()->find($candidato->id)->deleted_at);
        $this->assertNotNull($candidato->fresh()->aviso_inatividade_em);
    }

    public function test_login_cancela_o_processo_de_anonimizacao(): void
    {
        $candidato = $this->contaInativaHa(2);
        $candidato->forceFill(['aviso_inatividade_em' => now()->subDays(40)])->save();

        // O candidato volta antes da varredura seguinte.
        $this->post(route('candidato.login.post'), [
            'email'    => $candidato->email,
            'password' => 'password',
        ])->assertRedirect();

        $this->assertNull($candidato->fresh()->aviso_inatividade_em);

        $this->rodar();

        $this->assertNull(Candidato::withTrashed()->find($candidato->id)->deleted_at);
    }

    // ─── Guardas ─────────────────────────────────────────────────────────────

    public function test_processo_em_aberto_protege_a_conta(): void
    {
        Notification::fake();
        $candidato = $this->contaInativaHa(3);
        $this->candidaturaPara($candidato, 'entrevista', now()->subYears(3));

        $this->rodar();

        Notification::assertNothingSentTo($candidato);
        $this->assertNull($candidato->fresh()->aviso_inatividade_em);
    }

    public function test_inscricao_sem_avaliacao_no_drhflow_protege_a_conta(): void
    {
        Notification::fake();
        $candidato = $this->contaInativaHa(3);
        $codigo = $this->vagaDrhflow();
        $this->inscricaoDrhflow(
            \App\Support\Drhflow\MapeadorInscricao::cpf($candidato),
            $codigo,
            ['DT_CADASTRO' => now()->subYears(3)]
        );

        $this->rodar();

        Notification::assertNothingSentTo($candidato);
        $this->assertNull($candidato->fresh()->aviso_inatividade_em);
    }

    public function test_avaliacao_concluida_no_drhflow_nao_protege_a_conta(): void
    {
        Notification::fake();
        $candidato = $this->contaInativaHa(3);
        $codigo = $this->vagaDrhflow();
        $this->inscricaoDrhflow(
            \App\Support\Drhflow\MapeadorInscricao::cpf($candidato),
            $codigo,
            ['DT_CADASTRO' => now()->subYears(3), 'VL_MEDIA_AVALIACAO' => 7.5]
        );

        $this->rodar();

        Notification::assertSentTo($candidato, AvisoInatividadeCandidato::class);
    }

    public function test_drhflow_fora_do_ar_impede_a_anonimizacao(): void
    {
        // Anonimizar é irreversível. Sem conseguir confirmar que ninguém está
        // concorrendo, a rotina precisa não agir — adiar não custa nada.
        Notification::fake();
        $candidato = $this->contaInativaHa(3);

        \Illuminate\Support\Facades\DB::purge('drhflow');
        config(['database.connections.drhflow' => [
            'driver' => 'sqlite',
            'database' => '/caminho/inexistente/drhflow.sqlite',
            'prefix' => '',
        ]]);

        $this->rodar();

        Notification::assertNothingSentTo($candidato);
        $this->assertNull($candidato->fresh()->aviso_inatividade_em);
    }

    public function test_processo_encerrado_nao_protege_a_conta(): void
    {
        Notification::fake();
        $candidato = $this->contaInativaHa(3);
        $this->candidaturaPara($candidato, 'reprovado', now()->subYears(3));

        $this->rodar();

        Notification::assertSentTo($candidato, AvisoInatividadeCandidato::class);
    }

    public function test_carimbo_do_aviso_nao_conta_como_atividade(): void
    {
        // A rotina escreve na conta ao avisar. Se essa escrita fosse lida como uso,
        // a conta pareceria viva por efeito da própria varredura e nunca chegaria
        // à fase 2 — foi assim que o primeiro corte da política falhou.
        $candidato = $this->contaInativaHa(2);
        $atividadeAntes = $candidato->ultimaAtividadeEm();

        $this->rodar();

        $this->assertNotNull($candidato->fresh()->aviso_inatividade_em);
        $this->assertEquals(
            $atividadeAntes->timestamp,
            $candidato->fresh()->ultimaAtividadeEm()->timestamp,
        );
    }

    public function test_edicao_de_perfil_conta_como_atividade(): void
    {
        Notification::fake();
        $candidato = $this->contaInativaHa(2);

        $this->actingAs($candidato, 'candidato')->put(route('candidato.perfil.update'), [
            'nome'  => $candidato->nome,
            'email' => $candidato->email,
            'cpf'   => $candidato->cpf_formatado,
        ])->assertSessionHasNoErrors();

        $this->rodar();

        Notification::assertNothingSentTo($candidato);
    }

    public function test_dry_run_nao_altera_nada(): void
    {
        Notification::fake();
        $candidato = $this->contaInativaHa(2);

        $this->rodar(['--dry-run' => true]);

        Notification::assertNothingSentTo($candidato);
        $this->assertNull($candidato->fresh()->aviso_inatividade_em);
    }

    private function candidaturaPara(Candidato $candidato, string $status, ?\DateTimeInterface $quando = null): Candidatura
    {
        $coord = User::factory()->create(['perfil' => 'coordenador', 'ativo' => true]);

        $vaga = Vaga::create([
            'titulo'            => 'Vaga do teste de retenção',
            'descricao'         => 'Descrição da vaga usada no teste de retenção de contas inativas.',
            'requisitos'        => 'Requisitos da vaga de teste.',
            'tipo'              => 'estagio',
            'area'              => 'Tecnologia da Informação',
            'modalidade'        => 'presencial',
            'cidade'            => 'Florianópolis',
            'estado'            => 'SC',
            'pais'              => 'Brasil',
            'data_encerramento' => now()->addDays(30)->toDateString(),
            'status'            => 'ativa',
            'coordenador_id'    => $coord->id,
        ]);

        $candidatura = Candidatura::create([
            'vaga_id'      => $vaga->id,
            'candidato_id' => $candidato->id,
            'status'       => $status,
        ]);

        if ($quando) {
            Candidatura::withoutTimestamps(
                fn () => $candidatura->forceFill(['created_at' => $quando, 'updated_at' => $quando])->save()
            );
        }

        return $candidatura;
    }
}
