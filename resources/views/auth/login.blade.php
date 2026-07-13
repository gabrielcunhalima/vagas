<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> FAPEU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Reddit+Sans:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        html, body { font-family: "Reddit Sans", sans-serif; height: 100%; margin: 0; }
        a { text-decoration: none !important; }

        .login-bg {
            position: fixed; inset: 0; z-index: -1;
            background: linear-gradient(90deg,
                rgba(47,110,82,1) 0%, rgba(24,140,109,1) 12%, rgba(33,145,112,1) 26%,
                rgba(47,168,112,1) 57%, rgba(65,150,113,1) 75%,
                rgba(43,140,113,1) 91%, rgba(3,115,72,1) 100%);
            background-size: 400% 400%;
            animation: gradientShift 22s ease infinite;
        }
        .login-bg::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0;
            width: 100%; height: 40%;
            background: linear-gradient(180deg, transparent 0%, rgba(7,63,7,0.3) 100%);
        }
        @keyframes gradientShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-card {
            border: none; border-radius: 15px; overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            width: 100%; max-width: 420px;
        }
        .login-header {
            background: linear-gradient(45deg, #0d9571, #0c8061);
            padding: 2rem 2rem 1.5rem; text-align: center;
        }
        .login-body { background: #fff; padding: 2rem; }

        .form-control:focus {
            border-color: #0D9571;
            box-shadow: 0 0 0 0.2rem rgba(13,149,113,0.25);
        }
        .btn-login {
            background: #0D9571; color: #fff; font-weight: 600;
            border: none; border-radius: 8px; padding: 12px;
            width: 100%; font-size: 1rem;
            box-shadow: 0 4px 15px rgba(6,85,26,0.25);
            transition: all 0.3s ease;
        }
        .btn-login:hover { background: #0C8061; box-shadow: 0 8px 20px rgba(6,85,26,0.35); color: #fff; }
        .btn-login:active { transform: translateY(2px); }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 p-3">
    <div class="login-bg"></div>

    <div class="login-card">
        <div class="login-header">
            <div style="margin:0 auto 0.75rem;text-align:center;">
                <img src="{{ asset('imagens/fapeulogoverde.png') }}" alt="FAPEU" style="height:60px;width:auto;">
            </div>
            <!-- dps definir nome do portal e como vai chama -->
            <h5 style="color:#fff;font-weight:700;margin:0 0 0.2rem;">Portal de Vagas</h5>
            <p style="color:rgba(255,255,255,0.75);font-size:0.82rem;margin:0;">FAPEU</p>
        </div>

        <div class="login-body">
            @if($errors->any())
                <div class="alert alert-danger py-2 mb-3" style="font-size:0.875rem;border-left:4px solid #DC3545;border-radius:6px;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" style="font-weight:600;font-size:0.875rem;color:#495057;">E-mail</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:#F8F9FA;border-right:none;">
                            <i class="bi bi-envelope" style="color:#6C757D;"></i>
                        </span>
                        <input type="email" name="email" class="form-control" style="border-left:none;"
                               value="{{ old('email') }}" placeholder="seu@email.com" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label" style="font-weight:600;font-size:0.875rem;color:#495057;">Senha</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:#F8F9FA;border-right:none;">
                            <i class="bi bi-lock" style="color:#6C757D;"></i>
                        </span>
                        <input type="password" name="password" id="password" class="form-control" style="border-left:none;border-right:none;"
                               placeholder="Sua senha" required>
                        <button type="button" class="input-group-text" style="background:#F8F9FA;cursor:pointer;border-left:none;"
                                onclick="toggleSenha()">
                            <i class="bi bi-eye" id="eyeIcon" style="color:#6C757D;"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                               style="accent-color:#0D9571;">
                        <label class="form-check-label" for="remember" style="font-size:0.875rem;color:#6C757D;">
                            Lembrar-me
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Entrar
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('vagas.publicas.index') }}" style="font-size:0.82rem;color:#6C757D;">
                    <i class="bi bi-arrow-left me-1"></i>Voltar ao portal público
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSenha() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

    </script>
</body>
</html>
