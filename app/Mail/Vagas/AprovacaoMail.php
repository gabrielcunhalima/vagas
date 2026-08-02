<?php

namespace App\Mail\Vagas;

use App\Models\Vagas\Candidatura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AprovacaoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Candidatura $candidatura) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Parabéns, a vaga é sua! ' . $this->candidatura->vaga->titulo,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vagas.aprovacao',
        );
    }
}
