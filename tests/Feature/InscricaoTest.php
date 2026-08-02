<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vagas\Vaga;
use App\Models\Vagas\Candidatura;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\Vagas\CandidaturaRecebidaMail;
use App\Mail\Vagas\NovaCandidaturaMail;

class InscricaoTest extends TestCase
{
    use RefreshDatabase;

    private User $coord;

    protected function setUp(): void
    {
        parent::setUp();
        $this->coord = User::factory()->create(['perfil' => 'coordenador', 'ativo' => true]);
    }

    private function criarVaga(array $attrs = []): Vaga
    {
        return Vaga::create(array_merge([
            'titulo'            => 'Estágio em TI',
            'descricao'         => 'Descrição da vaga pública de estágio em TI para candidatura.',
            'requisitos'        => 'Requisitos básicos para estágio em TI.',
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
        ], $attrs));
    }

    private function dadosInscricao(array $over = []): array
    {
        Storage::fake('local');
        return array_merge([
            '_honeypot'          => '',
            'nome'               => 'João da Silva Santos',
            'email'              => 'joao@teste.com',
            'cpf'                => '529.982.247-25',
            'telefone'           => '48999001122',
            'curso'              => 'Ciência da Computação',
            'instituicao'        => 'UFSC',
            'semestre'           => '6',
            'previsao_conclusao' => now()->addYear()->format('Y-m-d'),
            'carta_apresentacao' => 'Tenho interesse nesta vaga por conta das tecnologias utilizadas.',
            'curriculo'          => UploadedFile::fake()->create('curriculo.pdf', 100, 'application/pdf'),
            'linkedin'           => 'https://linkedin.com/in/joaosilva',
            'pretensao_salarial' => '1500.00',
            'disponibilidade'    => 'Manhã',
            'pcd'                => false,
            'lgpd_consentimento' => true,
        ], $over);
    }

    // ── Página pública da vaga ────────────────────────────────────────────────

    public function test_pagina_vaga_publica_acessivel(): void
    {
        $vaga = $this->criarVaga();
        $response = $this->get("/vagas/{$vaga->id}");
        $response->assertStatus(200);
        $this->assertVeInertia($response, $vaga->titulo);
    }

    public function test_vaga_encerrada_nao_exibida_na_listagem(): void
    {
        $this->criarVaga(['titulo' => 'Vaga Ativa']);
        $this->criarVaga(['titulo' => 'Vaga Encerrada', 'status' => 'encerrada', 'data_encerramento' => now()->subDays(5)->toDateString()]);

        $response = $this->get('/vagas');
        $this->assertVeInertia($response, 'Vaga Ativa');
        $this->assertNaoVeInertia($response, 'Vaga Encerrada');
    }

    public function test_vaga_rascunho_nao_exibida_na_listagem_publica(): void
    {
        $this->criarVaga(['titulo' => 'Vaga Rascunho', 'status' => 'rascunho']);
        $response = $this->get('/vagas');
        $this->assertNaoVeInertia($response, 'Vaga Rascunho');
    }

    // ── Formulário de candidatura ─────────────────────────────────────────────

    public function test_formulario_candidatura_vaga_aberta(): void
    {
        $vaga = $this->criarVaga();
        $response = $this->get("/candidatura/{$vaga->id}");
        $response->assertStatus(200);
        $this->assertComponenteInertia($response, 'Publico/Candidatura');
    }

    public function test_formulario_candidatura_vaga_encerrada_retorna_404(): void
    {
        $vaga = $this->criarVaga([
            'status'            => 'ativa',
            'data_encerramento' => now()->subDays(1)->toDateString(),
        ]);
        $response = $this->get("/candidatura/{$vaga->id}");
        $response->assertStatus(404);
    }

    public function test_formulario_candidatura_vaga_rascunho_retorna_404(): void
    {
        $vaga = $this->criarVaga(['status' => 'rascunho']);
        $response = $this->get("/candidatura/{$vaga->id}");
        $response->assertStatus(404);
    }

    // ── Store candidatura com sucesso ─────────────────────────────────────────

    public function test_candidatura_armazenada_com_sucesso(): void
    {
        Mail::fake();
        Storage::fake('local');
        $vaga = $this->criarVaga();

        $response = $this->post("/candidatura/{$vaga->id}", $this->dadosInscricao());

        $response->assertRedirect(route('inscricao.confirmacao', $vaga));
        $this->assertDatabaseHas('candidaturas', [
            'vaga_id' => $vaga->id,
            'email'   => 'joao@teste.com',
            'cpf'     => '52998224725',
            'status'  => 'recebida',
        ]);
    }

