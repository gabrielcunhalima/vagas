@extends('emails.vagas.layout')
@section('body')
<h2>Vaga não autorizada</h2>
<p>Olá, <strong>{{ $vaga->coordenador?->name }}</strong>!</p>
<p>Informamos que a vaga abaixo <strong>não foi autorizada</strong> pelo gestor responsável.</p>

<div class="info-box" style="border-left-color:#DC3545;background:#FFF5F5;">
    <div class="info-row"><span class="info-label">Vaga:</span><span class="info-value">{{ $vaga->titulo }}</span></div>
    <div class="info-row"><span class="info-label">Projeto:</span><span class="info-value">{{ $vaga->projeto_nome ?? 'N/A' }}</span></div>
    <div class="info-row"><span class="info-label">Status:</span><span class="info-value" style="color:#DC3545;font-weight:700;">Recusada</span></div>
</div>

@if($vaga->motivo_recusa)
<div style="background:#FFF3CD;border-left:4px solid #FFC107;border-radius:6px;padding:14px 16px;margin:16px 0;">
    <div style="font-size:0.905rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#856404;margin-bottom:6px;">Motivo informado pelo gestor</div>
    <p style="margin:0;font-size:1rem;color:#3E3E3F;">{{ $vaga->motivo_recusa }}</p>
</div>
@endif

<p>Acesse o painel do coordenador para editar a vaga conforme o motivo indicado e reenviar para autorização.</p>
@endsection
