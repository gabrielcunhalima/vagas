<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vagas\Vaga;
use App\Models\Vagas\AlertaVaga;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\Vagas\VagaAutorizadaMail;
use App\Mail\Vagas\VagaRecusadaMail;
use App\Mail\Vagas\AlertaNovaVagaMail;

class VagaGestorTest extends TestCase
{
    use RefreshDatabase;

    private User $coord;
    private User $gestor;
    private User $coord_visitante;

    protected function setUp(): void
    {
        parent::setUp();
        $this->coord           = User::factory()->create(['perfil' => 'coordenador', 'ativo' => true]);
        $this->gestor          = User::factory()->create(['perfil' => 'gestor', 'ativo' => true]);
        $this->coord_visitante = User::factory()->create(['perfil' => 'coordenador', 'ativo' => true]);
    }

    private function criarVaga(array $attrs = []): Vaga
    {
        return Vaga::create(array_merge([
            'titulo'            => 'Vaga para Autorização',
            'descricao'         => 'Descrição da vaga que aguarda autorização do gestor.',
            'requisitos'        => 'Requisitos para a vaga de autorização.',
            'tipo'              => 'estagio',
            'area'              => 'Tecnologia da Informação',
            'modalidade'        => 'presencial',
            'cidade'            => 'Florianópolis',
            'estado'            => 'SC',
            'pais'              => 'Brasil',
            'data_encerramento' => now()->addDays(30)->toDateString(),
            'status'            => 'aguardando_autorizacao',
            'coordenador_id'    => $this->coord->id,
            'notificar_email'   => true,
        ], $attrs));
    }

    // ── Dashboard gestor ──────────────────────────────────────────────────────

    public function test_gestor_acessa_dashboard(): void
    {
        $response = $this->actingAs($this->gestor)->get('/gestor/dashboard');
        $response->assertStatus(200);
        $this->assertComponenteInertia($response, 'Gestor/Dashboard');
    }

    public function test_dashboard_gestor_exibe_stats(): void
    {
        $this->criarVaga();
        $response = $this->actingAs($this->gestor)->get('/gestor/dashboard');
        $response->assertStatus(200);
        $this->assertPropInertia($response, 'stats');
    }

    // ── Listagem gestor ───────────────────────────────────────────────────────

    public function test_gestor_lista_vagas_aguardando_autorizacao(): void
    {
        $this->criarVaga(['titulo' => 'Vaga Aguardando']);
        $this->criarVaga(['titulo' => 'Vaga Ativa', 'status' => 'ativa']);

        $response = $this->actingAs($this->gestor)->get('/gestor/vagas');
        $response->assertStatus(200);
        $this->assertVeInertia($response, 'Vaga Aguardando');
        $this->assertNaoVeInertia($response, 'Vaga Ativa');
    }

    public function test_gestor_lista_vagas_por_status_personalizado(): void
    {
        $this->criarVaga(['titulo' => 'Vaga Ativa', 'status' => 'ativa']);
        $response = $this->actingAs($this->gestor)->get('/gestor/vagas?status=ativa');
        $response->assertStatus(200);
        $this->assertVeInertia($response, 'Vaga Ativa');
    }

    public function test_gestor_busca_vagas(): void
    {
        $this->criarVaga(['titulo' => 'Estágio PHP']);
        $this->criarVaga(['titulo' => 'Analista de Dados']);

        $response = $this->actingAs($this->gestor)->get('/gestor/vagas?busca=PHP');
        $response->assertStatus(200);
        $this->assertVeInertia($response, 'Estágio PHP');
        $this->assertNaoVeInertia($response, 'Analista de Dados');
    }

    // ── Show gestor ───────────────────────────────────────────────────────────

    public function test_gestor_visualiza_detalhes_da_vaga(): void
    {
        $vaga = $this->criarVaga();
        $response = $this->actingAs($this->gestor)->get("/gestor/vagas/{$vaga->id}");
        $response->assertStatus(200);
        $this->assertComponenteInertia($response, 'Gestor/Vagas/Show');
        $this->assertVeInertia($response, $vaga->titulo);
    }

    // ── Autorizar vaga ────────────────────────────────────────────────────────

    public function test_gestor_autoriza_vaga_aguardando(): void
    {
        Mail::fake();
        $vaga = $this->criarVaga();

        $response = $this->actingAs($this->gestor)->patch("/gestor/vagas/{$vaga->id}/autorizar");
        $response->assertRedirect(route('gestor.vagas.index'));
        $response->assertSessionHas('sucesso');

        $this->assertDatabaseHas('vagas', [
            'id'        => $vaga->id,
            'status'    => 'ativa',
            'gestor_id' => $this->gestor->id,
        ]);
        $this->assertNotNull(Vaga::find($vaga->id)->autorizada_em);
    }

    public function test_autorizar_vaga_envia_email_ao_coordenador(): void
    {
        Mail::fake();
        $vaga = $this->criarVaga();

        $this->actingAs($this->gestor)->patch("/gestor/vagas/{$vaga->id}/autorizar");

        Mail::assertSent(VagaAutorizadaMail::class, function ($mail) {
            return $mail->hasTo($this->coord->email);
        });
    }

