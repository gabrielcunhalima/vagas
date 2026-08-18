<?php

namespace Tests\Feature;

use App\Mail\Vagas\CandidaturaRecebidaMail;
use App\Models\Candidato;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\UsaDrhflowFalso;
use Tests\TestCase;

/**
 * As pré-condições e a validação do envio da inscrição.
 *
 * A gravação em si — o que vai para cada coluna de `EN_CANDIDATO_VAGA_EMPREGO`,
 * a idempotência por CPF + vaga, o complemento local — está em
 * `Drhflow\InscricaoDrhflowTest`. Aqui ficam os portões: autenticação,
 * verificação de e-mail, perfil completo, vaga aberta e as regras do formulário.
 */
class InscricaoTest extends TestCase
{
    use RefreshDatabase, UsaDrhflowFalso;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configurarDrhflowFalso();
        Storage::fake(Candidato::DISCO_CURRICULOS);
    }

    /** Só o que é próprio da inscrição — o resto vem do perfil. */
    private function dadosInscricao(array $over = []): array
    {
        return array_merge([
            '_honeypot' => '',
            'carta_apresentacao' => 'Tenho interesse nesta vaga por conta das tecnologias utilizadas.',
            'conflito_interesse' => '0',
            'codigo_conduta_aceite' => '1',
        ], $over);
    }

    private function contagemNoDrhflow(): int
    {
        return $this->drhflow()->table('EN_CANDIDATO_VAGA_EMPREGO')->count();
    }

    // ─── Pré-condições de acesso ─────────────────────────────────────────────

    public function test_visitante_nao_autenticado_e_levado_ao_login(): void
    {
        $codigo = $this->vagaDrhflow();

        $this->get(route('inscricao.create', $codigo))
            ->assertRedirect(route('candidato.login', ['redirect' => "candidatura/{$codigo}"]));
    }

    public function test_candidato_sem_email_verificado_nao_alcanca_a_candidatura(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = Candidato::factory()->naoVerificado()->create();

        $this->actingAs($candidato, 'candidato')
            ->get(route('inscricao.create', $codigo))
            ->assertRedirect(route('candidato.verification.notice'));
    }

    public function test_envio_sem_conta_autenticada_e_recusado(): void
    {
        $codigo = $this->vagaDrhflow();

        $this->post(route('inscricao.store', $codigo), $this->dadosInscricao())
            ->assertRedirect(route('candidato.login', ['redirect' => "candidatura/{$codigo}"]));

        $this->assertSame(0, $this->contagemNoDrhflow());
    }

    public function test_formulario_candidatura_vaga_aberta(): void
    {
        $codigo = $this->vagaDrhflow();

        $res = $this->actingAs(Candidato::factory()->create(), 'candidato')
            ->get(route('inscricao.create', $codigo));

        $res->assertOk();
        $this->assertComponenteInertia($res, 'Publico/Candidatura');
        $this->assertPropInertia($res, 'perfil');
        $this->assertPropInertia($res, 'completude');
        $this->assertPropInertia($res, 'vaga');
    }

    public function test_formulario_candidatura_vaga_encerrada_retorna_404(): void
    {
        $codigo = $this->vagaDrhflow(['CD_SITUACAO' => 2]);

        $this->actingAs(Candidato::factory()->create(), 'candidato')
            ->get(route('inscricao.create', $codigo))
            ->assertNotFound();
    }

    public function test_inscricao_em_vaga_encerrada_retorna_404(): void
    {
        $codigo = $this->vagaDrhflow(['CD_SITUACAO' => 2]);

        $this->actingAs(Candidato::factory()->create(), 'candidato')
            ->post(route('inscricao.store', $codigo), $this->dadosInscricao())
            ->assertNotFound();

        $this->assertSame(0, $this->contagemNoDrhflow());
    }

    // ─── Perfil completo como condição ───────────────────────────────────────

    public function test_perfil_incompleto_nao_apresenta_formulario_de_envio(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = Candidato::factory()->minimo()->create();

        $res = $this->actingAs($candidato, 'candidato')->get(route('inscricao.create', $codigo));

        $res->assertOk();
        $this->assertFalse($this->propsInertia($res)['completude']['completo']);
        $this->assertNotEmpty($this->propsInertia($res)['completude']['pendencias']);
    }

    public function test_envio_com_perfil_incompleto_e_recusado(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = Candidato::factory()->minimo()->create();

        $this->actingAs($candidato, 'candidato')
            ->post(route('inscricao.store', $codigo), $this->dadosInscricao())
            ->assertRedirect(route('candidato.perfil.edit'));

        $this->assertSame(0, $this->contagemNoDrhflow());
    }

    public function test_perfil_sem_curriculo_bloqueia_o_envio(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = Candidato::factory()->semCurriculo()->create();

        $this->actingAs($candidato, 'candidato')
            ->post(route('inscricao.store', $codigo), $this->dadosInscricao())
            ->assertRedirect(route('candidato.perfil.edit'));

        $this->assertSame(0, $this->contagemNoDrhflow());
    }

    // ─── Envio ───────────────────────────────────────────────────────────────

    public function test_envio_grava_no_drhflow_e_conduz_ao_acompanhamento(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = Candidato::factory()->create();

        $this->actingAs($candidato, 'candidato')
            ->post(route('inscricao.store', $codigo), $this->dadosInscricao())
            ->assertRedirect(route('candidato.candidaturas.index'));

        $this->assertSame(1, $this->contagemNoDrhflow());
    }

    public function test_candidatura_envia_email_ao_candidato(): void
    {
        Mail::fake();
        $codigo = $this->vagaDrhflow();

        $this->actingAs(Candidato::factory()->create(), 'candidato')
            ->post(route('inscricao.store', $codigo), $this->dadosInscricao());

        Mail::assertSent(CandidaturaRecebidaMail::class);
    }

    // ─── Validação dos campos da vaga ────────────────────────────────────────

    public function test_conflito_declarado_sem_detalhe_falha(): void
    {
        $codigo = $this->vagaDrhflow();

        $this->actingAs(Candidato::factory()->create(), 'candidato')
            ->post(route('inscricao.store', $codigo), $this->dadosInscricao([
                'conflito_interesse' => '1',
                'conflito_interesse_detalhe' => '',
            ]))
            ->assertSessionHasErrors('conflito_interesse_detalhe');

        $this->assertSame(0, $this->contagemNoDrhflow());
    }

    public function test_sem_aceite_do_codigo_de_conduta_falha(): void
    {
        $codigo = $this->vagaDrhflow();

        $this->actingAs(Candidato::factory()->create(), 'candidato')
            ->post(route('inscricao.store', $codigo), $this->dadosInscricao(['codigo_conduta_aceite' => '0']))
            ->assertSessionHasErrors('codigo_conduta_aceite');

        $this->assertSame(0, $this->contagemNoDrhflow());
    }

    public function test_honeypot_preenchido_bloqueia_inscricao(): void
    {
        $codigo = $this->vagaDrhflow();

        $this->actingAs(Candidato::factory()->create(), 'candidato')
            ->post(route('inscricao.store', $codigo), $this->dadosInscricao(['_honeypot' => 'bot']))
            ->assertSessionHasErrors('_honeypot');

        $this->assertSame(0, $this->contagemNoDrhflow());
    }

    // ─── Unicidade por vaga ──────────────────────────────────────────────────

    public function test_mesma_conta_pode_se_candidatar_em_vagas_diferentes(): void
    {
        $primeira = $this->vagaDrhflow();
        $segunda = $this->vagaDrhflow(['CD_FUNCAO' => '0373']);
        $candidato = Candidato::factory()->create();

        $this->actingAs($candidato, 'candidato')->post(route('inscricao.store', $primeira), $this->dadosInscricao());
        $this->actingAs($candidato, 'candidato')->post(route('inscricao.store', $segunda), $this->dadosInscricao());

        $this->assertSame(2, $this->contagemNoDrhflow());
    }

    // ─── Legado ──────────────────────────────────────────────────────────────

    public function test_consulta_por_cpf_e_email_redireciona_para_area_autenticada(): void
    {
        $this->get('/minhas-candidaturas')->assertRedirect('/minha-conta/candidaturas');
    }
}
