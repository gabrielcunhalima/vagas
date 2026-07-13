@extends('emails.vagas.layout')
@section('body')
<h2>Candidatura recebida com sucesso!</h2>
<p>Olá, <strong>{{ $candidatura->nome }}</strong>!</p>
<p>Recebemos sua candidatura para a vaga abaixo. Assim que o coordenador analisar seu perfil, você será notificado(a) por e-mail.</p>

<div class="info-box">
    <div class="info-row"><span class="info-label">Vaga:</span><span class="info-value">{{ $candidatura->vaga->titulo }}</span></div>
    <div class="info-row"><span class="info-label">Projeto:</span><span class="info-value">{{ $candidatura->vaga->projeto_nome ?? '—' }}</span></div>
    <div class="info-row"><span class="info-label">Tipo:</span><span class="info-value">{{ $candidatura->vaga->tipo_label }}</span></div>
    <div class="info-row"><span class="info-label">Encerramento:</span><span class="info-value">{{ $candidatura->vaga->data_encerramento->format('d/m/Y') }}</span></div>
    <div class="info-row"><span class="info-label">Seu CPF:</span><span class="info-value">{{ $candidatura->cpf_formatado }}</span></div>
</div>

<p style="margin-top:16px;">Fique atento(a) ao seu e-mail. Em caso de seleção para entrevista, você receberá uma nova notificação com data, hora e local.</p>
<p>Boa sorte!</p>
@endsection
