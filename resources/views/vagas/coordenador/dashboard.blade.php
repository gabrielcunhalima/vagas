@extends('layouts.interno')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Portal de Vagas / Coordenador')

@section('topbar-actions')
    <a href="{{ route('coord.vagas.create') }}" class="btn btn-principal btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Nova Vaga
    </a>
@endsection

@section('content')

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(13,149,113,0.1);">
                <i class="bi bi-briefcase-fill" style="color:#0D9571;"></i>
            </div>
            <div>
                <div class="stat-number">{{ $stats['total_vagas'] }}</div>
                <div class="stat-label">Total de vagas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(25,135,84,0.1);">
                <i class="bi bi-check-circle-fill" style="color:#198754;"></i>
            </div>
            <div>
                <div class="stat-number">{{ $stats['vagas_ativas'] }}</div>
                <div class="stat-label">Vagas ativas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(13,110,253,0.1);">
                <i class="bi bi-people-fill" style="color:#0D6EFD;"></i>
            </div>
            <div>
                <div class="stat-number">{{ $stats['total_candidatos'] }}</div>
                <div class="stat-label">Total de candidatos</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(255,193,7,0.12);">
                <i class="bi bi-bell-fill" style="color:#856404;"></i>
            </div>
            <div>
                <div class="stat-number">{{ $stats['candidatos_novos'] }}</div>
                <div class="stat-label">Candidatos novos</div>
            </div>
        </div>
    </div>
</div>

<!-- @if($stats['aguardando_aut'] > 0)
    <div class="alert alert-aviso d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-hourglass-split" style="color:#856404;font-size:1.1rem;"></i>
        <span style="font-size:0.9rem;">
            Você tem <strong>{{ $stats['aguardando_aut'] }} {{ $stats['aguardando_aut'] === 1 ? 'vaga' : 'vagas' }}</strong>
            aguardando autorização do gestor.
        </span>
    </div>
@endif -->

@if($stats['entrevistas_hoje'] > 0)
    <div class="alert alert-principal d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-calendar-check-fill" style="color:#0D9571;font-size:1.1rem;"></i>
        <span style="font-size:0.9rem;">
            <strong>{{ $stats['entrevistas_hoje'] }} {{ $stats['entrevistas_hoje'] === 1 ? 'entrevista' : 'entrevistas' }}</strong>
            agendada{{ $stats['entrevistas_hoje'] === 1 ? '' : 's' }} para hoje.
        </span>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-interno">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-briefcase-fill me-2"></i>Vagas</span>
                <a href="{{ route('coord.vagas.index') }}" style="color:rgba(255,255,255,0.8);font-size:0.8rem;">
                    Ver todas <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="p-0">
                @forelse($vagasRecentes as $vaga)
                    <a href="{{ route('coord.candidaturas.index', $vaga) }}"
                       style="padding:0.9rem 1.25rem;border-bottom:1px solid #F1F5F4;display:flex;align-items:center;justify-content:space-between;gap:1rem;transition:background 0.15s;">
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:0.875rem;font-weight:600;color:#2C4A44;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $vaga->titulo }}
                            </div>
                            <div style="font-size:0.78rem;color:#6C757D;margin-top:2px;">
                                <i class="bi bi-people me-1"></i>{{ $vaga->candidaturas_count }} candidato{{ $vaga->candidaturas_count !== 1 ? 's' : '' }}
                                &nbsp;·&nbsp; Encerra {{ $vaga->data_encerramento->format('d/m/Y') }}
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <span class="status-badge status-{{ $vaga->status }}">{{ $vaga->status_label }}</span>
                            <i class="bi bi-arrow-right" style="color:#CACACA;font-size:0.85rem;"></i>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-4">
                        <i class="bi bi-briefcase" style="font-size:2rem;color:#CACACA;"></i>
                        <p style="color:#6C757D;margin:0.5rem 0 0;font-size:0.875rem;">Nenhuma vaga cadastrada ainda.</p>
                        <a href="{{ route('coord.vagas.create') }}" class="btn btn-principal btn-sm mt-2">Criar primeira vaga</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-interno">
            <div class="card-header">
                <span><i class="bi bi-person-lines-fill me-2"></i>Candidaturas recentes</span>
            </div>
            <div class="p-0">
                @forelse($candidaturasRecentes as $cand)
                    <a href="{{ route('coord.candidaturas.show', [$cand->vaga, $cand]) }}"
                       style="padding:0.85rem 1.25rem;border-bottom:1px solid #F1F5F4;display:flex;align-items:center;justify-content:space-between;gap:0.5rem;transition:background 0.15s;">
                        <div>
                            <div style="font-size:0.85rem;font-weight:600;color:#2C4A44;">{{ $cand->nome }}</div>
                            <div style="font-size:0.75rem;color:#6C757D;margin-top:1px;">{{ $cand->vaga->titulo }}</div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <span class="status-badge status-{{ $cand->status }}" style="white-space:nowrap;">
                                {{ $cand->status_label }}
                            </span>
                            <i class="bi bi-arrow-right" style="color:#CACACA;font-size:0.85rem;"></i>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-4">
                        <p style="color:#6C757D;margin:0;font-size:0.875rem;">Nenhuma candidatura ainda.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
