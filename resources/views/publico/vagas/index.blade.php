{{-- $vagas: LengthAwarePaginator<VagaDrhflow>. $filtros: array. $indisponivel: bool.
     $tipos/$escolaridades/$projetos/$ufs: array código => rótulo. $municipios: array de nomes.
     Um único <form> "flutuante" (sem envolver o conteúdo) porque os campos vivem em duas
     regiões da página (hero + barra lateral) — todos referenciam form="filtro-vagas". --}}
@php
    $container = 'mx-auto w-full px-4 lg:w-4/5 lg:px-0';
    $temFiltros = count(array_filter($filtros)) > 0;
@endphp

<x-layouts.public title="Vagas abertas">
    <form id="filtro-vagas" method="GET" action="{{ route('vagas.publicas.index') }}" data-vagas-filtro class="hidden"></form>

    <section class="relative isolate overflow-hidden">
        <img src="{{ asset('imagens/home-hero.jpg') }}" alt="" class="absolute inset-0 -z-20 size-full object-cover">
        <div class="absolute inset-0 -z-10 bg-black/50"></div>
        <div class="absolute inset-0 -z-10 bg-gradient-to-r from-brand-deep/80 to-transparent"></div>

        <div class="{{ $container }} py-10 lg:py-14">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_360px] lg:items-center lg:gap-12">
                <div class="max-w-2xl">
                    <h1 class="text-3xl font-bold leading-tight tracking-tight text-white sm:text-4xl xl:text-5xl">
                        Encontre sua próxima oportunidade
                    </h1>
                    <p class="mt-3 text-sm text-white/80 sm:text-base">
                        {{ $total }} {{ $total === 1 ? 'vaga aberta' : 'vagas abertas' }} em projetos.
                    </p>

                    <div class="mt-7 flex max-w-xl gap-2">
                        <div class="relative flex-1">
                            <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-white/85" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            <x-ui.input type="search" name="busca" form="filtro-vagas" value="{{ $filtros['busca'] ?? '' }}" placeholder="Cargo, área ou palavra-chave" class="h-11 border-white/25 bg-white/10 pl-9 text-white shadow-lg backdrop-blur-sm placeholder:text-white/85 focus-visible:border-white/40 focus-visible:ring-white/30" />
                        </div>
                        <x-ui.button type="submit" form="filtro-vagas" class="h-11 px-6">Buscar</x-ui.button>
                    </div>
                </div>

                <div class="flex flex-col gap-4 rounded-xl bg-white/10 p-4 ring-1 ring-white/25 backdrop-blur-sm transition-colors hover:bg-white/15 sm:flex-row sm:items-center sm:gap-5 lg:flex-col lg:items-start lg:gap-4">
                    <div class="flex min-w-0 flex-1 items-start gap-3 lg:w-full lg:flex-none">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/30">
                            <svg class="size-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-base font-semibold text-white">Não perca nenhuma vaga</p>
                            <p class="mt-0.5 text-sm text-white/80">Receba por e-mail as vagas que combinam com o seu perfil, assim que forem publicadas.</p>
                        </div>
                    </div>
                    <x-ui.button tag="a" href="{{ route('alertas.create') }}" class="h-11 w-full px-6 sm:w-auto lg:w-full">Criar alerta de vagas</x-ui.button>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="{{ $container }} pt-8">
            <div class="grid items-start gap-6 lg:grid-cols-[240px_1fr] xl:grid-cols-[240px_minmax(340px,420px)_1fr]">
                <aside class="rounded-xl bg-card p-4 ring-1 ring-foreground/10 lg:sticky lg:top-20">
                    <div class="mb-4 flex items-center justify-between">
                        <span class="inline-flex items-center gap-2 text-sm font-semibold">
                            <svg class="size-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="21" x2="14" y1="4" y2="4"/><line x1="10" x2="3" y1="4" y2="4"/><line x1="21" x2="12" y1="12" y2="12"/><line x1="8" x2="3" y1="12" y2="12"/><line x1="21" x2="16" y1="20" y2="20"/><line x1="12" x2="3" y1="20" y2="20"/><line x1="14" x2="14" y1="2" y2="6"/><line x1="8" x2="8" y1="10" y2="14"/><line x1="16" x2="16" y1="18" y2="22"/></svg>
                            Filtros
                        </span>
                        @if ($temFiltros)
                            <a href="{{ route('vagas.publicas.index') }}" data-vagas-limpar class="cursor-pointer text-xs font-medium text-muted-foreground transition-colors hover:text-foreground">Limpar</a>
                        @endif
                    </div>

                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1.5">
                            <x-ui.label class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Tipo de contratação</x-ui.label>
                            <select name="tipo" form="filtro-vagas" data-auto-apply class="h-9 w-full rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
                                <option value="">Todos</option>
                                @foreach ($tipos as $codigo => $rotulo)
                                    <option value="{{ $codigo }}" @selected(($filtros['tipo'] ?? '') === (string) $codigo)>{{ $rotulo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <x-ui.label class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Escolaridade</x-ui.label>
                            <select name="escolaridade" form="filtro-vagas" data-auto-apply class="h-9 w-full rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
                                <option value="">Todas</option>
                                @foreach ($escolaridades as $codigo => $rotulo)
                                    <option value="{{ $codigo }}" @selected(($filtros['escolaridade'] ?? '') === (string) $codigo)>{{ $rotulo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <x-ui.label class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Projeto</x-ui.label>
                            <select name="projeto" form="filtro-vagas" data-auto-apply class="h-9 w-full rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
                                <option value="">Todos</option>
                                @foreach ($projetos as $codigo => $rotulo)
                                    <option value="{{ $codigo }}" @selected(($filtros['projeto'] ?? '') === (string) $codigo)>{{ $rotulo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <x-ui.label class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Localização</x-ui.label>
                            <div class="grid grid-cols-[1fr_76px] gap-2">
                                <select name="cidade" form="filtro-vagas" data-auto-apply class="h-9 w-full rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
                                    <option value="">Cidade</option>
                                    @foreach ($municipios as $cidade)
                                        <option value="{{ $cidade }}" @selected(($filtros['cidade'] ?? '') === $cidade)>{{ $cidade }}</option>
                                    @endforeach
                                </select>
                                <select name="estado" form="filtro-vagas" data-auto-apply class="h-9 w-full rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
                                    <option value="">UF</option>
                                    @foreach ($ufs as $uf => $rotulo)
                                        <option value="{{ $uf }}" @selected(($filtros['estado'] ?? '') === $uf)>{{ $uf }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <x-ui.label class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Faixa salarial (R$)</x-ui.label>
                            <div class="grid grid-cols-2 gap-2">
                                <x-ui.input type="number" min="0" name="salario_min" form="filtro-vagas" value="{{ $filtros['salario_min'] ?? '' }}" placeholder="Mín." />
                                <x-ui.input type="number" min="0" name="salario_max" form="filtro-vagas" value="{{ $filtros['salario_max'] ?? '' }}" placeholder="Máx." />
                            </div>
                        </div>

                        <x-ui.button type="submit" form="filtro-vagas" variant="outline" class="w-full">Buscar</x-ui.button>
                    </div>
                </aside>

                <div data-vagas-resultado class="contents">
                    @include('publico.vagas._resultado', ['vagas' => $vagas, 'filtros' => $filtros, 'indisponivel' => $indisponivel])
                </div>
            </div>
        </div>

        <div data-sheet="vaga-detalhe" data-side="bottom" class="fixed inset-0 z-50 hidden xl:hidden">
            <div data-sheet-backdrop class="absolute inset-0 bg-black/50"></div>
            <div data-sheet-panel class="absolute inset-x-0 bottom-0 h-[92dvh] overflow-y-auto rounded-t-xl bg-card">
                <div data-vaga-detalhe-mobile></div>
            </div>
        </div>
    </section>
</x-layouts.public>
