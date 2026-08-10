<?php

namespace Tests\Feature;

use App\Models\Candidato;
use App\Models\User;
use App\Models\Vagas\Vaga;
use App\Models\Vagas\Candidatura;
use App\Models\Vagas\CandidaturaEvento;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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

    /** Só o que é próprio da inscrição — o resto vem do perfil. */
    private function dadosInscricao(array $over = []): array
    {
        return array_merge([
            '_honeypot'             => '',
            'carta_apresentacao'    => 'Tenho interesse nesta vaga por conta das tecnologias utilizadas.',
            'conflito_interesse'    => '0',
            'codigo_conduta_aceite' => '1',
        ], $over);
    }

    // ─── Pré-condições de acesso ─────────────────────────────────────────────

    public function test_visitante_nao_autenticado_e_levado_ao_login(): void
    {
        $vaga = $this->criarVaga();

        $this->get(route('inscricao.create', $vaga))
            ->assertRedirect(route('candidato.login', ['redirect' => "candidatura/{$vaga->id}"]));
    }

    public function test_candidato_sem_email_verificado_nao_alcanca_a_candidatura(): void
    {
        $vaga = $this->criarVaga();
        $candidato = Candidato::factory()->naoVerificado()->create();

        $this->actingAs($candidato, 'candidato')
            ->get(route('inscricao.create', $vaga))
            ->assertRedirect(route('candidato.verification.notice'));
    }

    public function test_envio_sem_conta_autenticada_e_recusado(): void
    {
        $vaga = $this->criarVaga();

        $this->post(route('inscricao.store', $vaga), $this->dadosInscricao())
            ->assertRedirect(route('candidato.login', ['redirect' => "candidatura/{$vaga->id}"]));

        $this->assertDatabaseCount('candidaturas', 0);
    }

    public function test_formulario_candidatura_vaga_aberta(): void
    {
        $vaga = $this->criarVaga();

        $res = $this->actingAs(Candidato::factory()->create(), 'candidato')
            ->get(route('inscricao.create', $vaga));

        $res->assertOk();
        $this->assertComponenteInertia($res, 'Publico/Candidatura');
        $this->assertPropInertia($res, 'perfil');
        $this->assertPropInertia($res, 'completude');
    }

    public function test_formulario_candidatura_vaga_encerrada_retorna_404(): void
    {
        $vaga = $this->criarVaga(['status' => 'encerrada']);

        $this->actingAs(Candidato::factory()->create(), 'candidato')
            ->get(route('inscricao.create', $vaga))
            ->assertNotFound();
    }

    public function test_inscricao_em_vaga_encerrada_retorna_404(): void
    {
        $vaga = $this->criarVaga(['status' => 'encerrada']);

        $this->actingAs(Candidato::factory()->create(), 'candidato')
            ->post(route('inscricao.store', $vaga), $this->dadosInscricao())
            ->assertNotFound();
    }

    // ─── Perfil completo como condição ───────────────────────────────────────

    public function test_perfil_incompleto_nao_apresenta_formulario_de_envio(): void
    {
        $vaga = $this->criarVaga();
        $candidato = Candidato::factory()->minimo()->create();

        $res = $this->actingAs($candidato, 'candidato')->get(route('inscricao.create', $vaga));

        $res->assertOk();
        $this->assertFalse($this->propsInertia($res)['completude']['completo']);
        $this->assertNotEmpty($this->propsInertia($res)['completude']['pendencias']);
    }

    public function test_envio_com_perfil_incompleto_e_recusado(): void
    {
        $vaga = $this->criarVaga();
        $candidato = Candidato::factory()->minimo()->create();

        $this->actingAs($candidato, 'candidato')
            ->post(route('inscricao.store', $vaga), $this->dadosInscricao())
            ->assertRedirect(route('candidato.perfil.edit'));

        $this->assertDatabaseCount('candidaturas', 0);
    }

    public function test_perfil_sem_curriculo_bloqueia_o_envio(): void
    {
        $vaga = $this->criarVaga();
        $candidato = Candidato::factory()->semCurriculo()->create();

        $this->actingAs($candidato, 'candidato')
            ->post(route('inscricao.store', $vaga), $this->dadosInscricao())
            ->assertRedirect(route('candidato.perfil.edit'));

        $this->assertDatabaseCount('candidaturas', 0);
    }

    // ─── Envio ───────────────────────────────────────────────────────────────

    public function test_candidatura_armazenada_e_vinculada_a_conta(): void
    {
        $vaga = $this->criarVaga();
        $candidato = Candidato::factory()->create();

        $this->actingAs($candidato, 'candidato')
            ->post(route('inscricao.store', $vaga), $this->dadosInscricao())
            ->assertRedirect(route('candidato.candidaturas.index'));

        $this->assertDatabaseHas('candidaturas', [
            'vaga_id'      => $vaga->id,
            'candidato_id' => $candidato->id,
            'status'       => 'recebida',
        ]);
    }

    public function test_candidatura_nao_guarda_copia_dos_dados_do_perfil(): void
    {
        $vaga = $this->criarVaga();
        $candidato = Candidato::factory()->create(['nome' => 'João da Silva']);

        $this->actingAs($candidato, 'candidato')
            ->post(route('inscricao.store', $vaga), $this->dadosInscricao());

        $candidatura = Candidatura::first();

        // Lê do perfil, não de coluna própria.
        $this->assertSame('João da Silva', $candidatura->nome);

        $candidato->update(['nome' => 'João da Silva Santos']);
        $this->assertSame('João da Silva Santos', $candidatura->fresh()->nome);
    }

    public function test_envio_registra_evento_de_submissao_com_curriculo_vigente(): void
    {
        $vaga = $this->criarVaga();
        $candidato = Candidato::factory()->create();

        $this->actingAs($candidato, 'candidato')
            ->post(route('inscricao.store', $vaga), $this->dadosInscricao());

        $evento = Candidatura::first()->eventos()->first();

        $this->assertSame(CandidaturaEvento::TIPO_SUBMISSAO, $evento->tipo);
        $this->assertSame('recebida', $evento->status_novo);
        $this->assertSame($candidato->curriculo_atual_id, $evento->curriculo_id_vigente);
    }

    public function test_aceites_sao_registrados_na_candidatura(): void
    {
        $vaga = $this->criarVaga();
        $candidato = Candidato::factory()->create();

        $this->actingAs($candidato, 'candidato')->post(route('inscricao.store', $vaga), $this->dadosInscricao([
            'conflito_interesse'         => '1',
            'conflito_interesse_detalhe' => 'Meu primo trabalha na equipe.',
        ]));

        $candidatura = Candidatura::first();

        $this->assertTrue($candidatura->conflito_interesse);
        $this->assertSame('Meu primo trabalha na equipe.', $candidatura->conflito_interesse_detalhe);
        $this->assertNotNull($candidatura->codigo_conduta_aceito_em);
    }

    public function test_candidatura_envia_email_ao_candidato(): void
    {
        Mail::fake();
        $vaga = $this->criarVaga();
        $candidato = Candidato::factory()->create();

        $this->actingAs($candidato, 'candidato')->post(route('inscricao.store', $vaga), $this->dadosInscricao());

        Mail::assertSent(CandidaturaRecebidaMail::class);
    }

    public function test_candidatura_notifica_coordenador_quando_notificar_email_true(): void
    {
        Mail::fake();
        $vaga = $this->criarVaga(['notificar_email' => true]);

        $this->actingAs(Candidato::factory()->create(), 'candidato')
            ->post(route('inscricao.store', $vaga), $this->dadosInscricao());

        Mail::assertSent(NovaCandidaturaMail::class);
    }

    public function test_candidatura_nao_notifica_coordenador_quando_notificar_email_false(): void
    {
        Mail::fake();
        $vaga = $this->criarVaga(['notificar_email' => false]);

        $this->actingAs(Candidato::factory()->create(), 'candidato')
            ->post(route('inscricao.store', $vaga), $this->dadosInscricao());

        Mail::assertNotSent(NovaCandidaturaMail::class);
    }

    // ─── Validação dos campos da vaga ────────────────────────────────────────

    public function test_conflito_declarado_sem_detalhe_falha(): void
    {
        $vaga = $this->criarVaga();

        $this->actingAs(Candidato::factory()->create(), 'candidato')
            ->post(route('inscricao.store', $vaga), $this->dadosInscricao([
                'conflito_interesse'         => '1',
                'conflito_interesse_detalhe' => '',
            ]))
            ->assertSessionHasErrors('conflito_interesse_detalhe');
    }

    public function test_sem_aceite_do_codigo_de_conduta_falha(): void
    {
        $vaga = $this->criarVaga();

        $this->actingAs(Candidato::factory()->create(), 'candidato')
            ->post(route('inscricao.store', $vaga), $this->dadosInscricao(['codigo_conduta_aceite' => '0']))
            ->assertSessionHasErrors('codigo_conduta_aceite');
    }

    public function test_honeypot_preenchido_bloqueia_inscricao(): void
    {
        $vaga = $this->criarVaga();

        $this->actingAs(Candidato::factory()->create(), 'candidato')
            ->post(route('inscricao.store', $vaga), $this->dadosInscricao(['_honeypot' => 'bot']))
            ->assertSessionHasErrors('_honeypot');

        $this->assertDatabaseCount('candidaturas', 0);
    }

    // ─── Unicidade por vaga ──────────────────────────────────────────────────

    public function test_inscricao_duplicada_na_mesma_vaga_e_impedida(): void
    {
        $vaga = $this->criarVaga();
        $candidato = Candidato::factory()->create();

        $this->actingAs($candidato, 'candidato')->post(route('inscricao.store', $vaga), $this->dadosInscricao());
        $this->actingAs($candidato, 'candidato')
            ->post(route('inscricao.store', $vaga), $this->dadosInscricao())
            ->assertRedirect(route('candidato.candidaturas.index'));

        $this->assertSame(1, Candidatura::where('vaga_id', $vaga->id)->count());
    }

    public function test_mesma_conta_pode_se_candidatar_em_vagas_diferentes(): void
    {
        $vagaA = $this->criarVaga();
        $vagaB = $this->criarVaga(['titulo' => 'Bolsa de Pesquisa']);
        $candidato = Candidato::factory()->create();

        $this->actingAs($candidato, 'candidato')->post(route('inscricao.store', $vagaA), $this->dadosInscricao());
        $this->actingAs($candidato, 'candidato')->post(route('inscricao.store', $vagaB), $this->dadosInscricao());

        $this->assertSame(2, Candidatura::where('candidato_id', $candidato->id)->count());
    }

    public function test_curriculo_substituido_vale_para_todas_as_candidaturas(): void
    {
        $vagaA = $this->criarVaga();
        $vagaB = $this->criarVaga(['titulo' => 'Bolsa de Pesquisa']);
        $candidato = Candidato::factory()->create();

        $this->actingAs($candidato, 'candidato')->post(route('inscricao.store', $vagaA), $this->dadosInscricao());
        $this->actingAs($candidato, 'candidato')->post(route('inscricao.store', $vagaB), $this->dadosInscricao());

        $versaoAntiga = $candidato->curriculo_atual_id;

        $nova = $candidato->curriculos()->create([
            'path'          => 'candidatos/curriculos/novo.pdf',
            'nome_original' => 'curriculo-v2.pdf',
            'enviado_em'    => now(),
        ]);
        $candidato->forceFill(['curriculo_atual_id' => $nova->id])->save();

        foreach (Candidatura::all() as $candidatura) {
            $this->assertSame('curriculo-v2.pdf', $candidatura->curriculo_nome_original);
        }

        // A versão anterior continua identificável pelo evento de submissão.
        $this->assertSame($versaoAntiga, Candidatura::first()->eventos()->first()->curriculo_id_vigente);
    }

    // ─── Listagem pública ────────────────────────────────────────────────────

    public function test_pagina_vaga_publica_acessivel(): void
    {
        $vaga = $this->criarVaga();

        $this->get(route('vagas.publicas.show', $vaga))->assertOk();
    }

    public function test_vaga_encerrada_nao_exibida_na_listagem(): void
    {
        $this->criarVaga(['status' => 'encerrada', 'titulo' => 'Vaga Encerrada XYZ']);

        $this->assertNaoVeInertia($this->get(route('vagas.publicas.index')), 'Vaga Encerrada XYZ');
    }

    public function test_vaga_rascunho_nao_exibida_na_listagem_publica(): void
    {
        $this->criarVaga(['status' => 'rascunho', 'titulo' => 'Rascunho Secreto ABC']);

        $this->assertNaoVeInertia($this->get(route('vagas.publicas.index')), 'Rascunho Secreto ABC');
    }

    // ─── Legado ──────────────────────────────────────────────────────────────

    public function test_consulta_por_cpf_e_email_redireciona_para_area_autenticada(): void
    {
        $this->get('/minhas-candidaturas')->assertRedirect('/minha-conta/candidaturas');
    }
}
