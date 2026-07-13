<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal de Vagas - FAPEU</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('imagens/fapeu_ico.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Reddit+Sans:ital,wght@0,200..900;1,200..900&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        html,
        body {
            font-family: "Reddit Sans", sans-serif;
            background: #F8F9FA;
            color: #3E3E3F;
            font-size: 18px;
        }

        a {
            text-decoration: none !important;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
            appearance: textfield;
        }

        .text-principal {
            color: #0D9571;
        }

        .bg-principal {
            background: #0D9571 !important;
        }

        .bg-principal2 {
            background: #074635 !important;
        }

        /* Navbar */
        .navbar-fapeu {
            background: #074635;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .navbar-brand-text {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
        }

        .navbar-brand-sub {
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.65);
        }

        .nav-link-pub {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .nav-link-pub:hover {
            color: #fff !important;
        }

        /* Botões */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-principal {
            background: #0D9571;
            color: #fff;
            font-weight: 600;
            border: 2px solid #0D9571;
            border-radius: 8px;
            padding: 10px 28px;
            transition: all 0.3s ease;
        }

        .btn-principal:hover {
            background: #0C8061;
            border-color: #0C8061;
            color: #fff;
            box-shadow: 0 8px 20px rgba(6, 85, 26, 0.3);
        }

        .btn-outline-principal {
            color: #0D9571;
            border: 2px solid #0D9571;
            border-radius: 8px;
            padding: 10px 28px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-principal:hover {
            background: #0D9571;
            color: #fff;
        }

        /* Cards */
        .card-vaga {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
            transition: all 0.3s ease;
        }

        /* Badges */
        .badge-tipo {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-estagio {
            background: rgba(111, 66, 193, 0.1);
            color: #6F42C1;
        }

        .badge-emprego {
            background: rgba(13, 110, 253, 0.1);
            color: #0D6EFD;
        }

        .badge-bolsa {
            background: rgba(13, 149, 113, 0.1);
            color: #0D9571;
        }

        .badge-presencial,
        .badge-remoto,
        .badge-hibrido {
            background: #EBEBEB;
            color: #555555;
        }

        /* Footer */
        .footer-pub {
            background: #074635;
            color: rgba(255, 255, 255, 0.65);
            padding: 2.5rem 0 1.5rem;
        }

        .footer-copy {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.4);
        }

        hr {
            border-color: rgba(255, 255, 255, 0.1);
        }

        /* Form */
        .form-control:focus,
        .form-select:focus {
            border-color: #0D9571;
            box-shadow: 0 0 0 0.2rem rgba(13, 149, 113, 0.25);
        }

        .form-label {
            color: #495057;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        /* Alerts */
        .alert-principal {
            border-left: 4px solid #0D9571;
            background: rgba(13, 149, 113, 0.05);
            border-color: rgba(13, 149, 113, 0.2);
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-fapeu navbar-expand-lg py-2 px-3">
        <div class="container-xl">
            <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="{{ asset('imagens/fapeulogoverde.png') }}" alt="FAPEU" style="height:48px;width:auto;">
                <div>
                    <div class="navbar-brand-text">Portal de Vagas</div>
                    <div class="navbar-brand-sub">FAPEU</div>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navPublico">
                <i class="bi bi-list text-white fs-4"></i>
            </button>

            <div class="collapse navbar-collapse" id="navPublico">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2 mt-3 mt-lg-0">

                    @auth('candidato')
                    {{-- Menu candidato autenticado --}}
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link nav-link-pub">
                            <i class="bi bi-house me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('vagas.publicas.index') }}" class="nav-link nav-link-pub">
                            <i class="bi bi-briefcase me-1"></i>Procurar vagas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('candidato.candidaturas.index') }}" class="nav-link nav-link-pub">
                            <i class="bi bi-file-earmark-check me-1"></i>Minhas candidaturas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('candidato.perfil.edit') }}" class="nav-link nav-link-pub">
                            <i class="bi bi-person-gear me-1"></i>Meus dados
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <div class="dropdown">
                            <button class="btn btn-sm d-flex align-items-center gap-2"
                                style="background:rgba(255,255,255,0.12);color:#fff;border:1.5px solid rgba(255,255,255,0.25);border-radius:20px;padding:6px 14px;font-size:0.875rem;font-weight:600;"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                <span>{{ Str::words(Auth::guard('candidato')->user()->nome, 1, '') }}</span>
                                <i class="bi bi-chevron-down" style="font-size:0.7rem;"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width:200px;border-radius:12px;border:1px solid #eee;">
                                <li>
                                    <div class="px-3 py-2" style="border-bottom:1px solid #f0f0f0;">
                                        <div style="font-weight:700;font-size:0.875rem;color:#2C4A44;">{{ Auth::guard('candidato')->user()->nome }}</div>
                                        <div style="font-size:0.78rem;color:#6c757d;">{{ Auth::guard('candidato')->user()->email }}</div>
                                    </div>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('candidato.perfil.edit') }}">
                                        <i class="bi bi-person-gear text-principal"></i> Meus dados
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('candidato.candidaturas.index') }}">
                                        <i class="bi bi-file-earmark-check text-principal"></i> Minhas candidaturas
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('candidato.logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger">
                                            <i class="bi bi-box-arrow-right"></i> Sair
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @else
                    {{-- Visitante --}}
                    <li class="nav-item">
                        <a href="{{ route('vagas.publicas.index') }}" class="nav-link nav-link-pub">
                            <i class="bi bi-briefcase me-1"></i>Vagas
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a href="{{ route('candidato.login') }}"
                            style="display:inline-flex;align-items:center;gap:0.4rem;padding:7px 18px;border-radius:20px;background:rgba(255,255,255,0.12);color:#fff;font-size:0.875rem;font-weight:600;border:1.5px solid rgba(255,255,255,0.25);transition:all 0.3s;">
                            <i class="bi bi-person-circle"></i> Entrar
                        </a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    {{-- Conteúdo --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="footer-pub mt-5">
        <div class="container-xl">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="{{ asset('imagens/fapeulogoverde.png') }}" alt="FAPEU" style="height:48px;width:auto;">
                        <div>
                            <div class="navbar-brand-text">Portal de Vagas</div>
                            <div class="navbar-brand-sub">FAPEU</div>
                        </div>
                    </div>
                    <p style="font-size:0.83rem;line-height:1.6;">
                        Conectando estudantes a oportunidades reais de estágio e emprego em projetos administrados pela Fundação.
                    </p>
                </div>
                <div class="col-6 col-lg-3 offset-lg-1">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:rgba(255,255,255,0.45);margin-bottom:0.75rem;">Navegação</div>
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.4rem;">
                        <li><a href="{{ route('home') }}" style="color:rgba(255,255,255,0.65);font-size:0.85rem;">Início</a></li>
                        <li><a href="{{ route('vagas.publicas.index') }}" style="color:rgba(255,255,255,0.65);font-size:0.85rem;">Ver vagas</a></li>
                        <li><a href="{{ route('politica.privacidade') }}" style="color:rgba(255,255,255,0.65);font-size:0.85rem;">Política de Privacidade</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-3">
                    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:rgba(255,255,255,0.45);margin-bottom:0.75rem;">Candidatos</div>
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.4rem;">
                        @auth('candidato')
                        <li><a href="{{ route('candidato.candidaturas.index') }}" style="color:rgba(255,255,255,0.65);font-size:0.85rem;">Minhas candidaturas</a></li>
                        <li><a href="{{ route('candidato.perfil.edit') }}" style="color:rgba(255,255,255,0.65);font-size:0.85rem;">Meus dados</a></li>
                        @else
                        <li><a href="{{ route('candidato.login') }}" style="color:rgba(255,255,255,0.65);font-size:0.85rem;">Entrar</a></li>
                        <li><a href="{{ route('candidato.registro') }}" style="color:rgba(255,255,255,0.65);font-size:0.85rem;">Criar conta</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
            <hr class="mb-3 mt-0">
            <a href="https://www.fapeu.org.br" target="_blank" class="d-block text-center text-decoration-none">
                <p class="footer-copy mb-0 text-center">© {{ date('Y') }} FAPEU — Todos os direitos reservados.</p>
            </a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>