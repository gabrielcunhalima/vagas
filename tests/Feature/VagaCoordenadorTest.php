<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vagas\Vaga;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class VagaCoordenadorTest extends TestCase
{
    use RefreshDatabase;

    private User $coord;
    private User $coord2;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->coord  = User::factory()->create(['perfil' => 'coordenador', 'ativo' => true, 'password' => Hash::make('password')]);
        $this->coord2 = User::factory()->create(['perfil' => 'coordenador', 'ativo' => true, 'password' => Hash::make('password')]);
        $this->admin  = User::factory()->create(['perfil' => 'admin', 'ativo' => true, 'password' => Hash::make('password')]);
    }

    private function vagaData(array $over = []): array
    {
        return array_merge([
            'titulo'            => 'Estágio em Desenvolvimento de Sistemas',
            'descricao'         => 'Descrição completa da vaga de desenvolvimento de sistemas.',
            'requisitos'        => 'Requisitos mínimos: cursando TI a partir do 4º semestre.',
            'tipo'              => 'estagio',
            'area'              => 'Tecnologia da Informação',
            'modalidade'        => 'presencial',
            'cidade'            => 'Florianópolis',
            'estado'            => 'SC',
            'pais'              => 'Brasil',
            'data_encerramento' => now()->addDays(30)->format('Y-m-d'),
            'remuneracao'       => '1200.00',
            'carga_horaria'     => 30,
            'notificar_email'   => true,
        ], $over);
    }

    private function criarVaga(array $attrs = [], ?User $coord = null): Vaga
    {
        $owner = $coord ?? $this->coord;
        return Vaga::create(array_merge([
            'titulo'            => 'Vaga de Teste',
            'descricao'         => 'Descrição detalhada da vaga de teste criada nos testes.',
            'requisitos'        => 'Requisitos mínimos para a vaga de teste.',
            'tipo'              => 'estagio',
            'area'              => 'Tecnologia da Informação',
            'modalidade'        => 'presencial',
            'cidade'            => 'Florianópolis',
            'estado'            => 'SC',
            'pais'              => 'Brasil',
            'data_encerramento' => now()->addDays(30)->toDateString(),
            'status'            => 'rascunho',
            'coordenador_id'    => $owner->id,
            'notificar_email'   => true,
        ], $attrs));
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function test_coordenador_acessa_dashboard(): void
    {
        $response = $this->actingAs($this->coord)->get('/coord/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('coord.dashboard');
    }

    public function test_dashboard_exibe_stats(): void
    {
        $this->criarVaga(['status' => 'ativa']);
        $this->criarVaga(['status' => 'rascunho']);

        $response = $this->actingAs($this->coord)->get('/coord/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('stats');
    }

    // ── Listagem de vagas ─────────────────────────────────────────────────────

    public function test_coordenador_lista_apenas_suas_vagas(): void
    {
        $this->criarVaga(['titulo' => 'Vaga do Coord1']);
        $this->criarVaga(['titulo' => 'Vaga do Coord2'], $this->coord2);

        $response = $this->actingAs($this->coord)->get('/coord/vagas');
        $response->assertStatus(200);
        $response->assertSee('Vaga do Coord1');
        $response->assertDontSee('Vaga do Coord2');
    }

    public function test_admin_lista_todas_as_vagas(): void
    {
        $this->criarVaga(['titulo' => 'Vaga do Coord1']);
        $this->criarVaga(['titulo' => 'Vaga do Coord2'], $this->coord2);

        $response = $this->actingAs($this->admin)->get('/coord/vagas');
        $response->assertStatus(200);
        $response->assertSee('Vaga do Coord1');
        $response->assertSee('Vaga do Coord2');
    }

    public function test_listagem_com_filtro_status(): void
    {
        $this->criarVaga(['titulo' => 'Vaga Rascunho', 'status' => 'rascunho']);
        $this->criarVaga(['titulo' => 'Vaga Ativa', 'status' => 'ativa']);

        $response = $this->actingAs($this->coord)->get('/coord/vagas?status=rascunho');
        $response->assertStatus(200);
        $response->assertSee('Vaga Rascunho');
        $response->assertDontSee('Vaga Ativa');
    }

    public function test_listagem_com_filtro_busca(): void
    {
        $this->criarVaga(['titulo' => 'Estágio PHP Laravel']);
        $this->criarVaga(['titulo' => 'Analista de Dados']);

        $response = $this->actingAs($this->coord)->get('/coord/vagas?busca=Laravel');
        $response->assertStatus(200);
        $response->assertSee('Estágio PHP Laravel');
        $response->assertDontSee('Analista de Dados');
    }

    // ── Formulário de criação ─────────────────────────────────────────────────

    public function test_acessa_formulario_criacao(): void
    {
        $response = $this->actingAs($this->coord)->get('/coord/vagas/create');
        $response->assertStatus(200);
        $response->assertViewIs('coord.vagas.form');
    }

    // ── Store - Salvar como rascunho ──────────────────────────────────────────

    public function test_cria_vaga_como_rascunho(): void
    {
        $response = $this->actingAs($this->coord)->post('/coord/vagas', array_merge(
            $this->vagaData(),
            ['acao' => 'salvar']
        ));
        $response->assertRedirect(route('coord.vagas.index'));
        $response->assertSessionHas('sucesso');
        $this->assertDatabaseHas('vagas', [
            'titulo'         => 'Estágio em Desenvolvimento de Sistemas',
            'status'         => 'rascunho',
            'coordenador_id' => $this->coord->id,
        ]);
    }

    // ── Store - Publicar (aguardando autorização) ─────────────────────────────

    public function test_cria_vaga_como_aguardando_autorizacao(): void
    {
        $response = $this->actingAs($this->coord)->post('/coord/vagas', array_merge(
            $this->vagaData(),
            ['acao' => 'publicar']
        ));
        $response->assertRedirect(route('coord.vagas.index'));
        $this->assertDatabaseHas('vagas', [
            'titulo'  => 'Estágio em Desenvolvimento de Sistemas',
            'status'  => 'aguardando_autorizacao',
        ]);
    }

    // ── Validação na criação ──────────────────────────────────────────────────

    public function test_criar_vaga_sem_titulo_falha(): void
    {
        $dados = $this->vagaData(['titulo' => '']);
        $response = $this->actingAs($this->coord)->post('/coord/vagas', $dados);
        $response->assertSessionHasErrors('titulo');
    }

    public function test_criar_vaga_sem_descricao_falha(): void
    {
        $dados = $this->vagaData(['descricao' => '']);
        $response = $this->actingAs($this->coord)->post('/coord/vagas', $dados);
        $response->assertSessionHasErrors('descricao');
    }

    public function test_criar_vaga_sem_requisitos_falha(): void
    {
        $dados = $this->vagaData(['requisitos' => '']);
        $response = $this->actingAs($this->coord)->post('/coord/vagas', $dados);
        $response->assertSessionHasErrors('requisitos');
    }

    public function test_criar_vaga_data_encerramento_passada_falha(): void
    {
        $dados = $this->vagaData(['data_encerramento' => now()->subDays(1)->format('Y-m-d')]);
        $response = $this->actingAs($this->coord)->post('/coord/vagas', $dados);
        $response->assertSessionHasErrors('data_encerramento');
    }

    public function test_criar_vaga_tipo_invalido_falha(): void
    {
        $dados = $this->vagaData(['tipo' => 'invalido']);
        $response = $this->actingAs($this->coord)->post('/coord/vagas', $dados);
        $response->assertSessionHasErrors('tipo');
    }

    public function test_criar_vaga_modalidade_invalida_falha(): void
    {
        $dados = $this->vagaData(['modalidade' => 'invalida']);
        $response = $this->actingAs($this->coord)->post('/coord/vagas', $dados);
        $response->assertSessionHasErrors('modalidade');
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function test_acessa_formulario_edicao_de_rascunho(): void
    {
        $vaga = $this->criarVaga(['status' => 'rascunho']);
        $response = $this->actingAs($this->coord)->get("/coord/vagas/{$vaga->id}/edit");
        $response->assertStatus(200);
        $response->assertViewIs('coord.vagas.form');
    }

    public function test_nao_pode_editar_vaga_ativa(): void
    {
        $vaga = $this->criarVaga(['status' => 'ativa']);
        $response = $this->actingAs($this->coord)->get("/coord/vagas/{$vaga->id}/edit");
        $response->assertStatus(403);
    }

    public function test_nao_pode_editar_vaga_encerrada(): void
    {
        $vaga = $this->criarVaga(['status' => 'encerrada']);
        $response = $this->actingAs($this->coord)->get("/coord/vagas/{$vaga->id}/edit");
        $response->assertStatus(403);
    }

    public function test_coordenador_nao_edita_vaga_de_outro_coordenador(): void
    {
        $vaga = $this->criarVaga(['status' => 'rascunho'], $this->coord2);
        $response = $this->actingAs($this->coord)->get("/coord/vagas/{$vaga->id}/edit");
        $response->assertStatus(403);
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function test_atualiza_vaga_rascunho(): void
    {
        $vaga = $this->criarVaga(['status' => 'rascunho']);
        $response = $this->actingAs($this->coord)->put("/coord/vagas/{$vaga->id}", array_merge(
            $this->vagaData(['titulo' => 'Título Atualizado']),
            ['acao' => 'salvar']
        ));
        $response->assertRedirect(route('coord.vagas.index'));
        $this->assertDatabaseHas('vagas', ['id' => $vaga->id, 'titulo' => 'Título Atualizado']);
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function test_coordenador_deleta_sua_vaga(): void
    {
        $vaga = $this->criarVaga(['status' => 'rascunho']);
        $response = $this->actingAs($this->coord)->delete("/coord/vagas/{$vaga->id}");
        $response->assertRedirect(route('coord.vagas.index'));
        $this->assertSoftDeleted('vagas', ['id' => $vaga->id]);
    }

    public function test_coordenador_nao_deleta_vaga_de_outro(): void
    {
        $vaga = $this->criarVaga(['status' => 'rascunho'], $this->coord2);
        $response = $this->actingAs($this->coord)->delete("/coord/vagas/{$vaga->id}");
        $response->assertStatus(403);
    }

    // ── Submeter (rascunho → aguardando) ─────────────────────────────────────

    public function test_submeter_rascunho_para_autorizacao(): void
    {
        $vaga = $this->criarVaga(['status' => 'rascunho']);
        $response = $this->actingAs($this->coord)->patch("/coord/vagas/{$vaga->id}/submeter");
        $response->assertRedirect();
        $this->assertDatabaseHas('vagas', ['id' => $vaga->id, 'status' => 'aguardando_autorizacao']);
    }

    public function test_submeter_vaga_que_nao_e_rascunho_falha(): void
    {
        $vaga = $this->criarVaga(['status' => 'ativa']);
        $response = $this->actingAs($this->coord)->patch("/coord/vagas/{$vaga->id}/submeter");
        $response->assertStatus(403);
    }

    // ── Desativar / Reativar ──────────────────────────────────────────────────

    public function test_desativar_vaga_ativa(): void
    {
        $vaga = $this->criarVaga(['status' => 'ativa']);
        $response = $this->actingAs($this->coord)->patch("/coord/vagas/{$vaga->id}/desativar");
        $response->assertRedirect();
        $this->assertDatabaseHas('vagas', ['id' => $vaga->id, 'status' => 'inativa']);
    }

    public function test_reativar_vaga_inativa_com_data_futura(): void
    {
        $vaga = $this->criarVaga([
            'status'            => 'inativa',
            'data_encerramento' => now()->addDays(10)->toDateString(),
        ]);
        $response = $this->actingAs($this->coord)->patch("/coord/vagas/{$vaga->id}/reativar");
        $response->assertRedirect();
        $this->assertDatabaseHas('vagas', ['id' => $vaga->id, 'status' => 'ativa']);
    }

    public function test_reativar_vaga_com_data_passada_falha(): void
    {
        $vaga = $this->criarVaga([
            'status'            => 'inativa',
            'data_encerramento' => now()->subDays(3)->toDateString(),
        ]);
        $response = $this->actingAs($this->coord)->patch("/coord/vagas/{$vaga->id}/reativar");
        $response->assertStatus(403);
    }

    public function test_reativar_vaga_que_nao_e_inativa_falha(): void
    {
        $vaga = $this->criarVaga(['status' => 'rascunho']);
        $response = $this->actingAs($this->coord)->patch("/coord/vagas/{$vaga->id}/reativar");
        $response->assertStatus(403);
    }

    // ── Toggle notificação ────────────────────────────────────────────────────

    public function test_toggle_notificacao_desativa(): void
    {
        $vaga = $this->criarVaga(['notificar_email' => true]);
        $this->actingAs($this->coord)->patch("/coord/vagas/{$vaga->id}/notificacao");
        $this->assertDatabaseHas('vagas', ['id' => $vaga->id, 'notificar_email' => false]);
    }

    public function test_toggle_notificacao_ativa(): void
    {
        $vaga = $this->criarVaga(['notificar_email' => false]);
        $this->actingAs($this->coord)->patch("/coord/vagas/{$vaga->id}/notificacao");
        $this->assertDatabaseHas('vagas', ['id' => $vaga->id, 'notificar_email' => true]);
    }
}
