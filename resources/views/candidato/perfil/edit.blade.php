@extends('layouts.publico')

@section('title', 'Meus dados — Portal de Vagas FAPEU')

@push('styles')
<style>
.card-interno { background:#fff; border-radius:14px; box-shadow:0 2px 10px rgba(0,0,0,0.06); }
.section-title { font-weight:700; color:#2C4A44; margin-bottom:1.25rem; padding-bottom:0.75rem; border-bottom:1px solid #F1F5F4; display:flex; align-items:center; gap:0.5rem; font-size:0.975rem; }
</style>
@endpush

@section('content')

<div style="background:linear-gradient(135deg,#074635 0%,#0D9571 100%);padding:2rem 0 1.75rem;">
    <div class="container-xl">
        <h1 style="font-size:1.5rem;font-weight:800;color:#fff;margin:0 0 0.2rem;">Meus dados</h1>
        <p style="color:rgba(255,255,255,0.78);font-size:0.875rem;margin:0;">
            Estes dados são usados automaticamente nas suas candidaturas
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

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" style="border-radius:10px;font-size:0.875rem;">
        <i class="bi bi-exclamation-triangle me-2"></i><strong>Corrija os erros:</strong>
        <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        {{-- Coluna principal --}}
        <div class="col-lg-8">

            <form action="{{ route('candidato.perfil.update') }}" method="POST" enctype="multipart/form-data" id="formPerfil">
                @csrf @method('PUT')

                {{-- Dados pessoais --}}
                <div class="card-interno p-4 mb-4">
                    <h6 class="section-title"><i class="bi bi-person-fill text-principal"></i> Dados pessoais</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nome completo <span class="text-danger">*</span></label>
                            <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror"
                                value="{{ old('nome', $candidato->nome) }}" required>
                            @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">E-mail <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $candidato->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CPF <span class="text-danger">*</span></label>
                            <input type="text" name="cpf" id="cpfPerfil" class="form-control @error('cpf') is-invalid @enderror"
                                value="{{ old('cpf', $candidato->cpf_formatado) }}" maxlength="14" required>
                            @error('cpf') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="telefone" id="telefonePerfil" class="form-control"
                                value="{{ old('telefone', $candidato->telefone) }}" maxlength="15" placeholder="(00) 00000-0000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">LinkedIn <span class="text-muted" style="font-size:0.8rem;">(opcional)</span></label>
                            <input type="url" name="linkedin" class="form-control @error('linkedin') is-invalid @enderror"
                                value="{{ old('linkedin', $candidato->linkedin) }}" placeholder="https://linkedin.com/in/seuperfil">
                            @error('linkedin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Dados acadêmicos --}}
                <div class="card-interno p-4 mb-4">
                    <h6 class="section-title"><i class="bi bi-mortarboard-fill text-principal"></i> Dados acadêmicos</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Curso</label>
                            <input type="text" name="curso" class="form-control"
                                value="{{ old('curso', $candidato->curso) }}" placeholder="Ex: Ciência da Computação">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Instituição</label>
                            <input type="text" name="instituicao" class="form-control"
                                value="{{ old('instituicao', $candidato->instituicao) }}" placeholder="Ex: UFSC">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Semestre atual</label>
                            <input type="text" name="semestre" class="form-control"
                                value="{{ old('semestre', $candidato->semestre) }}" placeholder="Ex: 5º">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Previsão de conclusão</label>
                            <input type="date" name="previsao_conclusao" class="form-control"
                                value="{{ old('previsao_conclusao', $candidato->previsao_conclusao?->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>

                {{-- Endereço --}}
                <div class="card-interno p-4 mb-4">
                    <h6 class="section-title"><i class="bi bi-geo-alt-fill text-principal"></i> Endereço</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">CEP</label>
                            <input type="text" name="cep" id="cepPerfil" class="form-control"
                                value="{{ old('cep', $candidato->cep) }}" placeholder="00000-000" maxlength="9">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Logradouro</label>
                            <input type="text" name="logradouro" id="logradouroPerfil" class="form-control"
                                value="{{ old('logradouro', $candidato->logradouro) }}" placeholder="Rua, Av...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Número</label>
                            <input type="text" name="numero" class="form-control"
                                value="{{ old('numero', $candidato->numero) }}" placeholder="123">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Complemento</label>
                            <input type="text" name="complemento" class="form-control"
                                value="{{ old('complemento', $candidato->complemento) }}" placeholder="Apto, Sala...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bairro</label>
                            <input type="text" name="bairro" id="bairroPerfil" class="form-control"
                                value="{{ old('bairro', $candidato->bairro) }}" placeholder="Bairro">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cidade</label>
                            <input type="text" name="cidade" id="cidadePerfil" class="form-control"
                                value="{{ old('cidade', $candidato->cidade) }}" placeholder="Cidade">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">UF</label>
                            <input type="text" name="estado" id="estadoPerfil" class="form-control"
                                value="{{ old('estado', $candidato->estado) }}" placeholder="SC" maxlength="2">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">País</label>
                            <input type="text" name="pais" class="form-control"
                                value="{{ old('pais', $candidato->pais ?? 'Brasil') }}">
                        </div>
                    </div>
                </div>

                {{-- Preferências --}}
                <div class="card-interno p-4 mb-4">
                    <h6 class="section-title"><i class="bi bi-sliders text-principal"></i> Preferências de candidatura</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Disponibilidade de início</label>
                            <select name="disponibilidade" class="form-select">
                                <option value="">Selecione...</option>
                                @foreach(['Imediata','15 dias','30 dias','60 dias'] as $op)
                                <option value="{{ $op }}" @selected(old('disponibilidade', $candidato->disponibilidade) === $op)>{{ $op }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pretensão salarial (R$)</label>
                            <input type="number" name="pretensao_salarial" class="form-control"
                                value="{{ old('pretensao_salarial', $candidato->pretensao_salarial) }}"
                                min="0" step="0.01" placeholder="0,00">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Pessoa com Deficiência (PCD)?</label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pcd" value="0" id="pcd_nao"
                                        @checked(!old('pcd', $candidato->pcd ? '1' : '0'))>
                                    <label class="form-check-label" for="pcd_nao">Não</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pcd" value="1" id="pcd_sim"
                                        @checked(old('pcd', $candidato->pcd ? '1' : '0') == '1')>
                                    <label class="form-check-label" for="pcd_sim">Sim</label>
                                </div>
                            </div>
                            <div id="pcdTipoDiv" style="{{ ($candidato->pcd || old('pcd') == '1') ? '' : 'display:none;' }}margin-top:0.5rem;">
                                <input type="text" name="pcd_tipo" class="form-control form-control-sm"
                                    value="{{ old('pcd_tipo', $candidato->pcd_tipo) }}"
                                    placeholder="Tipo de deficiência">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Currículo --}}
                <div class="card-interno p-4 mb-4">
                    <h6 class="section-title"><i class="bi bi-file-earmark-person-fill text-principal"></i> Currículo padrão</h6>
                    <p style="font-size:0.875rem;color:#6c757d;margin-bottom:1rem;">
                        Seu currículo padrão será sugerido automaticamente em candidaturas. Você pode substituí-lo na hora da candidatura.
                    </p>

                    @if($candidato->temCurriculo())
                    <div class="d-flex align-items-center gap-3 p-3 mb-3"
                        style="background:#f8fffe;border:1.5px solid rgba(13,149,113,0.2);border-radius:10px;">
                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-4"></i>
                        <div class="flex-grow-1">
                            <div style="font-weight:600;font-size:0.875rem;color:#2C4A44;">{{ $candidato->curriculo_nome_original }}</div>
                            <div style="font-size:0.78rem;color:#6c757d;">Currículo atual</div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('candidato.perfil.curriculo.download') }}" class="btn btn-sm btn-outline-principal" style="padding:5px 12px;font-size:0.8rem;">
                                <i class="bi bi-download"></i>
                            </a>
                            <form action="{{ route('candidato.perfil.curriculo.remover') }}" method="POST"
                                onsubmit="return confirm('Remover currículo?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="padding:5px 12px;font-size:0.8rem;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                    <div class="drop-zone" id="dropZonePerfil"
                        style="border:2px dashed #0D9571;border-radius:8px;background:rgba(13,149,113,0.04);padding:1.5rem;text-align:center;transition:all 0.3s;cursor:pointer;">
                        <input type="file" name="curriculo" id="curriculoPerfil" accept=".pdf" style="display:none;">
                        <div id="dropContentPerfil">
                            <i class="bi bi-cloud-upload-fill" style="font-size:1.8rem;color:#0D9571;"></i>
                            <p style="margin:0.4rem 0 0.2rem;font-weight:600;color:#2C4A44;font-size:0.875rem;">
                                {{ $candidato->temCurriculo() ? 'Substituir currículo' : 'Enviar currículo (PDF)' }}
                            </p>
                            <p style="margin:0;font-size:0.78rem;color:#6C757D;">Arraste o PDF ou clique · Máx. 5MB</p>
                        </div>
                        <div id="fileSelectedPerfil" style="display:none;">
                            <i class="bi bi-file-earmark-pdf-fill" style="font-size:1.8rem;color:#0D9571;"></i>
                            <p style="margin:0.4rem 0 0;font-weight:600;color:#2C4A44;font-size:0.875rem;" id="fileNamePerfil"></p>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-principal px-5" id="btnSalvar">
                        <i class="bi bi-check2-circle me-2"></i> Salvar dados
                    </button>
                </div>
            </form>

        </div>

        {{-- Coluna lateral --}}
        <div class="col-lg-4">

            {{-- Resumo do perfil --}}
            <div class="card-interno p-4 mb-4">
                <h6 class="section-title"><i class="bi bi-person-badge text-principal"></i> Resumo do perfil</h6>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#074635,#0D9571);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span style="color:#fff;font-size:1.2rem;font-weight:800;">{{ strtoupper(substr($candidato->nome, 0, 1)) }}</span>
                    </div>
                    <div>
                        <div style="font-weight:700;color:#2C4A44;font-size:0.9rem;">{{ $candidato->nome }}</div>
                        <div style="font-size:0.8rem;color:#6c757d;">{{ $candidato->email }}</div>
                    </div>
                </div>
                @if($candidato->curso)
                <div style="font-size:0.825rem;color:#495057;margin-bottom:0.4rem;">
                    <i class="bi bi-mortarboard me-1 text-principal"></i> {{ $candidato->curso }}
                    @if($candidato->instituicao) — {{ $candidato->instituicao }}@endif
                </div>
                @endif
                @if($candidato->cidade)
                <div style="font-size:0.825rem;color:#495057;margin-bottom:0.4rem;">
                    <i class="bi bi-geo-alt me-1 text-principal"></i> {{ $candidato->cidade }}{{ $candidato->estado ? '/' . $candidato->estado : '' }}
                </div>
                @endif
                @if($candidato->linkedin)
                <div style="font-size:0.825rem;margin-bottom:0.4rem;">
                    <i class="bi bi-linkedin me-1" style="color:#0077b5;"></i>
                    <a href="{{ $candidato->linkedin }}" target="_blank" style="color:#0D9571;">LinkedIn</a>
                </div>
                @endif
                <hr style="margin:0.75rem 0;">
                <div style="font-size:0.8rem;color:#6c757d;">
                    Membro desde {{ $candidato->created_at->format('M/Y') }}
                </div>
            </div>

            {{-- Alterar senha --}}
            <div class="card-interno p-4 mb-4">
                <h6 class="section-title"><i class="bi bi-shield-lock text-principal"></i> Alterar senha</h6>
                <form action="{{ route('candidato.perfil.senha') }}" method="POST" id="formSenha">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label" style="font-size:0.875rem;">Senha atual</label>
                        <input type="password" name="senha_atual"
                            class="form-control form-control-sm @error('senha_atual') is-invalid @enderror"
                            placeholder="Senha atual">
                        @error('senha_atual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:0.875rem;">Nova senha</label>
                        <input type="password" name="password"
                            class="form-control form-control-sm @error('password') is-invalid @enderror"
                            placeholder="Mín. 8 caracteres">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:0.875rem;">Confirmar nova senha</label>
                        <input type="password" name="password_confirmation"
                            class="form-control form-control-sm" placeholder="Repita a nova senha">
                    </div>
                    <button type="submit" class="btn btn-outline-principal w-100 btn-sm">
                        <i class="bi bi-key me-1"></i> Alterar senha
                    </button>
                </form>
            </div>

            {{-- LGPD / Excluir conta --}}
            <div class="card-interno p-4 mb-4" style="border:1.5px solid rgba(220,53,69,0.15);">
                <h6 style="font-weight:700;color:#DC3545;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;font-size:0.9rem;">
                    <i class="bi bi-shield-exclamation"></i> Privacidade (LGPD)
                </h6>
                <p style="font-size:0.8rem;color:#6c757d;line-height:1.5;margin-bottom:0.75rem;">
                    Seus dados são armazenados apenas para fins de processo seletivo e acessados
                    exclusivamente pelos coordenadores dos projetos aos quais você se candidatar.
                    Você pode excluir sua conta e todos os dados a qualquer momento.
                </p>
                <div style="font-size:0.78rem;color:#6c757d;margin-bottom:1rem;">
                    Consentimento concedido em:
                    <strong>{{ $candidato->lgpd_consentimento_em?->format('d/m/Y H:i') ?? '—' }}</strong>
                </div>
                <a href="{{ route('candidato.perfil.exportar') }}" class="btn btn-sm w-100 mb-2"
                    style="border:1.5px solid #0D9571;color:#0D9571;border-radius:8px;font-weight:600;">
                    <i class="bi bi-download me-1"></i> Exportar meus dados
                </a>
                <button class="btn btn-sm w-100" style="border:1.5px solid #DC3545;color:#DC3545;border-radius:8px;font-weight:600;"
                    data-bs-toggle="modal" data-bs-target="#modalExcluir">
                    <i class="bi bi-trash me-1"></i> Excluir minha conta
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Modal excluir conta --}}
<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Excluir conta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="font-size:0.875rem;color:#495057;">
                    Ao excluir sua conta, seus dados de perfil serão removidos. As candidaturas já enviadas
                    serão mantidas para controle do processo seletivo, mas desvinculadas do seu perfil,
                    conforme previsto na LGPD para obrigações legítimas do controlador.
                </p>
                <form action="{{ route('candidato.excluir') }}" method="POST" id="formExcluir">
                    @csrf @method('DELETE')
                    <div class="mb-3">
                        <label class="form-label" style="font-size:0.875rem;">Confirme sua senha para continuar</label>
                        <input type="password" name="password" class="form-control" placeholder="Sua senha atual" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="confirmar_exclusao" id="confirmCheck" value="1" required>
                        <label class="form-check-label" for="confirmCheck" style="font-size:0.875rem;">
                            Entendo que esta ação é irreversível e desejo excluir minha conta
                        </label>
                    </div>
                    <button type="submit" class="btn w-100" style="background:#DC3545;color:#fff;border-radius:8px;font-weight:600;">
                        <i class="bi bi-trash me-2"></i> Confirmar exclusão
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Máscara CPF
document.getElementById('cpfPerfil').addEventListener('input', function() {
    let v = this.value.replace(/\D/g, '').slice(0, 11);
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    this.value = v;
});

