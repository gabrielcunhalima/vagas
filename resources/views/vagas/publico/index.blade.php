@extends('layouts.publico')

@section('title', 'Vagas Disponíveis')

@section('content')

{{-- Hero --}}
<div style="background:linear-gradient(135deg,#074635 0%,#0D9571 100%);padding:3rem 0 2.5rem;">
    <div class="container-xl">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <!-- <div style="display:inline-flex;align-items:center;gap:0.5rem;background:rgba(255, 255, 255, 0.12);border:1px solid rgba(255,255,255,0.2);color:#fff;padding:5px 14px;border-radius:25px;font-size:0.78rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:1rem;">
                    <i class="bi bi-stars"></i> Oportunidades abertas
                </div> -->
                <h1 style="font-size:2rem;font-weight:800;color:#fff;line-height:1.15;margin-bottom:0.75rem;">
                    Vagas disponíveis para estágio, emprego e bolsas em projetos da FAPEU
                </h1>
                <p style="color:rgba(255,255,255,0.78);font-size:0.95rem;margin:0 0 0.5rem;">
                    <strong style="color:#fff">{{ $total }}</strong> {{ $total === 1 ? 'vaga encontrada' : 'vagas encontradas' }}
                    @if(request()->hasAny(['busca','area','tipo','modalidade','curso','cidade','estado','salario_min','salario_max'])) com os filtros aplicados @endif
                </p>
                <a href="{{ route('alertas.create') }}" style="color:rgba(255,255,255,0.7);font-size:0.82rem;text-decoration:none;">
                    <i class="bi bi-bell me-1"></i> Receber alertas de novas vagas
                </a>
            </div>
            <div class="col-lg-6">
                {{-- Barra de busca --}}
                <form action="{{ route('vagas.publicas.index') }}" method="GET">
                    <div style="background:rgba(255,255,255,0.12);backdrop-filter:blur(10px);border:1.5px solid rgba(255,255,255,0.25);border-radius:50px;padding:6px 6px 6px 18px;display:flex;align-items:center;gap:0.5rem;">
                        <i class="bi bi-search" style="color:rgba(255,255,255,0.7);"></i>
                        <input type="text" name="busca" value="{{ request('busca') }}"
                            placeholder="Cargo, área ou empresa…"
                            style="background:transparent;border:none;outline:none;color:#fff;font-size:0.9rem;flex:1;font-family:'Reddit Sans',sans-serif;">
                        <input type="hidden" name="area" value="{{ request('area') }}">
                        <input type="hidden" name="tipo" value="{{ request('tipo') }}">
                        <input type="hidden" name="modalidade" value="{{ request('modalidade') }}">
                        <input type="hidden" name="curso" value="{{ request('curso') }}">
                        <button type="submit" style="background:#fff;color:#0D9571;border:none;border-radius:50px;padding:8px 20px;font-size:0.85rem;font-weight:700;white-space:nowrap;display:flex;align-items:center;gap:0.4rem;cursor:pointer;">
                            <i class="bi bi-arrow-right"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container-xl py-4">
    <div class="row g-4">

        <div class="col-lg-3">
            <div class="card-interno p-0" style="background:#fff;border-radius:16px;box-shadow:0 4px 15px rgba(0,0,0,0.07);transition:all 0.3s;overflow:hidden;">
                <div style="background:#0D9571;padding:0.9rem 1.25rem;">
                    <span style="color:#fff;font-weight:700;font-size:0.88rem;display:flex;align-items:center;gap:0.5rem;">
                        <i class="bi bi-funnel-fill"></i> Filtros
                    </span>
                </div>
                <form action="{{ route('vagas.publicas.index') }}" method="GET" class="p-3">
                    @if(request('busca'))
                    <input type="hidden" name="busca" value="{{ request('busca') }}">
                    @endif

                    <div class="mb-3">
                        <label class="form-label" style="font-size:0.8rem;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:0.4px;">Área</label>
                        <select name="area" class="form-select form-select-sm">
                            <option value="">Todas as áreas</option>
                            @foreach($areas as $area)
                            <option value="{{ $area }}" {{ request('area') === $area ? 'selected' : '' }}>
                                {{ $area }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:0.8rem;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:0.4px;">Tipo</label>
                        <select name="tipo" class="form-select form-select-sm">
                            <option value="">Todos os tipos</option>
                            <option value="estagio" {{ request('tipo') === 'estagio'  ? 'selected' : '' }}>Estágio</option>
                            <option value="emprego" {{ request('tipo') === 'emprego'  ? 'selected' : '' }}>CLT</option>
                            <option value="bolsa" {{ request('tipo') === 'bolsa'    ? 'selected' : '' }}>Bolsa</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:0.8rem;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:0.4px;">Modalidade</label>
                        <select name="modalidade" class="form-select form-select-sm">
                            <option value="">Todas</option>
                            <option value="presencial" {{ request('modalidade') === 'presencial' ? 'selected' : '' }}>Presencial</option>
                            <option value="remoto" {{ request('modalidade') === 'remoto'     ? 'selected' : '' }}>Remoto</option>
                            <option value="hibrido" {{ request('modalidade') === 'hibrido'    ? 'selected' : '' }}>Híbrido</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:0.8rem;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:0.4px;">Curso</label>
                        <select name="curso" class="form-select form-select-sm">
                            <option value="">Todos os cursos</option>
                            @foreach($cursos as $curso)
                            <option value="{{ $curso }}" {{ request('curso') === $curso ? 'selected' : '' }}>
                                {{ $curso }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:0.8rem;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:0.4px;">Cidade</label>
                        <input type="text" name="cidade" value="{{ request('cidade') }}" class="form-control form-control-sm" placeholder="Ex: Florianópolis">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:0.8rem;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:0.4px;">Remuneração (R$)</label>
                        <div class="d-flex gap-1">
                            <input type="number" name="salario_min" value="{{ request('salario_min') }}" class="form-control form-control-sm" placeholder="Mín">
                            <input type="number" name="salario_max" value="{{ request('salario_max') }}" class="form-control form-control-sm" placeholder="Máx">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:0.8rem;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:0.4px;">Ordenar por</label>
                        <select name="ordenar" class="form-select form-select-sm">
                            <option value="recentes" {{ request('ordenar','recentes') === 'recentes' ? 'selected' : '' }}>Mais recentes</option>
                            <option value="encerramento" {{ request('ordenar') === 'encerramento' ? 'selected' : '' }}>Encerramento próximo</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-sm btn-principal">
                            <i class="bi bi-funnel me-1"></i> Aplicar filtros
                        </button>
                        @if(request()->hasAny(['busca','area','tipo','modalidade','curso','cidade','estado','salario_min','salario_max']))
                        <a href="{{ route('vagas.publicas.index') }}" class="btn btn-sm btn-danger">
                            <i class="bi bi-x-circle me-1"></i> Limpar filtros
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-9">
            @if($vagas->hasPages())
            <div class="d-flex justify-content-end mb-4">
                {{ $vagas->links() }}
            </div>
            @endif
            @forelse($vagas as $vaga)
            <div class="card-vaga mb-3 p-0" style="background:#fff;border-radius:16px;box-shadow:0 4px 15px rgba(0,0,0,0.07);transition:all 0.3s;overflow:hidden;">
                <div style="display:flex;gap:0;align-items:stretch;">
                    <!-- <div style="width:5px;background:{{ $vaga->tipo === 'emprego' ? '#0D6EFD' : ($vaga->tipo === 'bolsa' ? '#6F42C1' : '#0D9571') }};flex-shrink:0;border-radius:16px 0 0 16px;"></div> -->

                    @php $corTipo = $vaga->tipo === 'emprego' ? '#0D6EFD' : ($vaga->tipo === 'bolsa' ? '#0D9571' : '#6F42C1'); @endphp
                    <div style="flex:1;padding:1.25rem 1.5rem;">
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
                            <div>
                                <h5 style="font-size:1rem;font-weight:700;margin:0 0 4px;">
                                    <a href="{{ route('vagas.publicas.show', $vaga) }}" style="color:{{ $corTipo }};">
                                        {{ $vaga->titulo }}
                                    </a>
                                </h5>
                                @if($vaga->projeto_nome)
                                <div style="font-size:0.82rem;color:#6C757D;">
                                    {{ $vaga->projeto_nome }}
                                </div>
                                @endif
                            </div>
                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                <span class="badge-tipo badge-{{ $vaga->tipo }}">{{ $vaga->tipo_label }}</span>
                                <span class="badge-tipo badge-{{ $vaga->modalidade }}">{{ $vaga->modalidade_label }}</span>
                                @if($vaga->is_nova)
                                <span style="background:#dc3545;color:#fff;font-size:0.7rem;font-weight:700;padding:2px 8px;border-radius:20px;">NOVO</span>
                                @endif
                            </div>
                        </div>

                        <p style="font-size:0.875rem;color:#6C757D;line-height:1.5;margin:0 0 0.85rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            {{ $vaga->descricao }}
                        </p>

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div class="d-flex flex-wrap gap-2">
                                @if($vaga->area)
                                <span style="font-size:0.78rem;color:#6C757D;display:inline-flex;align-items:center;gap:0.3rem;">
                                    <i class="bi bi-tag-fill" style="color:{{ $corTipo }};"></i> {{ $vaga->area }}
                                </span>
                                @endif
                                @if($vaga->cidade)
                                <span style="font-size:0.78rem;color:#6C757D;display:inline-flex;align-items:center;gap:0.3rem;">
                                    <i class="bi bi-geo-alt-fill" style="color:{{ $corTipo }};"></i> {{ $vaga->cidade }}/{{ $vaga->estado }}
                                </span>
                                @elseif($vaga->local_trabalho)
                                <span style="font-size:0.78rem;color:#6C757D;display:inline-flex;align-items:center;gap:0.3rem;">
                                    <i class="bi bi-geo-alt-fill" style="color:{{ $corTipo }};"></i> {{ $vaga->local_trabalho }}
                                </span>
                                @endif
                                @if($vaga->carga_horaria)
                                <span style="font-size:0.78rem;color:#6C757D;display:inline-flex;align-items:center;gap:0.3rem;">
                                    <i class="bi bi-clock-fill" style="color:{{ $corTipo }};"></i> {{ $vaga->carga_horaria }}h/semana
                                </span>
                                @endif
                                @if($vaga->remuneracao)
                                <span style="font-size:0.78rem;color:#6C757D;display:inline-flex;align-items:center;gap:0.3rem;">
                                    <i class="bi bi-cash-stack" style="color:{{ $corTipo }};"></i> R$ {{ number_format($vaga->remuneracao, 2, ',', '.') }}
                                </span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span style="font-size:0.75rem;color:{{ $vaga->dias_restantes <= 5 ? '#DC3545' : '#6C757D' }};">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    @if($vaga->dias_restantes === 0)
                                    Encerra hoje
                                    @else
                                    {{ $vaga->dias_restantes }} {{ $vaga->dias_restantes === 1 ? 'dia restante' : 'dias restantes' }}
                                    @endif
                                </span>
                                <a href="{{ route('vagas.publicas.show', $vaga) }}"
                                    style="display:inline-flex;align-items:center;gap:0.35rem;padding:7px 18px;background:{{ $corTipo }};color:#fff;font-size:0.82rem;font-weight:600;border-radius:20px;transition:all 0.3s;">
                                    Ver vaga <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="bi bi-search" style="font-size:3rem;color:#CACACA;"></i>
                <h5 class="mt-3" style="color:#6C757D;">Nenhuma vaga encontrada</h5>
                <p style="color:#CACACA;font-size:0.9rem;">Tente outros filtros ou volte em breve.</p>
                <a href="{{ route('vagas.publicas.index') }}" class="btn btn-outline-principal mt-2">Limpar filtros</a>
            </div>
            @endforelse

            @if($vagas->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $vagas->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@endsection