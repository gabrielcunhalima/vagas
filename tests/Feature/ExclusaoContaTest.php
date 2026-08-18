<?php

namespace Tests\Feature;

use App\Models\Candidato;
use App\Models\User;
use App\Models\Vagas\Vaga;
use App\Models\Vagas\Candidatura;
use App\Services\AnonimizacaoService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

/**
 * A fonte única é o que torna a exclusão verificável: como não há cópia de dado
 * pessoal em lugar nenhum, apagar o perfil precisa bastar. Estes testes guardam
 * exatamente isso — inclusive o dado sensível, que a rotina anterior deixava
 * sobreviver por estar fora da lista enumerada à mão.
 */
class ExclusaoContaTest extends TestCase
{
    use RefreshDatabase;

    private function candidatoComCandidatura(): array
    {
        $coord = User::factory()->create(['perfil' => 'coordenador', 'ativo' => true]);

        $vaga = Vaga::create([
            'titulo'            => 'Vaga com Candidatura',
            'descricao'         => 'Descrição da vaga usada no teste de exclusão de conta.',
            'requisitos'        => 'Requisitos da vaga de teste.',
            'tipo'              => 'estagio',
            'area'              => 'Tecnologia da Informação',
            'modalidade'        => 'presencial',
            'cidade'            => 'Florianópolis',
            'estado'            => 'SC',
            'pais'              => 'Brasil',
            'data_encerramento' => now()->addDays(30)->toDateString(),
            'status'            => 'ativa',
            'coordenador_id'    => $coord->id,
        ]);

        $candidato = Candidato::factory()->create([
            'nome'                   => 'Maria Excluída',
            'email'                  => 'maria@teste.com',
            'pcd'                    => true,
            'pcd_tipo'               => 'Deficiência Visual',
            'possui_acessibilidade'  => true,
            'acessibilidade_detalhe' => 'Leitor de tela.',
            'pretensao_salarial'     => '2500.00',
        ]);

        $candidatura = Candidatura::create([
            'vaga_id'      => $vaga->id,
            'candidato_id' => $candidato->id,
            'status'       => 'recebida',
        ]);

        return [$candidato, $candidatura, $coord, $vaga];
    }

    public function test_exclusao_remove_dado_sensivel_do_perfil(): void
    {
        [$candidato] = $this->candidatoComCandidatura();

        app(AnonimizacaoService::class)->anonimizarCandidato($candidato);

        $perfil = Candidato::withTrashed()->find($candidato->id);

        // Dado sensível (art. 11) — o que a rotina anterior deixava passar.
        $this->assertNull($perfil->pcd_tipo);
        $this->assertFalse($perfil->pcd);
        $this->assertNull($perfil->acessibilidade_detalhe);
        $this->assertNull($perfil->possui_acessibilidade);

        // E o que ela também esquecia.
        $this->assertSame(0, $perfil->formacoes()->count());
        $this->assertNull($perfil->pretensao_salarial);

        $this->assertNotSame('maria@teste.com', $perfil->email);
        $this->assertSame('Candidato excluído', $perfil->nome);
    }

    public function test_exclusao_remove_todas_as_versoes_de_curriculo(): void
    {
        Storage::fake(\App\Models\Candidato::DISCO_CURRICULOS);
        [$candidato] = $this->candidatoComCandidatura();

        Storage::disk(\App\Models\Candidato::DISCO_CURRICULOS)->put($candidato->curriculoAtual->path, 'v1');

        $v2 = $candidato->curriculos()->create([
            'path'          => 'candidatos/curriculos/v2.pdf',
            'nome_original' => 'curriculo-v2.pdf',
            'enviado_em'    => now(),
        ]);
        Storage::disk(\App\Models\Candidato::DISCO_CURRICULOS)->put($v2->path, 'v2');
        $caminhoV1 = $candidato->curriculoAtual->path;

        app(AnonimizacaoService::class)->anonimizarCandidato($candidato);

        Storage::disk(\App\Models\Candidato::DISCO_CURRICULOS)->assertMissing($caminhoV1);
        Storage::disk(\App\Models\Candidato::DISCO_CURRICULOS)->assertMissing($v2->path);
        $this->assertSame(0, $candidato->curriculos()->count());
    }

    public function test_candidatura_permanece_como_registro_de_processo(): void
    {
        [$candidato, $candidatura] = $this->candidatoComCandidatura();

        app(AnonimizacaoService::class)->anonimizarCandidato($candidato);

        $this->assertNotNull($candidatura->fresh());
        $this->assertSame('recebida', $candidatura->fresh()->status);
    }

    public function test_coordenador_perde_acesso_aos_dados_de_conta_excluida(): void
    {
        [$candidato, $candidatura, $coord, $vaga] = $this->candidatoComCandidatura();

        app(AnonimizacaoService::class)->anonimizarCandidato($candidato);

        $res = $this->actingAs($coord)->get("/coord/vagas/{$vaga->id}/candidaturas/{$candidatura->id}");

        $res->assertOk();
        $this->assertTrue($this->propsInertia($res)['acessoExpirado']);
        $this->assertNaoVeInertia($res, 'maria@teste.com');
        $this->assertNaoVeInertia($res, 'Deficiência Visual');
    }

    public function test_alerta_e_removido_junto_com_a_conta(): void
    {
        [$candidato] = $this->candidatoComCandidatura();

        $this->actingAs($candidato, 'candidato')->post('/alertas', ['areas' => ['Administração']]);
        $this->assertDatabaseHas('vaga_alertas', ['candidato_id' => $candidato->id]);

        app(AnonimizacaoService::class)->anonimizarCandidato($candidato->fresh());

        $this->assertDatabaseMissing('vaga_alertas', ['candidato_id' => $candidato->id]);
    }

    public function test_exclusao_pela_area_do_candidato_exige_senha_correta(): void
    {
        [$candidato] = $this->candidatoComCandidatura();

        $this->actingAs($candidato, 'candidato')
            ->delete(route('candidato.excluir'), [
                'confirmar_exclusao' => true,
                'password'           => 'senha-errada',
            ])
            ->assertSessionHasErrors('password');

        $this->assertNull(Candidato::find($candidato->id)->deleted_at);
    }

    public function test_exclusao_pela_area_do_candidato_anonimiza_a_conta(): void
    {
        [$candidato] = $this->candidatoComCandidatura();

        $this->actingAs($candidato, 'candidato')
            ->delete(route('candidato.excluir'), [
                'confirmar_exclusao' => true,
                'password'           => 'password',
            ])
            ->assertRedirect(route('home'));

        $this->assertGuest('candidato');
        $this->assertNotNull(Candidato::withTrashed()->find($candidato->id)->deleted_at);
    }
}
