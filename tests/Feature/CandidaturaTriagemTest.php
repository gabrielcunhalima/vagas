<?php

namespace Tests\Feature;

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

        $this->candidatura = Candidatura::create([
            'vaga_id'               => $this->vaga->id,
            'nome'                  => 'Candidato de Teste',
            'email'                 => 'candidato@teste.com',
            'cpf'                   => '52998224725',
            'curso'                 => 'Ciência da Computação',
            'instituicao'           => 'UFSC',
            'status'                => 'recebida',
            'pais'                  => 'Brasil',
            'curriculo_path'        => 'vagas/curriculos/fake.pdf',
            'curriculo_nome_original' => 'curriculo.pdf',
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
        Candidatura::create([
            'vaga_id'     => $this->vaga->id,
            'nome'        => 'Candidato Em Análise',
            'email'       => 'analise@teste.com',
            'cpf'         => '71428793860',
            'curso'       => 'Sistemas',
            'instituicao' => 'UFSC',
            'status'      => 'em_analise',
            'pais'        => 'Brasil',
            'curriculo_path' => 'vagas/curriculos/fake2.pdf',
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
        Storage::fake('local');
        Storage::disk('local')->put('vagas/curriculos/fake.pdf', 'conteúdo do pdf');

        $response = $this->actingAs($this->coord)->get(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/curriculo"
        );
        $response->assertStatus(200);
    }

    public function test_download_curriculo_sem_arquivo_retorna_404(): void
    {
        $this->candidatura->update(['curriculo_path' => null]);

        $response = $this->actingAs($this->coord)->get(
            "/coord/vagas/{$this->vaga->id}/candidaturas/{$this->candidatura->id}/curriculo"
        );
        $response->assertStatus(404);
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
