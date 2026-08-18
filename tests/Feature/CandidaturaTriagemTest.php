<?php

namespace Tests\Feature;

use App\Models\Candidato;
use App\Models\User;
use App\Models\Vagas\Vaga;
use App\Models\Vagas\Candidatura;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\Vagas\ConviteEntrevistaMail;
use App\Mail\Vagas\AprovacaoMail;
use App\Mail\Vagas\ReprovacaoMail;

class CandidaturaTriagemTest extends TestCase
{
    use RefreshDatabase;

    private User $coord;
    private User $coord2;
    private User $admin;
    private Vaga $vaga;
    private Candidato $candidato;
    private Candidatura $candidatura;

    protected function setUp(): void
    {
        parent::setUp();
        $this->coord  = User::factory()->create(['perfil' => 'coordenador', 'ativo' => true]);
        $this->coord2 = User::factory()->create(['perfil' => 'coordenador', 'ativo' => true]);
        $this->admin  = User::factory()->create(['perfil' => 'admin', 'ativo' => true]);

        $this->vaga = Vaga::create([
            'titulo'            => 'Vaga com Candidaturas',
            'descricao'         => 'Descrição detalhada da vaga que tem candidaturas para triagem.',
            'requisitos'        => 'Requisitos para a vaga de triagem de candidaturas.',
            'tipo'              => 'estagio',
            'area'              => 'Tecnologia da Informação',
            'modalidade'        => 'presencial',
            'cidade'            => 'Florianópolis',
            'estado'            => 'SC',
            'pais'              => 'Brasil',
            'data_encerramento' => now()->addDays(30)->toDateString(),
            'status'            => 'ativa',
            'coordenador_id'    => $this->coord->id,
            'notificar_email'   => true,
        ]);

        $this->candidato = Candidato::factory()->create([
            'nome'  => 'Candidato de Teste',
            'email' => 'candidato@teste.com',
            'cpf'   => '52998224725',
        ]);

        $this->candidatura = Candidatura::create([
            'vaga_id'      => $this->vaga->id,
            'candidato_id' => $this->candidato->id,
            'status'       => 'recebida',
        ]);
    }

    /** Coloca a candidatura num estado terminal com a data da decisão registrada. */
    private function decidirEm(string $status, \DateTimeInterface $quando): void
    {
        $this->candidatura->update(['status' => $status]);
        $this->candidatura->eventos()->create([
            'tipo'            => \App\Models\Vagas\CandidaturaEvento::TIPO_TRANSICAO,
            'status_anterior' => 'entrevista',
            'status_novo'     => $status,
            'autor_id'        => $this->coord->id,
            'ocorrido_em'     => $quando,
        ]);
    }

    // ── Listagem candidaturas por vaga ────────────────────────────────────────

    public function test_coordenador_lista_candidaturas_da_sua_vaga(): void
    {
        $response = $this->actingAs($this->coord)->get("/coord/vagas/{$this->vaga->id}/candidaturas");
        $response->assertStatus(200);
        $this->assertComponenteInertia($response, 'Coord/Candidaturas/Index');
        $this->assertVeInertia($response, 'Candidato de Teste');
    }

    public function test_coordenador_nao_acessa_candidaturas_de_vaga_alheia(): void
    {
        $response = $this->actingAs($this->coord2)->get("/coord/vagas/{$this->vaga->id}/candidaturas");
        $response->assertStatus(403);
    }

    public function test_admin_acessa_candidaturas_de_qualquer_vaga(): void
    {
        $response = $this->actingAs($this->admin)->get("/coord/vagas/{$this->vaga->id}/candidaturas");
        $response->assertStatus(200);
    }

    public function test_filtro_por_status_na_listagem(): void
    {
        $outroCandidato = Candidato::factory()->create([
            'nome'  => 'Candidato Em Análise',
            'email' => 'analise@teste.com',
            'cpf'   => '71428793860',
        ]);

        Candidatura::create([
            'vaga_id'      => $this->vaga->id,
            'candidato_id' => $outroCandidato->id,
            'status'       => 'em_analise',
        ]);

        $response = $this->actingAs($this->coord)->get("/coord/vagas/{$this->vaga->id}/candidaturas?status=recebida");
        $response->assertStatus(200);
        $this->assertVeInertia($response, 'Candidato de Teste');
        $this->assertNaoVeInertia($response, 'Candidato Em Análise');
    }

