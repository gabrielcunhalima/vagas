<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vagas\Candidatura;

/**
 * Decaimento de acesso aos dados pessoais do candidato.
 *
 * Com o perfil vivo, o coordenador passaria a acompanhar indefinidamente os dados
 * atuais de quem se candidatou uma vez. Congelar os dados resolveria a privacidade
 * ao custo de recriar a cópia que esta mudança eliminou — então o que decai é o
 * ACESSO, não o dado: encerrado o processo, resta o registro dele.
 *
 * Nada aqui restringe o registro do processo (vaga, datas, status, eventos,
 * entrevista e observações internas), que o coordenador continua consultando.
 */
class CandidaturaPolicy
{
    /** Dias de carência após a decisão, cobrindo a janela de contestação. */
    public const CARENCIA_DIAS = 90;

    /** O coordenador responde pela vaga? Pré-condição de tudo o mais. */
    public function ver(User $user, Candidatura $candidatura): bool
    {
        return $user->isAdmin()
            || (int) $candidatura->vaga?->coordenador_id === (int) $user->id;
    }

    /**
     * Pode ver os dados pessoais atuais do candidato — nome, contato, endereço,
     * formação e currículo.
     */
    public function verDadosPessoais(User $user, Candidatura $candidatura): bool
    {
        if (!$this->ver($user, $candidatura)) {
            return false;
        }

        // Conta excluída: o acesso cessa na hora, sem prazo nem exceção.
        if (!$candidatura->candidato || $candidatura->candidato->trashed()) {
            return false;
        }

        // Aprovada não decai: os dados ainda servem para efetivar a contratação.
        if ($candidatura->status === 'aprovado') {
            return true;
        }

        if ($candidatura->status === 'reprovado') {
            $decidoEm = $candidatura->eventoDecisao?->ocorrido_em ?? $candidatura->updated_at;

            return $decidoEm === null
                || $decidoEm->diffInDays(now()) < self::CARENCIA_DIAS;
        }

        /*
         * Sem estado terminal: o processo pode ter sido simplesmente abandonado.
         * Sem esta regra o acesso nunca cairia para uma candidatura parada em
         * "recebida", e o decaimento viraria contornável por inércia.
         */
        if ($candidatura->vaga && $candidatura->vaga->status === 'encerrada') {
            $encerradaEm = $candidatura->vaga->data_encerramento;

            return $encerradaEm === null
                || $encerradaEm->diffInDays(now()) < self::CARENCIA_DIAS;
        }

        return true;
    }

    public function baixarCurriculo(User $user, Candidatura $candidatura): bool
    {
        return $this->verDadosPessoais($user, $candidatura) && $candidatura->temCurriculo();
    }
}
