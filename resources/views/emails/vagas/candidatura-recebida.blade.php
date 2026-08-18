@extends('emails.vagas.layout')
@section('body')
<h2>Candidatura recebida com sucesso!</h2>
<p>Olá, <strong>{{ $candidato->nome_exibicao }}</strong>!</p>
<p>Recebemos sua inscrição para a vaga abaixo. Assim que o RH analisar seu perfil, você será notificado(a) por e-mail.</p>

<div class="info-box">
    <div class="info-row"><span class="info-label">Vaga:</span><span class="info-value">{{ $vaga->titulo }}</span></div>
    @if ($vaga->projetoNome)
        <div class="info-row"><span class="info-label">Projeto:</span><span class="info-value">{{ $vaga->projetoNome }}</span></div>
    @endif
    @if ($vaga->tipo)
        <div class="info-row"><span class="info-label">Tipo:</span><span class="info-value">{{ $vaga->tipo }}</span></div>
    @endif
    @if ($vaga->localizacao())
        <div class="info-row"><span class="info-label">Local:</span><span class="info-value">{{ $vaga->localizacao() }}</span></div>
    @endif
    @if ($vaga->dataEncerramento)
        <div class="info-row"><span class="info-label">Encerramento:</span><span class="info-value">{{ $vaga->dataEncerramento->format('d/m/Y') }}</span></div>
    @endif
    <div class="info-row"><span class="info-label">Seu CPF:</span><span class="info-value">{{ $candidato->cpf_formatado }}</span></div>
</div>

<p style="margin-top:16px;">Fique atento(a) ao seu e-mail. Em caso de seleção para entrevista, você receberá uma nova notificação com data, hora e local.</p>
<p>Boa sorte!</p>
@endsection
