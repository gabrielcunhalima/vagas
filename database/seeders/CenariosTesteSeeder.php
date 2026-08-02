<?php

namespace Database\Seeders;

use App\Models\Candidato;
use App\Models\User;
use App\Models\Vagas\AlertaVaga;
use App\Models\Vagas\Candidatura;
use App\Models\Vagas\Vaga;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Cenários de teste ponta a ponta — idempotente (re-executável sem duplicar).
 *
 * Credenciais:
 *   Painel interno .... coordenador@fapeu.org.br / gestor@fapeu.org.br / admin@fapeu.org.br — senha: password
 *   Usuário inativo ... inativo@fapeu.org.br — senha: password (login deve ser bloqueado)
 *   Candidatos ........ candidato@teste.com (verificado, com currículo)
 *                       candidato.novo@teste.com (e-mail NÃO verificado — bloqueado até confirmar)
 *                       candidato.pcd@teste.com (verificado, PcD + acessibilidade + conflito de interesse)
 *                       — senha de todos: Senha@123
 */
class CenariosTesteSeeder extends Seeder
{
    private const SENHA_CANDIDATO = 'Senha@123';

    public function run(): void
    {
        $coordenador = User::where('email', 'coordenador@fapeu.org.br')->first()
            ?? User::where('perfil', 'coordenador')->first();
        $gestor = User::where('email', 'gestor@fapeu.org.br')->first()
            ?? User::where('perfil', 'gestor')->first();

        $this->usuarioInativo();
        $candidatos = $this->candidatos();
        $vagas      = $this->vagasPorStatus($coordenador, $gestor);
        $this->candidaturas($vagas['ativa'], $candidatos);
        $this->alertas();
    }

    /** Cenário: login bloqueado por usuário desativado. */
    private function usuarioInativo(): void
    {
        User::updateOrCreate(
            ['email' => 'inativo@fapeu.org.br'],
            [
                'name'     => 'Usuário Inativo',
                'password' => Hash::make('password'),
                'perfil'   => 'coordenador',
                'ativo'    => false,
            ]
        );
    }

    private function candidatos(): array
    {
        // PDF mínimo válido para testar download de currículo
        $curriculoPath = 'candidatos/curriculos/curriculo-teste.pdf';
        if (!Storage::disk('local')->exists($curriculoPath)) {
            Storage::disk('local')->put(
                $curriculoPath,
                "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]>>endobj\nxref\n0 4\ntrailer<</Size 4/Root 1 0 R>>\n%%EOF"
            );
        }

        $verificado = Candidato::updateOrCreate(
            ['email' => 'candidato@teste.com'],
            [
                'password'                => Hash::make(self::SENHA_CANDIDATO),
                'email_verified_at'       => now(),
                'nome'                    => 'Ana Beatriz Souza',
                'nacionalidade'           => 'Brasileira',
                'cpf'                     => '52998224725',
                'telefone'                => '48999110001',
                'linkedin'                => 'https://linkedin.com/in/ana-souza-teste',
                'curso'                   => 'Ciência da Computação',
                'instituicao'             => 'UFSC',
                'nivel_escolaridade'      => 'graduacao',
                'situacao_curso'          => 'cursando',
                'semestre'                => '6º',
                'previsao_conclusao'      => now()->addYear()->toDateString(),
                'cep'                     => '88034001',
                'logradouro'              => 'Rodovia Admar Gonzaga',
                'numero'                  => '1346',
                'bairro'                  => 'Itacorubi',
                'cidade'                  => 'Florianópolis',
                'estado'                  => 'SC',
                'pretensao_salarial'      => 1500.00,
                'disponibilidade'         => 'Imediata',
                'curriculo_path'          => $curriculoPath,
                'curriculo_nome_original' => 'curriculo-ana-souza.pdf',
                'lgpd_consentimento'      => true,
                'lgpd_consentimento_em'   => now(),
                'codigo_conduta_aceito_em'=> now(),
                'ativo'                   => true,
            ]
        );

        $naoVerificado = Candidato::updateOrCreate(
            ['email' => 'candidato.novo@teste.com'],
            [
                'password'                => Hash::make(self::SENHA_CANDIDATO),
                'email_verified_at'       => null,
                'nome'                    => 'Bruno Costa Lima',
                'nacionalidade'           => 'Brasileira',
                'cpf'                     => '11144477735',
                'telefone'                => '48999110002',
                'curso'                   => 'Administração',
                'instituicao'             => 'UDESC',
                'nivel_escolaridade'      => 'graduacao',
                'situacao_curso'          => 'cursando',
                'semestre'                => '3º',
                'previsao_conclusao'      => now()->addYears(2)->toDateString(),
                'lgpd_consentimento'      => true,
                'lgpd_consentimento_em'   => now(),
                'codigo_conduta_aceito_em'=> now(),
                'ativo'                   => true,
            ]
        );

        $pcd = Candidato::updateOrCreate(
            ['email' => 'candidato.pcd@teste.com'],
            [
                'password'                   => Hash::make(self::SENHA_CANDIDATO),
                'email_verified_at'          => now(),
                'nome'                       => 'Carla Mendes Oliveira',
                'nome_social'                => 'Carla Mendes',
                'nacionalidade'              => 'Brasileira',
                'cpf'                        => '93541134780',
                'telefone'                   => '48999110003',
                'curso'                      => 'Biblioteconomia',
                'instituicao'                => 'UFSC',
                'nivel_escolaridade'         => 'pos',
                'situacao_curso'             => 'concluido',
                'previsao_conclusao'         => now()->subYear()->toDateString(),
                'pcd'                        => true,
                'pcd_tipo'                   => 'Deficiência auditiva',
                'possui_acessibilidade'      => true,
                'acessibilidade_detalhe'     => 'Necessito de intérprete de Libras em entrevistas presenciais.',
                'conflito_interesse'         => true,
                'conflito_interesse_detalhe' => 'Prima trabalha no setor financeiro da FAPEU.',
                'disponibilidade'            => '30 dias',
                'lgpd_consentimento'         => true,
                'lgpd_consentimento_em'      => now(),
                'codigo_conduta_aceito_em'   => now(),
                'ativo'                      => true,
            ]
        );

        return compact('verificado', 'naoVerificado', 'pcd');
    }

