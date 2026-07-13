@extends('layouts.interno')

@section('title', 'Candidato — ' . $candidatura->nome)
<!-- @section('page-title', $candidatura->nome) -->
@section('breadcrumb', 'Coordenador / Vagas / Candidatos / Detalhe')

@section('content')

<div class="row g-4">

    <div class="col-lg-8">

        <div class="card-interno mb-4" style="padding:1.5rem;">
            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div class="d-flex align-items-center gap-3">
                    <!-- <div style="width:52px;height:52px;border-radius:50%;background:#0D9571;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:700;flex-shrink:0;">
                        {{ strtoupper(substr($candidatura->nome, 0, 2)) }}
                    </div> -->
                    <div class="col-sm-4">
                        <h5 style="margin:0 0 3px;font-weight:700;color:#2C4A44;">{{ $candidatura->nome }}</h5>
                    </div>
                </div>
                <span class="status-badge status-{{ $candidatura->status }} fs-6">{{ $candidatura->status_label }}</span>
            </div>

            <div class="row g-3">
                <div class="col-sm-4">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">CPF</div>
                    <div style="font-size:0.875rem;color:#3E3E3F;">{{ $candidatura->cpf_formatado }}</div>
                </div>
                @if($candidatura->telefone)
                <div class="col-sm-4">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Telefone</div>
                    <div style="font-size:0.875rem;color:#3E3E3F;">{{ $candidatura->telefone }}</div>
                </div>
                @endif
                @if($candidatura->email)
                <div class="col-sm-4">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Email</div>
                    <div style="font-size:0.875rem;color:#3E3E3F;">{{ $candidatura->email }}</div>
                </div>
                @endif
                <div class="col-sm-4">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Inscrito em</div>
                    <div style="font-size:0.875rem;color:#3E3E3F;">{{ $candidatura->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="col-sm-4">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Curso</div>
                    <div style="font-size:0.875rem;color:#3E3E3F;">{{ $candidatura->curso }}</div>
                </div>
                <div class="col-sm-4">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Instituição</div>
                    <div style="font-size:0.875rem;color:#3E3E3F;">{{ $candidatura->instituicao }}</div>
                </div>
                @if($candidatura->semestre)
                <div class="col-sm-4">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Semestre</div>
                    <div style="font-size:0.875rem;color:#3E3E3F;">{{ $candidatura->semestre }}</div>
                </div>
                @endif
                @if($candidatura->previsao_conclusao)
                <div class="col-sm-4">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Conclusão prevista</div>
                    <div style="font-size:0.875rem;color:#3E3E3F;">{{ $candidatura->previsao_conclusao->format('m/Y') }}</div>
                </div>
                @endif
                <div class="col-sm-4">
                    @if($candidatura->temCurriculo())
                    <a href="{{ route('coord.candidaturas.curriculo', [$vaga, $candidatura]) }}"
                        class="btn btn-principal w-100">
                        <i class="bi bi-file-earmark-pdf-fill me-2"></i> Download do currículo
                    </a>
                    @else
                    <p style="font-size:0.82rem;color:#6C757D;margin:0;text-align:center;">Nenhum currículo enviado.</p>
                    @endif
                </div>
            </div>
        </div>

        @if($candidatura->carta_apresentacao)
        <div class="card-interno mb-4" style="padding:1.5rem;">
            <h6 style="font-weight:700;color:#2C4A44;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.5rem;">
                Carta de apresentação
            </h6>
            <div style="font-size:0.875rem;line-height:1.75;color:#3E3E3F;white-space:pre-line;background:#F8F9FA;border-radius:8px;padding:1rem;">{{ $candidatura->carta_apresentacao }}</div>
        </div>
        @endif

        {{-- Dados entrevista (se houver) --}}
        @if($candidatura->entrevista_data)
        <div class="card-interno mb-4" style="padding:1.5rem;">
            <h6 style="font-weight:700;color:#2C4A44;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.5rem;">
                <i class="bi bi-calendar-event-fill text-principal"></i> Dados da entrevista
            </h6>
            <div class="row g-3">
                <div class="col-sm-6">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Data e hora</div>
                    <div style="font-size:0.875rem;font-weight:600;color:#2C4A44;">{{ $candidatura->entrevista_data->format('d/m/Y H:i') }}</div>
                </div>
                <div class="col-sm-6">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Local</div>
                    <div style="font-size:0.875rem;color:#3E3E3F;">{{ $candidatura->entrevista_local }}</div>
                </div>
                @if($candidatura->entrevista_observacoes)
                <div class="col-12">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6C757D;">Observações</div>
                    <div style="font-size:0.875rem;color:#3E3E3F;">{{ $candidatura->entrevista_observacoes }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

    </div>

    <div class="col-lg-4">

        <!-- <div class="card-interno mb-4" style="padding:1.25rem;">
            <h6 style="font-weight:700;color:#2C4A44;margin-bottom:0.75rem;font-size:0.875rem;">Currículo</h6>
            @if($candidatura->temCurriculo())
            <a href="{{ route('coord.candidaturas.curriculo', [$vaga, $candidatura]) }}"
                class="btn btn-principal w-100">
                <i class="bi bi-file-earmark-pdf-fill me-2"></i> Download do currículo
            </a>
            @else
            <p style="font-size:0.82rem;color:#6C757D;margin:0;text-align:center;">Nenhum currículo enviado.</p>
            @endif
        </div> -->

        @php $proximos = \App\Models\Vagas\Candidatura::$proximosStatus[$candidatura->status] ?? []; @endphp
        @if(count($proximos) > 0)
        <div class="card-interno mb-4 p-0" style="overflow:hidden;">
            <div class="card-header" style="font-size:0.85rem;">
                <i class="bi bi-arrow-right-circle me-2"></i> Avançar status
            </div>
            <div style="padding:1.25rem;">

                @if(in_array('entrevista', $proximos))
                <form method="POST" action="{{ route('coord.candidaturas.updateStatus', [$vaga, $candidatura]) }}" class="mb-3">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="entrevista">
                    <div class="mb-2">
                        <label class="form-label" style="font-size:0.8rem;">Data da entrevista <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="entrevista_data" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size:0.8rem;">Local <span class="text-danger">*</span></label>
                        <input type="text" name="entrevista_local" class="form-control form-control-sm" placeholder="Ex: Sala 201 / Google Meet" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size:0.8rem;">Observações</label>
                        <textarea name="entrevista_observacoes" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-sm w-100 btn-principal">
                        <i class="bi bi-calendar-check me-1"></i> Convocar para entrevista
                    </button>
                </form>
                @endif

                @if(in_array('aprovado', $proximos))
                <form method="POST" action="{{ route('coord.candidaturas.updateStatus', [$vaga, $candidatura]) }}" class="mb-2">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="aprovado">
                    <button type="submit" class="btn btn-sm w-100 btn-principal"
                        onclick="return confirm('Aprovar este candidato?')">
                        <i class="bi bi-check-circle me-1"></i> Aprovar
                    </button>
                </form>
                @endif

                @if(in_array('reprovado', $proximos))
                <form method="POST" action="{{ route('coord.candidaturas.updateStatus', [$vaga, $candidatura]) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="reprovado">
                    <button type="submit" class="btn btn-sm w-100 btn-danger"
                        onclick="return confirm('Reprovar este candidato? Um e-mail será enviado.')">
                        <i class="bi bi-x-circle me-1"></i> Reprovar
                    </button>
                </form>
                @endif

            </div>
        </div>
        @endif

        {{-- Obs internas --}}
        <div class="card-interno p-0" style="overflow:hidden;">
            <div class="card-header" style="font-size:0.85rem;">
                <i class="bi bi-sticky-fill me-2"></i> Observações internas
            </div>
            <div style="padding:1.25rem;">
                <form method="POST" action="{{ route('coord.candidaturas.updateStatus', [$vaga, $candidatura]) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="{{ $candidatura->status }}">
                    <textarea name="observacoes_internas" class="form-control form-control-sm mb-2" rows="4"
                        placeholder="Anotações visíveis apenas para coordenadores…">{{ $candidatura->observacoes_internas }}</textarea>
                    <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="bi bi-floppy me-1"></i> Salvar observações
                    </button>
                </form>
            </div>
        </div>

        <a href="{{ route('coord.candidaturas.index', $vaga) }}" class="d-block text-center mt-3"
            style="font-size:0.82rem;color:#6C757D;">
            <i class="bi bi-arrow-left me-1"></i> Voltar à lista
        </a>
    </div>

</div>

@endsection