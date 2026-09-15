@props(['title' => null])
@php
    $candidato = auth('candidato')->user();
    $container = 'mx-auto w-full px-4 lg:w-4/5 lg:px-0';
@endphp
<x-layouts.app :title="$title">
    <div class="flex min-h-dvh flex-col">
        @if ($candidato && !$candidato->hasVerifiedEmail())
            <div class="border-b border-amber-500/30 bg-amber-500/10">
                <div class="mx-auto flex w-full max-w-6xl flex-wrap items-center gap-x-3 gap-y-1 px-4 py-2.5 text-sm">
                    <svg class="size-4 shrink-0 text-amber-600 dark:text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.2 8.4c.5.38.8.97.8 1.6v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V10a2 2 0 0 1 .8-1.6l8-6a2 2 0 0 1 2.4 0z"/><path d="m22 10-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 10"/></svg>
                    <span>Confirme seu e-mail para acessar sua conta.</span>
                    <form method="POST" action="{{ route('candidato.verification.send') }}" class="inline">
                        @csrf
                        <button type="submit" class="font-semibold text-primary hover:underline">Reenviar confirmação</button>
                    </form>
                </div>
            </div>
        @elseif ($candidato && !$candidato->perfilCompleto())
            @php $pendencias = count($candidato->pendencias()); @endphp
            <div class="border-b bg-muted/60">
                <div class="mx-auto flex w-full max-w-6xl flex-wrap items-center gap-x-3 gap-y-1 px-4 py-2.5 text-sm">
                    <svg class="size-4 shrink-0 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Faltam {{ $pendencias }} {{ $pendencias === 1 ? 'informação' : 'informações' }} no seu perfil para você poder se candidatar.</span>
                    <a href="{{ route('candidato.perfil.edit') }}" class="font-semibold text-primary hover:underline">Completar perfil</a>
                </div>
            </div>
        @endif

        <header class="sticky top-0 z-40 border-b bg-background/85 backdrop-blur-md">
            <div class="{{ $container }} flex h-16 items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3">
                    <x-logo class="h-9" />
                    <span class="hidden text-sm font-bold tracking-tight text-foreground sm:inline">Portal de Vagas</span>
                </a>

                <div class="flex items-center gap-2.5">
                    <x-ui.button tag="button" variant="ghost" size="icon-lg" class="text-muted-foreground" data-tema-toggle aria-label="Alternar tema claro/escuro">
                        <svg class="size-5 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                        <svg class="hidden size-5 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                    </x-ui.button>

                    @if ($candidato)
                        <details class="group relative hidden lg:block">
                            <summary class="flex h-10 cursor-pointer list-none items-center gap-2.5 rounded-full border border-border bg-background px-2 pl-2 hover:bg-muted [&::-webkit-details-marker]:hidden">
                                <span class="flex size-7 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground">
                                    {{ \App\Support\Iniciais::de($candidato->nome_exibicao) }}
                                </span>
                                <span class="text-[0.925rem] font-medium">{{ $candidato->primeiro_nome }}</span>
                                <svg class="size-4 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                            </summary>
                            <div class="absolute right-0 z-10 mt-2 min-w-56 rounded-lg bg-popover p-1 text-popover-foreground shadow-lg ring-1 ring-foreground/10">
                                <div class="px-3 py-2">
                                    <div class="text-sm font-semibold">{{ $candidato->nome_exibicao }}</div>
                                    <div class="text-xs text-muted-foreground">{{ $candidato->email }}</div>
                                </div>
                                <div class="my-1 h-px bg-border"></div>
                                <a href="{{ route('candidato.candidaturas.index') }}" class="flex items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-muted">Minhas candidaturas</a>
                                <a href="{{ route('candidato.perfil.edit') }}" class="flex items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-muted">Meus dados</a>
                                <div class="my-1 h-px bg-border"></div>
                                <form method="POST" action="{{ route('candidato.logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-sm text-destructive hover:bg-destructive/10">Sair</button>
                                </form>
                            </div>
                        </details>
                    @else
                        <div class="hidden items-center gap-2 lg:flex">
                            <x-ui.button tag="a" href="{{ route('candidato.login') }}" variant="ghost" size="lg">Entrar</x-ui.button>
                            <x-ui.button tag="a" href="{{ route('candidato.registro') }}" size="lg">Criar conta</x-ui.button>
                        </div>
                    @endif

                    <x-ui.button tag="button" variant="ghost" size="icon" class="lg:hidden" data-sheet-trigger="menu-mobile" aria-label="Abrir menu">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                    </x-ui.button>
                </div>
            </div>
        </header>

        <div data-sheet data-side="right" class="fixed inset-0 z-50 hidden lg:hidden">
            <div data-sheet-backdrop class="absolute inset-0 bg-black/50"></div>
            <div data-sheet-panel class="absolute inset-y-0 right-0 w-72 overflow-y-auto bg-background shadow-xl">
                <div class="flex justify-end p-3">
                    <button type="button" data-sheet-close class="rounded-md p-1.5 text-muted-foreground hover:bg-muted" aria-label="Fechar menu">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>
                <nav class="flex flex-col gap-1 p-4 pt-0">
                    <a href="{{ route('vagas.publicas.index') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-muted">Vagas</a>
                    <a href="{{ route('alertas.create') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-muted">Alertas de vagas</a>
                    <div class="my-3 border-t"></div>

                    @if ($candidato)
                        <a href="{{ route('candidato.candidaturas.index') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-muted">Minhas candidaturas</a>
                        <a href="{{ route('candidato.perfil.edit') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-muted">Meus dados</a>
                        <form method="POST" action="{{ route('candidato.logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2.5 text-left text-sm font-medium text-destructive hover:bg-destructive/10">Sair</button>
                        </form>
                    @else
                        <div class="flex flex-col gap-2 px-1 pt-1">
                            <x-ui.button tag="a" href="{{ route('candidato.login') }}" variant="outline">Entrar</x-ui.button>
                            <x-ui.button tag="a" href="{{ route('candidato.registro') }}">Criar conta</x-ui.button>
                        </div>
                    @endif
                </nav>
            </div>
        </div>

        <main class="flex-1">{{ $slot }}</main>

        <footer class="mt-16 border-t bg-card">
            <div class="mx-auto grid w-full max-w-6xl gap-8 px-4 py-10 md:grid-cols-[2fr_1fr_1fr]">
                <div>
                    <x-logo class="h-9" />
                    <p class="mt-3 max-w-xs text-sm leading-relaxed text-muted-foreground">
                        Oportunidades de estágio, emprego e bolsa em projetos administrados pela FAPEU.
                    </p>
                </div>
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Portal</div>
                    <ul class="mt-3 flex flex-col gap-2 text-sm">
                        <li><a href="{{ route('vagas.publicas.index') }}" class="text-muted-foreground transition-colors hover:text-foreground">Vagas abertas</a></li>
                        <li><a href="{{ route('alertas.create') }}" class="text-muted-foreground transition-colors hover:text-foreground">Alertas de vagas</a></li>
                        <li><a href="{{ route('politica.privacidade') }}" class="text-muted-foreground transition-colors hover:text-foreground">Política de Privacidade</a></li>
                    </ul>
                </div>
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Candidatos</div>
                    <ul class="mt-3 flex flex-col gap-2 text-sm">
                        @if ($candidato)
                            <li><a href="{{ route('candidato.candidaturas.index') }}" class="text-muted-foreground transition-colors hover:text-foreground">Minhas candidaturas</a></li>
                            <li><a href="{{ route('candidato.perfil.edit') }}" class="text-muted-foreground transition-colors hover:text-foreground">Meus dados</a></li>
                        @else
                            <li><a href="{{ route('candidato.login') }}" class="text-muted-foreground transition-colors hover:text-foreground">Entrar</a></li>
                            <li><a href="{{ route('candidato.registro') }}" class="text-muted-foreground transition-colors hover:text-foreground">Criar conta</a></li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="border-t">
                <p class="mx-auto max-w-6xl px-4 py-4 text-xs text-muted-foreground">
                    © {{ date('Y') }} FAPEU, Fundação de Amparo à Pesquisa e Extensão Universitária
                </p>
            </div>
        </footer>
    </div>
</x-layouts.app>