    public function test_autorizar_vaga_dispara_alertas_compativeis(): void
    {
        Mail::fake();

        AlertaVaga::create([
            'email'      => 'assinante@email.com',
            'areas'      => ['Tecnologia da Informação'],
            'modalidades'=> [],
            'tipos'      => ['estagio'],
            'ativo'      => true,
            'token'      => str_repeat('a', 64),
        ]);

        $vaga = $this->criarVaga([
            'area' => 'Tecnologia da Informação',
            'tipo' => 'estagio',
        ]);

        $this->actingAs($this->gestor)->patch("/gestor/vagas/{$vaga->id}/autorizar");

        Mail::assertSent(AlertaNovaVagaMail::class, function ($mail) {
            return $mail->hasTo('assinante@email.com');
        });
    }

    public function test_alertas_incompativeis_nao_recebem_email(): void
    {
        Mail::fake();

        AlertaVaga::create([
            'email'      => 'assinante@email.com',
            'areas'      => ['Administração'], // área diferente
            'modalidades'=> [],
            'tipos'      => [],
            'ativo'      => true,
            'token'      => str_repeat('b', 64),
        ]);

        $vaga = $this->criarVaga(['area' => 'Tecnologia da Informação']);

        $this->actingAs($this->gestor)->patch("/gestor/vagas/{$vaga->id}/autorizar");

        Mail::assertNotSent(AlertaNovaVagaMail::class);
    }

    public function test_alertas_inativos_nao_recebem_email(): void
    {
        Mail::fake();

        AlertaVaga::create([
            'email'      => 'inativo@email.com',
            'areas'      => [],
            'modalidades'=> [],
            'tipos'      => [],
            'ativo'      => false,
            'token'      => str_repeat('c', 64),
        ]);

        $vaga = $this->criarVaga();
        $this->actingAs($this->gestor)->patch("/gestor/vagas/{$vaga->id}/autorizar");

        Mail::assertNotSent(AlertaNovaVagaMail::class);
    }

    public function test_autorizar_vaga_nao_aguardando_retorna_403(): void
    {
        $vaga = $this->criarVaga(['status' => 'ativa']);
        $response = $this->actingAs($this->gestor)->patch("/gestor/vagas/{$vaga->id}/autorizar");
        $response->assertStatus(403);
    }

    // ── Recusar vaga ──────────────────────────────────────────────────────────

    public function test_gestor_recusa_vaga_com_motivo(): void
    {
        Mail::fake();
        $vaga = $this->criarVaga();

        $response = $this->actingAs($this->gestor)->patch("/gestor/vagas/{$vaga->id}/recusar", [
            'motivo_recusa' => 'Os requisitos descritos são insuficientes.',
        ]);

        $response->assertRedirect(route('gestor.vagas.index'));
        $this->assertDatabaseHas('vagas', [
            'id'            => $vaga->id,
            'status'        => 'recusada',
            'motivo_recusa' => 'Os requisitos descritos são insuficientes.',
        ]);
    }

    public function test_recusar_vaga_envia_email_ao_coordenador(): void
    {
        Mail::fake();
        $vaga = $this->criarVaga();

        $this->actingAs($this->gestor)->patch("/gestor/vagas/{$vaga->id}/recusar", [
            'motivo_recusa' => 'Os requisitos descritos são insuficientes.',
        ]);

        Mail::assertSent(VagaRecusadaMail::class, function ($mail) {
            return $mail->hasTo($this->coord->email);
        });
    }

    public function test_recusar_vaga_sem_motivo_falha(): void
    {
        $vaga = $this->criarVaga();
        $response = $this->actingAs($this->gestor)->patch("/gestor/vagas/{$vaga->id}/recusar", [
            'motivo_recusa' => '',
        ]);
        $response->assertSessionHasErrors('motivo_recusa');
    }

    public function test_recusar_vaga_motivo_muito_curto_falha(): void
    {
        $vaga = $this->criarVaga();
        $response = $this->actingAs($this->gestor)->patch("/gestor/vagas/{$vaga->id}/recusar", [
            'motivo_recusa' => 'Curto',
        ]);
        $response->assertSessionHasErrors('motivo_recusa');
    }

    public function test_recusar_vaga_nao_aguardando_retorna_403(): void
    {
        $vaga = $this->criarVaga(['status' => 'ativa']);
        $response = $this->actingAs($this->gestor)->patch("/gestor/vagas/{$vaga->id}/recusar", [
            'motivo_recusa' => 'Motivo válido com mais de dez caracteres.',
        ]);
        $response->assertStatus(403);
    }

    // ── Isolamento de perfil ──────────────────────────────────────────────────

    public function test_coordenador_nao_acessa_rota_de_autorizacao(): void
    {
        $vaga = $this->criarVaga();
        $response = $this->actingAs($this->coord)->patch("/gestor/vagas/{$vaga->id}/autorizar");
        $response->assertStatus(403);
    }

    public function test_coordenador_nao_acessa_listagem_gestor(): void
    {
        $response = $this->actingAs($this->coord)->get('/gestor/vagas');
        $response->assertStatus(403);
    }
}