    public function test_candidatura_salva_curriculo(): void
    {
        Mail::fake();
        Storage::fake('local');
        $vaga = $this->criarVaga();

        $this->post("/candidatura/{$vaga->id}", $this->dadosInscricao());

        $candidatura = Candidatura::where('email', 'joao@teste.com')->first();
        $this->assertNotNull($candidatura->curriculo_path);
        Storage::disk('local')->assertExists($candidatura->curriculo_path);
    }

    public function test_candidatura_envia_email_ao_candidato(): void
    {
        Mail::fake();
        Storage::fake('local');
        $vaga = $this->criarVaga();

        $this->post("/candidatura/{$vaga->id}", $this->dadosInscricao());

        Mail::assertSent(CandidaturaRecebidaMail::class, fn($mail) => $mail->hasTo('joao@teste.com'));
    }

    public function test_candidatura_notifica_coordenador_quando_notificar_email_true(): void
    {
        Mail::fake();
        Storage::fake('local');
        $vaga = $this->criarVaga(['notificar_email' => true]);

        $this->post("/candidatura/{$vaga->id}", $this->dadosInscricao());

        Mail::assertSent(NovaCandidaturaMail::class, fn($mail) => $mail->hasTo($this->coord->email));
    }

    public function test_candidatura_nao_notifica_coordenador_quando_notificar_email_false(): void
    {
        Mail::fake();
        Storage::fake('local');
        $vaga = $this->criarVaga(['notificar_email' => false]);

        $this->post("/candidatura/{$vaga->id}", $this->dadosInscricao());

        Mail::assertNotSent(NovaCandidaturaMail::class);
    }

    public function test_page_confirmacao_exibida(): void
    {
        $vaga = $this->criarVaga();
        $response = $this->withSession(['candidatura_nome' => 'João'])
            ->get("/candidatura/{$vaga->id}/confirmacao");
        $response->assertStatus(200);
        $this->assertComponenteInertia($response, 'Publico/Confirmacao');
    }

    // ── Validações da inscrição ───────────────────────────────────────────────

    public function test_inscricao_sem_nome_falha(): void
    {
        Storage::fake('local');
        $vaga = $this->criarVaga();
        $response = $this->post("/candidatura/{$vaga->id}", $this->dadosInscricao(['nome' => '']));
        $response->assertSessionHasErrors('nome');
    }

    public function test_inscricao_sem_email_falha(): void
    {
        Storage::fake('local');
        $vaga = $this->criarVaga();
        $response = $this->post("/candidatura/{$vaga->id}", $this->dadosInscricao(['email' => '']));
        $response->assertSessionHasErrors('email');
    }

    public function test_inscricao_cpf_invalido_falha(): void
    {
        Storage::fake('local');
        $vaga = $this->criarVaga();
        $response = $this->post("/candidatura/{$vaga->id}", $this->dadosInscricao(['cpf' => '111.111.111-11']));
        $response->assertSessionHasErrors('cpf');
    }

    public function test_inscricao_sem_curriculo_falha(): void
    {
        $vaga = $this->criarVaga();
        $dados = $this->dadosInscricao();
        unset($dados['curriculo']);
        $response = $this->post("/candidatura/{$vaga->id}", $dados);
        $response->assertSessionHasErrors('curriculo');
    }

    public function test_inscricao_curriculo_nao_pdf_falha(): void
    {
        Storage::fake('local');
        $vaga = $this->criarVaga();
        $dados = $this->dadosInscricao([
            'curriculo' => UploadedFile::fake()->create('curriculo.docx', 100, 'application/msword'),
        ]);
        $response = $this->post("/candidatura/{$vaga->id}", $dados);
        $response->assertSessionHasErrors('curriculo');
    }

    public function test_inscricao_curriculo_maior_que_5mb_falha(): void
    {
        Storage::fake('local');
        $vaga = $this->criarVaga();
        $dados = $this->dadosInscricao([
            'curriculo' => UploadedFile::fake()->create('curriculo.pdf', 6000, 'application/pdf'),
        ]);
        $response = $this->post("/candidatura/{$vaga->id}", $dados);
        $response->assertSessionHasErrors('curriculo');
    }

