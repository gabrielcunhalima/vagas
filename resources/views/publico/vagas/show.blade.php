{{-- $vaga: App\Support\Drhflow\VagaDrhflow --}}
<x-layouts.public :title="$vaga->titulo">
    <div class="mx-auto w-full max-w-6xl px-4 pt-8">
        <a href="{{ route('vagas.publicas.index') }}" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7M19 12H5"/></svg>
            Todas as vagas
        </a>

        <header class="mt-5">
            <div class="flex flex-wrap items-center gap-2">
                @if ($vaga->isNova())
                    <x-badges.nova />
                @endif
                <x-badges.tipo-admissao :tipo="$vaga->tipo" :codigo="$vaga->tipoCodigo" />
            </div>
            <h1 class="mt-3 max-w-3xl text-2xl font-bold leading-tight tracking-tight sm:text-3xl">{{ $vaga->titulo }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-sm text-muted-foreground">
                @if ($vaga->localizacao())
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $vaga->localizacao() }}
                    </span>
                @endif
                @if ($vaga->projetoNome)
                    <span>Projeto — {{ $vaga->projetoNome }}</span>
                @endif
            </div>
        </header>

        <div class="mt-8 grid items-start gap-8 lg:grid-cols-[1fr_330px]">
            <article class="flex min-w-0 flex-col divide-y [&>*:not(:first-child)]:pt-8 [&>*:not(:last-child)]:pb-8">
                @php
                    $secaoTitulo = 'inline-flex w-fit items-center rounded-4xl bg-secondary px-3 py-1 text-[1.25rem] font-bold uppercase tracking-wider text-secondary-foreground';
                @endphp
                @if ($vaga->descricao)
                    <section>
                        <h2 class="{{ $secaoTitulo }}">Sobre a vaga</h2>
                        <div class="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{{ $vaga->descricao }}</div>
                    </section>
                @endif
                @if ($vaga->requisitos)
                    <section>
                        <h2 class="{{ $secaoTitulo }}">Requisitos</h2>
                        <div class="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{{ $vaga->requisitos }}</div>
                    </section>
                @endif
                @if ($vaga->beneficios)
                    <section>
                        <h2 class="{{ $secaoTitulo }}">Benefícios</h2>
                        <div class="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{{ $vaga->beneficios }}</div>
                    </section>
                @endif
                @if ($vaga->documentacao)
                    <section>
                        <h2 class="{{ $secaoTitulo }}">Documentação necessária</h2>
                        <div class="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{{ $vaga->documentacao }}</div>
                    </section>
                @endif
                @if ($vaga->horario)
                    <section>
                        <h2 class="{{ $secaoTitulo }}">Horário</h2>
                        <div class="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{{ $vaga->horario }}</div>
                    </section>
                @endif
                @if ($vaga->projetoNome)
                    <section>
                        <h2 class="{{ $secaoTitulo }}">Projeto</h2>
                        <div class="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{{ $vaga->projetoNome }}</div>
                    </section>
                @endif
            </article>

            <aside class="flex flex-col gap-4 lg:sticky lg:top-20">
                <div class="rounded-xl bg-card/45 p-5 shadow-lg shadow-black/[0.07] backdrop-blur-2xl backdrop-saturate-150 dark:bg-card/30 dark:shadow-black/40">
                    @php $dias = $vaga->diasRestantes(); @endphp
                    <div class="{{ $dias !== null && $dias <= 5 ? 'inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary dark:bg-primary/15' : 'inline-flex items-center gap-1.5 rounded-full bg-accent/80 px-3 py-1 text-xs font-semibold text-accent-foreground backdrop-blur-sm' }}">
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                        @if ($dias === null)
                            Inscrições abertas
                        @elseif ($dias === 0)
                            Encerra hoje
                        @elseif ($dias <= 5)
                            Últimos {{ $dias }} {{ $dias === 1 ? 'dia' : 'dias' }} para se inscrever
                        @else
                            Inscrições até {{ $vaga->dataEncerramentoFormatada() }}
                        @endif
                    </div>

                    <div class="mt-4 flex flex-col gap-3">
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <span class="inline-flex items-center gap-2 text-muted-foreground">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 14a2 2 0 0 1 0 4h-2v-4h2Z"/><path d="M9 10a2 2 0 0 0 0 4h6"/><circle cx="12" cy="12" r="10"/></svg>
                                Remuneração
                            </span>
                            <span class="text-right font-medium">{{ $vaga->remuneracaoFormatada() ?? 'A combinar' }}</span>
                        </div>
                        @if ($vaga->cargaHoraria)
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="inline-flex items-center gap-2 text-muted-foreground">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    Carga horária
                                </span>
                                <span class="text-right font-medium">{{ $vaga->cargaHoraria }}</span>
                            </div>
                        @endif
                        @if ($vaga->escolaridade)
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="inline-flex items-center gap-2 text-muted-foreground">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6M6 12.5V16a6 3 0 0 0 12 0v-3.5"/></svg>
                                    Escolaridade
                                </span>
                                <span class="text-right font-medium">{{ $vaga->escolaridade }}</span>
                            </div>
                        @endif
                        @if ($vaga->experiencia)
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="inline-flex items-center gap-2 text-muted-foreground">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                    Experiência
                                </span>
                                <span class="text-right font-medium">{{ $vaga->experiencia }}</span>
                            </div>
                        @endif
                        @if ($vaga->localizacao())
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="inline-flex items-center gap-2 text-muted-foreground">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                                    Local
                                </span>
                                <span class="text-right font-medium">{{ $vaga->localizacao() }}</span>
                            </div>
                        @endif
                    </div>

                    <x-ui.button tag="a" href="{{ route('inscricao.create', $vaga->codigo) }}" size="lg" class="mt-5 h-11 w-full gap-2 text-base font-semibold">
                        Candidatar-se agora
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </x-ui.button>
                </div>

                @php
                    $linkWhatsApp = 'https://wa.me/?text=' . urlencode($vaga->titulo . ', Portal de Vagas FAPEU: ' . route('vagas.publicas.show', $vaga->codigo));
                @endphp
                <div class="flex items-center justify-center gap-2 rounded-xl bg-card/45 p-2 backdrop-blur-2xl backdrop-saturate-150 dark:bg-card/30">
                    <x-ui.button variant="outline" size="sm" data-copiar-link class="gap-1.5">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                        <span data-copiar-link-label>Copiar link</span>
                    </x-ui.button>
                    <x-ui.button tag="a" href="{{ $linkWhatsApp }}" target="_blank" rel="noreferrer" variant="outline" size="sm" class="gap-1.5">
                        <x-icons.whats-app class="size-4" />
                        WhatsApp
                    </x-ui.button>
                </div>

                <a href="{{ route('alertas.create') }}" class="inline-flex items-center justify-center gap-1.5 text-xs text-muted-foreground transition-colors hover:text-foreground">
                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/></svg>
                    Quero receber vagas como esta por e-mail
                </a>
            </aside>
        </div>
    </div>
</x-layouts.public>
