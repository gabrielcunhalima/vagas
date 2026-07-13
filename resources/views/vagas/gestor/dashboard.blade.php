@extends('layouts.interno')

@section('title', 'Dashboard Gestor')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Portal de Vagas / Gestor')

@section('content')

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(255,193,7,0.12);">
                <i class="bi bi-hourglass-split" style="color:#856404;"></i>
            </div>
            <div>
                <div class="stat-number">{{ $stats['aguardando_aut'] }}</div>
                <div class="stat-label">Aguardando análise</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(13,149,113,0.1);">
                <i class="bi bi-check-circle-fill" style="color:#0D9571;"></i>
            </div>
            <div>
                <div class="stat-number">{{ $stats['autorizadas'] }}</div>
                <div class="stat-label">Autorizadas por mim</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(220,53,69,0.1);">
                <i class="bi bi-x-circle-fill" style="color:#DC3545;"></i>
            </div>
            <div>
                <div class="stat-number">{{ $stats['recusadas'] }}</div>
                <div class="stat-label">Recusadas por mim</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(13,110,253,0.1);">
                <i class="bi bi-briefcase-fill" style="color:#0D6EFD;"></i>
            </div>
            <div>
                <div class="stat-number">{{ $stats['total_ativas'] }}</div>
                <div class="stat-label">Vagas ativas no portal</div>
            </div>
        </div>
    </div>
</div>

<!-- @if($stats['aguardando_aut'] > 0)
    <div class="alert" style="border-left:4px solid #FFC107;background:rgba(255,193,7,0.08);border-color:rgba(255,193,7,0.3);display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;">
        <i class="bi bi-bell-fill" style="color:#856404;font-size:1.1rem;flex-shrink:0;"></i>
        <span style="font-size:0.9rem;">
            Há <strong>{{ $stats['aguardando_aut'] }} {{ $stats['aguardando_aut'] === 1 ? 'vaga' : 'vagas' }}</strong>
            aguardando sua análise.
            <a href="{{ route('gestor.vagas.index') }}" style="color:#0D9571;font-weight:600;margin-left:0.4rem;">
                Analisar agora <i class="bi bi-arrow-right"></i>
            </a>
        </span>
    </div>
@endif -->

<div class="card-interno p-0" style="overflow:hidden;">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-hourglass-split me-2"></i>Vagas pendentes de análise</span>
        <a href="{{ route('gestor.vagas.index') }}" style="color:rgba(255,255,255,0.8);font-size:0.8rem;">
            Ver todas <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    @forelse($vagasPendentes as $vaga)
        <div style="padding:1rem 1.25rem;border-bottom:1px solid #F1F5F4;display:flex;align-items:center;justify-content:space-between;gap:1rem;">
            <div style="flex:1;min-width:0;">
                <div style="font-size:0.875rem;font-weight:600;color:#2C4A44;">{{ $vaga->titulo }}</div>
                <div style="font-size:0.75rem;color:#6C757D;margin-top:2px;">
                    {{ $vaga->coordenador?->name ?? 'Coordenador' }}
                    &nbsp;·&nbsp; {{ $vaga->projeto_nome }}
                    &nbsp;·&nbsp; Encerra {{ $vaga->data_encerramento->format('d/m/Y') }}
                </div>
            </div>
            <a href="{{ route('gestor.vagas.show', $vaga) }}"
               style="display:inline-flex;align-items:center;gap:0.35rem;padding:6px 16px;background:#0D9571;color:#fff;font-size:0.8rem;font-weight:600;border-radius:20px;white-space:nowrap;transition:all 0.3s;">
                Analisar <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    @empty
        <div class="text-center py-4">
            <i class="bi bi-check2-all" style="font-size:2.5rem;color:#0D9571;opacity:0.4;"></i>
            <p style="color:#6C757D;margin:0.5rem 0 0;font-size:0.875rem;">Nenhuma vaga aguardando análise.</p>
        </div>
    @endforelse
</div>

@endsection
