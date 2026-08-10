<?php

namespace Tests\Feature;

use App\Models\Candidato;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CandidatoRegistroCpfTest extends TestCase
{
    use RefreshDatabase;

    private const CPF_VALIDO   = '52998224725';
    private const CPF_EXISTENTE = '11144477735';

    private function criarCandidato(string $cpf): Candidato
    {
        return Candidato::create([
            'nome'     => 'Candidato Existente',
            'email'    => 'existente@example.com',
            'cpf'      => $cpf,
            'password' => Hash::make('Senha@Forte1'),
            'ativo'    => true,
        ]);
    }

    private function verificar(string $cpf)
    {
        return $this->getJson(route('candidato.registro.verificar-cpf', ['cpf' => $cpf]));
    }

    // ── Consulta de disponibilidade ───────────────────────────────────────────

    public function test_cpf_valido_sem_conta_responde_disponivel(): void
    {
        $this->verificar(self::CPF_VALIDO)
            ->assertOk()
            ->assertExactJson(['existe' => false, 'valido' => true]);
    }

    public function test_cpf_de_candidato_existente_responde_duplicado(): void
    {
        $this->criarCandidato(self::CPF_EXISTENTE);

        $this->verificar(self::CPF_EXISTENTE)
            ->assertOk()
            ->assertExactJson(['existe' => true, 'valido' => true]);
    }

    public function test_cpf_aceita_mascara_na_consulta(): void
    {
        $this->criarCandidato(self::CPF_EXISTENTE);

        $this->verificar('111.444.777-35')
            ->assertOk()
            ->assertExactJson(['existe' => true, 'valido' => true]);
    }

    // ── CPF malformado não chega ao banco ─────────────────────────────────────

    public function test_cpf_com_digitos_verificadores_errados_responde_invalido(): void
    {
        $this->verificar('12345678900')
            ->assertOk()
            ->assertExactJson(['existe' => false, 'valido' => false]);
    }

    public function test_cpf_incompleto_responde_invalido(): void
    {
        $this->verificar('529982')
            ->assertOk()
            ->assertExactJson(['existe' => false, 'valido' => false]);
    }

    public function test_cpf_com_digitos_repetidos_responde_invalido(): void
    {
        $this->verificar('11111111111')
            ->assertOk()
            ->assertExactJson(['existe' => false, 'valido' => false]);
    }

    // ── Limite de requisições ─────────────────────────────────────────────────

    public function test_consultas_acima_do_limite_sao_recusadas(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $this->verificar(self::CPF_VALIDO)->assertOk();
        }

        $this->verificar(self::CPF_VALIDO)->assertStatus(429);
    }

    // ── O envio segue sendo a autoridade final ────────────────────────────────

    public function test_cadastro_com_cpf_ja_existente_e_rejeitado_no_envio(): void
    {
        $this->criarCandidato(self::CPF_EXISTENTE);

        $response = $this->post(route('candidato.registro.post'), $this->payloadValido([
            'cpf' => self::CPF_EXISTENTE,
        ]));

        $response->assertSessionHasErrors('cpf');
        $this->assertGuest('candidato');
        $this->assertSame(1, Candidato::where('cpf', self::CPF_EXISTENTE)->count());
    }

    public function test_cadastro_com_senha_fraca_e_rejeitado_no_envio(): void
    {
        $response = $this->post(route('candidato.registro.post'), $this->payloadValido([
            'password'              => 'senhafraca',
            'password_confirmation' => 'senhafraca',
        ]));

        $response->assertSessionHasErrors('password');
        $this->assertGuest('candidato');
    }

    public function test_cadastro_com_confirmacao_divergente_e_rejeitado_no_envio(): void
    {
        $response = $this->post(route('candidato.registro.post'), $this->payloadValido([
            'password_confirmation' => 'Outra@Senha1',
        ]));

        $response->assertSessionHasErrors('password');
        $this->assertGuest('candidato');
    }

    private function payloadValido(array $sobrescreve = []): array
    {
        return array_merge([
            'cpf'                   => self::CPF_VALIDO,
            'email'                 => 'novo@example.com',
            'password'              => 'Senha@Forte1',
            'password_confirmation' => 'Senha@Forte1',
            'lgpd_consentimento'    => 1,
        ], $sobrescreve);
    }
}