    /** Uma vaga por status para exercitar todas as telas internas e públicas. */
    private function vagasPorStatus(?User $coordenador, ?User $gestor): array
    {
        $base = [
            'descricao'       => 'Vaga de teste gerada pelo CenariosTesteSeeder para validação ponta a ponta do portal.',
            'requisitos'      => "Cursando a partir da 3ª fase.\nBoa comunicação escrita.",
            'beneficios'      => 'Vale-transporte e auxílio-alimentação.',
            'area'            => 'Administração',
            'curso_desejado'  => ['Administração'],
            'carga_horaria'   => 20,
            'modalidade'      => 'presencial',
            'cidade'          => 'Florianópolis',
            'estado'          => 'SC',
            'pais'            => 'Brasil',
            'coordenador_id'  => $coordenador?->id,
            'notificar_email' => true,
        ];

        $cenarios = [
            'ativa' => [
                'titulo'            => '[TESTE] Vaga ativa, recém-publicada',
                'tipo'              => 'estagio',
                'remuneracao'       => 1200.00,
                'remuneracao_max'   => 1600.00,
                'data_encerramento' => now()->addDays(25)->toDateString(),
                'status'            => 'ativa',
                'gestor_id'         => $gestor?->id,
                'autorizada_em'     => now(),
            ],
            'expirando' => [
                'titulo'            => '[TESTE] Vaga ativa, últimos dias',
                'tipo'              => 'bolsa',
                'modalidade'        => 'remoto',
                'remuneracao'       => 900.00,
                'data_encerramento' => now()->addDays(2)->toDateString(),
                'status'            => 'ativa',
                'gestor_id'         => $gestor?->id,
                'autorizada_em'     => now()->subDays(20),
            ],
            'aguardando' => [
                'titulo'            => '[TESTE] Vaga aguardando autorização',
                'tipo'              => 'emprego',
                'modalidade'        => 'hibrido',
                'remuneracao'       => 3500.00,
                'data_encerramento' => now()->addDays(40)->toDateString(),
                'status'            => 'aguardando_autorizacao',
            ],
            'rascunho' => [
                'titulo'            => '[TESTE] Vaga em rascunho',
                'tipo'              => 'estagio',
                'data_encerramento' => now()->addDays(60)->toDateString(),
                'status'            => 'rascunho',
            ],
            'recusada' => [
                'titulo'            => '[TESTE] Vaga recusada pelo gestor',
                'tipo'              => 'bolsa',
                'data_encerramento' => now()->addDays(30)->toDateString(),
                'status'            => 'recusada',
                'gestor_id'         => $gestor?->id,
                'motivo_recusa'     => 'Descrição insuficiente das atividades. Detalhe o escopo do projeto e os entregáveis esperados.',
            ],
            'encerrada' => [
                'titulo'            => '[TESTE] Vaga encerrada',
                'tipo'              => 'estagio',
                'data_encerramento' => now()->subDays(10)->toDateString(),
                'status'            => 'encerrada',
                'gestor_id'         => $gestor?->id,
                'autorizada_em'     => now()->subDays(90),
                'encerrada_em'      => now()->subDays(10),
            ],
            'inativa' => [
                'titulo'            => '[TESTE] Vaga desativada pelo coordenador',
                'tipo'              => 'emprego',
                'data_encerramento' => now()->addDays(15)->toDateString(),
                'status'            => 'inativa',
                'gestor_id'         => $gestor?->id,
                'autorizada_em'     => now()->subDays(30),
                'encerrada_em'      => now()->subDays(3),
            ],
        ];

        $vagas = [];
        foreach ($cenarios as $chave => $dados) {
            $vagas[$chave] = Vaga::updateOrCreate(
                ['titulo' => $dados['titulo']],
                array_merge($base, $dados)
            );
        }

        return $vagas;
    }

