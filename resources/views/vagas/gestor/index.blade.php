@extends('layouts.interno')

@section('title', 'Autorizar Vagas')
@section('page-title', 'Autorizar Vagas')
@section('breadcrumb', 'Gestor / Vagas')

@section('content')

{{-- Tabs --}}
<div class="d-flex gap-2 mb-4 flex-wrap">
    @foreach(['aguardando_autorizacao' => 'Aguardando', 'ativa' => 'Autorizadas', 'recusada' => 'Recusadas'] as $val => $label)
        <a href="{{ route('gestor.vagas.index', ['status' => $val]) }}"
           style="padding:7px 18px;border-radius:20px;font-size:0.85rem;font-weight:600;transition:all 0.2s;
                  {{ $status === $val
                      ? 'background:#0D9571;color:#fff;box-shadow:0 2px 8px rgba(13,149,113,0.3);'
                      : 'background:#fff;color:#6C757D;border:1px solid #E9ECEF;' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

<div class="card-interno p-0" style="overflow:hidden;">
    <table class="table table-vagas mb-0">
        <thead>
            <tr>
                <th>Vaga</th>
                <th>Coordenador</th>
                <th>Tipo / Área</th>
                <th>Encerramento</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vagas as $vaga)
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:0.875rem;color:#2C4A44;">{{ $vaga->titulo }}</div>
                        @if($vaga->projeto_nome)
                            <div style="font-size:0.75rem;color:#6C757D;">{{ $vaga->projeto_nome }}</div>
                        @endif
                    </td>
                    <td style="font-size:0.82rem;color:#3E3E3F;">{{ $vaga->coordenador?->name ?? '—' }}</td>
                    <td>
                        <div style="font-size:0.82rem;">{{ $vaga->tipo_label }}</div>
                        <div style="font-size:0.75rem;color:#6C757D;">{{ $vaga->area }}</div>
                    </td>
                    <td style="font-size:0.82rem;color:#6C757D;">{{ $vaga->data_encerramento->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('gestor.vagas.show', $vaga) }}"
                           style="display:inline-flex;align-items:center;gap:0.35rem;padding:5px 14px;background:#0D9571;color:#fff;font-size:0.78rem;font-weight:600;border-radius:20px;">
                            <i class="bi bi-eye"></i> Ver
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <i class="bi bi-briefcase" style="font-size:2.5rem;color:#CACACA;"></i>
                        <p style="color:#6C757D;margin:0.5rem 0 0;font-size:0.875rem;">Nenhuma vaga nesta categoria.</p>
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
