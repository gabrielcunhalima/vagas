<?php

namespace Tests\Unit;

use App\Models\Candidato;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * O critério de completude é o gate da candidatura e a origem da barra de
 * progresso. Ele mora em um lugar só (Candidato::CAMPOS_OBRIGATORIOS) justamente
 * para não divergir entre servidor e interface — estes testes guardam esse ponto.
 */
class PerfilCompletudeTest extends TestCase
{
    use RefreshDatabase;

    public function test_perfil_recem_criado_esta_incompleto(): void
    {
        $candidato = Candidato::factory()->minimo()->create();

        $this->assertFalse($candidato->perfilCompleto());
        $this->assertArrayHasKey('nome', $candidato->pendencias());
        $this->assertArrayHasKey('curriculo', $candidato->pendencias());
    }

    public function test_perfil_com_todos_os_campos_esta_completo(): void
    {
        $this->assertTrue(Candidato::factory()->create()->perfilCompleto());
    }

    public function test_telefone_em_branco_bloqueia(): void
    {
        $candidato = Candidato::factory()->create(['telefone' => null]);

        $this->assertFalse($candidato->perfilCompleto());
        $this->assertArrayHasKey('telefone', $candidato->pendencias());
    }

    public function test_curso_concluido_dispensa_o_semestre(): void
    {
        $candidato = Candidato::factory()->create();
        $candidato->formacoes()->update(['situacao_curso' => 'concluido', 'semestre' => null]);
        $candidato = $candidato->fresh();

        $this->assertArrayNotHasKey('formacao', $candidato->pendencias());
        $this->assertTrue($candidato->perfilCompleto());
    }

    public function test_curso_em_andamento_exige_o_semestre(): void
    {
        $candidato = Candidato::factory()->create();
        $candidato->formacoes()->update(['situacao_curso' => 'cursando', 'semestre' => null]);
        $candidato = $candidato->fresh();

        $this->assertArrayHasKey('formacao', $candidato->pendencias());
    }

    public function test_perfil_sem_nenhuma_formacao_fica_incompleto(): void
    {
        $candidato = Candidato::factory()->create();
        $candidato->formacoes()->delete();
        $candidato = $candidato->fresh();

        $this->assertFalse($candidato->perfilCompleto());
        $this->assertArrayHasKey('formacao', $candidato->pendencias());
    }

    public function test_formacao_cadastrada_pela_metade_nao_conta_como_completa(): void
    {
        $candidato = Candidato::factory()->create();
        $candidato->formacoes()->delete();
        $candidato->formacoes()->create(['curso' => 'Administração']);
        $candidato = $candidato->fresh();

        $this->assertArrayHasKey('formacao', $candidato->pendencias());
    }

    public function test_segunda_formacao_incompleta_nao_invalida_a_primeira(): void
    {
        $candidato = Candidato::factory()->create();
        $candidato->formacoes()->create(['curso' => 'Segunda formação, só o curso']);
        $candidato = $candidato->fresh();

        $this->assertTrue($candidato->perfilCompleto());
    }

    public function test_campos_opcionais_em_branco_nao_bloqueiam(): void
    {
        $candidato = Candidato::factory()->create([
            'nome_social'        => null,
            'linkedin'           => null,
            'cep'                => null,
            'logradouro'         => null,
            'cidade'             => null,
            'estado'             => null,
            'pretensao_salarial' => null,
            'disponibilidade'    => null,
        ]);

        $this->assertTrue($candidato->perfilCompleto());
    }

    public function test_acessibilidade_sem_resposta_bloqueia_mas_responder_nao_libera_sozinho(): void
    {
        $candidato = Candidato::factory()->create(['possui_acessibilidade' => null]);
        $this->assertArrayHasKey('possui_acessibilidade', $candidato->pendencias());

        // "Não" é uma resposta válida — nulo é que significa "não respondeu".
        $candidato->update(['possui_acessibilidade' => false]);
        $this->assertArrayNotHasKey('possui_acessibilidade', $candidato->fresh()->pendencias());
    }

    public function test_sem_curriculo_o_perfil_fica_incompleto(): void
    {
        $candidato = Candidato::factory()->semCurriculo()->create();

        $this->assertFalse($candidato->perfilCompleto());
        $this->assertArrayHasKey('curriculo', $candidato->pendencias());
    }

    public function test_remover_curriculo_devolve_o_perfil_ao_estado_incompleto(): void
    {
        $candidato = Candidato::factory()->create();
        $this->assertTrue($candidato->perfilCompleto());

        $candidato->removerCurriculoAtual();

        $this->assertFalse($candidato->perfilCompleto());
    }

    public function test_novo_curriculo_cria_versao_sem_descartar_a_anterior(): void
    {
        $candidato = Candidato::factory()->create();
        $primeira = $candidato->curriculo_atual_id;

        $segunda = $candidato->curriculos()->create([
            'path'          => 'candidatos/curriculos/v2.pdf',
            'nome_original' => 'curriculo-v2.pdf',
            'enviado_em'    => now(),
        ]);
        $candidato->forceFill(['curriculo_atual_id' => $segunda->id])->save();

        $this->assertSame(2, $candidato->curriculos()->count());
        $this->assertNotNull($candidato->curriculos()->find($primeira));
        $this->assertSame($segunda->id, $candidato->fresh()->curriculo_atual_id);
    }

    public function test_estado_de_completude_traz_progresso_para_a_interface(): void
    {
        $estado = Candidato::factory()->minimo()->create()->estadoCompletude();

        $this->assertFalse($estado['completo']);
        $this->assertSame(0, $estado['progresso']);
        $this->assertNotEmpty($estado['pendencias']);

        $this->assertSame(100, Candidato::factory()->create()->estadoCompletude()['progresso']);
    }

    public function test_nome_de_exibicao_cai_no_email_enquanto_o_nome_nao_existe(): void
    {
        $candidato = Candidato::factory()->minimo()->create(['email' => 'fulano@teste.com']);

        $this->assertSame('fulano', $candidato->nome_exibicao);

        $candidato->update(['nome' => 'Fulano de Tal']);
        $this->assertSame('Fulano de Tal', $candidato->fresh()->nome_exibicao);
    }
}