    /** Candidaturas em todos os status na vaga ativa (única constraint: vaga_id + cpf). */
    private function candidaturas(Vaga $vagaAtiva, array $candidatos): void
    {
        $base = [
            'curso'       => 'Administração',
            'instituicao' => 'UFSC',
            'semestre'    => '5º',
            'cidade'      => 'Florianópolis',
            'estado'      => 'SC',
            'pais'        => 'Brasil',
        ];

        // Vinculada a candidato logado (com currículo do perfil)
        Candidatura::updateOrCreate(
            ['vaga_id' => $vagaAtiva->id, 'cpf' => $candidatos['verificado']->cpf],
            array_merge($base, [
                'candidato_id'            => $candidatos['verificado']->id,
                'nome'                    => $candidatos['verificado']->nome,
                'email'                   => $candidatos['verificado']->email,
                'telefone'                => '48999110001',
                'curso'                   => $candidatos['verificado']->curso,
                'linkedin'                => $candidatos['verificado']->linkedin,
                'pretensao_salarial'      => 1500.00,
                'disponibilidade'         => 'Imediata',
                'curriculo_path'          => $candidatos['verificado']->curriculo_path,
                'curriculo_nome_original' => $candidatos['verificado']->curriculo_nome_original,
                'carta_apresentacao'      => 'Tenho grande interesse na vaga e experiência prévia com projetos de extensão.',
                'status'                  => 'recebida',
            ])
        );

        // Visitante sem conta — em análise
        Candidatura::updateOrCreate(
            ['vaga_id' => $vagaAtiva->id, 'cpf' => '15350946056'],
            array_merge($base, [
                'nome'   => 'Diego Ferreira Nunes',
                'email'  => 'diego.visitante@teste.com',
                'status' => 'em_analise',
                'observacoes_internas' => 'Perfil interessante; validar disponibilidade de horário.',
            ])
        );

        // Entrevista agendada (futura)
        Candidatura::updateOrCreate(
            ['vaga_id' => $vagaAtiva->id, 'cpf' => '31753961033'],
            array_merge($base, [
                'nome'                   => 'Elisa Martins Rocha',
                'email'                  => 'elisa.entrevista@teste.com',
                'telefone'               => '48999110004',
                'status'                 => 'entrevista',
                'entrevista_data'        => now()->addDays(3)->setTime(14, 30),
                'entrevista_local'       => 'Sala de reuniões FAPEU, Campus UFSC, Trindade',
                'entrevista_observacoes' => 'Trazer documento com foto. Duração prevista: 45 minutos.',
            ])
        );

        // Aprovado
        Candidatura::updateOrCreate(
            ['vaga_id' => $vagaAtiva->id, 'cpf' => '81074815010'],
            array_merge($base, [
                'nome'   => 'Felipe Andrade Santos',
                'email'  => 'felipe.aprovado@teste.com',
                'status' => 'aprovado',
                'observacoes_internas' => 'Excelente entrevista. Encaminhado para contratação.',
            ])
        );

        // Reprovado + PcD (vinculada à candidata PcD)
        Candidatura::updateOrCreate(
            ['vaga_id' => $vagaAtiva->id, 'cpf' => $candidatos['pcd']->cpf],
            array_merge($base, [
                'candidato_id' => $candidatos['pcd']->id,
                'nome'         => $candidatos['pcd']->nome,
                'email'        => $candidatos['pcd']->email,
                'curso'        => $candidatos['pcd']->curso,
                'pcd'          => true,
                'pcd_tipo'     => 'Deficiência auditiva',
                'status'       => 'reprovado',
                'observacoes_internas' => 'Perfil sênior demais para a vaga de estágio.',
            ])
        );
    }

    private function alertas(): void
    {
        AlertaVaga::updateOrCreate(
            ['email' => 'alerta.ti@teste.com'],
            [
                'areas'                 => ['Tecnologia da Informação'],
                'tipos'                 => ['estagio'],
                'modalidades'           => ['remoto', 'hibrido'],
                'ativo'                 => true,
                'token'                 => Str::random(64),
                'lgpd_consentimento'    => true,
                'lgpd_consentimento_em' => now(),
            ]
        );

        AlertaVaga::updateOrCreate(
            ['email' => 'alerta.todas@teste.com'],
            [
                'areas'                 => [],
                'tipos'                 => [],
                'modalidades'           => [],
                'ativo'                 => true,
                'token'                 => Str::random(64),
                'lgpd_consentimento'    => true,
                'lgpd_consentimento_em' => now(),
            ]
        );

        AlertaVaga::updateOrCreate(
            ['email' => 'alerta.cancelado@teste.com'],
            [
                'areas'                 => ['Saúde'],
                'tipos'                 => [],
                'modalidades'           => [],
                'ativo'                 => false,
                'token'                 => Str::random(64),
                'lgpd_consentimento'    => true,
                'lgpd_consentimento_em' => now(),
            ]
        );
    }
}
