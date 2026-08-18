<div class="flex h-full flex-col bg-sidebar text-sidebar-foreground">
    <div class="flex items-center gap-3 border-b border-sidebar-border px-5 py-5">
        <x-logo white class="h-9" />
        <div>
            <div class="text-sm font-bold leading-tight">Portal de Vagas</div>
            <div class="text-xs text-sidebar-foreground/55">Painel interno</div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto pb-4">
        @if (in_array($perfil, ['coordenador', 'admin']))
            <div class="px-6 pb-1.5 pt-5 text-[0.775rem] font-bold uppercase tracking-widest text-sidebar-foreground/40">Coordenador</div>
            <nav class="flex flex-col gap-0.5">
                <a href="{{ route('coord.dashboard') }}" class="mx-3 flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ $ativo('coord.dashboard') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/65 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                    <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('coord.vagas.index') }}" class="mx-3 flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ ($ativo('coord.vagas.*') && $atual !== 'coord.vagas.create') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/65 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                    <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    Minhas vagas
                </a>
                <a href="{{ route('coord.vagas.create') }}" class="mx-3 flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ $atual === 'coord.vagas.create' ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/65 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                    <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8"/></svg>
                    Nova vaga
                </a>
                <a href="{{ route('coord.candidaturas.todas') }}" class="mx-3 flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ $ativo('coord.candidaturas.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/65 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                    <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Candidaturas
                </a>
            </nav>
        @endif

        @if (in_array($perfil, ['gestor', 'admin']))
            <div class="px-6 pb-1.5 pt-5 text-[0.775rem] font-bold uppercase tracking-widest text-sidebar-foreground/40">Gestor</div>
            <nav class="flex flex-col gap-0.5">
                <a href="{{ route('gestor.dashboard') }}" class="mx-3 flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ $ativo('gestor.dashboard') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/65 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                    <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('gestor.vagas.index') }}" class="mx-3 flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ $ativo('gestor.vagas.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/65 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                    <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><path d="M12 3c1 1.5 3 2 5 2a3.5 3.5 0 0 1 0 7c0 2-1.5 3.5-3 5-1.5-1.5-3-3-3-5a3.5 3.5 0 0 1 0-7c2 0 4-.5 5-2Z"/></svg>
                    Autorizar vagas
                </a>
            </nav>
        @endif

        <div class="px-6 pb-1.5 pt-5 text-[0.775rem] font-bold uppercase tracking-widest text-sidebar-foreground/40">Sistema</div>
        <nav>
            <a href="{{ route('vagas.publicas.index') }}" target="_blank" rel="noreferrer" class="mx-3 flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-sidebar-foreground/65 transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground">
                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6M10 14 21 3M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                Ver portal público
            </a>
        </nav>
    </div>

    <div class="border-t border-sidebar-border px-3 py-3.5">
        <div class="flex items-center gap-2.5">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-sidebar-primary text-xs font-bold text-sidebar-primary-foreground">
                {{ \App\Support\Iniciais::de($user->name) }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="truncate text-sm font-semibold">{{ $user->name }}</div>
                <div class="text-xs capitalize text-sidebar-foreground/55">{{ $perfil }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Sair" class="rounded-md p-1.5 text-sidebar-foreground/55 transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>
