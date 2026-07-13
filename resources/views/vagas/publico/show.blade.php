@extends('layouts.publico')

@section('title', $vaga->titulo)

@section('content')

<div style="background:linear-gradient(135deg,#074635 0%,#0D9571 100%);padding:2.5rem 0 2rem;">
    <div class="container-xl">
        <a href="{{ route('vagas.publicas.index') }}" style="color:rgba(255,255,255,0.7);font-size:0.85rem;display:inline-flex;align-items:center;gap:0.4rem;margin-bottom:1rem;">
            <i class="bi bi-arrow-left"></i> Voltar às vagas
        </a>
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
            <div>
                <!-- <div class="d-flex flex-wrap gap-2 mb-2">
                    <span class="badge-tipo badge-{{ $vaga->tipo }}">{{ $vaga->tipo_label }}</span>
                    <span class="badge-tipo badge-{{ $vaga->modalidade }}">{{ $vaga->modalidade_label }}</span>
                </div> -->
                <h1 style="font-size:1.75rem;font-weight:800;color:#fff;line-height:1.2;margin:0 0 0.4rem;">{{ $vaga->titulo }}</h1>
                @if($vaga->projeto_nome)
                <div style="color:rgba(255,255,255,0.75);font-size:0.9rem;">
                    <i class="bi bi-building me-1"></i>{{ $vaga->projeto_nome }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="container-xl py-4">
    <div class="row g-4">

        <div class="col-lg-8">

            <div style="border-radius:16px;padding:1.5rem;margin-bottom:1.5rem;display:flex;flex-direction:column;gap:1.25rem;">

                <div>
                    <h5 style="font-weight:700;color:#2C4A44;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">
                        <i class="bi bi-file-text-fill text-principal"></i>
                        Sobre a vaga
                    </h5>
                    <div style="font-size:0.9rem;line-height:1.75;color:#3E3E3F;white-space:pre-line;">{{ $vaga->descricao }}</div>
                </div>

                <hr style="border-color:rgba(108,117,125,0.2);margin:0;">

                <div>
                    <h5 style="font-weight:700;color:#2C4A44;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">
                        <i class="bi bi-check2-circle text-principal"></i>
                        Requisitos
                    </h5>
                    <x-lista-itens :texto="$vaga->requisitos" />
                </div>

                @if($vaga->requisitos_desejaveis)
                <hr style="border-color:rgba(108,117,125,0.2);margin:0;">
                <div>
                    <h5 style="font-weight:700;color:#2C4A44;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">
                        <i class="bi bi-star-fill text-principal"></i>
                        Requisitos desejáveis
                    </h5>
                    <x-lista-itens :texto="$vaga->requisitos_desejaveis" />
                </div>
                @endif

                @if($vaga->beneficios)
                <hr style="border-color:rgba(108,117,125,0.2);margin:0;">
                <div>
                    <h5 style="font-weight:700;color:#2C4A44;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">
                        <i class="bi bi-gift-fill text-principal"></i>
                        Benefícios
                    </h5>
                    <x-lista-itens :texto="$vaga->beneficios" />
                </div>
                @endif

            </div>

            <div style="background:linear-gradient(135deg,#074635,#0D9571);border-radius:16px;padding:2rem;text-align:center;">
                <h5 style="color:#fff;font-weight:700;margin-bottom:0.5rem;">Interessado nesta vaga?</h5>
                <p style="color:rgba(255,255,255,0.8);font-size:0.9rem;margin-bottom:1.25rem;">
                    Preencha o formulário e envie sua candidatura. É rápido e simples.
                </p>
                <a href="{{ route('fazenda.ressacada') }}"
                    style="display:inline-flex;align-items:center;gap:0.5rem;padding:13px 32px;background:#fff;color:#0D9571;font-weight:700;font-size:0.95rem;border-radius:50px;box-shadow:0 4px 15px rgba(0,0,0,0.2);transition:all 0.3s;">
                    <i class="bi bi-lightbulb"></i> Saiba mais sobre o projeto
                </a>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-interno p-0 sticky-top" style="top:80px;overflow:hidden;">
                <div style="background:#0D9571;padding:1rem 1.25rem;">
                    <span style="color:#fff;font-weight:700;font-size:0.88rem;">Detalhes da vaga</span>
                </div>
                <div style="padding:1.25rem;">
                    <div class="d-flex flex-column gap-3">
                        <div style="display:flex;align-items:flex-start;gap:0.75rem;">
                            <div style="width:32px;height:32px;background:rgba(13,149,113,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-briefcase-fill" style="color:#0D9571;font-size:0.9rem;"></i>
                            </div>
                            <div>
                                <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Tipo</div>
                                <div style="font-size:0.875rem;font-weight:600;color:#2C4A44;">{{ $vaga->tipo_label }}</div>
                            </div>
                        </div>

                        <div style="display:flex;align-items:flex-start;gap:0.75rem;">
                            <div style="width:32px;height:32px;background:rgba(13,149,113,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-geo-alt-fill" style="color:#0D9571;font-size:0.9rem;"></i>
                            </div>
                            <div>
                                <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Modalidade</div>
                                <div style="font-size:0.875rem;font-weight:600;color:#2C4A44;">{{ $vaga->modalidade_label }}</div>
                                @if($vaga->local_trabalho)
                                <div style="font-size:0.8rem;color:#6C757D;">{{ $vaga->local_trabalho }}</div>
                                @endif
                            </div>
                        </div>

                        @if($vaga->area)
                        <div style="display:flex;align-items:flex-start;gap:0.75rem;">
                            <div style="width:32px;height:32px;background:rgba(13,149,113,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-tag-fill" style="color:#0D9571;font-size:0.9rem;"></i>
                            </div>
                            <div>
                                <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Área</div>
                                <div style="font-size:0.875rem;font-weight:600;color:#2C4A44;">{{ $vaga->area }}</div>
                            </div>
                        </div>
                        @endif

                        @if($vaga->carga_horaria)
                        <div style="display:flex;align-items:flex-start;gap:0.75rem;">
                            <div style="width:32px;height:32px;background:rgba(13,149,113,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-clock-fill" style="color:#0D9571;font-size:0.9rem;"></i>
                            </div>
                            <div>
                                <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Carga horária</div>
                                <div style="font-size:0.875rem;font-weight:600;color:#2C4A44;">{{ $vaga->carga_horaria }}h por semana</div>
                            </div>
                        </div>
                        @endif

                        @if($vaga->remuneracao)
                        <div style="display:flex;align-items:flex-start;gap:0.75rem;">
                            <div style="width:32px;height:32px;background:rgba(13,149,113,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-cash-stack" style="color:#0D9571;font-size:0.9rem;"></i>
                            </div>
                            <div>
                                <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Remuneração</div>
                                <div style="font-size:0.875rem;font-weight:600;color:#2C4A44;">R$ {{ number_format($vaga->remuneracao, 2, ',', '.') }}</div>
                            </div>
                        </div>
                        @endif

                        @if(!empty($vaga->curso_desejado))
                        <div style="display:flex;align-items:flex-start;gap:0.75rem;">
                            <div style="width:32px;height:32px;background:rgba(13,149,113,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-mortarboard-fill" style="color:#0D9571;font-size:0.9rem;"></i>
                            </div>
                            <div>
                                <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Curso{{ count($vaga->curso_desejado) > 1 ? 's desejados' : ' desejado' }}</div>
                                <div style="font-size:0.875rem;font-weight:600;color:#2C4A44;">{{ implode(', ', $vaga->curso_desejado) }}</div>
                            </div>
                        </div>
                        @endif

                        <div style="display:flex;align-items:flex-start;gap:0.75rem;">
                            <div style="width:32px;height:32px;background:{{ $vaga->dias_restantes <= 5 ? 'rgba(220,53,69,0.1)' : 'rgba(13,149,113,0.1)' }};border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-calendar-x-fill" style="color:{{ $vaga->dias_restantes <= 5 ? '#DC3545' : '#0D9571' }};font-size:0.9rem;"></i>
                            </div>
                            <div>
                                <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Encerramento</div>
                                <div style="font-size:0.875rem;font-weight:600;color:{{ $vaga->dias_restantes <= 5 ? '#DC3545' : '#2C4A44' }};">
                                    {{ $vaga->data_encerramento->format('d/m/Y') }}
                                </div>
                                <div style="font-size:0.78rem;color:#6C757D;">
                                    {{ $vaga->dias_restantes === 0 ? 'Encerra hoje' : $vaga->dias_restantes . ' dias restantes' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('inscricao.create', $vaga) }}" class="btn btn-principal w-100">
                            <i class="bi bi-send-fill me-2"></i> Candidatar-se
                        </a>
                    </div>

                    {{-- Compartilhar --}}
                    <div class="mt-3">
                        <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;margin-bottom:0.5rem;">Compartilhar</div>
                        <div class="d-flex gap-2">
                            <button onclick="navigator.clipboard.writeText(window.location.href);alert('Link copiado!')"
                                    style="flex:1;background:#f8f9fa;border:1px solid #dee2e6;border-radius:8px;padding:7px;font-size:0.8rem;color:#495057;cursor:pointer;">
                                <i class="bi bi-link-45deg"></i> Copiar link
                            </button>
                            <a href="https://api.whatsapp.com/send?text={{ 'Confira a vaga ' . urlencode($vaga->titulo . ' na FAPEU: ' . request()->url()) }}"
                               target="_blank"
                               style="flex:1;background:#25D366;border:none;border-radius:8px;padding:7px;font-size:0.8rem;color:#fff;text-align:center;text-decoration:none;">
                                <i class="bi bi-whatsapp"></i> WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Vagas relacionadas --}}
