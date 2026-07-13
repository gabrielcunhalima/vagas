<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrativo - Portal de Vagas FAPEU</title>
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

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 260px;
            z-index: 100;
            background: #074635;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand-icon {
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-brand-text {
            font-size: 0.9rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }

        .sidebar-brand-sub {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.55);
        }

        .sidebar-section {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: rgba(255, 255, 255, 0.35);
            padding: 1rem 1.25rem 0.35rem;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
            font-size: 0.88rem;
            padding: 0.6rem 1.25rem;
            border-radius: 0;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.06);
            border-left-color: rgba(255, 255, 255, 0.3);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(13, 149, 113, 0.2);
            border-left-color: #0D9571;
            font-weight: 600;
        }

        .sidebar .nav-link i {
            font-size: 1rem;
            width: 18px;
            text-align: center;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #0D9571;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .user-name {
            font-size: 0.82rem;
            font-weight: 600;
            color: #fff;
            line-height: 1.2;
        }

        .user-perfil {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.5);
            text-transform: capitalize;
        }

        /* ── Conteúdo principal ── */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Topbar ── */
        .topbar {
            background: #fff;
            border-bottom: 1px solid #E9ECEF;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .topbar-title {
            font-size: 1rem;
            font-weight: 700;
            color: #2C4A44;
        }

        .topbar-breadcrumb {
            font-size: 0.78rem;
            color: #6C757D;
        }

        /* ── Página ── */
        .page-body {
            padding: 1.75rem 1.5rem;
            flex: 1;
        }

        /* ── Linhas clicáveis ── */
        .card-interno a[href]:not(.btn):hover {
            background: #F8FAF9;
        }

        /* ── Cards ── */
        .card-interno {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
            background: #fff;
        }

        .card-interno .card-header {
            background: linear-gradient(45deg, #0d9571, #0c8061);
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 12px 12px 0 0 !important;
            padding: 0.9rem 1.25rem;
            border: none;
        }

        /* ── Stats card ── */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s;
        }

        .stat-card:hover {
            box-shadow: 0 8px 25px rgba(12, 128, 97, 0.3);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .stat-number {
            font-size: 1.75rem;
            font-weight: 800;
            color: #2C4A44;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #6C757D;
            font-weight: 500;
            margin-top: 3px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 22px;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 8px;
        }

        .btn.btn-sm {
            padding: 8px 22px;
            font-size: 0.875rem;
        }

        /* ── Botões ── */
        .btn-principal {
            background: #0D9571;
            color: #fff;
            font-weight: 600;
            border: 2px solid #0D9571;
            border-radius: 8px;
            padding: 8px 22px;
            box-shadow: 0 4px 15px rgba(6, 85, 26, 0.2);
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
            border: 1px solid #0D9571;
            border-radius: 8px;
            padding: 7px 18px;
            font-weight: 600;
            background: transparent;
            transition: all 0.3s;
        }

        .btn-outline-principal:hover {
            background: #0D9571;
            color: #fff;
        }

        /* ── Tabela ── */
        .table-vagas {
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .table-vagas thead th {
            background: #0D9571;
            color: #fff;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 14px;
            border: none;
        }

        .table-vagas tbody td {
            padding: 11px 14px;
            vertical-align: middle;
            border-bottom: 1px solid #E9ECEF;
            font-size: 0.875rem;
            background: #fff;
        }

        .table-vagas tbody tr:hover td {
            background: #f8faf9;
        }

        .table-vagas tbody tr:last-child td {
            border-bottom: none;
        }

        /* ── Badges de status ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .status-rascunho {
            background: #E9ECEF;
            color: #6C757D;
        }

        .status-aguardando_autorizacao {
            background: #FFF3CD;
            color: #856404;
        }

        .status-ativa {
            background: #D1E7DD;
            color: #0A5234;
        }

        .status-encerrada {
            background: #E2E3E5;
            color: #383D41;
        }

        .status-recusada {
            background: #F8D7DA;
            color: #842029;
        }

        .status-inativa {
            background: #E9ECEF;
            color: #6C757D;
        }

        .status-recebida {
            background: #CFE2FF;
            color: #084298;
        }

        .status-em_analise {
            background: #FFF3CD;
            color: #856404;
        }

        .status-entrevista {
            background: #CFF4FC;
            color: #055160;
        }

        .status-aprovado {
            background: #D1E7DD;
            color: #0A5234;
        }

        .status-reprovado {
            background: #F8D7DA;
            color: #842029;
        }

        /* ── Form ── */
        .form-control:focus,
        .form-select:focus {
            border-color: #0D9571;
            box-shadow: 0 0 0 0.2rem rgba(13, 149, 113, 0.25);
        }

        .form-label {
            color: #495057;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.4rem;
        }

        /* ── Alerts ── */
        .alert-principal {
            border-left: 4px solid #0D9571;
            background: rgba(13, 149, 113, 0.06);
            border-color: rgba(13, 149, 113, 0.2);
            color: #3E3E3F;
        }

        .alert-aviso {
            border-left: 4px solid #FFC107;
            background: rgba(255, 193, 7, 0.08);
            border-color: rgba(255, 193, 7, 0.3);
        }

        /* ── Responsivo ── */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-260px);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- Sidebar --}}
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('imagens/fapeulogoverde.png') }}" alt="FAPEU" style="height:48px;width:auto;">
            <div>
                <div class="sidebar-brand-text">Portal de Vagas</div>
                <div class="sidebar-brand-sub">FAPEU</div>
            </div>
        </div>

        @php $perfil = auth()->user()->perfil; @endphp

        @if($perfil === 'coordenador' || $perfil === 'admin')
        <div class="sidebar-section">Coordenador</div>
        <nav>
            <a href="{{ route('coord.dashboard') }}"
                class="nav-link {{ request()->routeIs('coord.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('coord.vagas.index') }}"
                class="nav-link {{ request()->routeIs('coord.vagas.*') && !request()->routeIs('coord.vagas.create') ? 'active' : '' }}">
                <i class="bi bi-briefcase"></i> Minhas Vagas
            </a>
            <a href="{{ route('coord.vagas.create') }}"
                class="nav-link {{ request()->routeIs('coord.vagas.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i> Nova Vaga
            </a>
            <a href="{{ route('coord.candidaturas.todas') }}"
                class="nav-link {{ request()->routeIs('coord.candidaturas.todas') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Candidaturas
            </a>
        </nav>
        @endif

        @if($perfil === 'gestor' || $perfil === 'admin')
        <div class="sidebar-section">Gestor</div>
        <nav>
            <a href="{{ route('gestor.dashboard') }}"
                class="nav-link {{ request()->routeIs('gestor.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('gestor.vagas.index') }}"
                class="nav-link {{ request()->routeIs('gestor.vagas.*') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i> Autorizar Vagas
            </a>
        </nav>
        @endif

        <div class="sidebar-section">Sistema</div>
        <nav>
            <a href="{{ route('vagas.publicas.index') }}" target="_blank" class="nav-link">
                <i class="bi bi-box-arrow-up-right"></i> Ver página pública
            </a>
        </nav>

        {{-- User info + logout --}}
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div style="flex:1;min-width:0;">
                    <div class="user-name text-truncate">{{ auth()->user()->name }}</div>
                    <div class="user-perfil">{{ auth()->user()->perfil }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="background:none;border:none;color:rgba(255,255,255,0.5);cursor:pointer;padding:4px;" title="Sair">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Conteúdo principal --}}
    <div class="main-content">
        <div class="topbar">
            <div>
                <div class="topbar-title">@yield('page-title', 'Painel')</div>
                <div class="topbar-breadcrumb">@yield('breadcrumb', 'FAPEU / Portal de Vagas')</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                {{-- Toggle sidebar mobile --}}
                <button class="btn btn-sm d-lg-none" id="sidebarToggle" style="background:none;border:1px solid #E9ECEF;">
                    <i class="bi bi-list fs-5"></i>
                </button>
                @yield('topbar-actions')
            </div>
        </div>

        {{-- Flash messages --}}
        <div class="px-4 pt-3">
            @if(session('sucesso'))
            <div class="alert alert-principal alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2" style="color:#0D9571;"></i>
                {{ session('sucesso') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('aviso'))
            <div class="alert alert-aviso alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2" style="color:#856404;"></i>
                {{ session('aviso') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('erro'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-x-circle-fill me-2"></i>
                {{ session('erro') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
        </div>

        <div class="page-body">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>
    @stack('scripts')
</body>

</html>