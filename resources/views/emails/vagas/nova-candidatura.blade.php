@extends('emails.vagas.layout')
@section('body')
<h2>Nova candidatura recebida</h2>
<p>Olá, <strong>{{ $candidatura->vaga->coordenador?->name }}</strong>!</p>
<p>Uma nova candidatura foi recebida para a vaga <strong>{{ $candidatura->vaga->titulo }}</strong>.</p>

<div class="info-box">
    <div class="info-row"><span class="info-label">Candidato:</span><span class="info-value">{{ $candidatura->nome }}</span></div>
    <div class="info-row"><span class="info-label">E-mail:</span><span class="info-value">{{ $candidatura->email }}</span></div>
    <div class="info-row"><span class="info-label">CPF:</span><span class="info-value">{{ $candidatura->cpf_formatado }}</span></div>
    <div class="info-row"><span class="info-label">Curso:</span><span class="info-value">{{ $candidatura->curso }}</span></div>
    <div class="info-row"><span class="info-label">Instituição:</span><span class="info-value">{{ $candidatura->instituicao }}</span></div>
    <div class="info-row"><span class="info-label">Recebida em:</span><span class="info-value">{{ $candidatura->created_at->format('d/m/Y \à\s H:i') }}</span></div>
</div>

<p>Acesse o painel para visualizar todos os detalhes e avançar o status desta candidatura.</p>
@endsection
