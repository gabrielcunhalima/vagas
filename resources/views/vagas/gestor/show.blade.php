@extends('layouts.interno')

@section('title', 'Analisar — ' . $vaga->titulo)
@section('page-title', 'Analisar Vaga')
@section('breadcrumb', 'Gestor / Vagas / Análise')

@section('content')

<div class="row g-4">
    <div class="col-lg-8">

        <div class="card-interno mb-4" style="padding:1.5rem;">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3 pb-3" style="border-bottom:1px solid #F1F5F4;">
                <div>
                    <h5 style="font-weight:700;color:#2C4A44;margin:0 0 4px;">{{ $vaga->titulo }}</h5>
                    <div style="font-size:0.82rem;color:#6C757D;">
                        {{ $vaga->projeto_nome }} &nbsp;·&nbsp; {{ $vaga->tipo_label }} &nbsp;·&nbsp; {{ $vaga->modalidade_label }}
                    </div>
                </div>
                <span class="status-badge status-{{ $vaga->status }}">{{ $vaga->status_label }}</span>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Coordenador</div>
                    <div style="font-size:0.875rem;color:#3E3E3F;">{{ $vaga->coordenador?->name ?? '—' }}</div>
                </div>
                <div class="col-sm-4">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Área</div>
                    <div style="font-size:0.875rem;color:#3E3E3F;">{{ $vaga->area }}</div>
                </div>
                <div class="col-sm-4">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Encerramento</div>
                    <div style="font-size:0.875rem;color:#3E3E3F;">{{ $vaga->data_encerramento->format('d/m/Y') }}</div>
                </div>
                @if($vaga->carga_horaria)
                <div class="col-sm-4">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Carga horária</div>
                    <div style="font-size:0.875rem;color:#3E3E3F;">{{ $vaga->carga_horaria }}h/semana</div>
                </div>
                @endif
                @if($vaga->remuneracao)
                <div class="col-sm-4">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Remuneração</div>
                    <div style="font-size:0.875rem;color:#3E3E3F;">R$ {{ number_format($vaga->remuneracao, 2, ',', '.') }}</div>
                </div>
                @endif
                @if($vaga->local_trabalho)
                <div class="col-sm-4">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Local</div>
                    <div style="font-size:0.875rem;color:#3E3E3F;">{{ $vaga->local_trabalho }}</div>
                </div>
                @endif
            </div>

            <div class="mb-4">
                <div style="font-size:0.82rem;font-weight:700;color:#2C4A44;margin-bottom:0.5rem;">Descrição</div>
                <div style="font-size:0.875rem;line-height:1.75;color:#3E3E3F;white-space:pre-line;">{{ $vaga->descricao }}</div>
            </div>

            <div class="mb-4">
                <div style="font-size:0.82rem;font-weight:700;color:#2C4A44;margin-bottom:0.5rem;">Requisitos</div>
                <x-lista-itens :texto="$vaga->requisitos" />
            </div>

            @if($vaga->requisitos_desejaveis)
            <div class="mb-4">
                <div style="font-size:0.82rem;font-weight:700;color:#2C4A44;margin-bottom:0.5rem;">Requisitos desejáveis</div>
                <x-lista-itens :texto="$vaga->requisitos_desejaveis" />
            </div>
            @endif

            @if($vaga->beneficios)
            <div>
                <div style="font-size:0.82rem;font-weight:700;color:#2C4A44;margin-bottom:0.5rem;">Benefícios</div>
                <x-lista-itens :texto="$vaga->beneficios" />
            </div>
            @endif
        </div>

    </div>

    {{-- Ações --}}
    <div class="col-lg-4">
        @if($vaga->status === 'aguardando_autorizacao')

            {{-- Autorizar --}}
            <div class="card-interno mb-3" style="padding:1.25rem;">
                <h6 style="font-weight:700;color:#2C4A44;margin-bottom:0.75rem;font-size:0.875rem;">
                    <i class="bi bi-check-circle text-principal me-1"></i> Autorizar vaga
                </h6>
                <p style="font-size:0.82rem;color:#6C757D;margin-bottom:1rem;">
                    Ao autorizar, a vaga será publicada imediatamente na página pública e o coordenador será notificado por e-mail.
                </p>
                <form method="POST" action="{{ route('gestor.vagas.autorizar', $vaga) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-principal w-100"
                            onclick="return confirm('Autorizar e publicar esta vaga?')">
                        <i class="bi bi-check-circle-fill me-2"></i> Autorizar e publicar
                    </button>
                </form>
            </div>

            {{-- Recusar --}}
            <div class="card-interno p-0" style="overflow:hidden;">
                <div class="card-header" style="background:linear-gradient(45deg,#dc3545,#c82333);">
                    <i class="bi bi-x-circle me-2"></i> Recusar vaga
                </div>
                <div style="padding:1.25rem;">
                    @if($errors->has('motivo_recusa'))
                        <div class="alert alert-danger py-2 mb-2" style="font-size:0.82rem;">
                            {{ $errors->first('motivo_recusa') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('gestor.vagas.recusar', $vaga) }}">
                        @csrf @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label" style="font-size:0.8rem;">Motivo da recusa <span class="text-danger">*</span></label>
                            <textarea name="motivo_recusa" rows="4" class="form-control form-control-sm"
                                      placeholder="Descreva o motivo pelo qual a vaga não pode ser publicada…"
                                      required minlength="10">{{ old('motivo_recusa') }}</textarea>
                        </div>
                        <button type="submit" class="btn w-100 btn-outline-danger"
                                onclick="return confirm('Recusar esta vaga? O coordenador será notificado.')">
                            <i class="bi bi-x-circle me-2"></i> Recusar vaga
                        </button>
                    </form>
                </div>
            </div>

        @else
            <div class="card-interno" style="padding:1.25rem;text-align:center;">
                <span class="status-badge status-{{ $vaga->status }} mb-2">{{ $vaga->status_label }}</span>
                @if($vaga->motivo_recusa)
                    <div style="background:#FFF3CD;border-radius:8px;padding:1rem;margin-top:0.75rem;text-align:left;">
                        <div style="font-size:0.75rem;font-weight:700;color:#856404;margin-bottom:0.35rem;text-transform:uppercase;">Motivo da recusa</div>
                        <div style="font-size:0.85rem;color:#3E3E3F;">{{ $vaga->motivo_recusa }}</div>
                    </div>
                @endif
            </div>
        @endif

        <a href="{{ route('gestor.vagas.index') }}" class="d-block text-center mt-3"
           style="font-size:0.82rem;color:#6C757D;">
            <i class="bi bi-arrow-left me-1"></i> Voltar à lista
        </a>
    </div>
</div>

@endsection
