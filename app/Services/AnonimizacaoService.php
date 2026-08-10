<?php

namespace App\Services;

use App\Models\Candidato;
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
 */
class AnonimizacaoService
{
    public function anonimizarCandidato(Candidato $candidato): void
    {
        // Todas as versões, não só a vigente: o histórico de currículos é dado
        // pessoal como qualquer outro.
        foreach ($candidato->curriculos as $versao) {
            Storage::disk('local')->delete($versao->path);
        }

        $anonimo = 'excluido_' . $candidato->id . '_' . substr(hash('sha256', $candidato->id . $candidato->cpf), 0, 16);

        $candidato->forceFill(['curriculo_atual_id' => null])->save();
        $candidato->curriculos()->delete();
        $candidato->formacoes()->delete();

        $candidato->update([
            'nome'                   => 'Candidato excluído',
            'nome_social'            => null,
            'email'                  => $anonimo . '@removido.invalid',
            'cpf'                    => substr(hash('sha256', $candidato->cpf), 0, 14),
            'telefone'               => null,
            'linkedin'               => null,
            'nacionalidade'          => null,
            'outras_formacoes_mec'   => null,
            'outros_cursos'          => null,
            'cep'                    => null,
            'logradouro'             => null,
            'numero'                 => null,
            'complemento'            => null,
            'bairro'                 => null,
            'cidade'                 => null,
            'estado'                 => null,
            'pretensao_salarial'     => null,
            'disponibilidade'        => null,
            'pcd'                    => false,
            'pcd_tipo'               => null,
            'possui_acessibilidade'  => null,
            'acessibilidade_detalhe' => null,
            'lgpd_consentimento'     => false,
            'ativo'                  => false,
        ]);

        // O alerta some junto: sem conta não há a quem enviar.
        $candidato->alerta?->delete();

        $candidato->delete();
    }
}
