<?php

namespace Tests\Feature;

use App\Models\Candidato;
use App\Models\Vagas\AlertaVaga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertaVagaTest extends TestCase
{
    use RefreshDatabase;

    // ─── Exigência de conta ──────────────────────────────────────────────────

    public function test_visitante_nao_autenticado_e_levado_ao_login(): void
    {
        $this->get('/alertas')->assertRedirect(route('candidato.login', ['redirect' => 'alertas']));
    }

    public function test_candidato_sem_email_verificado_nao_ativa_alerta(): void
    {
        $candidato = Candidato::factory()->naoVerificado()->create();

        $this->actingAs($candidato, 'candidato')
            ->post('/alertas', ['areas' => ['Administração']])
            ->assertRedirect(route('candidato.verification.notice'));

        $this->assertDatabaseCount('vaga_alertas', 0);
    }

    public function test_pagina_alertas_acessivel_para_conta_verificada(): void
    {
        $response = $this->actingAs(Candidato::factory()->create(), 'candidato')->get('/alertas');

        $response->assertStatus(200);
        $response->assertViewIs('publico.alertas');
    }

    public function test_pagina_alertas_exibe_areas_e_tipos(): void
    {
        $response = $this->actingAs(Candidato::factory()->create(), 'candidato')->get('/alertas');

        $response->assertViewHas('areas');
        $response->assertViewHas('tipos');
        $response->assertViewHas('modalidades');
    }

    public function test_perfil_incompleto_nao_impede_o_alerta(): void
    {
        $candidato = Candidato::factory()->minimo()->create();

        $this->actingAs($candidato, 'candidato')
            ->post('/alertas', ['areas' => ['Administração']])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('vaga_alertas', ['candidato_id' => $candidato->id, 'ativo' => true]);
    }

    // ─── Destino vem da conta ────────────────────────────────────────────────

    public function test_alerta_usa_o_email_da_conta(): void
    {
        $candidato = Candidato::factory()->create(['email' => 'dono@email.com']);

        $this->actingAs($candidato, 'candidato')->post('/alertas', ['areas' => []]);

        $this->assertDatabaseHas('vaga_alertas', [
            'candidato_id' => $candidato->id,
            'email' => 'dono@email.com',
        ]);
    }

    public function test_endereco_informado_no_envio_e_ignorado(): void
    {
        $candidato = Candidato::factory()->create(['email' => 'dono@email.com']);

        $this->actingAs($candidato, 'candidato')
            ->post('/alertas', ['email' => 'terceiro@email.com', 'areas' => []]);

        $this->assertDatabaseMissing('vaga_alertas', ['email' => 'terceiro@email.com']);
        $this->assertDatabaseHas('vaga_alertas', ['email' => 'dono@email.com']);
    }

    // ─── Preferências ────────────────────────────────────────────────────────

    public function test_cria_alerta_sem_filtros(): void
    {
        $candidato = Candidato::factory()->create();

        $response = $this->actingAs($candidato, 'candidato')->post('/alertas', []);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('vaga_alertas', ['candidato_id' => $candidato->id, 'ativo' => true]);
    }

    public function test_cria_alerta_com_filtros_de_area(): void
    {
        $candidato = Candidato::factory()->create();

        $this->actingAs($candidato, 'candidato')
            ->post('/alertas', ['areas' => ['Tecnologia da Informação', 'Administração']]);

        $this->assertContains('Tecnologia da Informação', $candidato->fresh()->alerta->areas);
    }

    public function test_cria_alerta_com_filtros_de_tipo_e_modalidade(): void
    {
        $candidato = Candidato::factory()->create();

        $this->actingAs($candidato, 'candidato')
            ->post('/alertas', ['tipos' => ['estagio', 'bolsa'], 'modalidades' => ['remoto']]);

        $this->assertContains('estagio', $candidato->fresh()->alerta->tipos);
        $this->assertContains('remoto', $candidato->fresh()->alerta->modalidades);
    }

    public function test_alerta_token_gerado_automaticamente(): void
    {
        $candidato = Candidato::factory()->create();

        $this->actingAs($candidato, 'candidato')->post('/alertas', []);

        $this->assertEquals(64, strlen($candidato->fresh()->alerta->token));
    }

    // ─── Um alerta por conta ─────────────────────────────────────────────────

    public function test_reconfigurar_atualiza_o_alerta_existente(): void
    {
        $candidato = Candidato::factory()->create();

        $this->actingAs($candidato, 'candidato')->post('/alertas', ['areas' => ['Administração']]);
        $this->actingAs($candidato, 'candidato')->post('/alertas', ['areas' => ['Tecnologia da Informação']]);

        $this->assertEquals(1, AlertaVaga::where('candidato_id', $candidato->id)->count());
        $this->assertContains('Tecnologia da Informação', $candidato->fresh()->alerta->areas);
    }

    public function test_reativa_alerta_cancelado_pelo_link(): void
    {
        $candidato = Candidato::factory()->create();
        $this->actingAs($candidato, 'candidato')->post('/alertas', []);

        $this->get("/alertas/cancelar/{$candidato->fresh()->alerta->token}");
        $this->assertFalse($candidato->fresh()->alerta->ativo);

        $this->actingAs($candidato, 'candidato')->post('/alertas', ['areas' => ['Administração']]);

        $this->assertTrue($candidato->fresh()->alerta->ativo);
        $this->assertEquals(1, AlertaVaga::where('candidato_id', $candidato->id)->count());
    }

    // ─── Cancelamento sem autenticação ───────────────────────────────────────

    public function test_cancelar_alerta_por_token_dispensa_login(): void
    {
        $candidato = Candidato::factory()->create();
        $this->actingAs($candidato, 'candidato')->post('/alertas', []);
        $token = $candidato->fresh()->alerta->token;

        // Sessão encerrada: sair de uma lista de e-mails não pode exigir conta.
        $this->post(route('candidato.logout'));

        $response = $this->get("/alertas/cancelar/{$token}");

        $response->assertStatus(200);
        $response->assertViewIs('publico.alerta-cancelado');
        $this->assertDatabaseHas('vaga_alertas', ['token' => $token, 'ativo' => false]);
    }

    public function test_cancelar_alerta_token_invalido_retorna_404(): void
    {
        $this->get('/alertas/cancelar/'.str_repeat('z', 64))->assertStatus(404);
    }
}
