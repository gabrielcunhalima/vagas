@extends('layouts.publico')

@section('title', 'Minhas Candidaturas — Portal de Vagas FAPEU')

@push('styles')
<style>
.card-interno { background:#fff; border-radius:14px; box-shadow:0 2px 10px rgba(0,0,0,0.06); }
.step-bar { display:flex; align-items:center; gap:0; }
.step { display:flex; flex-direction:column; align-items:center; gap:4px; flex:1; position:relative; }
.step:not(:last-child)::after { content:''; position:absolute; top:14px; left:50%; width:100%; height:2px; background:#dee2e6; z-index:0; }
.step.ativo:not(:last-child)::after { background:#0D9571; }
.step-dot { width:28px; height:28px; border-radius:50%; border:2px solid #dee2e6; background:#fff; z-index:1; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:700; color:#adb5bd; transition:all 0.3s; }
.step.ativo .step-dot { border-color:#0D9571; background:#0D9571; color:#fff; }
.step.reprovado .step-dot { border-color:#DC3545; background:#DC3545; color:#fff; }
.step-label { font-size:0.65rem; font-weight:600; color:#adb5bd; text-align:center; line-height:1.2; }
.step.ativo .step-label { color:#0D9571; }
.step.reprovado .step-label { color:#DC3545; }
</style>
@endpush

@section('content')

<div style="background:linear-gradient(135deg,#074635 0%,#0D9571 100%);padding:2rem 0 1.75rem;">
    <div class="container-xl">
        <h1 style="font-size:1.5rem;font-weight:800;color:#fff;margin:0 0 0.2rem;">Minhas candidaturas</h1>
        <p style="color:rgba(255,255,255,0.78);font-size:0.875rem;margin:0;">
            {{ $candidaturas->count() }} candidatura{{ $candidaturas->count() !== 1 ? 's' : '' }} encontrada{{ $candidaturas->count() !== 1 ? 's' : '' }}
        </p>
    </div>
</div>

<div class="container-xl py-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" style="border-radius:10px;font-size:0.875rem;">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show mb-4" style="border-radius:10px;font-size:0.875rem;">
        <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($candidaturas->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-file-earmark-x" style="font-size:3rem;color:#dee2e6;display:block;margin-bottom:1rem;"></i>
        <h4 style="color:#6c757d;font-weight:600;">Nenhuma candidatura ainda</h4>
        <p style="color:#adb5bd;font-size:0.9rem;">Candidate-se a uma vaga e acompanhe o processo aqui.</p>
        <a href="{{ route('vagas.publicas.index') }}" class="btn btn-principal mt-2">
            <i class="bi bi-briefcase me-2"></i> Procurar vagas
        </a>
    </div>
    @else

    <div class="row g-3">
        @foreach($candidaturas as $candidatura)
        @php
            $vaga = $candidatura->vaga;
            $passo = $candidatura->passo_progresso;
            $steps = [
                1 => 'Recebida',
                2 => 'Em análise',
                3 => 'Entrevista',
                4 => $candidatura->status === 'reprovado' ? 'Reprovado' : 'Aprovado',
                5 => 'Aprovado',
            ];
        @endphp
        <div class="col-12">
            <div class="card-interno p-4">
                <div class="row g-3 align-items-start">
                    {{-- Info da vaga --}}
                    <div class="col-lg-5">
                        <div class="d-flex align-items-start gap-3">
                            <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,#074635,#0D9571);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-briefcase-fill" style="color:#fff;font-size:1rem;"></i>
                            </div>
                            <div>
                                <a href="{{ $vaga ? route('vagas.publicas.show', $vaga) : '#' }}"
                                    style="font-weight:700;color:#2C4A44;font-size:0.95rem;display:block;line-height:1.3;">
                                    {{ $vaga->titulo ?? 'Vaga removida' }}
                                </a>
                                @if($vaga)
                                <div style="font-size:0.78rem;color:#6c757d;margin-top:3px;display:flex;flex-wrap:wrap;gap:0.4rem;align-items:center;">
                                    @if($vaga->cidade)
                                    <span><i class="bi bi-geo-alt"></i> {{ $vaga->cidade }}/{{ $vaga->estado }}</span>
                                    @endif
                                    <span class="badge-tipo badge-{{ $vaga->tipo }}" style="padding:2px 8px;border-radius:10px;font-size:0.7rem;font-weight:600;">
                                        {{ $vaga->tipo_label }}
                                    </span>
                                </div>
                                @endif
                                <div style="font-size:0.75rem;color:#adb5bd;margin-top:4px;">
                                    Candidatura enviada em {{ $candidatura->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Progress bar --}}
                    <div class="col-lg-5">
                        @if($candidatura->status === 'reprovado')
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge bg-danger" style="font-size:0.75rem;border-radius:20px;padding:5px 12px;">
                                <i class="bi bi-x-circle me-1"></i>Não aprovado
                            </span>
                            <span style="font-size:0.75rem;color:#6c757d;">Processo encerrado</span>
                        </div>
                        @elseif($candidatura->status === 'aprovado')
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge bg-success" style="font-size:0.75rem;border-radius:20px;padding:5px 12px;">
                                <i class="bi bi-check-circle me-1"></i>Aprovado!
                            </span>
                        </div>
                        @else
                        <div class="step-bar mt-1">
                            @php
                            $passosNomes = ['Recebida', 'Em análise', 'Entrevista', 'Aprovado'];
                            $passosIds   = [1, 2, 3, 4];
                            @endphp
                            @foreach($passosNomes as $i => $nome)
                            <div class="step {{ $passo > $passosIds[$i] || ($passo === $passosIds[$i]) ? 'ativo' : '' }}">
                                <div class="step-dot">
                                    @if($passo > $passosIds[$i])
                                    <i class="bi bi-check" style="font-size:0.75rem;"></i>
                                    @else
                                    {{ $passosIds[$i] }}
                                    @endif
                                </div>
                                <div class="step-label">{{ $nome }}</div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Aviso de entrevista --}}
                        @if($candidatura->status === 'entrevista' && $candidatura->entrevista_data)
                        <div class="mt-2 p-2" style="background:rgba(13,110,253,0.06);border-radius:8px;font-size:0.78rem;">
                            <i class="bi bi-calendar-event text-primary me-1"></i>
                            <strong>Entrevista:</strong>
                            {{ $candidatura->entrevista_data->format('d/m/Y \à\s H:i') }}
                            @if($candidatura->entrevista_local)
                            — {{ $candidatura->entrevista_local }}
                            @endif
                        </div>
                        @endif
                    </div>

                    {{-- Ação --}}
                    <div class="col-lg-2 text-lg-end">
                        <a href="{{ route('candidato.candidaturas.show', $candidatura) }}"
                            class="btn btn-outline-principal btn-sm" style="padding:6px 16px;font-size:0.8rem;">
                            <i class="bi bi-eye me-1"></i>Detalhes
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="text-center mt-5">
        <a href="{{ route('vagas.publicas.index') }}" class="btn btn-principal">
            <i class="bi bi-briefcase me-2"></i> Procurar mais vagas
        </a>
    </div>

    @endif
</div>

@endsection
