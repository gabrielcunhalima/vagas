<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function criarUsuario(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'password' => Hash::make('password'),
            'ativo'    => true,
            'perfil'   => 'coordenador',
        ], $attrs));
    }

    // ── Tela de login ─────────────────────────────────────────────────────────

    public function test_pagina_login_acessivel(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $this->assertComponenteInertia($response, 'Auth/Login');
    }

    public function test_usuario_autenticado_redirecionado_do_login(): void
    {
        $user = $this->criarUsuario(['perfil' => 'coordenador']);
        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect(route('coord.dashboard'));
    }

    public function test_gestor_autenticado_redirecionado_do_login(): void
    {
        $user = $this->criarUsuario(['perfil' => 'gestor']);
        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect(route('gestor.dashboard'));
    }

    // ── Login com sucesso ─────────────────────────────────────────────────────

    public function test_login_coordenador_redireciona_para_dashboard(): void
    {
        $user = $this->criarUsuario(['perfil' => 'coordenador']);
        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);
        $response->assertRedirect(route('coord.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_gestor_redireciona_para_dashboard_gestor(): void
    {
        $user = $this->criarUsuario(['perfil' => 'gestor']);
        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);
        $response->assertRedirect(route('gestor.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_admin_redireciona_para_dashboard_coord(): void
    {
        $user = $this->criarUsuario(['perfil' => 'admin']);
        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);
        $response->assertRedirect(route('coord.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    // ── Login com falha ───────────────────────────────────────────────────────

    public function test_login_senha_errada_retorna_erro(): void
    {
        $user = $this->criarUsuario();
        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'senha_errada',
        ]);
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_email_inexistente_retorna_erro(): void
    {
        $response = $this->post('/login', [
            'email'    => 'naoexiste@email.com',
            'password' => 'password',
        ]);
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_usuario_inativo_bloqueado(): void
    {
        $user = $this->criarUsuario(['ativo' => false]);
        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_sem_email_retorna_erro(): void
    {
        $response = $this->post('/login', ['password' => 'password']);
        $response->assertSessionHasErrors('email');
    }

    public function test_login_sem_senha_retorna_erro(): void
    {
        $user = $this->criarUsuario();
        $response = $this->post('/login', ['email' => $user->email]);
        $response->assertSessionHasErrors('password');
    }

    public function test_login_email_invalido_retorna_erro(): void
    {
        $response = $this->post('/login', [
            'email'    => 'nao_e_email',
            'password' => 'password',
        ]);
        $response->assertSessionHasErrors('email');
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function test_logout_desautentica_usuario(): void
    {
        $user = $this->criarUsuario();
        $this->actingAs($user);
        $response = $this->post('/logout');
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    // ── Proteção de rotas ─────────────────────────────────────────────────────

    public function test_rota_coord_dashboard_sem_autenticacao_redireciona_login(): void
    {
        $response = $this->get('/coord/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_rota_gestor_dashboard_sem_autenticacao_redireciona_login(): void
    {
        $response = $this->get('/gestor/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_rota_coord_vagas_sem_autenticacao_redireciona_login(): void
    {
        $response = $this->get('/coord/vagas');
        $response->assertRedirect('/login');
    }

    // ── Middleware de perfil ──────────────────────────────────────────────────

    public function test_coordenador_nao_acessa_rotas_gestor(): void
    {
        $coord = $this->criarUsuario(['perfil' => 'coordenador']);
        $response = $this->actingAs($coord)->get('/gestor/dashboard');
        $response->assertStatus(403);
    }

    public function test_gestor_nao_acessa_rotas_coordenador(): void
    {
        $gestor = $this->criarUsuario(['perfil' => 'gestor']);
        $response = $this->actingAs($gestor)->get('/coord/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_acessa_rotas_coordenador(): void
    {
        $admin = $this->criarUsuario(['perfil' => 'admin']);
        $response = $this->actingAs($admin)->get('/coord/dashboard');
        $response->assertStatus(200);
    }
}
