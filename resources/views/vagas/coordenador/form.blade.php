@extends('layouts.interno')

@section('title', $vaga->exists ? 'Editar Vaga' : 'Nova Vaga')
@section('page-title', $vaga->exists ? 'Editar Vaga' : 'Nova Vaga')
@section('breadcrumb', 'Coordenador / Vagas / ' . ($vaga->exists ? 'Editar' : 'Nova'))

@section('content')

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Corrija os erros:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $e)
                <li style="font-size:0.875rem;">{{ $e }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST"
      action="{{ $vaga->exists ? route('coord.vagas.update', $vaga) : route('coord.vagas.store') }}">
    @csrf
    @if($vaga->exists) @method('PUT') @endif

    <div class="row g-4">

        {{-- Coluna principal --}}
        <div class="col-lg-8">

            {{-- Identificação --}}
            <div class="card-interno mb-4" style="padding:1.5rem;">
                <h6 style="font-weight:700;color:#2C4A44;margin-bottom:1.25rem;padding-bottom:0.75rem;border-bottom:1px solid #F1F5F4;display:flex;align-items:center;gap:0.5rem;">
                     Identificação da vaga
                </h6>
                <div class="mb-3">
                    <label class="form-label">Título da vaga <span class="text-danger">*</span></label>
                    <input type="text" name="titulo" class="form-control @error('titulo') is-invalid @enderror"
                           value="{{ old('titulo', $vaga->titulo) }}" >
                    @error('titulo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição <span class="text-danger">*</span></label>
                    <textarea name="descricao" rows="5" class="form-control @error('descricao') is-invalid @enderror"
                              placeholder="Descreva as atividades que serão realizadas pelo candidato…" >{{ old('descricao', $vaga->descricao) }}</textarea>
                    @error('descricao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Requisitos <span class="text-danger">*</span></label>
                    <textarea name="requisitos" rows="4" class="form-control @error('requisitos') is-invalid @enderror"
                              placeholder="Um requisito por linha&#10;Ex: Graduação em andamento em Ciências Biológicas&#10;Disponibilidade de 20h semanais">{{ old('requisitos', $vaga->requisitos) }}</textarea>
                    @error('requisitos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Requisitos desejáveis</label>
                    <textarea name="requisitos_desejaveis" rows="3" class="form-control @error('requisitos_desejaveis') is-invalid @enderror"
                              placeholder="Um item por linha&#10;Ex: Experiência com análise de dados&#10;Conhecimento em R ou Python">{{ old('requisitos_desejaveis', $vaga->requisitos_desejaveis) }}</textarea>
                    @error('requisitos_desejaveis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Benefícios</label>
                    <textarea name="beneficios" rows="3" class="form-control @error('beneficios') is-invalid @enderror"
                              placeholder="Um benefício por linha&#10;Ex: Vale transporte&#10;Vale alimentação&#10;Bolsa auxílio">{{ old('beneficios', $vaga->beneficios) }}</textarea>
                    @error('beneficios') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Projeto --}}
            <div class="card-interno mb-4" style="padding:1.5rem;">
                <h6 style="font-weight:700;color:#2C4A44;margin-bottom:1.25rem;padding-bottom:0.75rem;border-bottom:1px solid #F1F5F4;display:flex;align-items:center;gap:0.5rem;">
                    Dados do projeto
                </h6>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Nome do projeto</label>
                        <input type="text" name="projeto_nome" class="form-control @error('projeto_nome') is-invalid @enderror"
                               value="{{ old('projeto_nome', $vaga->projeto_nome) }}">
                        @error('projeto_nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Código do projeto FAPEU</label>
                        <input type="text" name="projeto_codigo" class="form-control @error('projeto_codigo') is-invalid @enderror"
                               value="{{ old('projeto_codigo', $vaga->projeto_codigo) }}">
                        @error('projeto_codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

        </div>

        {{-- Coluna lateral --}}
        <div class="col-lg-4">

            {{-- Classificação --}}
            <div class="card-interno mb-4" style="padding:1.25rem;">
                <h6 style="font-weight:700;color:#2C4A44;margin-bottom:1.25rem;padding-bottom:0.75rem;border-bottom:1px solid #F1F5F4;font-size:0.875rem;">
                    Classificação
                </h6>
                <div class="mb-3">
                    <label class="form-label">Tipo <span class="text-danger">*</span></label>
                    <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" >
                        @foreach(\App\Models\Vagas\Vaga::$tiposLabel as $val => $label)
                            <option value="{{ $val }}" {{ old('tipo', $vaga->tipo) === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Área <span class="text-danger">*</span></label>
                    <select name="area" class="form-select @error('area') is-invalid @enderror">
                        <option value="">Selecione uma área…</option>
                        @foreach(\App\Models\Vagas\Vaga::$areas as $areaOpcao)
                            <option value="{{ $areaOpcao }}" {{ old('area', $vaga->area) === $areaOpcao ? 'selected' : '' }}>
                                {{ $areaOpcao }}
                            </option>
                        @endforeach
                    </select>
                    @error('area') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Cursos desejados</label>
                    @php $cursosSelecionados = old('curso_desejado', $vaga->curso_desejado ?? []); @endphp

                    {{-- Tags dos cursos já selecionados (inputs ocultos) --}}
                    <div id="cursos-tags" style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:6px;">
                        @foreach($cursosSelecionados as $c)
                            <span class="curso-tag" data-value="{{ $c }}"
                                  style="display:inline-flex;align-items:center;gap:5px;background:#E6F5F1;color:#0D9571;border:1px solid #B2DFDB;border-radius:20px;padding:3px 10px;font-size:0.8rem;font-weight:600;">
                                {{ $c }}
                                <button type="button" class="btn-remove-tag"
                                        style="background:none;border:none;padding:0;line-height:1;color:#0D9571;cursor:pointer;font-size:0.85rem;"
                                        title="Remover">&times;</button>
                                <input type="hidden" name="curso_desejado[]" value="{{ $c }}">
                            </span>
                        @endforeach
                    </div>

                    {{-- Campo de busca / autocomplete --}}
                    <div style="position:relative;">
                        <input type="text" id="curso-input" autocomplete="off"
                               class="form-control @error('curso_desejado') is-invalid @enderror"
                               placeholder="Digite para buscar um curso…">
                        <ul id="curso-dropdown"
                            style="display:none;position:absolute;top:100%;left:0;right:0;z-index:1000;
                                   background:#fff;border:1px solid #ced4da;border-top:none;border-radius:0 0 6px 6px;
                                   max-height:200px;overflow-y:auto;margin:0;padding:0;list-style:none;box-shadow:0 4px 12px rgba(0,0,0,0.08);">
                        </ul>
                    </div>
                    @error('curso_desejado') <div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Condições --}}
            <div class="card-interno mb-4" style="padding:1.25rem;">
                <h6 style="font-weight:700;color:#2C4A44;margin-bottom:1.25rem;padding-bottom:0.75rem;border-bottom:1px solid #F1F5F4;font-size:0.875rem;">
                    Condições
                </h6>
                <div class="mb-3">
                    <label class="form-label">Modalidade <span class="text-danger">*</span></label>
                    <select name="modalidade" class="form-select @error('modalidade') is-invalid @enderror" >
                        @foreach(\App\Models\Vagas\Vaga::$modalidadesLabel as $val => $label)
                            <option value="{{ $val }}" {{ old('modalidade', $vaga->modalidade) === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('modalidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Local de trabalho</label>
                    <input type="text" name="local_trabalho" class="form-control @error('local_trabalho') is-invalid @enderror"
                           value="{{ old('local_trabalho', $vaga->local_trabalho) }}">
                    @error('local_trabalho') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Carga horária</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="carga_horaria" class="form-control @error('carga_horaria') is-invalid @enderror"
                                   value="{{ old('carga_horaria', $vaga->carga_horaria) }}" min="1" max="44">
                            <span class="input-group-text">h/sem</span>
                        </div>
                        @error('carga_horaria') <div class="text-danger" style="font-size:0.78rem;">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label">Remuneração</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">R$</span>
                            <input type="number" name="remuneracao" step="0.01"
                                   class="form-control @error('remuneracao') is-invalid @enderror"
                                   value="{{ old('remuneracao', $vaga->remuneracao) }}" placeholder="0,00">
                        </div>
                        @error('remuneracao') <div class="text-danger" style="font-size:0.78rem;">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Data de encerramento das inscrições <span class="text-danger">*</span></label>
                    <input type="date" name="data_encerramento"
                           class="form-control @error('data_encerramento') is-invalid @enderror"
                           value="{{ old('data_encerramento', $vaga->data_encerramento?->format('Y-m-d')) }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}" >
                    @error('data_encerramento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div style="background:#F8F9FA;border-radius:8px;padding:0.75rem 1rem;">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch"
                               name="notificar_email" id="notificar_email" value="1"
                               {{ old('notificar_email', $vaga->notificar_email ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="notificar_email" style="font-size:0.875rem;font-weight:600;color:#2C4A44;">
                            Receber e-mails de novas candidaturas
                        </label>
                    </div>
                    <div style="font-size:0.75rem;color:#6C757D;margin-top:3px;padding-left:2.5rem;">
                        Você será notificado a cada nova inscrição nesta vaga.
                    </div>
                </div>
            </div>

            {{-- Ações --}}
            <div class="card-interno" style="padding:1.25rem;">
                <div class="d-grid gap-2">
                    <button type="submit" name="acao" value="publicar" class="btn btn-principal">
                        <i class="bi bi-send-fill me-2"></i>
                        {{ $vaga->exists ? 'Atualizar e enviar' : 'Criar e enviar para autorização' }}
                    </button>
                    <button type="submit" name="acao" value="rascunho" class="btn btn-outline-secondary">
                        <i class="bi bi-floppy me-2"></i> Salvar como rascunho
                    </button>
                    <a href="{{ route('coord.vagas.index') }}" class="btn btn-danger">
                        <i class="bi bi-x me-1"></i> Cancelar
                    </a>
                </div>
            </div>

        </div>
    </div>
</form>

@push('scripts')
<script>
(function () {
    const CURSOS = @json(\App\Models\Vagas\Vaga::$cursos);
    const tagsBox  = document.getElementById('cursos-tags');
    const input    = document.getElementById('curso-input');
    const dropdown = document.getElementById('curso-dropdown');

    function selecionados() {
        return [...tagsBox.querySelectorAll('.curso-tag')].map(t => t.dataset.value);
    }

    function addTag(valor) {
        if (selecionados().includes(valor)) return;

        const span = document.createElement('span');
        span.className = 'curso-tag';
        span.dataset.value = valor;
        span.style.cssText = 'display:inline-flex;align-items:center;gap:5px;background:#E6F5F1;color:#0D9571;border:1px solid #B2DFDB;border-radius:20px;padding:3px 10px;font-size:0.8rem;font-weight:600;';
        span.innerHTML = `${valor}
            <button type="button" class="btn-remove-tag"
                    style="background:none;border:none;padding:0;line-height:1;color:#0D9571;cursor:pointer;font-size:0.85rem;"
                    title="Remover">&times;</button>
            <input type="hidden" name="curso_desejado[]" value="${valor}">`;
        span.querySelector('.btn-remove-tag').addEventListener('click', () => span.remove());
        tagsBox.appendChild(span);
    }

    function renderDropdown(termo) {
        const filtrados = CURSOS.filter(c =>
            !selecionados().includes(c) &&
            c.toLowerCase().includes(termo.toLowerCase())
        );
        dropdown.innerHTML = '';
        if (!filtrados.length || !termo) { dropdown.style.display = 'none'; return; }
        filtrados.forEach(c => {
            const li = document.createElement('li');
            li.textContent = c;
            li.style.cssText = 'padding:8px 14px;cursor:pointer;font-size:0.875rem;';
            li.addEventListener('mousedown', e => {
                e.preventDefault();
                addTag(c);
                input.value = '';
                dropdown.style.display = 'none';
            });
            li.addEventListener('mouseenter', () => li.style.background = '#F0FAF7');
            li.addEventListener('mouseleave', () => li.style.background = '');
            dropdown.appendChild(li);
        });
        dropdown.style.display = 'block';
    }

    input.addEventListener('input', () => renderDropdown(input.value));
    input.addEventListener('focus', () => { if (input.value) renderDropdown(input.value); });
    input.addEventListener('blur',  () => setTimeout(() => { dropdown.style.display = 'none'; }, 150));

    // Botões de remoção das tags já renderizadas pelo Blade
    tagsBox.addEventListener('click', e => {
        if (e.target.classList.contains('btn-remove-tag')) {
            e.target.closest('.curso-tag').remove();
        }
    });
})();
</script>
@endpush

@endsection
