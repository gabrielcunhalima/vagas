<?php

namespace Tests\Unit;

use App\Models\Candidato;
use App\Models\User;
use App\Models\Vagas\Vaga;
use App\Models\Vagas\Candidatura;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CandidaturaModelTest extends TestCase
{
    use RefreshDatabase;

    private function makeVaga(): Vaga
    {
        $coord = User::factory()->create(['perfil' => 'coordenador', 'ativo' => true]);
        return Vaga::create([
            'titulo'            => 'Vaga Teste',
            'descricao'         => 'Descrição detalhada da vaga para testes unitários.',
            'requisitos'        => 'Requisitos mínimos para teste.',
            'tipo'              => 'estagio',
            'area'              => 'Tecnologia da Informação',
            'modalidade'        => 'presencial',
            'cidade'            => 'Florianópolis',
            'estado'            => 'SC',
            'pais'              => 'Brasil',
            'data_encerramento' => now()->addDays(30)->toDateString(),
            'status'            => 'ativa',
            'coordenador_id'    => $coord->id,
            'notificar_email'   => true,
        ]);
    }

    /**
     * Atributos de pessoa vão para o PERFIL; os de processo, para a candidatura.
     * A candidatura não guarda mais cópia de identidade — ver CAMPOS_DO_PERFIL.
     */
    private function makeCandidatura(Vaga $vaga, array $attrs = []): Candidatura
    {
        $doPerfil = array_intersect_key($attrs, array_flip([
            'nome', 'email', 'cpf', 'telefone', 'linkedin',
            'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado',
            'pretensao_salarial', 'disponibilidade', 'pcd', 'pcd_tipo',
        ]));

        $candidato = Candidato::factory()->create($doPerfil);

        return Candidatura::create(array_merge([
            'vaga_id'      => $vaga->id,
            'candidato_id' => $candidato->id,
            'status'       => 'recebida',
        ], array_diff_key($attrs, $doPerfil)));
    }

    // ── Accessors / Labels ────────────────────────────────────────────────────

    public function test_status_label_recebida(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'recebida']);
        $this->assertEquals('Recebida', $c->status_label);
    }

    public function test_status_label_em_analise(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'em_analise']);
        $this->assertEquals('Em Análise', $c->status_label);
    }

    public function test_status_label_entrevista(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'entrevista']);
        $this->assertEquals('Entrevista', $c->status_label);
    }

    public function test_status_label_aprovado(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'aprovado']);
        $this->assertEquals('Aprovado', $c->status_label);
    }

    public function test_status_label_reprovado(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'reprovado']);
        $this->assertEquals('Reprovado', $c->status_label);
    }

    public function test_status_cor_recebida(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'recebida']);
        $this->assertEquals('info', $c->status_cor);
    }

    public function test_status_cor_aprovado(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'aprovado']);
        $this->assertEquals('success', $c->status_cor);
    }

    public function test_status_cor_reprovado(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'reprovado']);
        $this->assertEquals('danger', $c->status_cor);
    }

    public function test_cpf_formatado(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['cpf' => '52998224725']);
        $this->assertEquals('529.982.247-25', $c->cpf_formatado);
    }

    public function test_dados_pessoais_sao_lidos_do_perfil(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['nome' => 'Ana Souza']);
        $c->candidato->formacoes()->update(['curso' => 'Direito']);

        $this->assertSame('Ana Souza', $c->nome);
        $this->assertSame('Direito', $c->formacoes->first()->curso);

        // Nenhuma cópia: alterar o perfil altera o que a candidatura apresenta.
        $c->candidato->update(['nome' => 'Ana Souza Lima']);
        $this->assertSame('Ana Souza Lima', $c->fresh()->nome);
    }

    public function test_formacoes_da_candidatura_vem_do_candidato(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga);
        $c->candidato->formacoes()->delete();
        $c->candidato->formacoes()->create(['curso' => 'Curso 1']);
        $c->candidato->formacoes()->create(['curso' => 'Curso 2']);

        $this->assertCount(2, $c->fresh()->formacoes);
        $this->assertSame(['Curso 1', 'Curso 2'], $c->fresh()->formacoes->pluck('curso')->all());
    }

    public function test_endereco_completo(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, [
            'logradouro' => 'Rua XV de Novembro',
            'numero'     => '100',
            'cidade'     => 'Florianópolis',
            'estado'     => 'SC',
        ]);
        $this->assertStringContainsString('Rua XV de Novembro', $c->endereco_completo);
        $this->assertStringContainsString('Florianópolis/SC', $c->endereco_completo);
    }

    // ── Transições de status ──────────────────────────────────────────────────

    public function test_recebida_pode_ir_para_em_analise(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'recebida']);
        $this->assertTrue($c->podeTransicionarPara('em_analise'));
    }

    public function test_recebida_pode_ir_para_entrevista(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'recebida']);
        $this->assertTrue($c->podeTransicionarPara('entrevista'));
    }

    public function test_recebida_pode_ir_para_reprovado(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'recebida']);
        $this->assertTrue($c->podeTransicionarPara('reprovado'));
    }

    public function test_recebida_nao_pode_ir_para_aprovado(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'recebida']);
        $this->assertFalse($c->podeTransicionarPara('aprovado'));
    }

    public function test_em_analise_pode_ir_para_entrevista(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'em_analise']);
        $this->assertTrue($c->podeTransicionarPara('entrevista'));
    }

    public function test_em_analise_pode_ir_para_reprovado(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'em_analise']);
        $this->assertTrue($c->podeTransicionarPara('reprovado'));
    }

    public function test_em_analise_nao_pode_ir_para_em_analise(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'em_analise']);
        $this->assertFalse($c->podeTransicionarPara('em_analise'));
    }

    public function test_entrevista_pode_ir_para_aprovado(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'entrevista']);
        $this->assertTrue($c->podeTransicionarPara('aprovado'));
    }

    public function test_entrevista_pode_ir_para_reprovado(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'entrevista']);
        $this->assertTrue($c->podeTransicionarPara('reprovado'));
    }

    public function test_aprovado_nao_tem_proximos_status(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'aprovado']);
        $this->assertFalse($c->podeTransicionarPara('reprovado'));
        $this->assertFalse($c->podeTransicionarPara('em_analise'));
    }

    public function test_reprovado_nao_tem_proximos_status(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['status' => 'reprovado']);
        $this->assertFalse($c->podeTransicionarPara('aprovado'));
        $this->assertFalse($c->podeTransicionarPara('em_analise'));
    }

    // ── tem_curriculo ─────────────────────────────────────────────────────────

    public function test_tem_curriculo_quando_o_perfil_tem_versao_vigente(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga);

        $this->assertTrue($c->temCurriculo());
        $this->assertSame('curriculo.pdf', $c->curriculo_nome_original);
    }

    public function test_nao_tem_curriculo_quando_o_perfil_nao_tem(): void
    {
        $vaga = $this->makeVaga();
        $candidato = Candidato::factory()->semCurriculo()->create();

        $c = Candidatura::create([
            'vaga_id'      => $vaga->id,
            'candidato_id' => $candidato->id,
            'status'       => 'recebida',
        ]);

        $this->assertFalse($c->temCurriculo());
    }

    public function test_curriculo_substituido_aparece_na_candidatura(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga);

        $nova = $c->candidato->curriculos()->create([
            'path'          => 'candidatos/curriculos/v2.pdf',
            'nome_original' => 'curriculo-v2.pdf',
            'enviado_em'    => now(),
        ]);
        $c->candidato->forceFill(['curriculo_atual_id' => $nova->id])->save();

        $this->assertSame('curriculo-v2.pdf', $c->fresh()->curriculo_nome_original);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function test_scope_por_status(): void
    {
        $vaga = $this->makeVaga();
        $this->makeCandidatura($vaga, ['status' => 'recebida']);
        $this->makeCandidatura($vaga, ['status' => 'em_analise']);
        $this->makeCandidatura($vaga, ['status' => 'aprovado']);

        $recebidas = Candidatura::porStatus('recebida')->get();
        $this->assertCount(1, $recebidas);
    }

    public function test_scope_busca_por_nome(): void
    {
        $vaga = $this->makeVaga();
        $this->makeCandidatura($vaga, ['nome' => 'João Buscável Silva']);
        $this->makeCandidatura($vaga, ['nome' => 'Maria Outra']);

        $resultado = Candidatura::busca('Buscável')->get();
        $this->assertCount(1, $resultado);
    }

    public function test_scope_busca_por_curso(): void
    {
        $vaga = $this->makeVaga();
        $c1 = $this->makeCandidatura($vaga);
        $c1->candidato->formacoes()->update(['curso' => 'Engenharia Civil']);
        $c2 = $this->makeCandidatura($vaga);
        $c2->candidato->formacoes()->update(['curso' => 'Direito']);

        $resultado = Candidatura::busca('Engenharia')->get();
        $this->assertCount(1, $resultado);
        $this->assertSame($c1->id, $resultado->first()->id);
    }

    // ── PCD ──────────────────────────────────────────────────────────────────

    public function test_pcd_cast_boolean(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['pcd' => true, 'pcd_tipo' => 'Deficiência Auditiva']);
        $this->assertIsBool($c->pcd);
        $this->assertTrue($c->pcd);
        $this->assertEquals('Deficiência Auditiva', $c->pcd_tipo);
    }

    // ── Relacionamento ────────────────────────────────────────────────────────

    public function test_relacionamento_vaga(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga);
        $this->assertNotNull($c->vaga);
        $this->assertEquals($vaga->id, $c->vaga->id);
    }

    // ── Soft delete ───────────────────────────────────────────────────────────

    public function test_soft_delete(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga);
        $id = $c->id;
        $c->delete();

        $this->assertNull(Candidatura::find($id));
        $this->assertNotNull(Candidatura::withTrashed()->find($id));
    }
}
