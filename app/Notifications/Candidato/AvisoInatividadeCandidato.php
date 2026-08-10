<?php

namespace App\Notifications\Candidato;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Aviso prévio de anonimização por inatividade.
 *
 * A anonimização é irreversível, então ela nunca acontece de surpresa: o titular
 * é avisado com antecedência e qualquer acesso à conta cancela o processo.
 */
class AvisoInatividadeCandidato extends Notification
{
    use Queueable;

    public function __construct(
        public int $diasParaAnonimizar,
        public int $anosInativa,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Sua conta no Portal de Vagas FAPEU será encerrada')
            ->view('emails.vagas.aviso-inatividade-candidato', [
                'candidato'          => $notifiable,
                'diasParaAnonimizar' => $this->diasParaAnonimizar,
                'anosInativa'        => $this->anosInativa,
                'url'                => route('candidato.login'),
            ]);
    }
}