// Máscara telefone
document.getElementById('telefonePerfil').addEventListener('input', function() {
    let v = this.value.replace(/\D/g, '').slice(0, 11);
    if (v.length <= 10) v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    else v = v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
    this.value = v;
});

// Máscara CEP + preenchimento automático
document.getElementById('cepPerfil').addEventListener('blur', async function() {
    const cep = this.value.replace(/\D/g, '');
    if (cep.length !== 8) return;
    try {
        const r = await fetch(`/api/cep/${cep}`);
        if (!r.ok) return;
        const d = await r.json();
        if (d.logradouro !== undefined) document.getElementById('logradouroPerfil').value = d.logradouro;
        if (d.bairro !== undefined) document.getElementById('bairroPerfil').value = d.bairro;
        if (d.cidade !== undefined) document.getElementById('cidadePerfil').value = d.cidade;
        if (d.estado !== undefined) document.getElementById('estadoPerfil').value = d.estado;
    } catch(e) {}
});

// PCD toggle
document.querySelectorAll('input[name="pcd"]').forEach(r => {
    r.addEventListener('change', function() {
        document.getElementById('pcdTipoDiv').style.display = this.value === '1' ? '' : 'none';
    });
});

// Drop zone currículo
const dropZone = document.getElementById('dropZonePerfil');
const fileInput = document.getElementById('curriculoPerfil');

dropZone.addEventListener('click', () => fileInput.click());
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.style.background = 'rgba(13,149,113,0.1)'; });
dropZone.addEventListener('dragleave', () => { dropZone.style.background = 'rgba(13,149,113,0.04)'; });
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.style.background = 'rgba(13,149,113,0.04)';
    const file = e.dataTransfer.files[0];
    if (file) setFile(file);
});
fileInput.addEventListener('change', () => { if (fileInput.files[0]) setFile(fileInput.files[0]); });

function setFile(file) {
    document.getElementById('dropContentPerfil').style.display = 'none';
    document.getElementById('fileSelectedPerfil').style.display = 'block';
    document.getElementById('fileNamePerfil').textContent = file.name;
    if (fileInput.files.length === 0) {
        const dt = new DataTransfer(); dt.items.add(file); fileInput.files = dt.files;
    }
}

document.getElementById('formPerfil').addEventListener('submit', function() {
    const btn = document.getElementById('btnSalvar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Salvando…';
});
</script>
@endpush
