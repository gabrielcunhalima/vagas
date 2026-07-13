@extends('emails.vagas.layout')

@section('conteudo')
<h2 style="color:#2d6a4f;margin-bottom:8px">Nova vaga disponível!</h2>
<p style="color:#555">Uma nova oportunidade compatível com seus interesses foi publicada.</p>

<div style="background:#f8f9fa;border-left:4px solid #2d6a4f;padding:16px;border-radius:4px;margin:20px 0">
    <h3 style="margin:0 0 8px;color:#1a1a1a">{{ $vaga->titulo }}</h3>
    <p style="margin:0 0 4px;color:#555">
        <strong>Tipo:</strong> {{ $vaga->tipoLabel }} &nbsp;|&nbsp;
        <strong>Modalidade:</strong> {{ $vaga->modalidadeLabel }}
    </p>
    @if($vaga->cidade)
    <p style="margin:0 0 4px;color:#555"><strong>Local:</strong> {{ $vaga->cidade }}/{{ $vaga->estado }}</p>
    @endif
    @if($vaga->remuneracao)
    <p style="margin:0 0 4px;color:#555">
        <strong>Remuneração:</strong> R$ {{ number_format($vaga->remuneracao, 2, ',', '.') }}
        @if($vaga->remuneracao_max) a R$ {{ number_format($vaga->remuneracao_max, 2, ',', '.') }} @endif
    </p>
    @endif
    <p style="margin:0;color:#555"><strong>Encerra em:</strong> {{ $vaga->data_encerramento?->format('d/m/Y') }}</p>
</div>

<div style="text-align:center;margin:24px 0">
    <a href="{{ url('/vagas/' . $vaga->id) }}"
       style="background:#2d6a4f;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:600;display:inline-block">
        Ver Vaga e Candidatar-se
    </a>
</div>

<p style="color:#999;font-size:12px;text-align:center;margin-top:24px">
    Você recebe este e-mail porque cadastrou um alerta de vagas.
    <a href="{{ url('/alertas/cancelar/' . $alerta->token) }}" style="color:#999">Cancelar alertas</a>
</p>
@endsection
