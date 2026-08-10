@extends('emails.vagas.layout')

@section('body')
<h2>Confirme seu e-mail</h2>
<p>Olá, {{ $candidato->nome_exibicao }}!</p>
<p>Recebemos seu cadastro no Portal de Vagas da FAPEU. Para liberar o acesso à sua conta, confirme seu e-mail clicando no botão abaixo.</p>

<div style="text-align:center;">
    <a href="{{ $url }}" class="btn">Confirmar e-mail</a>
</div>

<p>Se você não criou esta conta, pode ignorar este e-mail com segurança.</p>
<p style="font-size:0.925rem;color:#6C757D;">Este link expira em {{ config('auth.verification.expire', 60) }} minutos.</p>
@endsection
