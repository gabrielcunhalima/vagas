<?php

namespace Database\Seeders;

use App\Models\Candidato;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Candidato fictício com TODOS os campos do perfil preenchidos — nenhuma coluna
 * fica nula, para inspecionar todas as telas do portal do candidato.
 *
 * Credenciais: candidato.completo@teste.com / Senha@123
 */
class CandidatoSeeder extends Seeder
{
    private const EMAIL = 'candidato.completo@teste.com';
    private const SENHA = 'Senha@123';

    public function run(): void
    {
        $curriculoPath = $this->gerarCurriculo();

        $candidato = Candidato::updateOrCreate(
            ['email' => self::EMAIL],
            [
                // Autenticação
                'password'          => Hash::make(self::SENHA),
                'email_verified_at' => now()->subDays(30),

                // Dados pessoais
                'nome'          => 'Mariana Alves Ferreira',
                'nome_social'   => 'Mari Ferreira',
                'nacionalidade' => 'Brasileira',
                'cpf'           => '39053344705',
                'telefone'      => '48988776655',
                'linkedin'      => 'https://www.linkedin.com/in/mariana-ferreira-ficticia',

                // Outras qualificações em texto livre
                'outras_formacoes_mec' => 'Especialização em Gestão de Projetos - 2025',
                'outros_cursos'        => 'Curso de Excel Avançado - SENAC - 2024',

                // Endereço
                'cep'         => '88040900',
                'logradouro'  => 'Rua Engenheiro Agronômico Andrei Cristian Ferreira',
                'numero'      => '150',
                'complemento' => 'Bloco B, apto. 302',
                'bairro'      => 'Trindade',
                'cidade'      => 'Florianópolis',
                'estado'      => 'SC',
                'pais'        => 'Brasil',

                // Preferências de candidatura
                'pretensao_salarial' => 2200.00,
                'disponibilidade'    => '15 dias',

                // PcD e acessibilidade
                'pcd'                    => true,
                'pcd_tipo'               => 'Deficiência física (mobilidade reduzida)',
                'possui_acessibilidade'  => true,
                'acessibilidade_detalhe' => 'Utilizo cadeira de rodas. Preciso de acesso por rampa ou elevador '
                    . 'e de mesa com altura livre de no mínimo 70 cm no posto de trabalho.',

                // Currículo
                'curriculo_path'          => $curriculoPath,
                'curriculo_nome_original' => 'curriculo-mariana-ferreira.pdf',

                // LGPD
                'lgpd_consentimento'    => true,
                'lgpd_consentimento_em' => now()->subDays(30),

                'ativo' => true,
            ]
        );

        // Idempotente: substitui a lista inteira a cada execução do seeder.
        $candidato->formacoes()->delete();
        $candidato->formacoes()->create([
            'nivel_escolaridade' => 'graduacao',
            'situacao_curso'     => 'cursando',
            'curso'              => 'Engenharia de Produção',
            'instituicao'        => 'Universidade Federal de Santa Catarina',
            'semestre'           => '7º',
            'previsao_conclusao' => now()->addMonths(14)->toDateString(),
        ]);

        $this->command?->info('Candidato: ' . self::EMAIL . ' / ' . self::SENHA);
    }

    /** Grava um PDF real (abre em qualquer leitor) para exercitar o download do currículo. */
    private function gerarCurriculo(): string
    {
        $path = 'candidatos/curriculos/curriculo-mariana-ferreira.pdf';

        if (!Storage::disk('local')->exists($path)) {
            Storage::disk('local')->put($path, $this->montarPdf([
                'MARIANA ALVES FERREIRA',
                'Engenharia de Producao - UFSC - 7o semestre',
                'Florianopolis/SC - (48) 98877-6655',
                'mariana.ferreira@exemplo.com.br',
                '',
                'EXPERIENCIA',
                '2025-2026  Estagiaria de processos - Empresa Ficticia Ltda.',
                '2024-2025  Bolsista de extensao - Laboratorio de Produtividade UFSC',
                '',
                'CURRICULO FICTICIO GERADO PELO CandidatoSeeder',
            ]));
        }

        return $path;
    }

    /** Monta um PDF de uma página com xref válido (offsets calculados). */
    private function montarPdf(array $linhas): string
    {
        $texto = "BT\n/F1 11 Tf\n14 TL\n60 780 Td\n";
        foreach ($linhas as $linha) {
            $escapada = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $linha);
            $texto .= "({$escapada}) Tj T*\n";
        }
        $texto .= "ET";

        $objetos = [
            '<</Type/Catalog/Pages 2 0 R>>',
            '<</Type/Pages/Kids[3 0 R]/Count 1>>',
            '<</Type/Page/Parent 2 0 R/MediaBox[0 0 595 842]'
                . '/Resources<</Font<</F1 4 0 R>>>>/Contents 5 0 R>>',
            '<</Type/Font/Subtype/Type1/BaseFont/Helvetica/Encoding/WinAnsiEncoding>>',
            '<</Length ' . strlen($texto) . ">>\nstream\n{$texto}\nendstream",
        ];

        $pdf     = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objetos as $i => $corpo) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1) . " 0 obj\n{$corpo}\nendobj\n";
        }

        $inicioXref = strlen($pdf);
        $pdf .= 'xref' . "\n0 " . (count($objetos) + 1) . "\n0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        $pdf .= 'trailer<</Size ' . (count($objetos) + 1) . "/Root 1 0 R>>\n"
              . "startxref\n{$inicioXref}\n%%EOF";

        return $pdf;
    }
}
