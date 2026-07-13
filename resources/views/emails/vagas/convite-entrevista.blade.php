@extends('emails.vagas.layout')
@section('body')
<h2>Você foi selecionado(a) para entrevista! 🎉</h2>
<p>Olá, <strong>{{ $candidatura->nome }}</strong>!</p>
<p>Temos ótimas notícias: sua candidatura foi analisada e você foi selecionado(a) para a etapa de entrevista da vaga <strong>{{ $candidatura->vaga->titulo }}</strong>.</p>

<div class="info-box">
    <div class="info-row"><span class="info-label">Vaga:</span><span class="info-value">{{ $candidatura->vaga->titulo }}</span></div>
    <div class="info-row"><span class="info-label">Data:</span><span class="info-value">{{ $candidatura->entrevista_data?->format('d/m/Y') }}</span></div>
    <div class="info-row"><span class="info-label">Horário:</span><span class="info-value">{{ $candidatura->entrevista_data?->format('H:i') }}</span></div>
    <div class="info-row"><span class="info-label">Local:</span><span class="info-value">{{ $candidatura->entrevista_local }}</span></div>
    @if($candidatura->entrevista_observacoes)
    <div class="info-row"><span class="info-label">Observações:</span><span class="info-value">{{ $candidatura->entrevista_observacoes }}</span></div>
    @endif
</div>

<p>Caso não possa comparecer ou tenha alguma dúvida, entre em contato com a equipe FAPEU.</p>
<p>Boa sorte na entrevista!</p>
@endsection
