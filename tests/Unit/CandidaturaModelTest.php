<?php

namespace Tests\Unit;

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

    private function makeCandidatura(Vaga $vaga, array $attrs = []): Candidatura
    {
        static $cpfCounter = 0;
        $cpfCounter++;
        $cpfs = ['52998224725', '71428793860', '87748248800', '11144477735', '47593888856',
                 '65571705827', '72605533701', '22233388813', '33344455567', '44455566676'];
        $cpf = $cpfs[$cpfCounter % count($cpfs)];

        return Candidatura::create(array_merge([
            'vaga_id'               => $vaga->id,
            'nome'                  => 'Candidato Teste ' . $cpfCounter,
            'email'                 => "candidato{$cpfCounter}@email.com",
            'cpf'                   => $cpf,
            'curso'                 => 'Ciência da Computação',
            'instituicao'           => 'UFSC',
            'status'                => 'recebida',
            'pais'                  => 'Brasil',
            'curriculo_path'        => "vagas/curriculos/fake{$cpfCounter}.pdf",
            'curriculo_nome_original' => "curriculo{$cpfCounter}.pdf",
        ], $attrs));
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

    public function test_tem_curriculo_com_path(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['curriculo_path' => 'vagas/curriculos/teste.pdf']);
        $this->assertTrue($c->temCurriculo());
    }

    public function test_tem_curriculo_sem_path(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['curriculo_path' => null]);
        $this->assertFalse($c->temCurriculo());
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

    public function test_tem_curriculo_true(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['curriculo_path' => 'vagas/curriculos/teste.pdf']);
        $this->assertTrue($c->temCurriculo());
    }

    public function test_tem_curriculo_false(): void
    {
        $vaga = $this->makeVaga();
        $c = $this->makeCandidatura($vaga, ['curriculo_path' => null]);
        $this->assertFalse($c->temCurriculo());
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
