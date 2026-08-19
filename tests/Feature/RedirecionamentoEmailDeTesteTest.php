<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RedirecionamentoEmailDeTesteTest extends TestCase
{
    // Trava de AppServiceProvider::redirecionarEmailsDeTeste(): o banco de
    // homologação/teste tem e-mails de candidatos reais, então todo envio,
    // mesmo sem Mail::fake(), precisa chegar só a este endereço.

    public function test_endereco_de_seguranca_esta_configurado(): void
    {
        $this->assertSame('gabriel.lima@fapeu.org.br', config('mail.always_to'));
    }

    public function test_email_para_destinatario_real_e_redirecionado(): void
    {
        Mail::raw('Conteúdo de teste', function ($message): void {
            $message->to('candidato-de-verdade@exemplo.com')->subject('Teste');
        });

        $destinatarios = collect(Mail::getSymfonyTransport()->messages()->last()->getEnvelope()->getRecipients())
            ->map(fn ($endereco) => $endereco->getAddress())
            ->all();

        $this->assertSame(['gabriel.lima@fapeu.org.br'], $destinatarios);
    }
}
