@extends('layouts.interno')

@section('title', 'Minhas Vagas')
@section('page-title', 'Minhas Vagas')
@section('breadcrumb', 'Coordenador / Vagas')

@section('topbar-actions')
    <a href="{{ route('coord.vagas.create') }}" class="btn btn-principal btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Nova Vaga
    </a>
@endsection

@section('content')

{{-- Filtros --}}
<div class="card-interno p-3 mb-4">
    <form action="{{ route('coord.vagas.index') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-12 col-lg-12">
            <label class="form-label small text-muted mb-1">Busca</label>
            <input type="text" name="busca" class="form-control form-control-sm"
                   placeholder="Buscar por título…" value="{{ request('busca') }}">
        </div>

        <div class="col-6 col-md-3 col-lg-2">
            <label class="form-label small text-muted mb-1">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">Todos</option>
                @foreach(\App\Models\Vagas\Vaga::$statusLabel as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
            <label class="form-label small text-muted mb-1">Área</label>
            <select name="area" class="form-select form-select-sm">
                <option value="">Todas</option>
                @foreach($areas as $area)
                    <option value="{{ $area }}" {{ request('area') === $area ? 'selected' : '' }}>{{ $area }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
            <label class="form-label small text-muted mb-1">Tipo</label>
            <select name="tipo" class="form-select form-select-sm">
                <option value="">Todos</option>
                @foreach(\App\Models\Vagas\Vaga::$tiposLabel as $val => $label)
                    <option value="{{ $val }}" {{ request('tipo') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
            <label class="form-label small text-muted mb-1">Encerramento de</label>
            <input type="date" name="encerramento_de" class="form-control form-control-sm"
                   value="{{ request('encerramento_de') }}">
        </div>

        <div class="col-6 col-md-3 col-lg-2">
            <label class="form-label small text-muted mb-1">Encerramento até</label>
            <input type="date" name="encerramento_ate" class="form-control form-control-sm"
                   value="{{ request('encerramento_ate') }}">
        </div>

        <div class="col-12 col-lg-auto d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-principal">
                <i class="bi bi-search me-1"></i> Filtrar
            </button>
            @if(request()->hasAny(['busca','status','area','tipo','encerramento_de','encerramento_ate']))
                <a href="{{ route('coord.vagas.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x"></i> Limpar
                </a>
            @endif
        </div>
    </form>
</div>

<div class="card-interno p-0" style="overflow:hidden;">
    <table class="table table-vagas mb-0">
        <thead>
            <tr>
                <th>Vaga</th>
                <th>Área / Tipo</th>
                <th>Encerramento</th>
                <th>Candidatos</th>
                <th>Status</th>
                <th style="width:50px;" title="E-mail de candidaturas">Notificação</th>
                <th style="width:120px;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vagas as $vaga)
                <tr>
                    <td>
                        <div style="font-weight:600;color:#2C4A44;font-size:0.875rem;">{{ $vaga->titulo }}</div>
                        @if($vaga->projeto_nome)
                            <div style="font-size:0.75rem;color:#6C757D;">{{ $vaga->projeto_nome }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-size:0.8rem;color:#3E3E3F;">{{ $vaga->area }}</div>
                        <div style="font-size:0.75rem;color:#6C757D;">{{ $vaga->tipo_label }}</div>
                    </td>
                    <td>
                        <div style="font-size:0.82rem;color:{{ $vaga->dias_restantes <= 3 && $vaga->status === 'ativa' ? '#DC3545' : '#3E3E3F' }};">
                            {{ $vaga->data_encerramento->format('d/m/Y') }}
                        </div>
                        @if($vaga->status === 'ativa')
                            <div style="font-size:0.72rem;color:#6C757D;">{{ $vaga->dias_restantes }} dias</div>
                        @endif
                    </td>
                    <td>
                        @if($vaga->status === 'ativa' || $vaga->candidaturas()->count() > 0)
                            <a href="{{ route('coord.candidaturas.index', $vaga) }}"
                               style="font-size:0.875rem;font-weight:600;color:#0D9571;display:inline-flex;align-items:center;gap:0.3rem;">
                                <i class="bi bi-people-fill"></i>
                                {{ $vaga->candidaturas()->count() }}
                            </a>
                        @else
                            <span style="font-size:0.82rem;color:#CACACA;">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge status-{{ $vaga->status }}">{{ $vaga->status_label }}</span>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('coord.vagas.notificacao', $vaga) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    title="{{ $vaga->notificar_email ? 'Desativar notificações' : 'Ativar notificações' }}"
                                    style="background:none;border:none;padding:2px 4px;font-size:1rem;cursor:pointer;color:{{ $vaga->notificar_email ? '#0D9571' : '#CACACA' }};">
                                <i class="bi bi-bell{{ $vaga->notificar_email ? '-fill' : '' }}"></i>
                            </button>
                        </form>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            @if(in_array($vaga->status, ['rascunho', 'recusada']))
                                <a href="{{ route('coord.vagas.edit', $vaga) }}"
                                   class="btn btn-sm btn-outline-secondary" title="Editar" style="padding:3px 8px;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($vaga->status === 'rascunho')
                                    <form method="POST" action="{{ route('coord.vagas.submeter', $vaga) }}" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-principal" title="Enviar para autorização" style="padding:3px 8px;">
                                            <i class="bi bi-send-fill"></i>
                                        </button>
                                    </form>
                                @endif
                            @endif
                            @if($vaga->status === 'ativa')
                                <a href="{{ route('coord.candidaturas.index', $vaga) }}"
                                   class="btn btn-sm btn-outline-principal" title="Ver candidatos" style="padding:3px 8px;">
                                    <i class="bi bi-people"></i>
                                </a>
                                <form method="POST" action="{{ route('coord.vagas.desativar', $vaga) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            title="Desativar"
                                            style="padding:3px 8px;"
                                            onclick="return confirm('Desativar esta vaga?')">
                                        <i class="bi bi-pause-fill"></i>
                                    </button>
                                </form>
                            @endif
                            @if($vaga->status === 'inativa' && !($vaga->data_encerramento && $vaga->data_encerramento->isPast()))
                                <form method="POST" action="{{ route('coord.vagas.reativar', $vaga) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-principal"
                                            title="Reativar vaga"
                                            style="padding:3px 8px;"
                                            onclick="return confirm('Reativar esta vaga?')">
                                        <i class="bi bi-play-fill"></i>
                                    </button>
                                </form>
                            @endif
                            @if(in_array($vaga->status, ['rascunho', 'recusada', 'inativa']))
                                <form method="POST" action="{{ route('coord.vagas.destroy', $vaga) }}" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            title="Excluir"
                                            style="padding:3px 8px;"
                                            onclick="return confirm('Excluir definitivamente esta vaga?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <i class="bi bi-briefcase" style="font-size:2.5rem;color:#CACACA;"></i>
                        <p style="color:#6C757D;margin:0.5rem 0 0;">Nenhuma vaga encontrada.</p>
                        <a href="{{ route('coord.vagas.create') }}" class="btn btn-principal btn-sm mt-2">
                            <i class="bi bi-plus-circle me-1"></i> Criar vaga
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($vagas->hasPages())
    <div class="d-flex justify-content-center mt-3">{{ $vagas->links() }}</div>
@endif

@endsection