@if($vagasRelacionadas->count() > 0)
<div class="container-xl py-4">
    <h3 style="font-size:1rem;font-weight:700;color:#2C4A44;margin-bottom:1rem;">Vagas relacionadas em <em>{{ $vaga->area }}</em></h3>
    <div class="row g-3">
        @foreach($vagasRelacionadas as $rel)
        @php $corRel = $rel->tipo === 'emprego' ? '#0D6EFD' : ($rel->tipo === 'bolsa' ? '#0D9571' : '#6F42C1'); @endphp
        <div class="col-md-4">
            <div style="background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.07);padding:1rem;height:100%;display:flex;flex-direction:column;justify-content:space-between;">
                <div>
                    <div class="d-flex gap-1 mb-1">
                        <span class="badge-tipo badge-{{ $rel->tipo }}" style="font-size:0.7rem">{{ $rel->tipo_label }}</span>
                        <span class="badge-tipo badge-{{ $rel->modalidade }}" style="font-size:0.7rem">{{ $rel->modalidade_label }}</span>
                    </div>
                    <h5 style="font-size:0.9rem;font-weight:700;margin:0 0 0.25rem;">
                        <a href="{{ route('vagas.publicas.show', $rel) }}" style="color:{{ $corRel }};text-decoration:none;">{{ $rel->titulo }}</a>
                    </h5>
                    @if($rel->cidade)
                    <div style="font-size:0.78rem;color:#6C757D;"><i class="bi bi-geo-alt"></i> {{ $rel->cidade }}/{{ $rel->estado }}</div>
                    @endif
                </div>
                <div class="mt-2">
                    <a href="{{ route('vagas.publicas.show', $rel) }}" style="font-size:0.8rem;color:{{ $corRel }};font-weight:600;text-decoration:none;">
                        Ver vaga <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection