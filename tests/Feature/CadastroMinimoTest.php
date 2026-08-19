<?php

namespace Tests\Feature;

use App\Models\Candidato;
use App\Models\User;
use App\Models\Vagas\Vaga;
use App\Notifications\Candidato\VerificarEmailCandidato;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CadastroMinimoTest extends TestCase
{
    use RefreshDatabase;

    private function dadosCadastro(array $over = []): array
    {
        return array_merge([
            'cpf' => '529.982.247-25',
            'email' => 'novo@teste.com',
            'password' => 'SenhaForte1!',
            'password_confirmation' => 'SenhaForte1!',
            'lgpd_consentimento' => true,
        ], $over);
    }

    // ─── Campos exigidos ─────────────────────────────────────────────────────

    public function test_cadastro_cria_conta_autenticada_com_perfil_incompleto(): void
    {
        $this->post(route('candidato.registro.post'), $this->dadosCadastro())
            ->assertRedirect(route('candidato.verification.notice'));

        $candidato = Candidato::where('email', 'novo@teste.com')->firstOrFail();

        $this->assertAuthenticatedAs($candidato, 'candidato');
        $this->assertFalse($candidato->perfilCompleto());
        $this->assertNull($candidato->nome);
        $this->assertNotNull($candidato->lgpd_consentimento_em);
    }

    public function test_cadastro_sem_consentimento_e_recusado(): void
    {
        $this->post(route('candidato.registro.post'), $this->dadosCadastro(['lgpd_consentimento' => false]))
            ->assertSessionHasErrors('lgpd_consentimento');

        $this->assertDatabaseCount('candidatos', 0);
    }

    public function test_cadastro_nao_exige_nenhum_dado_de_perfil(): void
    {
        // Nem nome, nem formação, nem currículo, nem código de conduta.
        $this->post(route('candidato.registro.post'), $this->dadosCadastro())
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('candidatos', 1);
    }

    public function test_cadastro_envia_verificacao_de_email(): void
    {
        Notification::fake();

        $this->post(route('candidato.registro.post'), $this->dadosCadastro());

        Notification::assertSentTo(
            Candidato::where('email', 'novo@teste.com')->first(),
            VerificarEmailCandidato::class
        );
    }

    // ─── Estado navegável antes de verificar ─────────────────────────────────

    public function test_conta_nao_verificada_pode_preencher_o_perfil(): void
    {
        $candidato = Candidato::factory()->minimo()->naoVerificado()->create();

        $this->actingAs($candidato, 'candidato')
            ->get(route('candidato.perfil.edit'))
            ->assertOk();
    }

    public function test_conta_nao_verificada_nao_consulta_candidaturas(): void
    {
        $candidato = Candidato::factory()->naoVerificado()->create();

        $this->actingAs($candidato, 'candidato')
            ->get(route('candidato.candidaturas.index'))
            ->assertRedirect(route('candidato.verification.notice'));
    }

    // ─── Sem adoção retroativa ───────────────────────────────────────────────

    /**
     * `adotarCandidaturasAnteriores()` foi removido junto com as colunas
     * `cpf`/`email` de `candidaturas` — não sobra o que casar, então o cadastro
     * apenas cria a conta, sem nenhum aviso sobre candidaturas incorporadas.
     */
    public function test_cadastro_novo_nao_menciona_candidaturas_incorporadas(): void
    {
        $this->post(route('candidato.registro.post'), $this->dadosCadastro())
            ->assertSessionHas('success', 'Conta criada com sucesso! Confirme seu e-mail para acessar.');
    }

    /**
     * A migration de limpeza apagou as candidaturas remanescentes sem conta e
     * `candidato_id` virou NOT NULL: a partir daqui é impossível existir uma
     * candidatura órfã para adotar.
     */
    public function test_candidatura_sem_candidato_id_nao_e_mais_permitida(): void
    {
        $coord = User::factory()->create(['perfil' => 'coordenador', 'ativo' => true]);

        $vaga = Vaga::create([
            'titulo' => 'Vaga Antiga',
            'descricao' => 'Descrição da vaga anterior ao cadastro obrigatório de conta.',
            'requisitos' => 'Requisitos da vaga antiga.',
            'tipo' => 'estagio',
            'area' => 'Tecnologia da Informação',
            'modalidade' => 'presencial',
            'cidade' => 'Florianópolis',
            'estado' => 'SC',
            'pais' => 'Brasil',
            'data_encerramento' => now()->addDays(30)->toDateString(),
            'status' => 'ativa',
            'coordenador_id' => $coord->id,
        ]);

        $this->expectException(QueryException::class);

        DB::table('candidaturas')->insert([
            'vaga_id' => $vaga->id,
            'status' => 'recebida',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
