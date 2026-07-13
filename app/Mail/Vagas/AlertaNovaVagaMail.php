<?php

namespace App\Mail\Vagas;

use App\Models\Vagas\Vaga;
use App\Models\Vagas\AlertaVaga;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlertaNovaVagaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Vaga $vaga,
        public AlertaVaga $alerta
    ) {}

    public function build()
    {
        return $this->subject('Nova vaga disponível: ' . $this->vaga->titulo)
                    ->view('emails.vagas.alerta-nova-vaga');
    }
}
