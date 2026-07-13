@extends('layouts.interno')

@section('title', 'Todas as Candidaturas')
@section('page-title', 'Candidaturas')
@section('breadcrumb', 'Coordenador / Candidaturas')

@section('content')

{{-- Tabs de status --}}
@php
    $statusTabsLabels = [
        ''           => ['label' => 'Todas',       'count' => $contadores['todos']],
        'recebida'   => ['label' => 'Recebidas',   'count' => $contadores['recebida']],
        'em_analise' => ['label' => 'Em análise',  'count' => $contadores['em_analise']],
        'entrevista' => ['label' => 'Entrevista',  'count' => $contadores['entrevista']],
        'aprovado'   => ['label' => 'Aprovados',   'count' => $contadores['aprovado']],
        'reprovado'  => ['label' => 'Reprovados',  'count' => $contadores['reprovado']],
    ];
    $statusAtual = request('status', '');
@endphp

<div class="mb-3 d-flex flex-wrap gap-2">
    @foreach($statusTabsLabels as $val => $info)
        <a href="{{ route('coord.candidaturas.todas', array_merge(request()->except('status', 'page'), ['status' => $val])) }}"
           style="display:inline-flex;align-items:center;gap:0.4rem;padding:6px 14px;border-radius:20px;font-size:0.82rem;font-weight:600;transition:all 0.2s;
                  {{ $statusAtual === $val
                      ? 'background:#0D9571;color:#fff;box-shadow:0 2px 8px rgba(13,149,113,0.3);'
                      : 'background:#fff;color:#6C757D;border:1px solid #E9ECEF;' }}">
            {{ $info['label'] }}
            <span style="background:{{ $statusAtual === $val ? 'rgba(255,255,255,0.25)' : '#F1F5F4' }};color:{{ $statusAtual === $val ? '#fff' : '#3E3E3F' }};padding:1px 7px;border-radius:10px;font-size:0.72rem;">
                {{ $info['count'] }}
            </span>
        </a>
    @endforeach
</div>

{{-- Filtros --}}
<div class="card-interno p-3 mb-3">
    <form method="GET" action="{{ route('coord.candidaturas.todas') }}" class="d-flex flex-wrap gap-2 align-items-end">
        <input type="hidden" name="status" value="{{ request('status') }}">

        <div style="flex:1;min-width:200px;">
            <input type="text" name="busca" class="form-control form-control-sm"
                   placeholder="Nome, e-mail ou CPF…" value="{{ request('busca') }}">
        </div>

        <div style="min-width:200px;">
            <select name="vaga_id" class="form-select form-select-sm">
                <option value="">Todas as vagas</option>
                @foreach($vagas as $v)
                    <option value="{{ $v->id }}" {{ request('vaga_id') == $v->id ? 'selected' : '' }}>
                        {{ $v->titulo }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-sm btn-principal">
            <i class="bi bi-search me-1"></i> Filtrar
        </button>

        @if(request()->hasAny(['busca', 'vaga_id']))
            <a href="{{ route('coord.candidaturas.todas', array_filter(['status' => request('status')])) }}"
               class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x"></i> Limpar
            </a>
        @endif
    </form>
</div>

{{-- Tabela --}}
<div class="card-interno p-0" style="overflow:hidden;">
    <table class="table table-vagas mb-0">
        <thead>
            <tr>
                <th>Candidato</th>
                <th>Vaga / Projeto</th>
                <th>Curso / Instituição</th>
                <th>Inscrição</th>
                <th>Status</th>
                <th style="width:90px;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($candidaturas as $cand)
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:0.875rem;color:#2C4A44;">{{ $cand->nome }}</div>
                        <div style="font-size:0.75rem;color:#6C757D;">{{ $cand->email }}</div>
                    </td>
                    <td>
                        <div style="font-size:0.82rem;font-weight:600;color:#2C4A44;">{{ $cand->vaga->titulo }}</div>
                        @if($cand->vaga->projeto_nome)
                            <div style="font-size:0.75rem;color:#6C757D;">{{ $cand->vaga->projeto_nome }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-size:0.82rem;color:#3E3E3F;">{{ $cand->curso }}</div>
                        <div style="font-size:0.75rem;color:#6C757D;">{{ $cand->instituicao }}</div>
                    </td>
                    <td style="font-size:0.82rem;color:#6C757D;white-space:nowrap;">
                        {{ $cand->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td>
                        <span class="status-badge status-{{ $cand->status }}">{{ $cand->status_label }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('coord.candidaturas.show', [$cand->vaga, $cand]) }}"
                               class="btn btn-sm btn-outline-principal" title="Ver detalhes" style="padding:3px 8px;">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($cand->temCurriculo())
                                <a href="{{ route('coord.candidaturas.curriculo', [$cand->vaga, $cand]) }}"
                                   class="btn btn-sm btn-outline-secondary" title="Download currículo" style="padding:3px 8px;">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <i class="bi bi-people" style="font-size:2.5rem;color:#CACACA;"></i>
                        <p style="color:#6C757D;margin:0.5rem 0 0;font-size:0.875rem;">Nenhuma candidatura encontrada.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($candidaturas->hasPages())
    <div class="d-flex justify-content-end mt-3">{{ $candidaturas->links() }}</div>
@endif

@endsection
