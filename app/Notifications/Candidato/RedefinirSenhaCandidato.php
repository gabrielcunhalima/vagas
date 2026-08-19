<?php

namespace App\Notifications\Candidato;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class RedefinirSenhaCandidato extends ResetPassword
{
    protected function resetUrl($notifiable)
    {
        return url(route('candidato.senha.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }

    public function toMail($notifiable)
    {
        $url = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Redefinição de senha: Portal de Vagas FAPEU')
            ->view('emails.vagas.redefinir-senha-candidato', [
                'candidato' => $notifiable,
                'url' => $url,
            ]);
    }
}