    public function test_inscricao_duplicada_mesmo_cpf_falha(): void
    {
        Mail::fake();
        Storage::fake('local');
        $vaga = $this->criarVaga();

        // Primeira candidatura
        $this->post("/candidatura/{$vaga->id}", $this->dadosInscricao());

        // Segunda candidatura com mesmo CPF
        $response = $this->post("/candidatura/{$vaga->id}", $this->dadosInscricao([
            'email' => 'outro@email.com',
        ]));
        $response->assertSessionHasErrors('cpf');
    }

    public function test_mesmo_cpf_pode_se_candidatar_em_vagas_diferentes(): void
    {
        Mail::fake();
        Storage::fake('local');
        $vaga1 = $this->criarVaga(['titulo' => 'Vaga 1']);
        $vaga2 = $this->criarVaga(['titulo' => 'Vaga 2']);

        $this->post("/candidatura/{$vaga1->id}", $this->dadosInscricao());
        $response = $this->post("/candidatura/{$vaga2->id}", $this->dadosInscricao());

        $response->assertRedirect(route('inscricao.confirmacao', $vaga2));
        $this->assertEquals(2, Candidatura::where('cpf', '52998224725')->count());
    }

    public function test_honeypot_preenchido_bloqueia_inscricao(): void
    {
        Storage::fake('local');
        $vaga = $this->criarVaga();
        $response = $this->post("/candidatura/{$vaga->id}", $this->dadosInscricao([
            '_honeypot' => 'bot preencheu isso',
        ]));
        $response->assertSessionHasErrors('_honeypot');
    }

    public function test_inscricao_com_pcd_salva_tipo(): void
    {
        Mail::fake();
        Storage::fake('local');
        $vaga = $this->criarVaga();

        $this->post("/candidatura/{$vaga->id}", $this->dadosInscricao([
            'pcd'      => true,
            'pcd_tipo' => 'Deficiência Física',
        ]));

        $this->assertDatabaseHas('candidaturas', [
            'vaga_id'  => $vaga->id,
            'pcd'      => true,
            'pcd_tipo' => 'Deficiência Física',
        ]);
    }

    public function test_inscricao_em_vaga_encerrada_retorna_404(): void
    {
        Storage::fake('local');
        $vaga = $this->criarVaga([
            'status'            => 'ativa',
            'data_encerramento' => now()->subDays(1)->toDateString(),
        ]);
        $response = $this->post("/candidatura/{$vaga->id}", $this->dadosInscricao());
        $response->assertStatus(404);
    }

    // ── Consulta de candidaturas ──────────────────────────────────────────────

    public function test_pagina_consulta_candidatura_acessivel(): void
    {
        $response = $this->get('/minhas-candidaturas');
        $response->assertStatus(200);
        $this->assertComponenteInertia($response, 'Publico/ConsultaCandidatura');
    }

    public function test_consulta_candidatura_por_cpf_e_email(): void
    {
        Mail::fake();
        Storage::fake('local');
        $vaga = $this->criarVaga();
        $this->post("/candidatura/{$vaga->id}", $this->dadosInscricao());

        $response = $this->post('/minhas-candidaturas', [
            'cpf'   => '529.982.247-25',
            'email' => 'joao@teste.com',
        ]);

        $response->assertStatus(200);
        $this->assertPropInertia($response, 'candidaturas');
        $this->assertVeInertia($response, 'Estágio em TI');
    }

    public function test_consulta_sem_candidaturas_retorna_lista_vazia(): void
    {
        $response = $this->post('/minhas-candidaturas', [
            'cpf'   => '529.982.247-25',
            'email' => 'naoexiste@email.com',
        ]);
        $response->assertStatus(200);
        $candidaturas = $this->propsInertia($response)['candidaturas'];
        $this->assertCount(0, $candidaturas);
    }

    public function test_consulta_sem_cpf_falha(): void
    {
        $response = $this->post('/minhas-candidaturas', ['email' => 'teste@email.com']);
        $response->assertSessionHasErrors('cpf');
    }

    public function test_consulta_sem_email_falha(): void
    {
        $response = $this->post('/minhas-candidaturas', ['cpf' => '529.982.247-25']);
        $response->assertSessionHasErrors('email');
    }
}
