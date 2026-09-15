<?php

namespace Tests\Feature\Drhflow;

use App\Models\Candidato;
use App\Models\CandidatoFormacao;
use App\Services\AnonimizacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\UsaDrhflowFalso;
use Tests\TestCase;

/**
 * As garantias da capacidade `vagas-drhflow` sobre o banco do RH.
 *
 * A verificação central é de execução, não de leitura de código: um ouvinte de
 * consultas grava tudo que sai pela conexão `drhflow` enquanto os fluxos reais
 * do portal rodam, e o teste afirma sobre o SQL efetivamente emitido. Uma busca
 * textual por `->delete(` erraria nas duas direções — acusaria um `delete()` do
 * Eloquent no MySQL e deixaria passar um DELETE montado por outro caminho.
 *
 * A proteção que não depende do código — usuário de banco com permissão restrita
 * — é tarefa de entrega (1.5) e continua obrigatória: a credencial em uso hoje é
 * `sa`.
 */
class PreservacaoDoDrhflowTest extends TestCase
{
    use RefreshDatabase, UsaDrhflowFalso;

    /** @var list<string> */
    private array $sqlEmitido = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->configurarDrhflowFalso();

        $this->sqlEmitido = [];

        // Connection::listen() registra no dispatcher global e dispara para
        // todas as conexões — sem o filtro, um delete do Eloquent no MySQL
        // apareceria aqui como se fosse do DRHFlow.
        DB::listen(function ($consulta) {
            if ($consulta->connectionName === 'drhflow') {
                $this->sqlEmitido[] = $consulta->sql;
            }
        });
    }

    /**
     * Zera o registro. Chamado depois de montar o cenário, para que as
     * inserções do próprio fixture não contem como escrita do portal.
     */
    private function comecarAObservar(): void
    {
        $this->sqlEmitido = [];
    }

    private function candidatoCompleto(): Candidato
    {
        $candidato = Candidato::create([
            'nome' => 'Maria da Silva',
            'email' => 'maria@example.com',
            'password' => 'segredo-de-teste',
            'cpf' => '00001594923',
            'telefone' => '48999990000',
            'nacionalidade' => 'Brasileira',
            'cidade' => 'Florianópolis',
            'estado' => 'SC',
            'possui_acessibilidade' => false,
            'ativo' => true,
        ]);

        // Não é fillable, e sem isto o middleware `candidato.verified` desvia o
        // envio para a tela de verificação antes de chegar ao controller.
        $candidato->forceFill(['email_verified_at' => now()])->save();

        CandidatoFormacao::create([
            'candidato_id' => $candidato->id,
            'nivel_escolaridade' => 'graduacao',
            'situacao_curso' => 'cursando',
            'curso' => 'Ciência da Computação',
            'instituicao' => 'UFSC',
            'semestre' => '5',
            'previsao_conclusao' => now()->addYear(),
        ]);

        Storage::fake(Candidato::DISCO_CURRICULOS);
        $candidato->adicionarCurriculo(
            UploadedFile::fake()->create('cv.pdf', 40, 'application/pdf')
        );

        return $candidato->fresh(['formacoes', 'curriculos']);
    }

    /** @return list<string> */
    private function comandos(string ...$verbos): array
    {
        return array_values(array_filter(
            $this->sqlEmitido,
            fn (string $sql) => (bool) preg_match('/^\s*('.implode('|', $verbos).')\b/i', $sql)
        ));
    }

    // ── Execução: o que o portal realmente emite ──────────────────────────────

    public function test_inscricao_nao_emite_delete_nem_ddl_no_drhflow(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = $this->candidatoCompleto();

        $this->comecarAObservar();

        $this->actingAs($candidato, 'candidato')->post("/candidatura/{$codigo}", [
            '_honeypot' => '',
            'carta_apresentacao' => 'Tenho interesse na vaga.',
            'conflito_interesse' => 0,
            'politica_privacidade_aceite' => 1,
        ]);

        $this->assertSame([], $this->comandos('delete', 'drop', 'truncate', 'alter', 'create'));
    }

    public function test_inscricao_so_escreve_na_tabela_de_inscricao(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = $this->candidatoCompleto();

        $this->comecarAObservar();

        $resposta = $this->actingAs($candidato, 'candidato')->post("/candidatura/{$codigo}", [
            '_honeypot' => '',
            'conflito_interesse' => 0,
            'politica_privacidade_aceite' => 1,
        ]);

        $resposta->assertSessionHasNoErrors();

        $escritas = $this->comandos('insert', 'update');

        $this->assertNotEmpty($escritas, 'A inscrição não gravou nada — o teste perderia o sentido.');

        foreach ($escritas as $sql) {
            $this->assertStringContainsString(
                'EN_CANDIDATO_VAGA_EMPREGO',
                $sql,
                "Escrita fora da tabela de inscrição: {$sql}"
            );
        }
    }

    public function test_a_vaga_permanece_inalterada_apos_a_inscricao(): void
    {
        $codigo = $this->vagaDrhflow();
        $antes = (array) $this->drhflow()->table('EN_VAGA_EMPREGO')->where('CD_VAGA_EMPREGO', $codigo)->first();

        $candidato = $this->candidatoCompleto();

        $this->comecarAObservar();

        $this->actingAs($candidato, 'candidato')->post("/candidatura/{$codigo}", [
            '_honeypot' => '',
            'conflito_interesse' => 0,
            'politica_privacidade_aceite' => 1,
        ]);

        $depois = (array) $this->drhflow()->table('EN_VAGA_EMPREGO')->where('CD_VAGA_EMPREGO', $codigo)->first();

        $this->assertSame($antes, $depois);
    }

    public function test_exclusao_de_conta_nao_remove_linha_do_drhflow(): void
    {
        $codigo = $this->vagaDrhflow();
        $candidato = $this->candidatoCompleto();
        $this->inscricaoDrhflow('00001594923', $codigo, ['DE_EMAIL' => 'maria@example.com']);

        $this->comecarAObservar();

        app(AnonimizacaoService::class)->anonimizarCandidato($candidato);

        $this->assertSame([], $this->comandos('delete', 'drop', 'truncate'));
        $this->assertSame(
            1,
            $this->drhflow()->table('EN_CANDIDATO_VAGA_EMPREGO')->where('CD_VAGA_EMPREGO', $codigo)->count()
        );
    }

    public function test_listagem_publica_nao_escreve_nada(): void
    {
        $this->vagaDrhflow();

        $this->comecarAObservar();

        $this->get('/vagas')->assertOk();

        $this->assertSame([], $this->comandos('insert', 'update', 'delete', 'drop', 'truncate', 'alter', 'create'));
    }

    // ── Esquema ───────────────────────────────────────────────────────────────

    public function test_migrations_rodam_na_conexao_do_portal(): void
    {
        $this->assertNotSame('drhflow', config('database.migrations.connection'));
        $this->assertSame(config('database.default'), config('database.migrations.connection'));
    }

    public function test_nenhuma_migration_declara_a_conexao_do_drhflow(): void
    {
        $arquivos = glob(database_path('migrations/*.php')) ?: [];
        $this->assertNotEmpty($arquivos, 'Nenhuma migration encontrada — o teste perderia o sentido.');

        foreach ($arquivos as $arquivo) {
            $conteudo = file_get_contents($arquivo) ?: '';

            $this->assertDoesNotMatchRegularExpression(
                '/\$connection\s*=\s*[\'"]drhflow[\'"]/i',
                $conteudo,
                basename($arquivo).' declara a conexão do DRHFlow. O esquema do RH não é gerido por este projeto.'
            );

            $this->assertDoesNotMatchRegularExpression(
                '/Schema::connection\(\s*[\'"]drhflow[\'"]/i',
                $conteudo,
                basename($arquivo).' altera o esquema do DRHFlow.'
            );
        }
    }

    public function test_o_esquema_do_drhflow_nao_existe_no_banco_do_portal(): void
    {
        // As migrations do portal criaram o esquema do portal e nada além dele.
        // Se alguma tivesse alcançado o DRHFlow, a tabela apareceria dos dois
        // lados — e `inscricao_complementos` é o oposto: local por desenho.
        $this->assertTrue(Schema::hasTable('inscricao_complementos'));
        $this->assertFalse(Schema::hasTable('EN_CANDIDATO_VAGA_EMPREGO'));
        $this->assertFalse(Schema::hasTable('EN_VAGA_EMPREGO'));
    }

    // ── Configuração ──────────────────────────────────────────────────────────

    public function test_a_unica_tabela_de_escrita_declarada_e_a_de_inscricao(): void
    {
        $this->assertSame('EN_CANDIDATO_VAGA_EMPREGO', config('drhflow.tabela_inscricao'));
    }

    public function test_credenciais_do_drhflow_vem_do_ambiente(): void
    {
        $arquivo = file_get_contents(config_path('database.php')) ?: '';

        preg_match('/\'drhflow\'\s*=>\s*\[(.*?)\n        \],/s', $arquivo, $bloco);
        $this->assertNotEmpty($bloco, 'Bloco de configuração da conexão drhflow não encontrado.');

        foreach (['host', 'database', 'username', 'password'] as $chave) {
            $this->assertMatchesRegularExpression(
                "/'{$chave}'\s*=>\s*env\(/",
                $bloco[1],
                "A chave '{$chave}' da conexão drhflow precisa vir de env()."
            );
        }

        $this->assertStringNotContainsString('150.162.78.4', $bloco[1], 'Host de produção fixo no código-fonte.');
    }
}
