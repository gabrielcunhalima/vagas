@extends('emails.vagas.layout')
@section('body')
<h2>Sua vaga foi publicada! ✅</h2>
<p>Olá, <strong>{{ $vaga->coordenador?->name }}</strong>!</p>
<p>A vaga abaixo foi <strong>autorizada pelo gestor</strong> e está publicada na página pública do Portal de Vagas FAPEU. Os candidatos já podem visualizá-la e se inscrever.</p>

<div class="info-box">
    <div class="info-row"><span class="info-label">Vaga:</span><span class="info-value">{{ $vaga->titulo }}</span></div>
    <div class="info-row"><span class="info-label">Projeto:</span><span class="info-value">{{ $vaga->projeto_nome ?? '—' }}</span></div>
    <div class="info-row"><span class="info-label">Tipo:</span><span class="info-value">{{ $vaga->tipo_label }}</span></div>
    <div class="info-row"><span class="info-label">Encerramento:</span><span class="info-value">{{ $vaga->data_encerramento->format('d/m/Y') }}</span></div>
    <div class="info-row"><span class="info-label">Publicada em:</span><span class="info-value">{{ $vaga->autorizada_em?->format('d/m/Y H:i') }}</span></div>
</div>

<p>Acesse o painel do coordenador para acompanhar as candidaturas em tempo real.</p>
@endsection
