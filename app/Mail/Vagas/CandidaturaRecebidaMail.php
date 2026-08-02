<?php

namespace App\Mail\Vagas;

use App\Models\Vagas\Candidatura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidaturaRecebidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Candidatura $candidatura) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Candidatura Recebida: ' . $this->candidatura->vaga->titulo,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vagas.candidatura-recebida',
        );
    }
}
