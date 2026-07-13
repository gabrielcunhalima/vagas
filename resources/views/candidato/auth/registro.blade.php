<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" type="image/x-icon" href="{{ asset('imagens/fapeu_ico.ico') }}">
    <title>Criar conta — Portal de Vagas FAPEU</title>

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: 'Outfit', sans-serif; min-height: 100vh; display: flex; background: #f0f4f8; }

        .login-left { display: none; width: 38%; background: linear-gradient(145deg, #074635 0%, #0D9571 55%, #1ab88a 100%); position: relative; overflow: hidden; flex-direction: column; align-items: center; justify-content: center; padding: 48px 40px; }
        @media (min-width: 992px) { .login-left { display: flex; } }
        .dot-pattern { position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 24px 24px; }
        .blob { position: absolute; border-radius: 50%; background: rgba(255,255,255,0.06); }
        .blob-1 { width: 300px; height: 300px; top: -80px; right: -80px; }
        .blob-2 { width: 200px; height: 200px; bottom: 60px; left: -60px; }
        .blob-3 { width: 150px; height: 150px; bottom: 200px; right: 30px; }

        .login-brand-card { position: relative; z-index: 1; text-align: center; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 24px; padding: 40px 36px; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); max-width: 320px; width: 100%; }
        .login-brand-card img { height: 72px; filter: drop-shadow(0 4px 16px rgba(0,0,0,0.2)); margin-bottom: 20px; }
        .login-brand-title { font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 6px; }
        .login-brand-sub { font-size: 14px; color: rgba(255,255,255,0.7); line-height: 1.5; }

        .login-right { flex: 1; display: flex; align-items: flex-start; justify-content: center; padding: 40px 24px; background: #fff; overflow-y: auto; }
        .login-form-box { width: 100%; max-width: 640px; animation: fadeInUp 0.55s ease-out both; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }

        .login-logo-mobile { display: flex; flex-direction: column; align-items: center; margin-bottom: 24px; }
        @media (min-width: 992px) { .login-logo-mobile { display: none; } }
        .login-logo-mobile img { height: 52px; }

        .login-title { font-size: 22px; font-weight: 700; color: #0f1e32; margin-bottom: 4px; }
        .login-subtitle { font-size: 15px; color: #7a9ab5; margin-bottom: 24px; }

        .field-label { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: #3a5a7a; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
        .field-label .opt { font-size: 11px; font-weight: 500; text-transform: none; letter-spacing: 0; color: #a0bdd4; }
        .field, .field-select, .field-textarea { width: 100%; padding: 11px 14px; border: 1.5px solid #d0dce8; border-radius: 12px; font-family: 'Outfit', sans-serif; font-size: 14px; color: #0f1e32; background: #f7fafc; outline: none; transition: border-color .15s, box-shadow .15s; }
        .field:focus, .field-select:focus, .field-textarea:focus { border-color: #0D9571; background: #fff; box-shadow: 0 0 0 3px rgba(13,149,113,0.12); }
        .field.is-invalid { border-color: #f43f5e; }
        .field.is-valid { border-color: #0D9571; }
        input:-webkit-autofill { -webkit-box-shadow: 0 0 0 1000px #f7fafc inset !important; -webkit-text-fill-color: #0f1e32 !important; }

        .field-wrapper { position: relative; }
        .field-wrapper .field { padding-right: 40px; }
        .toggle-pw { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #7a9ab5; font-size: 16px; background: none; border: none; padding: 0; }
        .toggle-pw:hover { color: #0D9571; }

        .field-error { font-size: 12px; color: #f43f5e; margin-top: 4px; }
        .field-feedback { font-size: 12px; margin-top: 4px; }

        .btn-login { padding: 13px 28px; background: linear-gradient(135deg, #0D9571, #0C8061); color: #fff; font-family: 'Outfit', sans-serif; font-size: 14px; font-weight: 700; border: none; border-radius: 12px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: all .2s; box-shadow: 0 4px 14px rgba(13,149,113,.3); }
        .btn-login:hover { background: linear-gradient(135deg, #0C8061, #074635); box-shadow: 0 6px 20px rgba(13,149,113,.4); }
        .btn-login.shake { animation: shake 0.4s ease; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-4px); } 75% { transform: translateX(4px); } }

        .btn-outline { padding: 13px 24px; background: transparent; color: #3a5a7a; font-family: 'Outfit', sans-serif; font-size: 14px; font-weight: 700; border: 1.5px solid #d0dce8; border-radius: 12px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: all .15s; }
        .btn-outline:hover { border-color: #0D9571; color: #0D9571; }

        .login-alert { background: #fff1f2; border: 1px solid #fecdd3; border-left: 4px solid #f43f5e; border-radius: 10px; padding: 12px 16px; color: #be123c; font-size: 13px; margin-bottom: 20px; }
        .login-alert ul { margin: 4px 0 0; padding-left: 18px; }
        .login-warning { background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b; border-radius: 10px; padding: 12px 16px; color: #92400e; font-size: 13px; margin-bottom: 16px; display:flex; align-items:flex-start; gap:8px; }
        .login-footer-text { text-align: center; font-size: 13px; color: #a0bdd4; margin-top: 24px; }
        .login-back { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #7a9ab5; margin-bottom: 16px; }
        .login-back:hover { color: #0D9571; }

        .box-muted { background: #f7fafc; border-radius: 12px; padding: 16px; }
        .box-accent { background: #ecfdf5; border: 1.5px solid rgba(13,149,113,0.25); border-radius: 12px; padding: 16px; }

        .rule-list { list-style: none; margin: 8px 0 0; padding: 0; font-size: 13px; }
        .rule-list li { color: #a0bdd4; margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
        .rule-list li.ok { color: #0D9571; font-weight: 600; }

        .check-row { display: flex; align-items: flex-start; gap: 10px; cursor: pointer; }
        .check-row input { margin-top: 3px; accent-color: #0D9571; width: 16px; height: 16px; flex-shrink: 0; }
        .check-row span { font-size: 13px; color: #3a5a7a; line-height: 1.5; }
        .check-row a { color: #0D9571; font-weight: 600; }

        .radio-row { display: flex; gap: 20px; }
        .radio-opt { display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 14px; color: #3a5a7a; }
        .radio-opt input { accent-color: #0D9571; width: 16px; height: 16px; }

        .drop-zone { border: 2px dashed #0D9571; border-radius: 12px; background: rgba(13,149,113,0.04); padding: 24px; text-align: center; cursor: pointer; transition: background .2s; }

        .wizard-step { display: none; }
        .wizard-step.active { display: block; animation: fadeInUp .35s ease-out both; }

        .stepper { display: flex; justify-content: space-between; margin-bottom: 28px; gap: 4px; }
        .step-indicator { flex: 1; text-align: center; }
        .step-circle { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; background: #e9eef3; color: #a0bdd4; margin: 0 auto 6px; transition: all .2s; }
        .step-circle.active { background: #0D9571; color: #fff; }
        .step-circle.done { background: #074635; color: #fff; }
        .step-label { font-size: 10.5px; color: #a0bdd4; font-weight: 500; }
        .step-label.active { color: #0D9571; font-weight: 700; }
        .step-label.done { color: #074635; }

        .row-g { display: flex; flex-wrap: wrap; gap: 16px; margin-bottom: 4px; }
        .col-6 { flex: 1 1 calc(50% - 8px); min-width: 200px; }
        .col-4 { flex: 1 1 calc(33.33% - 11px); min-width: 120px; }
        .col-12 { flex: 1 1 100%; }
        .field-group { margin-bottom: 16px; }

        @media (max-width: 576px) {
            .col-6, .col-4 { flex: 1 1 100%; }
            .stepper { display: none; }
        }
    </style>
</head>
<body>

<div class="login-left">
    <div class="dot-pattern"></div>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
    <div class="login-brand-card">
        <img src="{{ asset('imagens/fapeulogoverde.png') }}" alt="FAPEU">
        <div class="login-brand-title">Portal de Vagas<br></div>
        <!-- <div class="login-brand-sub">Crie sua conta e candidate-se<br>em segundos usando seus dados salvos</div> -->
    </div>
</div>

<div class="login-right">
    <div class="login-form-box">

        <div class="login-logo-mobile">
            <img src="{{ asset('imagens/fapeulogoverde.png') }}" alt="FAPEU">
        </div>

        <a href="{{ route('vagas.publicas.index') }}" class="login-back">
            <i class="bi bi-arrow-left"></i> Voltar ao portal
        </a>

        <div class="login-title">Criar conta gratuita</div>
        <div class="login-subtitle">Portal de Vagas FAPEU</div>

        @if($errors->any())
        <div class="login-alert">
            <strong>Corrija os erros abaixo:</strong>
            <ul>
                @foreach($errors->all() as $erro)
                <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="stepper" id="stepper">
            @foreach(['Conta','Pessoais','Endereço','Formação','Acessibilidade','Questionário'] as $i => $label)
            <div class="step-indicator" data-step-indicator="{{ $i + 1 }}">
                <div class="step-circle">{{ $i + 1 }}</div>
                <div class="step-label">{{ $label }}</div>
            </div>
            @endforeach
        </div>

        <form action="{{ route('candidato.registro.post') }}" method="POST" enctype="multipart/form-data" id="formRegistro" novalidate>
            @csrf
            @if($redirect ?? null)
            <input type="hidden" name="redirect" value="{{ $redirect }}">
            @endif

            {{-- ETAPA 1 — Conta --}}
            <div class="wizard-step active" data-step="1">
                <div id="alertaCpfDuplicado" class="login-warning" style="display:none;">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                    <div>Este CPF já possui cadastro. <a href="{{ route('candidato.login') }}" style="color:#0D9571;font-weight:700;">Entrar na conta</a></div>
                </div>

                <div class="row-g">
                    <div class="col-6 field-group">
                        <label class="field-label">CPF</label>
                        <input type="text" name="cpf" id="cpfReg" class="field" value="{{ old('cpf') }}" maxlength="14" required>
                        <div class="field-error" id="cpfFeedback">@error('cpf'){{ $message }}@enderror</div>
                    </div>
                    <div class="col-6 field-group">
                        <label class="field-label">E-mail</label>
                        <input type="email" name="email" class="field" value="{{ old('email') }}" required>
                        @error('email') <div class="field-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-6 field-group">
                        <label class="field-label">Senha</label>
                        <div class="field-wrapper">
                            <input type="password" name="password" id="senhaInput" class="field" required>
                            <button type="button" class="toggle-pw" id="toggleSenha" aria-label="Mostrar senha">
                                <i class="bi bi-eye" id="olhoIcone"></i>
                            </button>
                        </div>
                        @error('password') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 field-group">
                        <label class="field-label">Confirmar senha</label>
                        <div class="field-wrapper">
                            <input type="password" name="password_confirmation" id="senhaConfirmInput" class="field" required>
                            <button type="button" class="toggle-pw" id="toggleSenhaConfirm" aria-label="Mostrar senha">
                                <i class="bi bi-eye" id="olhoConfirmIcone"></i>
                            </button>
                        </div>
                        <div class="field-feedback" id="senhaConfirmFeedback" style="display:none;"></div>
                    </div>

                    <div class="col-12">
                        <div class="box-muted">
                            <div style="font-size:12px;font-weight:700;color:#3a5a7a;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">A senha deve conter</div>
                            <ul class="rule-list" id="regrasSenha">
                                <li data-regra="minLength"><i class="bi bi-circle"></i>Mínimo de 8 caracteres</li>
                                <li data-regra="maiuscula"><i class="bi bi-circle"></i>1 letra maiúscula</li>
                                <li data-regra="minuscula"><i class="bi bi-circle"></i>1 letra minúscula</li>
                                <li data-regra="numero"><i class="bi bi-circle"></i>1 número</li>
                                <li data-regra="especial"><i class="bi bi-circle"></i>1 caractere especial</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ETAPA 2 — Dados pessoais --}}
            <div class="wizard-step" data-step="2">
                <div class="row-g">
                    <div class="col-12 field-group">
                        <label class="field-label">Nome completo</label>
                        <input type="text" name="nome" class="field" value="{{ old('nome') }}" required>
                        @error('nome') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 field-group">
                        <label class="field-label">Nome social <span class="opt">(opcional)</span></label>
                        <input type="text" name="nome_social" class="field" value="{{ old('nome_social') }}">
                        @error('nome_social') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 field-group">
                        <label class="field-label">Nacionalidade</label>
                        <input type="text" name="nacionalidade" class="field" value="{{ old('nacionalidade', 'Brasileira') }}" required>
                        @error('nacionalidade') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 field-group">
                        <label class="field-label">Telefone <span class="opt"></span></label>
                        <input type="text" name="telefone" id="telefoneReg" class="field" value="{{ old('telefone') }}" maxlength="15">
                        @error('telefone') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- ETAPA 3 — Endereço --}}
            <div class="wizard-step" data-step="3">
                <div class="row-g">
                    <div class="col-4 field-group">
                        <label class="field-label">CEP</label>
                        <input type="text" name="cep" id="cepReg" class="field" value="{{ old('cep') }}" maxlength="9">
                        @error('cep') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-4 field-group">
                        <label class="field-label">UF</label>
                        <input type="text" name="estado" id="estadoReg" class="field" value="{{ old('estado') }}" maxlength="2">
                        @error('estado') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-4 field-group">
                        <label class="field-label">Município</label>
                        <input type="text" name="cidade" id="cidadeReg" class="field" value="{{ old('cidade') }}">
                        @error('cidade') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 field-group">
                        <label class="field-label">Bairro</label>
                        <input type="text" name="bairro" id="bairroReg" class="field" value="{{ old('bairro') }}">
                        @error('bairro') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 field-group">
                        <label class="field-label">Logradouro</label>
                        <input type="text" name="logradouro" id="logradouroReg" class="field" value="{{ old('logradouro') }}">
                        @error('logradouro') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 field-group">
                        <label class="field-label">Número</label>
                        <input type="text" name="numero" class="field" value="{{ old('numero') }}">
                        @error('numero') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 field-group">
                        <label class="field-label">Complemento <span class="opt">(opcional)</span></label>
                        <input type="text" name="complemento" class="field" value="{{ old('complemento') }}">
                        @error('complemento') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- ETAPA 4 — Formação --}}
            <div class="wizard-step" data-step="4">
                <div class="row-g">
                    <div class="col-6 field-group">
                        <label class="field-label">Nível de escolaridade</label>
                        <select name="nivel_escolaridade" class="field-select" required>
                            <option value="">Selecione...</option>
                            @foreach(['medio' => 'Ensino médio', 'tecnico' => 'Técnico', 'graduacao' => 'Graduação', 'pos' => 'Pós-graduação', 'mestrado' => 'Mestrado', 'doutorado' => 'Doutorado'] as $valor => $label)
                            <option value="{{ $valor }}" @selected(old('nivel_escolaridade') === $valor)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('nivel_escolaridade') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 field-group">
                        <label class="field-label">Situação</label>
                        <select name="situacao_curso" id="situacaoCurso" class="field-select" required>
                            <option value="">Selecione...</option>
                            <option value="cursando" @selected(old('situacao_curso') === 'cursando')>Cursando</option>
                            <option value="concluido" @selected(old('situacao_curso') === 'concluido')>Concluído</option>
                        </select>
                        @error('situacao_curso') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 field-group">
                        <label class="field-label">Curso</label>
                        <input type="text" name="curso" class="field" value="{{ old('curso') }}" required>
                        @error('curso') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 field-group">
                        <label class="field-label">Instituição de ensino</label>
                        <input type="text" name="instituicao" class="field" value="{{ old('instituicao') }}" required>
                        @error('instituicao') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 field-group" id="semestreDiv">
                        <label class="field-label">Semestre atual</label>
                        <input type="text" name="semestre" class="field" value="{{ old('semestre') }}">
                        @error('semestre') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6 field-group">
                        <label class="field-label" id="dataConclusaoLabel">Previsão de conclusão</label>
                        <input type="date" name="previsao_conclusao" class="field" value="{{ old('previsao_conclusao') }}" required>
                        @error('previsao_conclusao') <div class="field-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 field-group">
                        <label class="field-label">Currículo <span class="opt">(opcional, PDF até 5MB)</span></label>
                        <div class="drop-zone" id="dropZone">
                            <input type="file" name="curriculo" id="curriculo" accept=".pdf" style="display:none;">
                            <div id="dropContent">
                                <i class="bi bi-cloud-upload-fill" style="font-size:28px;color:#0D9571;"></i>
                                <p style="margin:8px 0 4px;font-weight:600;color:#0f1e32;font-size:14px;">
                                    Arraste o PDF aqui ou <span style="color:#0D9571;cursor:pointer;" onclick="event.stopPropagation();document.getElementById('curriculo').click()">clique para selecionar</span>
                                </p>
                                <p style="margin:0;font-size:12px;color:#a0bdd4;">Apenas PDF — Máximo 5MB</p>
                            </div>
                            <div id="fileSelected" style="display:none;">
                                <i class="bi bi-file-earmark-pdf-fill" style="font-size:28px;color:#0D9571;"></i>
                                <p style="margin:8px 0 0;font-weight:600;color:#0f1e32;font-size:14px;" id="fileName"></p>
                                <button type="button" onclick="clearFile()" style="background:none;border:none;color:#f43f5e;font-size:12px;cursor:pointer;margin-top:4px;">
                                    <i class="bi bi-x-circle"></i> Remover
                                </button>
                            </div>
                        </div>
                        @error('curriculo') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- ETAPA 5 — Acessibilidade --}}
            <div class="wizard-step" data-step="5">
                <p style="font-size:14px;color:#3a5a7a;margin-bottom:16px;">
                    Você necessita ou possui alguma condição de acessibilidade que devemos considerar
                    (ex: deficiência física, visual, auditiva, intelectual, neurodivergência, etc.)?
                </p>

                <div class="field-group">
                    <div class="radio-row">
                        <label class="radio-opt">
                            <input type="radio" name="possui_acessibilidade" value="0" id="acessibilidade_nao" @checked(old('possui_acessibilidade', '0') == '0')>
                            Não se aplica
                        </label>
                        <label class="radio-opt">
                            <input type="radio" name="possui_acessibilidade" value="1" id="acessibilidade_sim" @checked(old('possui_acessibilidade') == '1')>
                            Sim, possuo
                        </label>
                    </div>
                    <div id="acessibilidadeDetalheDiv" style="{{ old('possui_acessibilidade') == '1' ? '' : 'display:none;' }}margin-top:14px;">
                        <label class="field-label">Descreva a acessibilidade necessária</label>
                        <textarea name="acessibilidade_detalhe" id="acessibilidadeDetalheInput" rows="3" class="field-textarea"
                            placeholder="Descreva sua condição e, se aplicável, os recursos de acessibilidade que necessita">{{ old('acessibilidade_detalhe') }}</textarea>
                        @error('acessibilidade_detalhe') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- ETAPA 6 — Questionário + aceites --}}
            <div class="wizard-step" data-step="6">
                <div class="box-muted" style="font-size:13px;color:#3a5a7a;margin-bottom:16px;">
                    <p style="margin:0 0 8px;font-weight:700;">Você é cônjuge, companheiro(a) ou parente — em linha reta ou colateral, por consanguinidade ou afinidade, até o terceiro grau — de:</p>
                    <ul style="margin:0;padding-left:20px;">
                        <li>servidor das instituições apoiadas pela FAPEU, que atue na direção da Fundação;</li>
                        <li>dirigente das instituições apoiadas pela FAPEU;</li>
                        <li>ocupante de cargo de direção superior das instituições apoiadas pela FAPEU;</li>
                        <li>coordenador de projeto administrado pela FAPEU;</li>
                        <li>fiscal de contrato entre a FAPEU e terceiros?</li>
                    </ul>
                </div>

                <div class="field-group">
                    <div class="radio-row">
                        <label class="radio-opt">
                            <input type="radio" name="conflito_interesse" value="0" id="conflito_nao" @checked(old('conflito_interesse', '0') == '0')>
                            Não se aplica
                        </label>
                        <label class="radio-opt">
                            <input type="radio" name="conflito_interesse" value="1" id="conflito_sim" @checked(old('conflito_interesse') == '1')>
                            Sim, se aplica
                        </label>
                    </div>
                    <div id="conflitoDetalheDiv" style="{{ old('conflito_interesse') == '1' ? '' : 'display:none;' }}margin-top:14px;">
                        <label class="field-label">Detalhe a relação</label>
                        <textarea name="conflito_interesse_detalhe" id="conflitoDetalheInput" rows="3" class="field-textarea"
                            placeholder="Descreva o grau de parentesco e a pessoa/cargo relacionado">{{ old('conflito_interesse_detalhe') }}</textarea>
                        @error('conflito_interesse_detalhe') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="box-accent field-group">
                    <label class="check-row">
                        <input type="checkbox" name="codigo_conduta_aceite" id="codigoConduta" value="1" {{ old('codigo_conduta_aceite') ? 'checked' : '' }} required>
                        <span>
                            Li e estou de acordo com o
                            <a href="https://fapeu.org.br/codigoconduta" target="_blank">Código de Conduta da FAPEU</a>.
                        </span>
                    </label>
                    @error('codigo_conduta_aceite') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="box-accent field-group">
                    <label class="check-row">
                        <input type="checkbox" name="lgpd_consentimento" id="lgpd" value="1" {{ old('lgpd_consentimento') ? 'checked' : '' }} required>
                        <span>
                            <strong>Concordo com o armazenamento dos meus dados</strong> de acordo com a
                            <abbr title="Lei Geral de Proteção de Dados">LGPD</abbr> (Lei nº 13.709/2018) e com a
                            <a href="{{ route('politica.privacidade') }}" target="_blank">Política de Privacidade</a> da FAPEU.
                            Meus dados serão utilizados <strong>exclusivamente</strong> para fins de processo seletivo
                            e serão acessados apenas pelos coordenadores dos projetos aos quais me candidatar.
                            Posso revogar este consentimento e solicitar a exclusão dos meus dados a qualquer momento
                            em <em>Meus Dados → Excluir conta</em>.
                        </span>
                    </label>
                    @error('lgpd_consentimento') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Navegação --}}
            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="button" class="btn-outline" id="btnVoltar" style="display:none;" onclick="mudarEtapa(-1)">
                    <i class="bi bi-arrow-left"></i> Voltar
                </button>
                <div class="flex-fill"></div>
                <button type="button" class="btn-login" id="btnAvancar" onclick="mudarEtapa(1)">
                    Avançar <i class="bi bi-arrow-right"></i>
                </button>
                <button type="submit" class="btn-login" id="btnRegistro" style="display:none;">
                    <i class="bi bi-person-check-fill"></i> Finalizar cadastro
                </button>
            </div>
        </form>

        <div class="login-footer-text">
            Já tem conta?
            <a href="{{ route('candidato.login', $redirect ? ['redirect' => $redirect] : []) }}" style="color:#0D9571;font-weight:600;">Entrar</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const TOTAL_ETAPAS = 6;
let etapaAtual = 1;

function atualizarStepper() {
    document.querySelectorAll('.step-indicator').forEach(function(el) {
        const n = parseInt(el.dataset.stepIndicator, 10);
        const circulo = el.querySelector('.step-circle');
        const label = el.querySelector('.step-label');
        circulo.classList.remove('active', 'done');
        label.classList.remove('active', 'done');
        if (n === etapaAtual) {
            circulo.classList.add('active');
            label.classList.add('active');
        } else if (n < etapaAtual) {
            circulo.classList.add('done');
            label.classList.add('done');
        }
    });
}

function mostrarEtapa(n) {
    document.querySelectorAll('.wizard-step').forEach(function(el) {
        el.classList.toggle('active', parseInt(el.dataset.step, 10) === n);
    });
    document.getElementById('btnVoltar').style.display = n === 1 ? 'none' : 'inline-flex';
    document.getElementById('btnAvancar').style.display = n === TOTAL_ETAPAS ? 'none' : 'inline-flex';
    document.getElementById('btnRegistro').style.display = n === TOTAL_ETAPAS ? 'inline-flex' : 'none';
    atualizarStepper();
}

function validarEtapaAtual() {
    const step = document.querySelector('.wizard-step[data-step="' + etapaAtual + '"]');
    const campos = step.querySelectorAll('input, select, textarea');
    let valido = true;
    campos.forEach(function(campo) {
        if (campo.offsetParent === null) return;
        if (!campo.checkValidity()) {
            campo.reportValidity();
            valido = false;
        }
    });
    if (!valido) return false;

    if (etapaAtual === 1) {
        if (document.getElementById('cpfReg').classList.contains('is-invalid')) return false;
        if (document.getElementById('alertaCpfDuplicado').style.display !== 'none') return false;
        if (!senhaAtendeRegras()) return false;
        if (document.getElementById('senhaInput').value !== document.getElementById('senhaConfirmInput').value) {
            mostrarErroConfirmacaoSenha();
            return false;
        }
    }

    return true;
}

function mudarEtapa(direcao) {
    if (direcao > 0 && !validarEtapaAtual()) return;
    etapaAtual = Math.min(TOTAL_ETAPAS, Math.max(1, etapaAtual + direcao));
    mostrarEtapa(etapaAtual);
}

// ── Força de senha ──────────────────────────────────────────────
const regrasSenha = {
    minLength: v => v.length >= 8,
    maiuscula: v => /[A-Z]/.test(v),
    minuscula: v => /[a-z]/.test(v),
    numero:    v => /[0-9]/.test(v),
    especial:  v => /[^A-Za-z0-9]/.test(v),
};

function senhaAtendeRegras() {
    const valor = document.getElementById('senhaInput').value;
    return Object.values(regrasSenha).every(fn => fn(valor));
}

function atualizarRegrasSenha() {
    const valor = document.getElementById('senhaInput').value;
    Object.keys(regrasSenha).forEach(function(chave) {
        const li = document.querySelector('#regrasSenha li[data-regra="' + chave + '"]');
        const ok = regrasSenha[chave](valor);
        const icone = li.querySelector('i');
        li.classList.toggle('ok', ok);
        icone.className = ok ? 'bi bi-check-circle-fill' : 'bi bi-circle';
    });
}

function mostrarErroConfirmacaoSenha() {
    const feedback = document.getElementById('senhaConfirmFeedback');
    const input = document.getElementById('senhaConfirmInput');
    feedback.textContent = 'As senhas não coincidem.';
    feedback.style.color = '#f43f5e';
    feedback.style.display = 'block';
    input.classList.add('is-invalid');
    input.classList.remove('is-valid');
}

document.getElementById('senhaInput').addEventListener('input', function() {
    atualizarRegrasSenha();
    validarConfirmacaoSenha();
});
document.getElementById('senhaConfirmInput').addEventListener('input', validarConfirmacaoSenha);

function validarConfirmacaoSenha() {
    const senha = document.getElementById('senhaInput').value;
    const confirmacao = document.getElementById('senhaConfirmInput').value;
    const feedback = document.getElementById('senhaConfirmFeedback');
    const input = document.getElementById('senhaConfirmInput');
    if (confirmacao.length === 0) {
        feedback.style.display = 'none';
        input.classList.remove('is-invalid', 'is-valid');
    } else if (senha === confirmacao) {
        feedback.textContent = 'As senhas coincidem.';
        feedback.style.color = '#0D9571';
        feedback.style.display = 'block';
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    } else {
        mostrarErroConfirmacaoSenha();
    }
}

// ── Olhinho: senha e confirmação ─────────────────────────────────
function montarToggle(botaoId, inputId, iconeId) {
    const botao = document.getElementById(botaoId);
    const input = document.getElementById(inputId);
    const icone = document.getElementById(iconeId);
    botao.addEventListener('click', function() {
        input.type = input.type === 'password' ? 'text' : 'password';
        icone.className = input.type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
}
montarToggle('toggleSenha', 'senhaInput', 'olhoIcone');
montarToggle('toggleSenhaConfirm', 'senhaConfirmInput', 'olhoConfirmIcone');

// ── CPF: máscara + checagem de duplicidade ──────────────────────
const cpfInput = document.getElementById('cpfReg');
cpfInput.addEventListener('input', function() {
    let v = this.value.replace(/\D/g, '').slice(0, 11);
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    this.value = v;
    this.classList.remove('is-invalid', 'is-valid');
    document.getElementById('alertaCpfDuplicado').style.display = 'none';
});

function validarCpfAlgoritmo(cpf) {
    if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;
    for (let t = 9; t < 11; t++) {
        let soma = 0;
        for (let i = 0; i < t; i++) soma += parseInt(cpf[i]) * (t + 1 - i);
        const resto = soma % 11;
        if (parseInt(cpf[t]) !== (resto < 2 ? 0 : 11 - resto)) return false;
    }
    return true;
}

cpfInput.addEventListener('blur', async function() {
    const digits = this.value.replace(/\D/g, '');
    const feedback = document.getElementById('cpfFeedback');
    const alerta = document.getElementById('alertaCpfDuplicado');
    alerta.style.display = 'none';

    if (digits.length === 0) {
        this.classList.remove('is-invalid', 'is-valid');
        feedback.textContent = '';
        return;
    }
    if (!validarCpfAlgoritmo(digits)) {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
        feedback.textContent = 'CPF inválido.';
        return;
    }
    this.classList.remove('is-invalid');
    this.classList.add('is-valid');
    feedback.textContent = '';

    try {
        const r = await fetch(`{{ route('candidato.registro.verificar-cpf') }}?cpf=${digits}`);
        if (!r.ok) return;
        const d = await r.json();
        if (d.existe) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
            alerta.style.display = 'flex';
        }
    } catch (e) {}
});

// ── Telefone ─────────────────────────────────────────────────────
document.getElementById('telefoneReg').addEventListener('input', function() {
    let v = this.value.replace(/\D/g, '').slice(0, 11);
    if (v.length <= 10) v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    else v = v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
    this.value = v;
});

// ── CEP: máscara + ViaCEP ────────────────────────────────────────
document.getElementById('cepReg').addEventListener('input', function() {
    let v = this.value.replace(/\D/g, '').slice(0, 8);
    v = v.replace(/(\d{5})(\d{1,3})$/, '$1-$2');
    this.value = v;
});

document.getElementById('cepReg').addEventListener('blur', async function() {
    const cep = this.value.replace(/\D/g, '');
    if (cep.length !== 8) return;
    try {
        const r = await fetch(`/api/cep/${cep}`);
        if (!r.ok) return;
        const d = await r.json();
        if (d.logradouro !== undefined) document.getElementById('logradouroReg').value = d.logradouro;
        if (d.bairro !== undefined) document.getElementById('bairroReg').value = d.bairro;
        if (d.cidade !== undefined) document.getElementById('cidadeReg').value = d.cidade;
        if (d.estado !== undefined) document.getElementById('estadoReg').value = d.estado;
    } catch (e) {}
});

// ── Formação: cursando x concluído ──────────────────────────────
document.getElementById('situacaoCurso').addEventListener('change', function() {
    const concluido = this.value === 'concluido';
    document.getElementById('dataConclusaoLabel').textContent = concluido ? 'Data de conclusão' : 'Previsão de conclusão';
    document.getElementById('semestreDiv').style.display = concluido ? 'none' : '';
});

// ── Drop zone de currículo ───────────────────────────────────────
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

// ── Acessibilidade ───────────────────────────────────────────────
document.querySelectorAll('input[name="possui_acessibilidade"]').forEach(function(r) {
    r.addEventListener('change', function() {
        const mostrar = this.value === '1';
        document.getElementById('acessibilidadeDetalheDiv').style.display = mostrar ? '' : 'none';
        document.getElementById('acessibilidadeDetalheInput').required = mostrar;
    });
});

// ── Questionário (conflito de interesse) ─────────────────────────
document.querySelectorAll('input[name="conflito_interesse"]').forEach(function(r) {
    r.addEventListener('change', function() {
        const mostrar = this.value === '1';
        document.getElementById('conflitoDetalheDiv').style.display = mostrar ? '' : 'none';
        document.getElementById('conflitoDetalheInput').required = mostrar;
    });
});

// ── Submit ───────────────────────────────────────────────────────
document.getElementById('formRegistro').addEventListener('submit', function(e) {
    if (!validarEtapaAtual()) {
        e.preventDefault();
        return;
    }
    const btn = document.getElementById('btnRegistro');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" style="width:14px;height:14px;"></span>Criando conta...';
});

mostrarEtapa(etapaAtual);
</script>

</body>
</html>
