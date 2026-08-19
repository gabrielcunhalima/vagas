@extends('emails.vagas.layout')

@section('body')
<h2>Redefinição de senha</h2>
<p>Olá, {{ $candidato->nome_exibicao }}!</p>
<p>Recebemos uma solicitação para redefinir a senha da sua conta no Portal de Vagas da FAPEU. Clique no botão abaixo para criar uma nova senha.</p>

<div style="text-align:center;">
    <a href="{{ $url }}" class="btn">Redefinir minha senha</a>
</div>

<p>Se você não solicitou a redefinição de senha, ignore este e-mail, sua senha atual continuará válida.</p>
<p style="font-size:0.925rem;color:#6C757D;">Este link expira em {{ config('auth.passwords.candidatos.expire', 60) }} minutos.</p>
@endsection
