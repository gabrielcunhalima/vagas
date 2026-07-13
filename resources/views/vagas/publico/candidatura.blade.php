@extends('layouts.publico')

@section('title', 'Candidatar-se — ' . $vaga->titulo)

@section('content')

<div style="background:linear-gradient(135deg,#074635 0%,#0D9571 100%);padding:2rem 0 1.75rem;">
    <div class="container-xl">
        <a href="{{ route('vagas.publicas.show', $vaga) }}" style="color:rgba(255,255,255,0.7);font-size:0.85rem;display:inline-flex;align-items:center;gap:0.4rem;margin-bottom:0.75rem;">
            <i class="bi bi-arrow-left"></i> Voltar à vaga
        </a>
        <h1 style="font-size:1.5rem;font-weight:800;color:#fff;margin:0 0 0.25rem;">Formulário de candidatura</h1>
        <p style="color:rgba(255,255,255,0.78);font-size:0.9rem;margin:0;">{{ $vaga->titulo }}</p>
    </div>
</div>

<div class="container-xl py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            @if($candidato)
            <div class="alert mb-4 d-flex align-items-center gap-3"
                style="background:#f0fdf8;border:1.5px solid rgba(13,149,113,0.3);border-radius:12px;padding:1rem 1.25rem;">
                <i class="bi bi-person-check-fill text-principal" style="font-size:1.5rem;flex-shrink:0;"></i>
                <div class="flex-grow-1">
                    <div style="font-weight:700;color:#2C4A44;font-size:0.9rem;">
                        Candidatando como {{ $candidato->nome }}
                    </div>
                    <div style="font-size:0.8rem;color:#6c757d;">
                        Campos pré-preenchidos com seus dados cadastrados.
                        <a href="{{ route('candidato.perfil.edit') }}" style="color:#0D9571;">Atualizar perfil</a>
                    </div>
                </div>
            </div>
            @else
            <div class="alert mb-4 d-flex align-items-center gap-3"
                style="background:#fffbf0;border:1.5px solid rgba(255,193,7,0.4);border-radius:12px;padding:1rem 1.25rem;">
                <i class="bi bi-person-circle" style="font-size:1.5rem;color:#ffc107;flex-shrink:0;"></i>
                <div class="flex-grow-1">
                    <div style="font-weight:700;color:#2C4A44;font-size:0.9rem;">Candidate-se mais rápido</div>
                    <div style="font-size:0.8rem;color:#6c757d;">
                        <a href="{{ route('candidato.login', ['redirect' => request()->path()]) }}" style="color:#0D9571;font-weight:600;">Entre na sua conta</a>
                        ou <a href="{{ route('candidato.registro', ['redirect' => request()->path()]) }}" style="color:#0D9571;font-weight:600;">cadastre-se</a>
                        para preencher automaticamente e acompanhar suas candidaturas.
                    </div>
                </div>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Corrija os erros abaixo:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $erro)
                    <li style="font-size:0.875rem;">{{ $erro }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <form action="{{ route('inscricao.store', $vaga) }}" method="POST" enctype="multipart/form-data" id="formCandidatura">
                @csrf

                {{-- Dados pessoais --}}
                <div class="card-interno mb-4" style="padding:1.5rem;">
                    <h6 style="font-weight:700;color:#2C4A44;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.5rem;padding-bottom:0.75rem;border-bottom:1px solid #F1F5F4;">
                        <i class="bi bi-person-fill text-principal"></i> Dados pessoais
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome completo <span class="text-danger">*</span></label>
                            <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror"
                                value="{{ old('nome', $prefill['nome'] ?? '') }}"
                                placeholder="Seu nome completo"
                                {{ $candidato ? 'readonly' : 'required' }}>
                            @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CPF <span class="text-danger">*</span></label>
                            <input type="text" name="cpf" id="cpf" class="form-control @error('cpf') is-invalid @enderror"
                                value="{{ old('cpf', $candidato ? $candidato->cpf_formatado : '') }}"
                                placeholder="000.000.000-00" maxlength="14"
                                {{ $candidato ? 'readonly' : 'required' }}>
                            <div class="invalid-feedback" id="cpfFeedback">@error('cpf'){{ $message }}@enderror</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">E-mail <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $prefill['email'] ?? '') }}"
                                placeholder="seu@email.com"
                                {{ $candidato ? 'readonly' : 'required' }}>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="telefone" id="telefone" class="form-control @error('telefone') is-invalid @enderror"
                                value="{{ old('telefone', $prefill['telefone'] ?? '') }}"
                                placeholder="(00) 00000-0000" maxlength="15">
                            @error('telefone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Dados acadêmicos --}}
                <div class="card-interno mb-4" style="padding:1.5rem;">
                    <h6 style="font-weight:700;color:#2C4A44;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.5rem;padding-bottom:0.75rem;border-bottom:1px solid #F1F5F4;">
                        <i class="bi bi-mortarboard-fill text-principal"></i> Dados acadêmicos
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Curso <span class="text-danger">*</span></label>
                            <input type="text" name="curso" class="form-control @error('curso') is-invalid @enderror"
                                value="{{ old('curso', $prefill['curso'] ?? '') }}"
                                placeholder="Ex: Ciência da Computação" required>
                            @error('curso') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Instituição de ensino <span class="text-danger">*</span></label>
                            <input type="text" name="instituicao" class="form-control @error('instituicao') is-invalid @enderror"
                                value="{{ old('instituicao', $prefill['instituicao'] ?? '') }}"
                                placeholder="Ex: UFSC" required>
                            @error('instituicao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Semestre atual</label>
                            <input type="text" name="semestre" class="form-control @error('semestre') is-invalid @enderror"
                                value="{{ old('semestre', $prefill['semestre'] ?? '') }}"
                                placeholder="Ex: 5º">
                            @error('semestre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Previsão de conclusão</label>
                            <input type="date" name="previsao_conclusao" class="form-control @error('previsao_conclusao') is-invalid @enderror"
                                value="{{ old('previsao_conclusao', $prefill['previsao_conclusao'] ?? '') }}">
                            @error('previsao_conclusao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Carta, LinkedIn, PCD, Currículo --}}
                <div class="card-interno mb-4" style="padding:1.5rem;">
                    <h6 style="font-weight:700;color:#2C4A44;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.5rem;padding-bottom:0.75rem;border-bottom:1px solid #F1F5F4;">
                        <i class="bi bi-file-earmark-text-fill text-principal"></i> Carta e informações adicionais
                    </h6>

                    <div class="mb-3">
                        <label class="form-label">Carta de apresentação</label>
                        <textarea name="carta_apresentacao" rows="5"
                            class="form-control @error('carta_apresentacao') is-invalid @enderror"
                            placeholder="Conte um pouco sobre você e por que se interessa por esta vaga…">{{ old('carta_apresentacao') }}</textarea>
                        @error('carta_apresentacao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">LinkedIn <span class="text-muted" style="font-size:0.8rem;">(opcional)</span></label>
                            <input type="url" name="linkedin" value="{{ old('linkedin', $prefill['linkedin'] ?? '') }}"
                                class="form-control @error('linkedin') is-invalid @enderror"
                                placeholder="https://linkedin.com/in/seuperfil">
                            @error('linkedin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Disponibilidade de início <span class="text-muted" style="font-size:0.8rem;">(opcional)</span></label>
                            <select name="disponibilidade" class="form-select">
                                <option value="">Selecione...</option>
                                @foreach(['Imediata','15 dias','30 dias','60 dias'] as $op)
                                <option value="{{ $op }}"
                                    @selected(old('disponibilidade', $prefill['disponibilidade'] ?? '') === $op)>
                                    {{ $op }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pretensão salarial (R$) <span class="text-muted" style="font-size:0.8rem;">(opcional)</span></label>
                            <input type="number" name="pretensao_salarial"
                                value="{{ old('pretensao_salarial', $prefill['pretensao_salarial'] ?? '') }}"
                                class="form-control" min="0" step="0.01" placeholder="0,00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pessoa com Deficiência (PCD)?</label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pcd" value="0" id="pcd_nao"
                                        @checked(!old('pcd', $prefill['pcd'] ?? '0'))>
                                    <label class="form-check-label" for="pcd_nao">Não</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pcd" value="1" id="pcd_sim"
                                        @checked(old('pcd', $prefill['pcd'] ?? '0') == '1')>
                                    <label class="form-check-label" for="pcd_sim">Sim</label>
                                </div>
                            </div>
                            <div id="pcdTipoDiv"
                                style="{{ (old('pcd', $prefill['pcd'] ?? '0') == '1') ? '' : 'display:none;' }}margin-top:0.5rem;">
                                <input type="text" name="pcd_tipo"
                                    value="{{ old('pcd_tipo', $prefill['pcd_tipo'] ?? '') }}"
                                    class="form-control form-control-sm" placeholder="Tipo de deficiência">
                            </div>
                        </div>
                    </div>

                    {{-- Currículo --}}
                    <div class="mt-3">
                        @if($candidato && $candidato->temCurriculo())
                        <div class="d-flex align-items-center gap-3 p-3 mb-3"
                            style="background:#f8fffe;border:1.5px solid rgba(13,149,113,0.2);border-radius:10px;">
                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                            <div class="flex-grow-1">
                                <div style="font-weight:600;font-size:0.875rem;color:#2C4A44;">{{ $candidato->curriculo_nome_original }}</div>
                                <div style="font-size:0.78rem;color:#6c757d;">Currículo do seu perfil será usado</div>
                            </div>
                            <a href="{{ route('candidato.perfil.curriculo.download') }}"
                                class="btn btn-sm btn-outline-principal" style="padding:4px 10px;font-size:0.78rem;">
                                <i class="bi bi-download me-1"></i>Baixar
                            </a>
                        </div>
                        <div style="font-size:0.8rem;color:#6c757d;margin-bottom:0.5rem;">
                            Enviar um currículo diferente para esta candidatura (opcional):
                        </div>
                        @else
                        <label class="form-label">Currículo (PDF, máx. 5MB)</label>
                        @endif

                        <div class="drop-zone" id="dropZone"
                            style="border:2px dashed #0D9571;border-radius:8px;background:rgba(13,149,113,0.04);padding:2rem;text-align:center;transition:all 0.3s;cursor:pointer;">
                            <input type="file" name="curriculo" id="curriculo" accept=".pdf"
                                class="@error('curriculo') is-invalid @enderror" style="display:none;">
                            <div id="dropContent">
                                <i class="bi bi-cloud-upload-fill" style="font-size:2rem;color:#0D9571;"></i>
                                <p style="margin:0.5rem 0 0.25rem;font-weight:600;color:#2C4A44;font-size:0.9rem;">
                                    Arraste o PDF aqui ou <span style="color:#0D9571;cursor:pointer;"
                                        onclick="event.stopPropagation();document.getElementById('curriculo').click()">clique para selecionar</span>
                                </p>
                                <p style="margin:0;font-size:0.78rem;color:#6C757D;">Apenas PDF • Máximo 5MB</p>
                            </div>
                            <div id="fileSelected" style="display:none;">
                                <i class="bi bi-file-earmark-pdf-fill" style="font-size:2rem;color:#0D9571;"></i>
                                <p style="margin:0.5rem 0 0;font-weight:600;color:#2C4A44;font-size:0.9rem;" id="fileName"></p>
                                <button type="button" onclick="clearFile()"
                                    style="background:none;border:none;color:#DC3545;font-size:0.8rem;cursor:pointer;margin-top:4px;">
                                    <i class="bi bi-x-circle me-1"></i>Remover
                                </button>
                            </div>
                        </div>
                        @error('curriculo') <div class="text-danger mt-1" style="font-size:0.875rem;">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- LGPD (apenas para não-logados) --}}
                @if(!$candidato)
                <div class="p-3 mb-4" style="background:#f8fffe;border:1.5px solid rgba(13,149,113,0.25);border-radius:10px;">
                    <div class="form-check">
                        <input class="form-check-input @error('lgpd_consentimento') is-invalid @enderror"
                            type="checkbox" name="lgpd_consentimento" id="lgpd" value="1"
                            {{ old('lgpd_consentimento') ? 'checked' : '' }} required>
                        <label class="form-check-label" for="lgpd" style="font-size:0.875rem;line-height:1.5;">
                            Autorizo o armazenamento dos meus dados pessoais para fins de processo seletivo,
                            conforme a <abbr title="Lei Geral de Proteção de Dados">LGPD</abbr> (Lei nº 13.709/2018) e a
                            <a href="{{ route('politica.privacidade') }}" target="_blank" style="color:#0D9571;font-weight:600;">Política de Privacidade</a>.
                            Meus dados serão acessados <strong>exclusivamente</strong> pelos coordenadores dos projetos
                            aos quais me candidatar.
                        </label>
                        @error('lgpd_consentimento')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @endif

                {{-- Honeypot --}}
                <input type="text" name="_honeypot" style="display:none;" tabindex="-1" autocomplete="off">

                <div class="d-flex flex-wrap gap-2 justify-content-end align-items-center">
                    <a href="{{ route('vagas.publicas.show', $vaga) }}"
                        class="btn btn-outline-secondary btn-sm px-4" style="padding:10px 28px;">
                        Detalhes da vaga
                    </a>
                    <button type="submit" class="btn btn-principal btn-sm" id="btnEnviar">
                        <i class="bi bi-send-fill me-2"></i> Enviar candidatura
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Máscara + validação CPF (só para não-logados)
    function validarCpf(cpf) {
        if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;
        for (let t = 9; t < 11; t++) {
            let soma = 0;
            for (let i = 0; i < t; i++) soma += parseInt(cpf[i]) * (t + 1 - i);
            const resto = soma % 11;
            if (parseInt(cpf[t]) !== (resto < 2 ? 0 : 11 - resto)) return false;
        }
        return true;
    }

    const cpfInput = document.getElementById('cpf');
    if (cpfInput && !cpfInput.readOnly) {
        cpfInput.addEventListener('input', function() {
            // Aplica máscara
            let v = this.value.replace(/\D/g, '').slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            this.value = v;
            // Limpa estado de validação enquanto digita
            this.classList.remove('is-invalid', 'is-valid');
            document.getElementById('cpfFeedback').textContent = '';
        });

        cpfInput.addEventListener('blur', function() {
            const digits = this.value.replace(/\D/g, '');
            const feedback = document.getElementById('cpfFeedback');
            if (digits.length === 0) {
                this.classList.remove('is-invalid', 'is-valid');
                feedback.textContent = '';
                return;
            }
            if (!validarCpf(digits)) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                feedback.textContent = 'CPF inválido.';
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                feedback.textContent = '';
            }
        });
    }

    // PCD toggle
    document.querySelectorAll('input[name="pcd"]').forEach(function(r) {
        r.addEventListener('change', function() {
            document.getElementById('pcdTipoDiv').style.display = this.value === '1' ? '' : 'none';
        });
    });

    // Máscara telefone
    const telInput = document.getElementById('telefone');
    if (telInput) {
        telInput.addEventListener('input', function() {
            let v = this.value.replace(/\D/g, '').slice(0, 11);
            if (v.length <= 10) v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            else v = v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
            this.value = v;
        });
    }

    // Drop zone
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('curriculo');

    dropZone.addEventListener('click', () => fileInput.click());
    dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.style.background = 'rgba(13,149,113,0.1)'; });
    dropZone.addEventListener('dragleave', () => { dropZone.style.background = 'rgba(13,149,113,0.04)'; });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.style.background = 'rgba(13,149,113,0.04)';
        const file = e.dataTransfer.files[0];
        if (file) setFile(file);
    });
    fileInput.addEventListener('change', () => { if (fileInput.files[0]) setFile(fileInput.files[0]); });

    function setFile(file) {
        document.getElementById('dropContent').style.display = 'none';
        document.getElementById('fileSelected').style.display = 'block';
        document.getElementById('fileName').textContent = file.name;
        if (fileInput.files.length === 0) {
            const dt = new DataTransfer(); dt.items.add(file); fileInput.files = dt.files;
        }
    }

    function clearFile() {
        fileInput.value = '';
        document.getElementById('dropContent').style.display = 'block';
        document.getElementById('fileSelected').style.display = 'none';
    }

    // Prevenir duplo envio
    document.getElementById('formCandidatura').addEventListener('submit', function() {
        const btn = document.getElementById('btnEnviar');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Enviando…';
    });
</script>
@endpush
