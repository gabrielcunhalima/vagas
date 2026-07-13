<?php

namespace App\Mail\Vagas;

use App\Models\Vagas\Vaga;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VagaRecusadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Vaga $vaga) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sua vaga foi recusada — ' . $this->vaga->titulo,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vagas.vaga-recusada',
        );
    }
}
