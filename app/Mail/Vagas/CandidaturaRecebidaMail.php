<?php

namespace App\Mail\Vagas;

use App\Models\Candidato;
use App\Support\Drhflow\VagaDrhflow;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Confirmação de inscrição enviada ao candidato.
 *
 * Recebe a vaga do DRHFlow e o candidato, em vez do model `Candidatura`: a
 * inscrição agora vive em `EN_CANDIDATO_VAGA_EMPREGO` e não tem model local.
 */
class CandidaturaRecebidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public VagaDrhflow $vaga,
        public Candidato $candidato,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Candidatura recebida: '.($this->vaga->titulo ?? 'vaga FAPEU'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vagas.candidatura-recebida',
        );
    }
}
