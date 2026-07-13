<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vagas\Vaga;
use App\Models\Vagas\Candidatura;
use App\Models\Vagas\AlertaVaga;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder completo para ambiente de testes.
 * Cobre todos os cenários: perfis, status de vagas, fluxos de candidatura, alertas.
 */
class TestSeeder extends Seeder
{
    public function run(): void
    {
        $this->criarUsuarios();
        $this->criarVagas();
        $this->criarCandidaturas();
        $this->criarAlertas();
    }

    private function criarUsuarios(): void
    {
        // Coordenador ativo padrão
        User::create([
            'name'     => 'Coordenador Ativo',
            'email'    => 'coord1@fapeu.org.br',
            'password' => Hash::make('password'),
            'perfil'   => 'coordenador',
            'cpf'      => '11144477735',
            'ativo'    => true,
        ]);

        // Segundo coordenador (para testar isolamento de vagas)
        User::create([
            'name'     => 'Coordenador Dois',
            'email'    => 'coord2@fapeu.org.br',
            'password' => Hash::make('password'),
            'perfil'   => 'coordenador',
            'cpf'      => '22233388813',
            'ativo'    => true,
        ]);

        // Coordenador inativo (para testar bloqueio de login)
        User::create([
            'name'     => 'Coordenador Inativo',
            'email'    => 'coord_inativo@fapeu.org.br',
            'password' => Hash::make('password'),
            'perfil'   => 'coordenador',
            'cpf'      => '33344455567',
            'ativo'    => false,
        ]);

        // Gestor ativo
        User::create([
            'name'     => 'Gestor Ativo',
            'email'    => 'gestor1@fapeu.org.br',
            'password' => Hash::make('password'),
            'perfil'   => 'gestor',
            'cpf'      => '44455566676',
            'ativo'    => true,
        ]);

        // Admin
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin1@fapeu.org.br',
            'password' => Hash::make('password'),
            'perfil'   => 'admin',
            'cpf'      => '55566677784',
            'ativo'    => true,
        ]);
    }

    private function criarVagas(): void
    {
        $coord1  = User::where('email', 'coord1@fapeu.org.br')->first();
        $coord2  = User::where('email', 'coord2@fapeu.org.br')->first();
        $gestor  = User::where('email', 'gestor1@fapeu.org.br')->first();

        $base = [
            'descricao'      => 'Descrição detalhada da vaga de teste com no mínimo vinte caracteres.',
            'requisitos'     => 'Requisitos mínimos para a vaga com pelo menos dez caracteres.',
            'modalidade'     => 'presencial',
            'cidade'         => 'Florianópolis',
            'estado'         => 'SC',
            'pais'           => 'Brasil',
            'notificar_email'=> true,
            'carga_horaria'  => 30,
            'remuneracao'    => 1200.00,
        ];

        // 1. Rascunho - coord1
        Vaga::create(array_merge($base, [
            'titulo'            => 'Vaga Rascunho TI',
            'tipo'              => 'estagio',
            'area'              => 'Tecnologia da Informação',
            'status'            => 'rascunho',
            'data_encerramento' => now()->addDays(30)->toDateString(),
            'coordenador_id'    => $coord1->id,
            'gestor_id'         => null,
        ]));

        // 2. Aguardando autorização - coord1
        Vaga::create(array_merge($base, [
            'titulo'            => 'Vaga Aguardando Autorização TI',
            'tipo'              => 'estagio',
            'area'              => 'Tecnologia da Informação',
            'status'            => 'aguardando_autorizacao',
            'data_encerramento' => now()->addDays(30)->toDateString(),
            'coordenador_id'    => $coord1->id,
            'gestor_id'         => null,
        ]));

        // 3. Ativa presencial - coord1
        Vaga::create(array_merge($base, [
            'titulo'            => 'Vaga Ativa Presencial TI',
            'tipo'              => 'estagio',
            'area'              => 'Tecnologia da Informação',
            'curso_desejado'    => ['Ciência da Computação', 'Sistemas de Informação'],
            'status'            => 'ativa',
            'data_encerramento' => now()->addDays(30)->toDateString(),
            'coordenador_id'    => $coord1->id,
            'gestor_id'         => $gestor->id,
            'autorizada_em'     => now()->subDay(),
        ]));

        // 4. Ativa remota - coord1
        Vaga::create(array_merge($base, [
            'titulo'            => 'Vaga Ativa Remota Administração',
            'tipo'              => 'emprego',
            'area'              => 'Administração',
            'modalidade'        => 'remoto',
            'remuneracao'       => 4000.00,
            'remuneracao_max'   => 6000.00,
            'status'            => 'ativa',
            'data_encerramento' => now()->addDays(45)->toDateString(),
            'coordenador_id'    => $coord1->id,
            'gestor_id'         => $gestor->id,
            'autorizada_em'     => now()->subDay(),
        ]));

        // 5. Ativa híbrida - coord1 (para testar alerta compatível)
        Vaga::create(array_merge($base, [
            'titulo'            => 'Vaga Ativa Híbrida Saúde Bolsa',
            'tipo'              => 'bolsa',
            'area'              => 'Saúde',
            'modalidade'        => 'hibrido',
            'remuneracao'       => 700.00,
            'status'            => 'ativa',
            'data_encerramento' => now()->addDays(20)->toDateString(),
            'coordenador_id'    => $coord1->id,
            'gestor_id'         => $gestor->id,
            'autorizada_em'     => now()->subDay(),
        ]));

        // 6. Encerrada (data no passado) - coord1
        Vaga::create(array_merge($base, [
            'titulo'            => 'Vaga Encerrada Administração',
            'tipo'              => 'estagio',
            'area'              => 'Administração',
            'status'            => 'encerrada',
            'data_encerramento' => now()->subDays(5)->toDateString(),
            'coordenador_id'    => $coord1->id,
            'gestor_id'         => $gestor->id,
        ]));

        // 7. Recusada - coord1
        Vaga::create(array_merge($base, [
            'titulo'            => 'Vaga Recusada Contabilidade',
            'tipo'              => 'estagio',
            'area'              => 'Ciências Contábeis',
            'status'            => 'recusada',
            'motivo_recusa'     => 'Requisitos insuficientes para publicação desta vaga.',
            'data_encerramento' => now()->addDays(20)->toDateString(),
            'coordenador_id'    => $coord1->id,
            'gestor_id'         => $gestor->id,
        ]));

        // 8. Inativa (foi ativa, coordenador desativou) - coord1
        Vaga::create(array_merge($base, [
            'titulo'            => 'Vaga Inativa Engenharia',
            'tipo'              => 'emprego',
            'area'              => 'Engenharia',
            'status'            => 'inativa',
            'data_encerramento' => now()->addDays(10)->toDateString(),
            'coordenador_id'    => $coord1->id,
            'gestor_id'         => $gestor->id,
            'encerrada_em'      => now()->subDays(2),
        ]));

        // 9. Ativa - coord2 (para testar isolamento de acesso)
        Vaga::create(array_merge($base, [
            'titulo'            => 'Vaga do Coordenador Dois',
            'tipo'              => 'estagio',
            'area'              => 'Comunicação e Marketing',
            'modalidade'        => 'remoto',
            'status'            => 'ativa',
            'data_encerramento' => now()->addDays(25)->toDateString(),
            'coordenador_id'    => $coord2->id,
            'gestor_id'         => $gestor->id,
            'autorizada_em'     => now()->subDay(),
        ]));

        // 10. Ativa sem remuneração - coord1 (vaga voluntária)
        Vaga::create(array_merge($base, [
            'titulo'            => 'Vaga Voluntária Educação',
            'tipo'              => 'bolsa',
            'area'              => 'Educação',
            'remuneracao'       => null,
            'status'            => 'ativa',
            'data_encerramento' => now()->addDays(60)->toDateString(),
            'coordenador_id'    => $coord1->id,
            'gestor_id'         => $gestor->id,
            'autorizada_em'     => now()->subDay(),
        ]));
    }

    private function criarCandidaturas(): void
    {
        $vagaAtiva = Vaga::where('titulo', 'Vaga Ativa Presencial TI')->first();
        $vagaAtiva2 = Vaga::where('titulo', 'Vaga Ativa Remota Administração')->first();

        if (!$vagaAtiva || !$vagaAtiva2) {
            return;
        }

        // Candidatura 1: status recebida
        Candidatura::create([
            'vaga_id'               => $vagaAtiva->id,
            'nome'                  => 'João Silva Santos',
            'email'                 => 'joao@email.com',
            'cpf'                   => '52998224725',
            'telefone'              => '48999001122',
            'curso'                 => 'Ciência da Computação',
            'instituicao'           => 'UFSC',
            'semestre'              => '6',
            'previsao_conclusao'    => now()->addYear()->toDateString(),
            'carta_apresentacao'    => 'Estou muito interessado nesta vaga de estágio.',
            'status'                => 'recebida',
            'cidade'                => 'Florianópolis',
            'estado'                => 'SC',
            'pais'                  => 'Brasil',
            'curriculo_path'        => 'vagas/curriculos/fake-joao.pdf',
            'curriculo_nome_original' => 'curriculo-joao.pdf',
            'linkedin'              => 'https://linkedin.com/in/joaosilva',
            'pretensao_salarial'    => 1500.00,
            'disponibilidade'       => 'Manhã e Tarde',
            'pcd'                   => false,
        ]);

        // Candidatura 2: status em_analise
        Candidatura::create([
            'vaga_id'               => $vagaAtiva->id,
            'nome'                  => 'Maria Oliveira Costa',
            'email'                 => 'maria@email.com',
            'cpf'                   => '71428793860',
            'telefone'              => '48988002233',
            'curso'                 => 'Sistemas de Informação',
            'instituicao'           => 'UDESC',
            'semestre'              => '8',
            'status'                => 'em_analise',
            'cidade'                => 'Florianópolis',
            'estado'                => 'SC',
            'pais'                  => 'Brasil',
            'curriculo_path'        => 'vagas/curriculos/fake-maria.pdf',
            'curriculo_nome_original' => 'curriculo-maria.pdf',
            'pcd'                   => true,
            'pcd_tipo'              => 'Deficiência Visual',
        ]);

        // Candidatura 3: status entrevista
        Candidatura::create([
            'vaga_id'               => $vagaAtiva->id,
            'nome'                  => 'Carlos Eduardo Pereira',
            'email'                 => 'carlos@email.com',
            'cpf'                   => '87748248800',
            'curso'                 => 'Análise e Desenvolvimento de Sistemas',
            'instituicao'           => 'Estácio',
            'semestre'              => '5',
            'status'                => 'entrevista',
            'entrevista_data'       => now()->addDays(3),
            'entrevista_local'      => 'Sala de Reuniões A - FAPEU',
            'entrevista_observacoes'=> 'Trazer portfólio atualizado.',
            'pais'                  => 'Brasil',
            'curriculo_path'        => 'vagas/curriculos/fake-carlos.pdf',
            'curriculo_nome_original' => 'curriculo-carlos.pdf',
        ]);

        // Candidatura 4: status aprovado
        Candidatura::create([
            'vaga_id'               => $vagaAtiva->id,
            'nome'                  => 'Ana Paula Rodrigues',
            'email'                 => 'ana@email.com',
            'cpf'                   => '11144477735',
            'curso'                 => 'Ciência da Computação',
            'instituicao'           => 'UFSC',
            'semestre'              => '7',
            'status'                => 'aprovado',
            'pais'                  => 'Brasil',
            'curriculo_path'        => 'vagas/curriculos/fake-ana.pdf',
            'curriculo_nome_original' => 'curriculo-ana.pdf',
        ]);

        // Candidatura 5: status reprovado
        Candidatura::create([
            'vaga_id'               => $vagaAtiva->id,
            'nome'                  => 'Pedro Henrique Lima',
            'email'                 => 'pedro@email.com',
            'cpf'                   => '47593888856',
            'curso'                 => 'Sistemas de Informação',
            'instituicao'           => 'UFSC',
            'semestre'              => '4',
            'status'                => 'reprovado',
            'observacoes_internas'  => 'Perfil não atende os requisitos mínimos.',
            'pais'                  => 'Brasil',
            'curriculo_path'        => 'vagas/curriculos/fake-pedro.pdf',
            'curriculo_nome_original' => 'curriculo-pedro.pdf',
        ]);

        // Candidatura 6: vaga diferente - para testar isolamento
        Candidatura::create([
            'vaga_id'               => $vagaAtiva2->id,
            'nome'                  => 'Fernanda Souza',
            'email'                 => 'fernanda@email.com',
            'cpf'                   => '65571705827',
            'curso'                 => 'Administração',
            'instituicao'           => 'UFSC',
            'semestre'              => '6',
            'status'                => 'recebida',
            'pais'                  => 'Brasil',
            'curriculo_path'        => 'vagas/curriculos/fake-fernanda.pdf',
            'curriculo_nome_original' => 'curriculo-fernanda.pdf',
        ]);
    }

    private function criarAlertas(): void
    {
        // Alerta ativo - todas as áreas/tipos (filtro amplo)
        AlertaVaga::create([
            'email'      => 'alerta_amplo@email.com',
            'areas'      => [],
            'modalidades'=> [],
            'tipos'      => [],
            'ativo'      => true,
            'token'      => 'token_amplo_ativo_teste_64_chars_aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        ]);

        // Alerta ativo - filtrado por área TI
        AlertaVaga::create([
            'email'      => 'alerta_ti@email.com',
            'areas'      => ['Tecnologia da Informação'],
            'modalidades'=> [],
            'tipos'      => ['estagio'],
            'ativo'      => true,
            'token'      => 'token_ti_ativo_teste_64_chars_aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        ]);

        // Alerta inativo (cancelado)
        AlertaVaga::create([
            'email'      => 'alerta_inativo@email.com',
            'areas'      => ['Administração'],
            'modalidades'=> ['remoto'],
            'tipos'      => [],
            'ativo'      => false,
            'token'      => 'token_inativo_teste_64_chars_aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        ]);

        // Alerta ativo - tipo bolsa modalidade presencial
        AlertaVaga::create([
            'email'      => 'alerta_bolsa@email.com',
            'areas'      => [],
            'modalidades'=> ['presencial'],
            'tipos'      => ['bolsa'],
            'ativo'      => true,
            'token'      => 'token_bolsa_ativo_64_chars_aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        ]);
    }
}