    public function test_busca_candidaturas_por_nome(): void
    {
        $response = $this->actingAs($this->coord)
            ->get("/coord/vagas/{$this->vaga->id}/candidaturas?busca=Candidato+de+Teste");
        $response->assertStatus(200);
        $this->assertVeInertia($response, 'Candidato de Teste');
    }

    // ── Listagem todas candidaturas ───────────────────────────────────────────

    public function test_coordenador_acessa_listagem_geral_de_candidaturas(): void
    {
        $response = $this->actingAs($this->coord)->get('/coord/candidaturas');
        $response->assertStatus(200);
        $this->assertComponenteInertia($response, 'Coord/Candidaturas/Todas');
    }

    public function test_listagem_geral_exibe_contadores(): void
    {
        $response = $this->actingAs($this->coord)->get('/coord/candidaturas');
        $this->assertPropInertia($response, 'contadores');
    }

    // ── Show candidatura ──────────────────────────────────────────────────────

    public function test_coordenador_visualiza_candidatura(): void
    {
        $response = $this->actingAs($this->coord)->get(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}"
        );
        $response->assertStatus(200);
        $this->assertComponenteInertia($response, 'Coord/Candidaturas/Show');
        $this->assertVeInertia($response, 'Candidato de Teste');
    }

    public function test_candidatura_de_outra_vaga_retorna_404(): void
    {
        $outraVaga = Vaga::create([
            'titulo'            => 'Outra Vaga',
            'descricao'         => 'Descrição de outra vaga para teste de isolamento.',
            'requisitos'        => 'Requisitos de outra vaga.',
            'tipo'              => 'estagio',
            'area'              => 'Administração',
            'modalidade'        => 'presencial',
            'cidade'            => 'Florianópolis',
            'estado'            => 'SC',
            'pais'              => 'Brasil',
            'data_encerramento' => now()->addDays(30)->toDateString(),
            'status'            => 'ativa',
            'coordenador_id'    => $this->coord->id,
            'notificar_email'   => true,
        ]);

        $response = $this->actingAs($this->coord)->get(
            "/coord/vagas/{$outraVaga->id}/candidaturas/{$this->candidatura->id}"
        );
        $response->assertStatus(404);
    }

    // ── Update status: recebida → em_analise ─────────────────────────────────

    public function test_atualiza_status_para_em_analise(): void
    {
        Mail::fake();
        $response = $this->actingAs($this->coord)->patch(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/status",
            ['status' => 'em_analise']
        );
        $response->assertRedirect();
        $this->assertDatabaseHas('candidaturas', ['id' => $this->candidatura->id, 'status' => 'em_analise']);
        Mail::assertNothingSent(); // em_analise não dispara email
    }

    // ── Update status: → entrevista ───────────────────────────────────────────

    public function test_atualiza_status_para_entrevista_com_data_e_local(): void
    {
        Mail::fake();
        $response = $this->actingAs($this->coord)->patch(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/status",
            [
                'status'           => 'entrevista',
                'entrevista_data'  => now()->addDays(5)->format('Y-m-d H:i:s'),
                'entrevista_local' => 'Sala de Reuniões A',
            ]
        );
        $response->assertRedirect();
        $this->assertDatabaseHas('candidaturas', [
            'id'               => $this->candidatura->id,
            'status'           => 'entrevista',
            'entrevista_local' => 'Sala de Reuniões A',
        ]);
    }

    public function test_entrevista_envia_email_convite(): void
    {
        Mail::fake();
        $this->actingAs($this->coord)->patch(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/status",
            [
                'status'           => 'entrevista',
                'entrevista_data'  => now()->addDays(5)->format('Y-m-d H:i:s'),
                'entrevista_local' => 'Sala de Reuniões A',
            ]
        );
        Mail::assertSent(ConviteEntrevistaMail::class, fn($mail) => $mail->hasTo('candidato@teste.com'));
    }

    public function test_entrevista_sem_data_falha(): void
    {
        $response = $this->actingAs($this->coord)->patch(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/status",
            [
                'status'           => 'entrevista',
                'entrevista_local' => 'Sala A',
            ]
        );
        $response->assertSessionHasErrors('entrevista_data');
    }

    public function test_entrevista_sem_local_falha(): void
    {
        $response = $this->actingAs($this->coord)->patch(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/status",
            [
                'status'          => 'entrevista',
                'entrevista_data' => now()->addDays(5)->format('Y-m-d H:i:s'),
            ]
        );
        $response->assertSessionHasErrors('entrevista_local');
    }

    public function test_entrevista_com_data_passada_falha(): void
    {
        $response = $this->actingAs($this->coord)->patch(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/status",
            [
                'status'           => 'entrevista',
                'entrevista_data'  => now()->subDays(1)->format('Y-m-d H:i:s'),
                'entrevista_local' => 'Sala A',
            ]
        );
        $response->assertSessionHasErrors('entrevista_data');
    }

    // ── Update status: → aprovado ─────────────────────────────────────────────

    public function test_aprovacao_a_partir_de_entrevista(): void
    {
        Mail::fake();
        $this->candidatura->update(['status' => 'entrevista']);

        $this->actingAs($this->coord)->patch(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/status",
            ['status' => 'aprovado']
        );

        $this->assertDatabaseHas('candidaturas', ['id' => $this->candidatura->id, 'status' => 'aprovado']);
        Mail::assertSent(AprovacaoMail::class, fn($mail) => $mail->hasTo('candidato@teste.com'));
    }

    // ── Update status: → reprovado ────────────────────────────────────────────

    public function test_reprovacao_a_partir_de_recebida(): void
    {
        Mail::fake();
        $this->actingAs($this->coord)->patch(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/status",
            ['status' => 'reprovado']
        );

        $this->assertDatabaseHas('candidaturas', ['id' => $this->candidatura->id, 'status' => 'reprovado']);
        Mail::assertSent(ReprovacaoMail::class, fn($mail) => $mail->hasTo('candidato@teste.com'));
    }

    public function test_reprovacao_a_partir_de_em_analise(): void
    {
        Mail::fake();
        $this->candidatura->update(['status' => 'em_analise']);

        $this->actingAs($this->coord)->patch(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/status",
            ['status' => 'reprovado']
        );

        $this->assertDatabaseHas('candidaturas', ['id' => $this->candidatura->id, 'status' => 'reprovado']);
    }

    // ── Transições inválidas ──────────────────────────────────────────────────

    public function test_transicao_invalida_recebida_para_aprovado_retorna_erro(): void
    {
        $response = $this->actingAs($this->coord)->patch(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/status",
            ['status' => 'aprovado']
        );
        $response->assertStatus(422);
        $this->assertDatabaseHas('candidaturas', ['id' => $this->candidatura->id, 'status' => 'recebida']);
    }

    public function test_transicao_aprovado_para_qualquer_status_nao_permitida(): void
    {
        $this->candidatura->update(['status' => 'aprovado']);

        $response = $this->actingAs($this->coord)->patch(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/status",
            ['status' => 'reprovado']
        );
        $response->assertStatus(422);
    }

    public function test_transicao_reprovado_nao_permite_retorno(): void
    {
        $this->candidatura->update(['status' => 'reprovado']);

        $response = $this->actingAs($this->coord)->patch(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/status",
            ['status' => 'em_analise']
        );
        $response->assertStatus(422);
    }

    // ── Atualizar apenas observações (sem mudar status) ───────────────────────

    public function test_salva_observacoes_sem_mudar_status(): void
    {
        Mail::fake();
        $response = $this->actingAs($this->coord)->patch(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/status",
            [
                'status'               => 'recebida',
                'observacoes_internas' => 'Candidato promissor, acompanhar.',
            ]
        );
        $response->assertRedirect();
        $this->assertDatabaseHas('candidaturas', [
            'id'                   => $this->candidatura->id,
            'observacoes_internas' => 'Candidato promissor, acompanhar.',
            'status'               => 'recebida',
        ]);
        Mail::assertNothingSent();
    }

    // ── Download currículo ────────────────────────────────────────────────────

    public function test_download_curriculo_disponivel(): void
    {
        Storage::fake(\App\Models\Candidato::DISCO_CURRICULOS);
        Storage::disk(\App\Models\Candidato::DISCO_CURRICULOS)->put($this->candidato->curriculoAtual->path, 'conteúdo do pdf');

        $response = $this->actingAs($this->coord)->get(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/curriculo"
        );
        $response->assertStatus(200);
    }

    public function test_download_curriculo_sem_curriculo_no_perfil_e_recusado(): void
    {
        $this->candidato->removerCurriculoAtual();

        $response = $this->actingAs($this->coord)->get(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/curriculo"
        );
        $response->assertForbidden();
    }

    // ─── Decaimento de acesso ────────────────────────────────────────────────

    public function test_reprovada_dentro_da_carencia_mantem_os_dados_visiveis(): void
    {
        $this->decidirEm('reprovado', now()->subDays(10));

        $res = $this->actingAs($this->coord)->get(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}"
        );

        $res->assertOk();
        $this->assertFalse($this->propsInertia($res)['acessoExpirado']);
        $this->assertVeInertia($res, 'Candidato de Teste');
    }

    public function test_reprovada_apos_a_carencia_esconde_os_dados_pessoais(): void
    {
        $this->decidirEm('reprovado', now()->subDays(120));

        $res = $this->actingAs($this->coord)->get(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}"
        );

        $res->assertOk();
        $this->assertTrue($this->propsInertia($res)['acessoExpirado']);
        $this->assertNaoVeInertia($res, 'Candidato de Teste');
        $this->assertNaoVeInertia($res, 'candidato@teste.com');
    }

    public function test_aprovada_nao_perde_acesso_com_o_tempo(): void
    {
        $this->decidirEm('aprovado', now()->subDays(400));

        $res = $this->actingAs($this->coord)->get(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}"
        );

        $res->assertOk();
        $this->assertFalse($this->propsInertia($res)['acessoExpirado']);
        $this->assertVeInertia($res, 'Candidato de Teste');
    }

    public function test_candidatura_parada_em_vaga_encerrada_perde_acesso(): void
    {
        $this->vaga->update([
            'status'            => 'encerrada',
            'data_encerramento' => now()->subDays(120)->toDateString(),
        ]);

        $res = $this->actingAs($this->coord)->get(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}"
        );

        $res->assertOk();
        $this->assertTrue($this->propsInertia($res)['acessoExpirado']);
    }

    public function test_registro_do_processo_permanece_apos_o_decaimento(): void
    {
        $this->candidatura->update(['observacoes_internas' => 'Anotação da equipe.']);
        $this->decidirEm('reprovado', now()->subDays(120));

        $res = $this->actingAs($this->coord)->get(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}"
        );

        $this->assertVeInertia($res, 'Anotação da equipe.');
        $this->assertNotEmpty($this->propsInertia($res)['motivoExpiracao']);
    }

    public function test_download_de_curriculo_recusado_apos_o_decaimento(): void
    {
        Storage::fake(\App\Models\Candidato::DISCO_CURRICULOS);
        Storage::disk(\App\Models\Candidato::DISCO_CURRICULOS)->put($this->candidato->curriculoAtual->path, 'conteúdo do pdf');
        $this->decidirEm('reprovado', now()->subDays(120));

        $this->actingAs($this->coord)
            ->get("/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/curriculo")
            ->assertForbidden();
    }

    public function test_listagem_nao_expoe_dados_de_candidatura_com_acesso_expirado(): void
    {
        $this->decidirEm('reprovado', now()->subDays(120));

        $res = $this->actingAs($this->coord)->get("/coord/vagas/{$this->vaga->id}/candidaturas");

        $this->assertNaoVeInertia($res, 'Candidato de Teste');
        $this->assertNaoVeInertia($res, 'candidato@teste.com');
    }

    public function test_conta_excluida_encerra_o_acesso_na_hora(): void
    {
        app(\App\Services\AnonimizacaoService::class)->anonimizarCandidato($this->candidato);

        $res = $this->actingAs($this->coord)->get(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}"
        );

        $res->assertOk();
        $this->assertTrue($this->propsInertia($res)['acessoExpirado']);
        $this->assertNaoVeInertia($res, 'candidato@teste.com');
    }

    // ─── Histórico do processo ───────────────────────────────────────────────

    public function test_transicao_de_status_registra_evento(): void
    {
        $this->actingAs($this->coord)->patch(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/status",
            ['status' => 'em_analise']
        );

        $evento = $this->candidatura->eventos()->latest('ocorrido_em')->first();

        $this->assertSame('recebida', $evento->status_anterior);
        $this->assertSame('em_analise', $evento->status_novo);
        $this->assertSame($this->coord->id, $evento->autor_id);
    }

    public function test_historico_chega_na_tela_da_candidatura(): void
    {
        $this->actingAs($this->coord)->patch(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/status",
            ['status' => 'em_analise']
        );

        $res = $this->actingAs($this->coord)->get(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}"
        );

        $this->assertNotEmpty($this->propsInertia($res)['candidatura']['eventos']);
    }

    public function test_coordenador_nao_acessa_candidatura_de_vaga_alheia_status(): void
    {
        $response = $this->actingAs($this->coord2)->patch(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/status",
            ['status' => 'em_analise']
        );
        $response->assertStatus(403);
    }
}
