<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" type="image/x-icon" href="{{ asset('imagens/fapeu_ico.ico') }}">
    <title>Entrar — Portal de Vagas FAPEU</title>

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: 'Outfit', sans-serif; min-height: 100vh; display: flex; background: #f0f4f8; }

        .login-left { display: none; width: 46%; background: linear-gradient(145deg, #074635 0%, #0D9571 55%, #1ab88a 100%); position: relative; overflow: hidden; flex-direction: column; align-items: center; justify-content: center; padding: 48px 40px; }
        @media (min-width: 992px) { .login-left { display: flex; } }
        .dot-pattern { position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 24px 24px; }
        .blob { position: absolute; border-radius: 50%; background: rgba(255,255,255,0.06); }
        .blob-1 { width: 300px; height: 300px; top: -80px; right: -80px; }
        .blob-2 { width: 200px; height: 200px; bottom: 60px; left: -60px; }
        .blob-3 { width: 150px; height: 150px; bottom: 200px; right: 30px; }

        .login-brand-card { position: relative; z-index: 1; text-align: center; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 24px; padding: 40px 36px; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); max-width: 340px; width: 100%; }
        .login-brand-card img { height: 80px; filter: drop-shadow(0 4px 16px rgba(0,0,0,0.2)); margin-bottom: 20px; }
        .login-brand-title { font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 6px; }
        .login-brand-sub { font-size: 15px; color: rgba(255,255,255,0.7); line-height: 1.5; }

        .login-right { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 24px; background: #fff; }
        .login-form-box { width: 100%; max-width: 400px; animation: fadeInUp 0.55s ease-out both; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }

        .login-logo-mobile { display: flex; flex-direction: column; align-items: center; margin-bottom: 32px; }
        @media (min-width: 992px) { .login-logo-mobile { display: none; } }
        .login-logo-mobile img { height: 56px; }

        .login-title { font-size: 22px; font-weight: 700; color: #0f1e32; margin-bottom: 4px; }
        .login-subtitle { font-size: 16px; color: #7a9ab5; margin-bottom: 28px; }

        .field-label { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .7px; color: #3a5a7a; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
        .field { width: 100%; padding: 11px 14px; border: 1.5px solid #d0dce8; border-radius: 12px; font-family: 'Outfit', sans-serif; font-size: 14px; color: #0f1e32; background: #f7fafc; outline: none; transition: border-color .15s, box-shadow .15s; }
        .field:focus { border-color: #0D9571; background: #fff; box-shadow: 0 0 0 3px rgba(13,149,113,0.12); }
        input:-webkit-autofill { -webkit-box-shadow: 0 0 0 1000px #f7fafc inset !important; -webkit-text-fill-color: #0f1e32 !important; }

        .field-wrapper { position: relative; }
        .field-wrapper .field { padding-right: 40px; }
        .toggle-pw { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #7a9ab5; font-size: 16px; background: none; border: none; padding: 0; }
        .toggle-pw:hover { color: #0D9571; }

        .btn-login { width: 100%; padding: 14px 20px; background: linear-gradient(135deg, #0D9571, #0C8061); color: #fff; font-family: 'Outfit', sans-serif; font-size: 14px; font-weight: 700; border: none; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all .2s; box-shadow: 0 4px 14px rgba(13,149,113,.3); margin-top: 8px; }
        .btn-login:hover { background: linear-gradient(135deg, #0C8061, #074635); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(13,149,113,.4); }
        .btn-login:active { transform: translateY(0); }
        .btn-login.shake { animation: shake 0.4s ease; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-4px); } 75% { transform: translateX(4px); } }

        .login-alert { background: #fff1f2; border: 1px solid #fecdd3; border-left: 4px solid #f43f5e; border-radius: 10px; padding: 12px 16px; color: #be123c; font-size: 13px; display: flex; align-items: flex-start; gap: 8px; margin-bottom: 20px; }
        .login-info { background: #ecfdf5; border: 1px solid #a7f3d0; border-left: 4px solid #0D9571; border-radius: 10px; padding: 12px 16px; color: #065f46; font-size: 13px; display: flex; align-items: flex-start; gap: 8px; margin-bottom: 20px; }
        .login-footer-text { text-align: center; font-size: 13px; color: #a0bdd4; margin-top: 28px; }
        .login-back { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #7a9ab5; margin-bottom: 20px; }
        .login-back:hover { color: #0D9571; }
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
        <div class="login-brand-title">Portal de vagas FAPEU</div>
        <!-- <div class="login-brand-sub">Acompanhe suas candidaturas e<br>candidate-se com um clique</div> -->
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

        <div class="login-title">Acesse sua conta</div>
        <div class="login-subtitle">Portal de Vagas FAPEU</div>

        @if(session('info'))
        <div class="login-info">
            <i class="bi bi-info-circle-fill mt-1" style="flex-shrink:0;"></i>
            <div>{{ session('info') }}</div>
        </div>
        @endif

        @if($errors->any())
        <div class="login-alert">
            <i class="bi bi-exclamation-circle-fill mt-1" style="flex-shrink:0;"></i>
            <div>{{ $errors->first() }}</div>
        </div>
        @endif

        <form action="{{ route('candidato.login.post') }}" method="POST" id="loginForm" novalidate>
            @csrf
            @if($redirect ?? null)
            <input type="hidden" name="redirect" value="{{ $redirect }}">
            @endif

            <div class="mb-4">
                <label class="field-label"><i class="bi bi-envelope-fill"></i> E-mail</label>
                <input type="email" name="email" id="email" class="field"
                    value="{{ old('email') }}" autocomplete="username" autofocus required>
            </div>

            <div class="mb-4">
                <label class="field-label"><i class="bi bi-lock-fill"></i> Senha</label>
                <div class="field-wrapper">
                    <input type="password" name="password" id="password" class="field"
                        autocomplete="current-password" required>
                    <button type="button" class="toggle-pw" id="togglePw" aria-label="Mostrar senha">
                        <i class="bi bi-eye" id="togglePwIcon"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3 d-flex align-items-center">
                <input type="checkbox" name="remember" id="remember" style="accent-color:#0D9571;margin-right:8px;">
                <label for="remember" style="font-size:13px;color:#7a9ab5;">Lembrar-me</label>
            </div>

            <button type="submit" class="btn-login" id="btnLogin">
                <i class="bi bi-box-arrow-in-right"></i> Entrar
            </button>
        </form>

        <div class="login-footer-text">
            Não tem conta?
            <a href="{{ route('candidato.registro', $redirect ? ['redirect' => $redirect] : []) }}" style="color:#0D9571;font-weight:600;">Cadastre-se gratuitamente</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const toggleBtn  = document.getElementById('togglePw');
const pwInput    = document.getElementById('password');
const toggleIcon = document.getElementById('togglePwIcon');
toggleBtn.addEventListener('click', () => {
    const visible = pwInput.type === 'text';
    pwInput.type  = visible ? 'password' : 'text';
    toggleIcon.className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
});

const loginForm = document.getElementById('loginForm');
const btnLogin  = document.getElementById('btnLogin');
loginForm.addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        btnLogin.classList.add('shake');
        setTimeout(() => btnLogin.classList.remove('shake'), 500);
    } else {
        btnLogin.innerHTML = '<span class="spinner-border spinner-border-sm me-2" style="width:14px;height:14px;"></span>Entrando...';
        btnLogin.disabled = true;
    }
});
</script>

</body>
</html>
