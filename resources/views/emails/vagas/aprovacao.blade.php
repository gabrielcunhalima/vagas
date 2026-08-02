@extends('emails.vagas.layout')
@section('body')
<h2>Parabéns, você foi aprovado(a)! 🏆</h2>
<p>Olá, <strong>{{ $candidatura->nome }}</strong>!</p>
<p>É com grande satisfação que informamos que você foi <strong>aprovado(a)</strong> para a vaga abaixo. Em breve você receberá mais informações sobre os próximos passos para a contratação.</p>

<div class="info-box" style="border-left-color:#198754;">
    <div class="info-row"><span class="info-label">Vaga:</span><span class="info-value">{{ $candidatura->vaga->titulo }}</span></div>
    <div class="info-row"><span class="info-label">Projeto:</span><span class="info-value">{{ $candidatura->vaga->projeto_nome ?? 'N/A' }}</span></div>
    <div class="info-row"><span class="info-label">Tipo:</span><span class="info-value">{{ $candidatura->vaga->tipo_label }}</span></div>
</div>

<p>Aguarde contato da equipe FAPEU com as instruções para formalização da contratação.</p>
<p>Bem-vindo(a) à equipe!</p>
@endsection
