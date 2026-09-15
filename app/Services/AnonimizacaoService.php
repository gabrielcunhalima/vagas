<?php

namespace App\Services;

use App\Models\Candidato;
use App\Support\Drhflow\CurriculoDrhflowRepository;
use App\Support\Drhflow\DrhflowIndisponivelException;
use App\Support\Drhflow\InscricaoDrhflowRepository;
use App\Support\Drhflow\MapeadorInscricao;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Com fonte única, apagar o perfil apaga o dado em toda parte.
 *
 * A versão anterior deste serviço percorria candidatura por candidatura zerando 15
 * campos copiados — e a lista já havia atrasado em relação ao schema, deixando
 * `curso`, `instituicao`, `semestre`, `previsao_conclusao`, `pretensao_salarial`,
 * `disponibilidade`, `pcd` e `pcd_tipo` sobreviverem à exclusão pedida pelo titular
 * (`pcd`/`pcd_tipo` são dado sensível, art. 11 da LGPD).
 *
 * Não há mais lista a manter em dia: as candidaturas leem do perfil, então
 * anonimizar o perfil basta. Elas permanecem como registro de processo, e os
 * eventos também — por não conterem dado pessoal, nada neles precisa ser tocado.
 *
 * O DRHFlow é a exceção: lá a inscrição **é** uma cópia dos dados, e o RH pediu
 * que nada seja apagado daquele banco. As duas exigências se conciliam
 * substituindo os campos de identificação por marcadores e mantendo a linha —
 * o registro do processo permanece, a identidade sai (design D11).
 */
class AnonimizacaoService
{
    public function __construct(
        private readonly InscricaoDrhflowRepository $inscricoes = new InscricaoDrhflowRepository,
        private readonly CurriculoDrhflowRepository $curriculos = new CurriculoDrhflowRepository,
    ) {}

    public function anonimizarCandidato(Candidato $candidato): void
    {
        $anonimo = 'excluido_'.$candidato->id.'_'.substr(hash('sha256', $candidato->id.$candidato->cpf), 0, 16);

        // Antes de mexer no perfil: o CPF em claro é o que correlaciona com o
        // DRHFlow, e daqui a pouco ele vira hash.
        $this->anonimizarNoDrhflow($candidato, $anonimo);

        // Todas as versões, não só a vigente: o histórico de currículos é dado
        // pessoal como qualquer outro. A pasta do CPF sai inteira.
        $this->removerCurriculos($candidato);

        $candidato->forceFill(['curriculo_atual_id' => null])->save();
        $candidato->curriculos()->delete();
        $candidato->formacoes()->delete();

        // O complemento local guarda carta de apresentação e detalhe de conflito
        // de interesse — texto livre escrito pelo titular. Sai junto.
        $candidato->inscricaoComplementos()->delete();

        $candidato->update([
            'nome' => 'Candidato excluído',
            'nome_social' => null,
            'email' => $anonimo.'@removido.invalid',
            'cpf' => substr(hash('sha256', $candidato->cpf), 0, 14),
            'telefone' => null,
            'linkedin' => null,
            'nacionalidade' => null,
            'outras_formacoes_mec' => null,
            'outros_cursos' => null,
            'cep' => null,
            'logradouro' => null,
            'numero' => null,
            'complemento' => null,
            'bairro' => null,
            'cidade' => null,
            'estado' => null,
            'pretensao_salarial' => null,
            'disponibilidade' => null,
            'pcd' => false,
            'pcd_tipo' => null,
            'possui_acessibilidade' => null,
            'acessibilidade_detalhe' => null,
            'lgpd_consentimento' => false,
            'ativo' => false,
        ]);

        // O alerta some junto: sem conta não há a quem enviar.
        $candidato->alerta?->delete();

        $candidato->delete();
    }

    /**
     * Substitui os campos de identificação nas linhas do CPF em
     * `EN_CANDIDATO_VAGA_EMPREGO`.
     *
     * Nenhuma linha é removida, e `NU_CPF`, `CD_VAGA_EMPREGO` e tudo que o RH
     * preencheu (datas de entrevista, notas, média) ficam intactos.
     *
     * O DRHFlow fora do ar não pode impedir a exclusão da conta: o titular tem
     * direito a ela, e o portal não controla a disponibilidade daquele banco. A
     * falha é registrada com o CPF anonimizado para reconciliação manual.
     */
    private function anonimizarNoDrhflow(Candidato $candidato, string $marcador): void
    {
        try {
            $cpf = MapeadorInscricao::cpf($candidato);
            $linhas = $this->inscricoes->anonimizar($cpf, $marcador);

            // O nome do arquivo costuma ser o nome da pessoa, e o PDF sai logo
            // abaixo: a linha fica, o apontamento não.
            $this->curriculos->esvaziar($cpf);

            Log::info('Inscrições anonimizadas no DRHFlow após exclusão de conta.', [
                'candidato_id' => $candidato->id,
                'linhas' => $linhas,
            ]);
        } catch (DrhflowIndisponivelException $e) {
            Log::error('Exclusão de conta concluída, mas o DRHFlow não pôde ser anonimizado.', [
                'candidato_id' => $candidato->id,
                'marcador' => $marcador,
                'erro' => $e->getMessage(),
            ]);
        }
    }

    /** Os PDFs e a pasta do CPF, que fica vazia depois disso. */
    private function removerCurriculos(Candidato $candidato): void
    {
        $disco = Storage::disk(Candidato::DISCO_CURRICULOS);

        foreach ($candidato->curriculos as $versao) {
            $disco->delete($versao->path);
        }

        $pasta = $candidato->pastaCurriculos();

        if ($pasta !== '' && $disco->directoryExists($pasta)) {
            $disco->deleteDirectory($pasta);
        }
    }
}
