<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FAPEU</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Reddit+Sans:ital,wght@0,200..900;1,200..900&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ─── Base ─────────────────────────────────────────── */
        html, body {
            font-family: "Reddit Sans", sans-serif;
            background: #F8F9FA;
            color: #3E3E3F;
        }

        a { text-decoration: none !important; }

        /* ─── Cores utilitárias ─────────────────────────────── */
        .text-principal  { color: #0D9571; }
        .text-cinza      { color: #6C757D; }
        .bg-principal    { background: #0D9571 !important; }
        .bg-principal2   { background: #074635 !important; }

        /* ─── Gradiente animado de fundo (hero) ─────────────── */
        .dynamic-gradient-bg {
            position: absolute;
            inset: 0;
            z-index: 0;
            background: linear-gradient(
                90deg,
                rgba(47,110,82,1)   0%,
                rgba(24,140,109,1) 12%,
                rgba(33,145,112,1) 26%,
                rgba(47,168,112,1) 57%,
                rgba(65,150,113,1) 75%,
                rgba(43,140,113,1) 91%,
                rgba(3,115,72,1)  100%
            );
            background-size: 400% 400%;
            animation: gradientShift 22s ease infinite;
        }

        .dynamic-gradient-bg::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0;
            width: 100%; height: 40%;
            background: linear-gradient(180deg, transparent 0%, rgba(7,63,7,0.35) 100%);
        }

        @keyframes gradientShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* ─── Navbar ────────────────────────────────────────── */
        .navbar-fapeu {
            position: relative;
            z-index: 10;
            background: rgba(7, 70, 53, 0.55);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .navbar-brand-text {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            color: #fff;
        }

        .navbar-brand-sub {
            font-size: 0.75rem;
            font-weight: 400;
            color: rgba(255,255,255,0.75);
            letter-spacing: 0.3px;
        }

        .nav-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 7px 18px;
            border-radius: 25px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.3s ease;
            color: rgba(255,255,255,0.9);
            border: 1.5px solid rgba(255,255,255,0.25);
        }

        .nav-pill:hover {
            background: rgba(255,255,255,0.15);
            color: #fff;
            border-color: rgba(255,255,255,0.5);
        }

        .nav-pill-primary {
            background: #0D9571;
            border-color: #0D9571;
            color: #fff;
            box-shadow: 0 4px 15px rgba(6,85,26,0.3);
        }

        .nav-pill-primary:hover {
            background: #0C8061;
            border-color: #0C8061;
            box-shadow: 0 8px 20px rgba(6,85,26,0.4);
            color: #fff;
        }

        /* ─── Hero ──────────────────────────────────────────── */
        .hero-section {
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .hero-content {
            position: relative;
            z-index: 5;
            flex: 1;
            display: flex;
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            padding: 6px 16px;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }

        .hero-title {
            font-size: clamp(2.2rem, 5vw, 4rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.1;
            letter-spacing: -1px;
            margin-bottom: 1.25rem;
        }

        .hero-title span {
            color: rgba(255,255,255,0.65);
        }

        .hero-sub {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.82);
            line-height: 1.6;
            max-width: 520px;
            margin-bottom: 2.5rem;
        }

        .hero-btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .btn-hero-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 13px 32px;
            background: #fff;
            color: #0D9571;
            font-weight: 700;
            font-size: 1rem;
            border-radius: 50px;
            border: 2px solid #fff;
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .btn-hero-primary:hover {
            background: #f0fdf8;
            box-shadow: 0 12px 40px rgba(0,0,0,0.25);
            /* transform: translateY(-2px); */
            color: #0C8061;
        }

        .btn-hero-outline {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 13px 32px;
            background: transparent;
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 50px;
            border: 2px solid rgba(255,255,255,0.5);
            transition: all 0.3s ease;
        }

        .btn-hero-outline:hover {
            background: rgba(255,255,255,0.12);
            border-color: rgba(255,255,255,0.8);
            color: #fff;
        }

        /* ─── Stats flutuantes ──────────────────────────────── */
        .hero-stats {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.15);
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 1.75rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.65);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        /* ─── Card flutuante lateral ────────────────────────── */
        .hero-card-float {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 2rem;
            animation: floatCard 6s ease-in-out infinite;
        }

        @keyframes floatCard {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-12px); }
        }

        .vaga-mini-card {
            background: rgba(255,255,255,0.95);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }

        .vaga-mini-card:last-child { margin-bottom: 0; }

        .vaga-mini-card:hover {
            /* transform: translateX(4px); */
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }

        .vaga-mini-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .vaga-mini-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: #3E3E3F;
            margin-bottom: 2px;
            line-height: 1.2;
        }

        .vaga-mini-meta {
            font-size: 0.75rem;
            color: #6C757D;
        }

        .vaga-mini-tag {
            margin-left: auto;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }

        /* ─── Scroll indicator ──────────────────────────────── */
        .scroll-indicator {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255,255,255,0.6);
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            animation: bounce 2s ease infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50%       { transform: translateX(-50%) translateY(6px); }
        }

        /* ─── Seção de funcionalidades ──────────────────────── */
        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(13,149,113,0.08);
            color: #0D9571;
            padding: 5px 14px;
            border-radius: 25px;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: clamp(1.6rem, 3vw, 2.5rem);
            font-weight: 800;
            color: #2C4A44;
            line-height: 1.15;
            letter-spacing: -0.5px;
        }

        .section-sub {
            font-size: 1rem;
            color: #6C757D;
            line-height: 1.6;
            max-width: 540px;
        }

        /* ─── Feature cards ─────────────────────────────────── */
        .feature-card {
            background: #fff;
            border: none;
            border-radius: 16px;
            padding: 2rem 1.75rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 4px; height: 100%;
            background: #0D9571;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            /* transform: translateY(-6px); */
            box-shadow: 0 12px 35px rgba(0,0,0,0.12);
        }

        .feature-card:hover::before { opacity: 1; }

        .feature-icon-wrap {
            width: 56px;
            height: 56px;
            background: rgba(13,149,113,0.1);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #0D9571;
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon-wrap {
            background: #0D9571;
            color: #fff;
        }

        .feature-card h5 {
            font-size: 1rem;
            font-weight: 700;
            color: #2C4A44;
            margin-bottom: 0.5rem;
        }

        .feature-card p {
            font-size: 0.88rem;
            color: #6C757D;
            line-height: 1.6;
            margin: 0;
        }

        /* ─── CTA cards (Publicar / Buscar) ─────────────────── */
        .cta-card {
            border-radius: 20px;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .cta-card-empresas {
            background: linear-gradient(135deg, #074635 0%, #0D9571 100%);
            color: #fff;
        }

        .cta-card-candidatos {
            background: #fff;
            border: 2px solid #E9ECEF;
            color: #3E3E3F;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        }

        .cta-card-pattern {
            position: absolute;
            top: -30px; right: -30px;
            width: 180px; height: 180px;
            border-radius: 50%;
            opacity: 0.06;
            background: #fff;
        }

        .cta-card h3 {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 0.75rem;
        }

        .cta-card p {
            font-size: 0.95rem;
            line-height: 1.6;
            opacity: 0.85;
            margin-bottom: 2rem;
            flex: 1;
        }

        .cta-card-candidatos p { opacity: 1; color: #6C757D; }

        .btn-cta-white {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 12px 28px;
            background: #fff;
            color: #0D9571;
            font-weight: 700;
            font-size: 0.95rem;
            border-radius: 50px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
            transition: all 0.3s ease;
            align-self: flex-start;
        }

        .btn-cta-white:hover {
            background: #f0fdf8;
            /* transform: translateY(-2px); */
            box-shadow: 0 8px 25px rgba(0,0,0,0.18);
            color: #0C8061;
        }

        .btn-cta-green {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 12px 28px;
            background: #0D9571;
            color: #fff;
            font-weight: 700;
            font-size: 0.95rem;
            border-radius: 50px;
            border: none;
            box-shadow: 0 4px 15px rgba(6,85,26,0.2);
            transition: all 0.3s ease;
            align-self: flex-start;
        }

        .btn-cta-green:hover {
            background: #0C8061;
            /* transform: translateY(-2px); */
            box-shadow: 0 8px 25px rgba(6,85,26,0.3);
            color: #fff;
        }

        /* ─── Vagas em destaque ─────────────────────────────── */
        .vaga-card {
            background: #fff;
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .vaga-card:hover {
            /* transform: translateY(-5px); */
            box-shadow: 0 12px 35px rgba(0,0,0,0.12);
        }

        .vaga-card-header {
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 1px solid #F1F5F4;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .empresa-logo {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
        }

        .vaga-titulo {
            font-size: 0.95rem;
            font-weight: 700;
            color: #2C4A44;
            line-height: 1.3;
            margin-bottom: 3px;
        }

        .vaga-empresa {
            font-size: 0.8rem;
            color: #6C757D;
            font-weight: 500;
        }

        .vaga-card-body {
            padding: 1rem 1.5rem;
        }

        .vaga-info-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .vaga-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: #F8F9FA;
            color: #495057;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .vaga-chip i { color: #0D9571; }

        .vaga-card-footer {
            padding: 1rem 1.5rem;
            background: #F8F9FA;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .vaga-deadline {
            font-size: 0.75rem;
            color: #6C757D;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .btn-ver-vaga {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 6px 16px;
            background: #0D9571;
            color: #fff;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 20px;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-ver-vaga:hover {
            background: #0C8061;
            color: #fff;
            /* transform: translateX(2px); */
        }

        /* ─── Badge de status ───────────────────────────────── */
        .badge-novo {
            background: rgba(13,149,113,0.1);
            color: #0D9571;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-urgente {
            background: rgba(220,53,69,0.1);
            color: #DC3545;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-destaque {
            background: rgba(255,193,7,0.15);
            color: #927420;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* ─── Footer ────────────────────────────────────────── */
        .footer-fapeu {
            background: #074635;
            color: rgba(255,255,255,0.75);
            padding: 3rem 0 1.5rem;
        }

        .footer-brand {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .footer-divider {
            border-color: rgba(255,255,255,0.1);
            margin: 2rem 0 1.5rem;
        }

        .footer-copy {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.45);
        }

        /* ─── Search bar ────────────────────────────────────── */
        .search-bar-hero {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(12px);
            border: 1.5px solid rgba(255,255,255,0.3);
            border-radius: 50px;
            padding: 6px 6px 6px 20px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            max-width: 500px;
            margin-bottom: 2rem;
        }

        .search-bar-hero input {
            background: transparent;
            border: none;
            outline: none;
            color: #fff;
            font-size: 0.9rem;
            flex: 1;
            font-family: "Reddit Sans", sans-serif;
        }

        .search-bar-hero input::placeholder { color: rgba(255,255,255,0.6); }

        .btn-search {
            background: #fff;
            color: #0D9571;
            border: none;
            border-radius: 50px;
            padding: 8px 20px;
            font-size: 0.85rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        .btn-search:hover {
            background: #f0fdf8;
            color: #0C8061;
        }

        /* ─── Responsividade ────────────────────────────────── */
        @media (max-width: 767px) {
            .hero-title { font-size: 2rem; }
            .hero-card-float { display: none; }
            .hero-stats { gap: 1rem; }
            .cta-card { padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>

<!-- ════════════════════════════════════════════════════════════
     HERO SECTION (fundo animado + navbar flutuante)
════════════════════════════════════════════════════════════ -->
<section class="hero-section">
    <!-- Gradiente animado -->
    <div class="dynamic-gradient-bg"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-fapeu px-3 px-md-4 py-2">
        <div class="container-xl d-flex align-items-center justify-content-between">
            <!-- Brand -->
            <a href="/" class="d-flex align-items-center gap-2">
                <div style="width:36px;height:36px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-mortarboard-fill text-white" style="font-size:1.1rem;"></i>
                </div>
                <div>
                    <div class="navbar-brand-text">Portal de Vagas</div>
                    <div class="navbar-brand-sub">FAPEU</div>
                </div>
            </a>

            <!-- Links -->
            <div class="d-flex align-items-center gap-2">
                <a href="#vagas" class="nav-pill d-none d-md-inline-flex">
                    <i class="bi bi-briefcase"></i> Vagas
                </a>
                <a href="#como-funciona" class="nav-pill d-none d-md-inline-flex">
                    <i class="bi bi-info-circle"></i> Como funciona
                </a>
                <a href="/login" class="nav-pill">
                    <i class="bi bi-box-arrow-in-right"></i> Entrar
                </a>
                <a href="/register" class="nav-pill nav-pill-primary">
                    <i class="bi bi-person-plus-fill"></i> Cadastrar
                </a>
            </div>
        </div>
    </nav>

    <!-- Conteúdo principal do hero -->
    <div class="hero-content">
        <div class="container-xl py-5">
            <div class="row align-items-center g-5">

                <!-- Texto -->
                <div class="col-lg-6">
                    <div class="hero-badge">
                        <i class="bi bi-stars"></i>
                        Oportunidades de Estágio
                    </div>

                    <h1 class="hero-title">
                        Conecte seu<br>
                        <span>futuro ao</span><br>
                        mercado
                    </h1>

                    <p class="hero-sub">
                        Encontre vagas de estágio alinhadas ao seu perfil acadêmico.
                        Empresas parceiras da FAPEU oferecem oportunidades reais de desenvolvimento profissional.
                    </p>

                    <!-- Barra de busca -->
                    <div class="search-bar-hero">
                        <i class="bi bi-search text-white opacity-75"></i>
                        <input type="text" placeholder="Buscar por cargo, área ou empresa…">
                        <button class="btn-search">
                            <i class="bi bi-arrow-right"></i> Buscar
                        </button>
                    </div>

                    <div class="hero-btn-group">
                        <a href="#vagas" class="btn-hero-primary">
                            <i class="bi bi-briefcase-fill"></i>
                            Ver vagas disponíveis
                        </a>
                        <a href="/register" class="btn-hero-outline">
                            <i class="bi bi-person-plus"></i>
                            Criar meu perfil
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="stat-number">120+</div>
                            <div class="stat-label">Vagas ativas</div>
                        </div>
                        <div style="width:1px;background:rgba(255,255,255,0.15);"></div>
                        <div class="stat-item">
                            <div class="stat-number">40+</div>
                            <div class="stat-label">Empresas parceiras</div>
                        </div>
                        <div style="width:1px;background:rgba(255,255,255,0.15);"></div>
                        <div class="stat-item">
                            <div class="stat-number">850+</div>
                            <div class="stat-label">Estudantes inscritos</div>
                        </div>
                    </div>
                </div>

                <!-- Card flutuante -->
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="hero-card-float">
                        <div style="color:rgba(255,255,255,0.85);font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.5rem;">
                            <i class="bi bi-lightning-charge-fill" style="color:#FFC107;"></i>
                            Vagas em destaque
                        </div>

                        <div class="vaga-mini-card">
                            <div class="vaga-mini-icon" style="background:#e8f5e9;">
                                <i class="bi bi-code-slash" style="color:#2e7d32;"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div class="vaga-mini-title">Desenvolvedor Web</div>
                                <div class="vaga-mini-meta">TechSolutions • Remoto</div>
                            </div>
                            <span class="vaga-mini-tag" style="background:rgba(13,149,113,0.1);color:#0D9571;">Novo</span>
                        </div>

                        <div class="vaga-mini-card">
                            <div class="vaga-mini-icon" style="background:#e3f2fd;">
                                <i class="bi bi-bar-chart-fill" style="color:#1565c0;"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div class="vaga-mini-title">Análise de Dados</div>
                                <div class="vaga-mini-meta">DataCorp • Híbrido</div>
                            </div>
                            <span class="vaga-mini-tag" style="background:rgba(255,193,7,0.15);color:#927420;">Destaque</span>
                        </div>

                        <div class="vaga-mini-card">
                            <div class="vaga-mini-icon" style="background:#fce4ec;">
                                <i class="bi bi-megaphone-fill" style="color:#c62828;"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div class="vaga-mini-title">Marketing Digital</div>
                                <div class="vaga-mini-meta">AgênciaMedia • Presencial</div>
                            </div>
                            <span class="vaga-mini-tag" style="background:rgba(220,53,69,0.1);color:#DC3545;">Urgente</span>
                        </div>

                        <div class="vaga-mini-card">
                            <div class="vaga-mini-icon" style="background:#f3e5f5;">
                                <i class="bi bi-calculator-fill" style="color:#7b1fa2;"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div class="vaga-mini-title">Ciências Contábeis</div>
                                <div class="vaga-mini-meta">Contábil Plus • Presencial</div>
                            </div>
                            <span class="vaga-mini-tag" style="background:rgba(13,149,113,0.1);color:#0D9571;">Novo</span>
                        </div>

                        <div style="margin-top:1.25rem;text-align:center;">
                            <a href="#vagas" style="color:rgba(255,255,255,0.75);font-size:0.8rem;font-weight:600;display:inline-flex;align-items:center;gap:0.4rem;">
                                Ver todas as vagas <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div class="scroll-indicator">
        <span>rolar</span>
        <i class="bi bi-chevron-down"></i>
    </div>
</section>


<!-- ════════════════════════════════════════════════════════════
     COMO FUNCIONA
════════════════════════════════════════════════════════════ -->
<section id="como-funciona" class="py-6" style="padding:5rem 0;background:#fff;">
    <div class="container-xl">
        <div class="text-center mb-5">
            <div class="section-label mx-auto">
                <i class="bi bi-map"></i> Como funciona
            </div>
            <h2 class="section-title">Simples do início ao fim</h2>
            <p class="section-sub mx-auto mt-2">
                Três passos para empresas publicarem vagas e estudantes encontrarem oportunidades.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <h5>1. Crie seu perfil</h5>
                    <p>Cadastre-se como estudante ou empresa em poucos minutos. Preencha seus dados e comece imediatamente.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <i class="bi bi-search-heart-fill"></i>
                    </div>
                    <h5>2. Encontre ou publique vagas</h5>
                    <p>Estudantes buscam por área, modalidade ou empresa. Empresas publicam vagas com todos os requisitos.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <i class="bi bi-handshake-fill"></i>
                    </div>
                    <h5>3. Conecte-se</h5>
                    <p>Candidatos se inscrevem com um clique. Empresas acompanham inscrições e entram em contato diretamente.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ════════════════════════════════════════════════════════════
     VAGAS EM DESTAQUE
════════════════════════════════════════════════════════════ -->
<section id="vagas" style="padding:5rem 0;background:#F8F9FA;">
    <div class="container-xl">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-5">
            <div>
                <div class="section-label">
                    <i class="bi bi-briefcase-fill"></i> Vagas disponíveis
                </div>
                <h2 class="section-title mb-1">Oportunidades em aberto</h2>
                <p class="section-sub mb-0">Vagas publicadas recentemente pelas empresas parceiras.</p>
            </div>
            <a href="/vagas" class="btn-cta-green" style="padding:10px 24px;font-size:0.875rem;">
                <i class="bi bi-grid-3x3-gap-fill"></i> Ver todas as vagas
            </a>
        </div>

        <div class="row g-4">

            <!-- Vaga 1 -->
            <div class="col-md-6 col-xl-4">
                <div class="vaga-card">
                    <div class="vaga-card-header">
                        <div class="empresa-logo" style="background:linear-gradient(135deg,#0D9571,#074635);">TC</div>
                        <div style="flex:1;min-width:0;">
                            <div class="vaga-titulo">Desenvolvedor Web — Estágio</div>
                            <div class="vaga-empresa">TechCore Sistemas</div>
                        </div>
                        <span class="badge-novo">Novo</span>
                    </div>
                    <div class="vaga-card-body">
                        <div class="vaga-info-row">
                            <span class="vaga-chip"><i class="bi bi-geo-alt-fill"></i> Remoto</span>
                            <span class="vaga-chip"><i class="bi bi-clock-fill"></i> 20h/semana</span>
                            <span class="vaga-chip"><i class="bi bi-mortarboard-fill"></i> TI / Eng.</span>
                        </div>
                        <p style="font-size:0.85rem;color:#6C757D;margin:0;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            Desenvolver e manter aplicações web usando PHP e JavaScript. Boa oportunidade para aprender na prática.
                        </p>
                    </div>
                    <div class="vaga-card-footer">
                        <div class="vaga-deadline">
                            <i class="bi bi-calendar3"></i> Encerra em 30 abr
                        </div>
                        <a href="/vagas/1" class="btn-ver-vaga">
                            Ver vaga <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Vaga 2 -->
            <div class="col-md-6 col-xl-4">
                <div class="vaga-card">
                    <div class="vaga-card-header">
                        <div class="empresa-logo" style="background:linear-gradient(135deg,#1565c0,#0D47A1);">DC</div>
                        <div style="flex:1;min-width:0;">
                            <div class="vaga-titulo">Analista de Dados — Estágio</div>
                            <div class="vaga-empresa">DataCorp Analytics</div>
                        </div>
                        <span class="badge-destaque">Destaque</span>
                    </div>
                    <div class="vaga-card-body">
                        <div class="vaga-info-row">
                            <span class="vaga-chip"><i class="bi bi-geo-alt-fill"></i> Híbrido</span>
                            <span class="vaga-chip"><i class="bi bi-clock-fill"></i> 30h/semana</span>
                            <span class="vaga-chip"><i class="bi bi-mortarboard-fill"></i> Estatística</span>
                        </div>
                        <p style="font-size:0.85rem;color:#6C757D;margin:0;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            Apoio em análise exploratória de dados, criação de dashboards e relatórios gerenciais com Python e Power BI.
                        </p>
                    </div>
                    <div class="vaga-card-footer">
                        <div class="vaga-deadline">
                            <i class="bi bi-calendar3"></i> Encerra em 25 abr
                        </div>
                        <a href="/vagas/2" class="btn-ver-vaga">
                            Ver vaga <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Vaga 3 -->
            <div class="col-md-6 col-xl-4">
                <div class="vaga-card">
                    <div class="vaga-card-header">
                        <div class="empresa-logo" style="background:linear-gradient(135deg,#c62828,#b71c1c);">AM</div>
                        <div style="flex:1;min-width:0;">
                            <div class="vaga-titulo">Marketing Digital — Estágio</div>
                            <div class="vaga-empresa">AgênciaMedia</div>
                        </div>
                        <span class="badge-urgente">Urgente</span>
                    </div>
                    <div class="vaga-card-body">
                        <div class="vaga-info-row">
                            <span class="vaga-chip"><i class="bi bi-geo-alt-fill"></i> Presencial</span>
                            <span class="vaga-chip"><i class="bi bi-clock-fill"></i> 25h/semana</span>
                            <span class="vaga-chip"><i class="bi bi-mortarboard-fill"></i> Com. / Publi.</span>
                        </div>
                        <p style="font-size:0.85rem;color:#6C757D;margin:0;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            Gestão de redes sociais, criação de conteúdo e acompanhamento de campanhas pagas nas principais plataformas.
                        </p>
                    </div>
                    <div class="vaga-card-footer">
                        <div class="vaga-deadline">
                            <i class="bi bi-calendar3"></i> Encerra em 20 abr
                        </div>
                        <a href="/vagas/3" class="btn-ver-vaga">
                            Ver vaga <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Vaga 4 -->
            <div class="col-md-6 col-xl-4">
                <div class="vaga-card">
                    <div class="vaga-card-header">
                        <div class="empresa-logo" style="background:linear-gradient(135deg,#7b1fa2,#6a1b9a);">CP</div>
                        <div style="flex:1;min-width:0;">
                            <div class="vaga-titulo">Ciências Contábeis — Estágio</div>
                            <div class="vaga-empresa">Contábil Plus</div>
                        </div>
                        <span class="badge-novo">Novo</span>
                    </div>
                    <div class="vaga-card-body">
                        <div class="vaga-info-row">
                            <span class="vaga-chip"><i class="bi bi-geo-alt-fill"></i> Presencial</span>
                            <span class="vaga-chip"><i class="bi bi-clock-fill"></i> 20h/semana</span>
                            <span class="vaga-chip"><i class="bi bi-mortarboat-fill"></i> Contabilidade</span>
                        </div>
                        <p style="font-size:0.85rem;color:#6C757D;margin:0;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            Auxílio no lançamento de notas fiscais, conciliação bancária e preparação de obrigações acessórias.
                        </p>
                    </div>
                    <div class="vaga-card-footer">
                        <div class="vaga-deadline">
                            <i class="bi bi-calendar3"></i> Encerra em 10 mai
                        </div>
                        <a href="/vagas/4" class="btn-ver-vaga">
                            Ver vaga <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Vaga 5 -->
            <div class="col-md-6 col-xl-4">
                <div class="vaga-card">
                    <div class="vaga-card-header">
                        <div class="empresa-logo" style="background:linear-gradient(135deg,#e65100,#bf360c);">EG</div>
                        <div style="flex:1;min-width:0;">
                            <div class="vaga-titulo">Engenharia Civil — Estágio</div>
                            <div class="vaga-empresa">EngGlobal Construções</div>
                        </div>
                        <span class="badge-novo">Novo</span>
                    </div>
                    <div class="vaga-card-body">
                        <div class="vaga-info-row">
                            <span class="vaga-chip"><i class="bi bi-geo-alt-fill"></i> Presencial</span>
                            <span class="vaga-chip"><i class="bi bi-clock-fill"></i> 30h/semana</span>
                            <span class="vaga-chip"><i class="bi bi-mortarboard-fill"></i> Eng. Civil</span>
                        </div>
                        <p style="font-size:0.85rem;color:#6C757D;margin:0;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            Acompanhamento de obras, elaboração de projetos e relatórios técnicos sob supervisão de engenheiro responsável.
                        </p>
                    </div>
                    <div class="vaga-card-footer">
                        <div class="vaga-deadline">
                            <i class="bi bi-calendar3"></i> Encerra em 05 mai
                        </div>
                        <a href="/vagas/5" class="btn-ver-vaga">
                            Ver vaga <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Vaga 6 -->
            <div class="col-md-6 col-xl-4">
                <div class="vaga-card">
                    <div class="vaga-card-header">
                        <div class="empresa-logo" style="background:linear-gradient(135deg,#0D9571,#197060);">HS</div>
                        <div style="flex:1;min-width:0;">
                            <div class="vaga-titulo">Recursos Humanos — Estágio</div>
                            <div class="vaga-empresa">HumanoSoft</div>
                        </div>
                        <span class="badge-destaque">Destaque</span>
                    </div>
                    <div class="vaga-card-body">
                        <div class="vaga-info-row">
                            <span class="vaga-chip"><i class="bi bi-geo-alt-fill"></i> Híbrido</span>
                            <span class="vaga-chip"><i class="bi bi-clock-fill"></i> 20h/semana</span>
                            <span class="vaga-chip"><i class="bi bi-mortarboard-fill"></i> Psicologia / Adm.</span>
                        </div>
                        <p style="font-size:0.85rem;color:#6C757D;margin:0;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            Apoio em processos de R&S, onboarding, administração de pessoal e atividades de endomarketing.
                        </p>
                    </div>
                    <div class="vaga-card-footer">
                        <div class="vaga-deadline">
                            <i class="bi bi-calendar3"></i> Encerra em 15 mai
                        </div>
                        <a href="/vagas/6" class="btn-ver-vaga">
                            Ver vaga <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <div class="text-center mt-5">
            <a href="/vagas" class="btn-cta-green mx-auto" style="padding:14px 40px;font-size:0.95rem;">
                <i class="bi bi-grid-3x3-gap-fill"></i> Ver todas as vagas disponíveis
            </a>
        </div>
    </div>
</section>


<!-- ════════════════════════════════════════════════════════════
     CTA — EMPRESA / CANDIDATO
════════════════════════════════════════════════════════════ -->
<section style="padding:5rem 0;background:#fff;">
    <div class="container-xl">
        <div class="row g-4">

            <!-- Empresas -->
            <div class="col-lg-6">
                <div class="cta-card cta-card-empresas">
                    <div class="cta-card-pattern"></div>
                    <div class="cta-card-pattern" style="bottom:-30px;left:-30px;top:auto;right:auto;"></div>
                    <div style="position:relative;z-index:1;display:flex;flex-direction:column;height:100%;">
                        <div style="width:52px;height:52px;background:rgba(255,255,255,0.15);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#fff;margin-bottom:1.5rem;">
                            <i class="bi bi-building-fill"></i>
                        </div>
                        <h3 style="color:#fff;">Sua empresa<br>no portal FAPEU</h3>
                        <p style="color:rgba(255,255,255,0.8);">
                            Publique vagas de estágio, receba inscrições e conecte-se a estudantes qualificados das principais universidades da região.
                        </p>
                        <ul style="color:rgba(255,255,255,0.75);font-size:0.88rem;padding-left:1.25rem;margin-bottom:2rem;line-height:2;">
                            <li>Publicação gratuita de vagas</li>
                            <li>Gestão centralizada de candidaturas</li>
                            <li>Suporte da equipe FAPEU</li>
                        </ul>
                        <a href="/empresas/cadastro" class="btn-cta-white">
                            <i class="bi bi-building-fill"></i> Cadastrar minha empresa
                        </a>
                    </div>
                </div>
            </div>

            <!-- Candidatos -->
            <div class="col-lg-6">
                <div class="cta-card cta-card-candidatos">
                    <div style="width:52px;height:52px;background:rgba(13,149,113,0.08);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#0D9571;margin-bottom:1.5rem;">
                        <i class="bi bi-person-graduation"></i>
                    </div>
                    <h3 style="color:#2C4A44;">Pronto para<br>seu primeiro estágio?</h3>
                    <p>
                        Crie seu perfil, anexe seu currículo e candidate-se às vagas que combinam com sua área de formação em poucos cliques.
                    </p>
                    <ul style="color:#6C757D;font-size:0.88rem;padding-left:1.25rem;margin-bottom:2rem;line-height:2;">
                        <li>Perfil personalizado por curso</li>
                        <li>Notificações de novas vagas</li>
                        <li>Acompanhe suas candidaturas</li>
                    </ul>
                    <a href="/register" class="btn-cta-green">
                        <i class="bi bi-person-plus-fill"></i> Criar meu perfil agora
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ════════════════════════════════════════════════════════════
     FEATURES (diferenciais)
════════════════════════════════════════════════════════════ -->
<section style="padding:5rem 0;background:#F8F9FA;">
    <div class="container-xl">
        <div class="text-center mb-5">
            <div class="section-label mx-auto">
                <i class="bi bi-award-fill"></i> Por que usar
            </div>
            <h2 class="section-title">Um portal feito para facilitar</h2>
        </div>

        <div class="row g-4">
            <div class="col-sm-6 col-lg-3">
                <div class="feature-card text-center">
                    <div class="feature-icon-wrap mx-auto">
                        <i class="bi bi-shield-check-fill"></i>
                    </div>
                    <h5>Vagas verificadas</h5>
                    <p>Todas as vagas passam por validação da equipe FAPEU antes de serem publicadas.</p>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="feature-card text-center">
                    <div class="feature-icon-wrap mx-auto">
                        <i class="bi bi-lightning-fill"></i>
                    </div>
                    <h5>Candidatura rápida</h5>
                    <p>Com o perfil completo, candidate-se a qualquer vaga em menos de 30 segundos.</p>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="feature-card text-center">
                    <div class="feature-icon-wrap mx-auto">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                    <h5>Alertas de vagas</h5>
                    <p>Receba notificações assim que uma vaga compatível com seu perfil for publicada.</p>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="feature-card text-center">
                    <div class="feature-icon-wrap mx-auto">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h5>Suporte dedicado</h5>
                    <p>Equipe FAPEU disponível para auxiliar empresas e estudantes durante todo o processo.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ════════════════════════════════════════════════════════════
     FOOTER
════════════════════════════════════════════════════════════ -->
<footer class="footer-fapeu">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div style="width:36px;height:36px;background:rgba(255,255,255,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-mortarboard-fill text-white"></i>
                    </div>
                    <div class="footer-brand">Portal de Vagas — FAPEU</div>
                </div>
                <p style="font-size:0.85rem;line-height:1.6;color:rgba(255,255,255,0.55);">
                    Conectando estudantes a oportunidades reais de estágio em empresas parceiras da Fundação de Apoio à Pesquisa e Extensão Universitária.
                </p>
            </div>

            <div class="col-6 col-lg-2 offset-lg-2">
                <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:rgba(255,255,255,0.5);margin-bottom:1rem;">Para estudantes</div>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.5rem;">
                    <li><a href="/vagas" style="color:rgba(255,255,255,0.7);font-size:0.875rem;">Ver vagas</a></li>
                    <li><a href="/register" style="color:rgba(255,255,255,0.7);font-size:0.875rem;">Criar perfil</a></li>
                    <li><a href="/login" style="color:rgba(255,255,255,0.7);font-size:0.875rem;">Minha conta</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:rgba(255,255,255,0.5);margin-bottom:1rem;">Para empresas</div>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.5rem;">
                    <li><a href="/empresas/cadastro" style="color:rgba(255,255,255,0.7);font-size:0.875rem;">Cadastrar empresa</a></li>
                    <li><a href="/vagas/criar" style="color:rgba(255,255,255,0.7);font-size:0.875rem;">Publicar vaga</a></li>
                    <li><a href="/empresas/login" style="color:rgba(255,255,255,0.7);font-size:0.875rem;">Área da empresa</a></li>
                </ul>
            </div>

            <div class="col-lg-2">
                <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:rgba(255,255,255,0.5);margin-bottom:1rem;">Institucional</div>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.5rem;">
                    <li><a href="#" style="color:rgba(255,255,255,0.7);font-size:0.875rem;">Sobre a FAPEU</a></li>
                    <li><a href="#" style="color:rgba(255,255,255,0.7);font-size:0.875rem;">Contato</a></li>
                    <li><a href="#" style="color:rgba(255,255,255,0.7);font-size:0.875rem;">Política de privacidade</a></li>
                </ul>
            </div>
        </div>

        <hr class="footer-divider">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <p class="footer-copy mb-0">© {{ date('Y') }} FAPEU — Fundação de Apoio à Pesquisa e Extensão Universitária. Todos os direitos reservados.</p>
            <div class="d-flex gap-3">
                <a href="#" style="color:rgba(255,255,255,0.4);font-size:1.1rem;" title="Instagram">
                    <i class="bi bi-instagram"></i>
                </a>
                <a href="#" style="color:rgba(255,255,255,0.4);font-size:1.1rem;" title="LinkedIn">
                    <i class="bi bi-linkedin"></i>
                </a>
                <a href="#" style="color:rgba(255,255,255,0.4);font-size:1.1rem;" title="E-mail">
                    <i class="bi bi-envelope-fill"></i>
                </a>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Scroll suave para âncoras
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
</script>

</body>
</html>
