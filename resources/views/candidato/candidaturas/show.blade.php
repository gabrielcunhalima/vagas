@extends('layouts.publico')

@section('title', 'Detalhes da Candidatura — ' . ($candidatura->vaga->titulo ?? 'Vaga'))

@push('styles')
<style>
.card-interno { background:#fff; border-radius:14px; box-shadow:0 2px 10px rgba(0,0,0,0.06); }
.info-row { display:flex; gap:0.5rem; align-items:flex-start; padding:0.6rem 0; border-bottom:1px solid #f1f5f4; font-size:0.875rem; }
.info-row:last-child { border-bottom:none; }
.info-label { color:#6c757d; font-weight:600; min-width:160px; flex-shrink:0; }
.info-val { color:#2C4A44; }
.step-bar { display:flex; align-items:center; }
.step { display:flex; flex-direction:column; align-items:center; gap:6px; flex:1; position:relative; }
.step:not(:last-child)::after { content:''; position:absolute; top:18px; left:50%; width:100%; height:2px; background:#dee2e6; z-index:0; }
.step.ativo:not(:last-child)::after { background:#0D9571; }
.step-dot { width:36px; height:36px; border-radius:50%; border:2px solid #dee2e6; background:#fff; z-index:1; display:flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:700; color:#adb5bd; transition:all 0.3s; }
.step.ativo .step-dot { border-color:#0D9571; background:#0D9571; color:#fff; }
.step.reprovado .step-dot { border-color:#DC3545; background:#DC3545; color:#fff; }
.step-label { font-size:0.7rem; font-weight:600; color:#adb5bd; text-align:center; }
.step.ativo .step-label { color:#0D9571; }
.step.reprovado .step-label { color:#DC3545; }
</style>
@endpush

@section('content')

<div style="background:linear-gradient(135deg,#074635 0%,#0D9571 100%);padding:2rem 0 1.75rem;">
    <div class="container-xl">
        <a href="{{ route('candidato.candidaturas.index') }}"
            style="color:rgba(255,255,255,0.7);font-size:0.85rem;display:inline-flex;align-items:center;gap:0.4rem;margin-bottom:0.75rem;">
            <i class="bi bi-arrow-left"></i> Minhas candidaturas
        </a>
        <h1 style="font-size:1.5rem;font-weight:800;color:#fff;margin:0 0 0.25rem;">
            {{ $candidatura->vaga->titulo ?? 'Candidatura' }}
        </h1>
        <p style="color:rgba(255,255,255,0.78);font-size:0.9rem;margin:0;">
            Candidatura enviada em {{ $candidatura->created_at->format('d/m/Y \à\s H:i') }}
        </p>
    </div>
</div>

<div class="container-xl py-4">
    <div class="row g-4">

        {{-- Coluna principal --}}
        <div class="col-lg-8">

            {{-- Status e progress --}}
            <div class="card-interno p-4 mb-4">
                <h6 style="font-weight:700;color:#2C4A44;margin-bottom:1.5rem;font-size:0.95rem;">
                    Status da candidatura
                </h6>

                @if($candidatura->status === 'reprovado')
                <div class="text-center py-2">
                    <div style="width:60px;height:60px;border-radius:50%;background:#DC3545;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                        <i class="bi bi-x-lg" style="color:#fff;font-size:1.5rem;"></i>
                    </div>
                    <div style="font-weight:700;color:#DC3545;font-size:1rem;">Não selecionado</div>
                    <div style="font-size:0.875rem;color:#6c757d;margin-top:0.25rem;">Obrigado pela sua candidatura.</div>
                </div>
                @elseif($candidatura->status === 'aprovado')
                <div class="text-center py-2">
                    <div style="width:60px;height:60px;border-radius:50%;background:#0D9571;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                        <i class="bi bi-check-lg" style="color:#fff;font-size:1.5rem;"></i>
                    </div>
                    <div style="font-weight:700;color:#0D9571;font-size:1rem;">Aprovado!</div>
                    <div style="font-size:0.875rem;color:#6c757d;margin-top:0.25rem;">Parabéns! Aguarde o contato do coordenador.</div>
                </div>
                @else
                <div class="step-bar mb-3">
                    @php
                    $passosNomes = ['Recebida', 'Em análise', 'Entrevista', 'Aprovado'];
                    $passosIds   = [1, 2, 3, 4];
                    $passo       = $candidatura->passo_progresso;
                    @endphp
                    @foreach($passosNomes as $i => $nome)
                    <div class="step {{ $passo >= $passosIds[$i] ? 'ativo' : '' }}">
                        <div class="step-dot">
                            @if($passo > $passosIds[$i])
                            <i class="bi bi-check" style="font-size:0.85rem;"></i>
                            @else
                            {{ $passosIds[$i] }}
                            @endif
                        </div>
                        <div class="step-label">{{ $nome }}</div>
                    </div>
                    @endforeach
                </div>
                <div class="text-center" style="font-size:0.85rem;color:#6c757d;">
                    Status atual:
                    <span class="badge bg-{{ $candidatura->status_cor }}" style="border-radius:20px;font-size:0.8rem;padding:4px 12px;">
                        {{ $candidatura->status_label }}
                    </span>
                </div>
                @endif

                {{-- Detalhes de entrevista --}}
                @if($candidatura->status === 'entrevista' && $candidatura->entrevista_data)
                <div class="mt-3 p-3" style="background:rgba(13,110,253,0.06);border-radius:10px;border:1.5px solid rgba(13,110,253,0.15);">
                    <div style="font-weight:700;color:#0D6EFD;font-size:0.875rem;margin-bottom:0.5rem;">
                        <i class="bi bi-calendar-event me-2"></i>Entrevista agendada
                    </div>
                    <div style="font-size:0.875rem;color:#495057;">
                        <strong>Data:</strong> {{ $candidatura->entrevista_data->format('d/m/Y \à\s H:i') }}<br>
                        @if($candidatura->entrevista_local)
                        <strong>Local:</strong> {{ $candidatura->entrevista_local }}<br>
                        @endif
                        @if($candidatura->entrevista_observacoes)
                        <strong>Obs:</strong> {{ $candidatura->entrevista_observacoes }}
                        @endif
                    </div>
                </div>
                @endif
            </div>

            {{-- Dados da candidatura --}}
            <div class="card-interno p-4 mb-4">
                <h6 style="font-weight:700;color:#2C4A44;margin-bottom:1rem;font-size:0.95rem;">
                    <i class="bi bi-person-fill text-principal me-2"></i>Dados enviados
                </h6>
                <div class="info-row"><span class="info-label">Nome</span><span class="info-val">{{ $candidatura->nome }}</span></div>
                <div class="info-row"><span class="info-label">E-mail</span><span class="info-val">{{ $candidatura->email }}</span></div>
                <div class="info-row"><span class="info-label">CPF</span><span class="info-val">{{ $candidatura->cpf_formatado }}</span></div>
                @if($candidatura->telefone)
                <div class="info-row"><span class="info-label">Telefone</span><span class="info-val">{{ $candidatura->telefone }}</span></div>
                @endif
                @if($candidatura->linkedin)
                <div class="info-row">
                    <span class="info-label">LinkedIn</span>
                    <span class="info-val"><a href="{{ $candidatura->linkedin }}" target="_blank" style="color:#0D9571;">{{ $candidatura->linkedin }}</a></span>
                </div>
                @endif
                <div class="info-row"><span class="info-label">Curso</span><span class="info-val">{{ $candidatura->curso }}</span></div>
                <div class="info-row"><span class="info-label">Instituição</span><span class="info-val">{{ $candidatura->instituicao }}</span></div>
                @if($candidatura->semestre)
                <div class="info-row"><span class="info-label">Semestre</span><span class="info-val">{{ $candidatura->semestre }}</span></div>
                @endif
                @if($candidatura->previsao_conclusao)
                <div class="info-row"><span class="info-label">Prev. conclusão</span><span class="info-val">{{ $candidatura->previsao_conclusao->format('m/Y') }}</span></div>
                @endif
                @if($candidatura->disponibilidade)
                <div class="info-row"><span class="info-label">Disponibilidade</span><span class="info-val">{{ $candidatura->disponibilidade }}</span></div>
                @endif
                @if($candidatura->pretensao_salarial)
                <div class="info-row"><span class="info-label">Pretensão salarial</span><span class="info-val">R$ {{ number_format($candidatura->pretensao_salarial, 2, ',', '.') }}</span></div>
                @endif
                <div class="info-row">
                    <span class="info-label">PCD</span>
                    <span class="info-val">
                        {{ $candidatura->pcd ? 'Sim' : 'Não' }}
                        @if($candidatura->pcd && $candidatura->pcd_tipo) — {{ $candidatura->pcd_tipo }}@endif
                    </span>
                </div>

                @if($candidatura->carta_apresentacao)
                <div class="mt-3">
                    <div style="font-weight:600;color:#6c757d;font-size:0.8rem;margin-bottom:0.5rem;">CARTA DE APRESENTAÇÃO</div>
                    <div style="font-size:0.875rem;color:#495057;line-height:1.6;white-space:pre-wrap;background:#f8f9fa;padding:1rem;border-radius:8px;">{{ $candidatura->carta_apresentacao }}</div>
                </div>
                @endif
            </div>

        </div>

        {{-- Coluna lateral --}}
        <div class="col-lg-4">

            {{-- Info da vaga --}}
            @if($candidatura->vaga)
            <div class="card-interno p-4 mb-4">
                <h6 style="font-weight:700;color:#2C4A44;margin-bottom:1rem;font-size:0.9rem;">
                    <i class="bi bi-briefcase text-principal me-2"></i>Sobre a vaga
                </h6>
                <div style="font-weight:700;color:#2C4A44;font-size:0.9rem;margin-bottom:0.5rem;">
                    {{ $candidatura->vaga->titulo }}
                </div>
                @if($candidatura->vaga->cidade)
                <div style="font-size:0.8rem;color:#6c757d;margin-bottom:0.4rem;">
                    <i class="bi bi-geo-alt me-1"></i>{{ $candidatura->vaga->cidade }}/{{ $candidatura->vaga->estado }}
                </div>
                @endif
                <div style="font-size:0.8rem;color:#6c757d;margin-bottom:0.75rem;">
                    <i class="bi bi-calendar3 me-1"></i>Encerra em {{ $candidatura->vaga->data_encerramento->format('d/m/Y') }}
                </div>
                @if($candidatura->vaga->esta_aberta)
                <a href="{{ route('vagas.publicas.show', $candidatura->vaga) }}"
                    class="btn btn-outline-principal w-100 btn-sm">
                    <i class="bi bi-eye me-1"></i>Ver vaga
                </a>
                @else
                <span class="badge bg-secondary w-100 py-2" style="border-radius:8px;font-size:0.8rem;">Vaga encerrada</span>
                @endif
            </div>
            @endif

            {{-- Currículo --}}
            @if($candidatura->temCurriculo())
            <div class="card-interno p-4 mb-4">
                <h6 style="font-weight:700;color:#2C4A44;margin-bottom:1rem;font-size:0.9rem;">
                    <i class="bi bi-file-earmark-person text-principal me-2"></i>Currículo enviado
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-4"></i>
                    <div class="flex-grow-1" style="min-width:0;">
                        <div style="font-size:0.8rem;font-weight:600;color:#2C4A44;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $candidatura->curriculo_nome_original }}
                        </div>
                    </div>
                    <a href="{{ route('candidato.candidaturas.curriculo', $candidatura) }}"
                        class="btn btn-sm btn-outline-principal" style="padding:4px 10px;font-size:0.78rem;flex-shrink:0;">
                        <i class="bi bi-download"></i>
                    </a>
                </div>
            </div>
            @endif

            {{-- Aviso LGPD --}}
            <div class="card-interno p-4" style="border:1px solid rgba(13,149,113,0.15);">
                <div style="font-size:0.78rem;color:#6c757d;line-height:1.5;">
                    <i class="bi bi-shield-check text-principal me-1"></i>
                    Seus dados estão protegidos pela <strong>LGPD</strong> e são acessados apenas pelos
                    coordenadores do processo seletivo desta vaga.
                    <a href="{{ route('candidato.perfil.edit') }}" style="color:#0D9571;">Gerenciar dados</a>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
