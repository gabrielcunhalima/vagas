<?php

namespace App\Mail\Vagas;

use App\Models\Vagas\Vaga;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VagaAutorizadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Vaga $vaga) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sua vaga foi publicada com sucesso: '.$this->vaga->titulo,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vagas.vaga-autorizada',
        );
    }
}
