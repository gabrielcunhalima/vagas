<?php

namespace Tests\Feature;

use App\Models\Vagas\AlertaVaga;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AlertaVagaTest extends TestCase
{
    use RefreshDatabase;

    // ── Página de criação ─────────────────────────────────────────────────────

    public function test_pagina_alertas_acessivel(): void
    {
        $response = $this->get('/alertas');
        $response->assertStatus(200);
        $response->assertViewIs('vagas.publico.alertas');
    }

    public function test_pagina_alertas_exibe_areas_e_tipos(): void
    {
        $response = $this->get('/alertas');
        $response->assertViewHas('areas');
        $response->assertViewHas('tipos');
        $response->assertViewHas('modalidades');
    }

    // ── Criação de alerta ─────────────────────────────────────────────────────

    public function test_cria_alerta_sem_filtros(): void
    {
        $response = $this->post('/alertas', [
            'email' => 'usuario@email.com',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('vaga_alertas', [
            'email' => 'usuario@email.com',
            'ativo' => true,
        ]);
    }

    public function test_cria_alerta_com_filtros_de_area(): void
    {
        $response = $this->post('/alertas', [
            'email' => 'usuario@email.com',
            'areas' => ['Tecnologia da Informação', 'Administração'],
        ]);
        $response->assertRedirect();
        $alerta = AlertaVaga::where('email', 'usuario@email.com')->first();
        $this->assertNotNull($alerta);
        $this->assertContains('Tecnologia da Informação', $alerta->areas);
    }

    public function test_cria_alerta_com_filtros_de_tipo_e_modalidade(): void
    {
        $this->post('/alertas', [
            'email'       => 'usuario@email.com',
            'tipos'       => ['estagio', 'bolsa'],
            'modalidades' => ['remoto'],
        ]);
        $alerta = AlertaVaga::where('email', 'usuario@email.com')->first();
        $this->assertContains('estagio', $alerta->tipos);
        $this->assertContains('remoto', $alerta->modalidades);
    }

    public function test_alerta_token_gerado_automaticamente(): void
    {
        $this->post('/alertas', ['email' => 'usuario@email.com']);
        $alerta = AlertaVaga::where('email', 'usuario@email.com')->first();
        $this->assertEquals(64, strlen($alerta->token));
    }

    // ── Atualização de alerta existente ──────────────────────────────────────

    public function test_atualiza_alerta_existente_para_mesmo_email(): void
    {
        AlertaVaga::create([
            'email'      => 'repetido@email.com',
            'areas'      => ['Administração'],
            'modalidades'=> [],
            'tipos'      => [],
            'ativo'      => false,
            'token'      => str_repeat('x', 64),
        ]);

        $this->post('/alertas', [
            'email' => 'repetido@email.com',
            'areas' => ['Tecnologia da Informação'],
        ]);

        $this->assertEquals(1, AlertaVaga::where('email', 'repetido@email.com')->count());
        $alerta = AlertaVaga::where('email', 'repetido@email.com')->first();
        $this->assertContains('Tecnologia da Informação', $alerta->areas);
        $this->assertTrue($alerta->ativo); // Reativado
    }

    // ── Validação ─────────────────────────────────────────────────────────────

    public function test_criar_alerta_sem_email_falha(): void
    {
        $response = $this->post('/alertas', []);
        $response->assertSessionHasErrors('email');
    }

    public function test_criar_alerta_email_invalido_falha(): void
    {
        $response = $this->post('/alertas', ['email' => 'nao_e_email']);
        $response->assertSessionHasErrors('email');
    }

    // ── Cancelar alerta ───────────────────────────────────────────────────────

    public function test_cancelar_alerta_por_token(): void
    {
        $token = str_repeat('a', 64);
        AlertaVaga::create([
            'email'      => 'cancelar@email.com',
            'areas'      => [],
            'modalidades'=> [],
            'tipos'      => [],
            'ativo'      => true,
            'token'      => $token,
        ]);

        $response = $this->get("/alertas/cancelar/{$token}");
        $response->assertStatus(200);
        $response->assertViewIs('vagas.publico.alerta-cancelado');
        $this->assertDatabaseHas('vaga_alertas', [
            'token' => $token,
            'ativo' => false,
        ]);
    }

    public function test_cancelar_alerta_token_invalido_retorna_404(): void
    {
        $response = $this->get('/alertas/cancelar/' . str_repeat('z', 64));
        $response->assertStatus(404);
    }
}
