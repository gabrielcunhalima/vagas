{{-- $candidaturas: Collection<InscricaoDrhflow>. $indisponivel: bool --}}
<x-layouts.public title="Minhas candidaturas">
    <div class="mx-auto w-full max-w-3xl px-4 pt-10">
        <h1 class="text-2xl font-bold tracking-tight">Minhas candidaturas</h1>
        <p class="mt-1 text-sm text-muted-foreground">Acompanhe aqui o andamento de cada processo seletivo.</p>

        @if ($indisponivel)
            <div class="mt-6 rounded-xl bg-card ring-1 ring-foreground/10">
                <x-empty-state title="Candidaturas temporariamente indisponíveis" description="Não conseguimos consultar suas candidaturas agora. Elas continuam registradas — tente novamente em alguns minutos.">
                    <x-slot:icon>
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4M12 17h.01"/></svg>
                    </x-slot:icon>
                </x-empty-state>
            </div>
        @elseif ($candidaturas->isEmpty())
            <div class="mt-6 rounded-xl bg-card ring-1 ring-foreground/10">
                <x-empty-state title="Você ainda não se candidatou" description="Explore as vagas abertas e envie sua primeira candidatura, leva menos de 5 minutos.">
                    <x-slot:icon>
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </x-slot:icon>
                    <x-ui.button tag="a" href="{{ route('vagas.publicas.index') }}" class="gap-1.5">
                        Ver vagas abertas
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </x-ui.button>
                </x-empty-state>
            </div>
        @else
            <div class="mt-6 flex flex-col gap-4">
                @foreach ($candidaturas as $c)
                    <a href="{{ route('candidato.candidaturas.show', $c->cdVagaEmprego) }}" class="group block rounded-xl bg-card p-5 ring-1 ring-foreground/10 transition-all hover:shadow-md hover:ring-primary/40">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($c->vaga)
                                        <x-badges.tipo-admissao :tipo="$c->vaga->tipo" :codigo="$c->vaga->tipoCodigo" />
                                    @endif
                                    <x-andamento-badge :andamento="$c->andamento()" :rotulo="$c->andamentoRotulo()" />
                                </div>
                                <h2 class="mt-2 font-semibold leading-snug transition-colors group-hover:text-primary">{{ $c->vaga->titulo ?? 'Vaga encerrada' }}</h2>
                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted-foreground">
                                    @if ($c->vaga && $c->vaga->localizacao())
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                                            {{ $c->vaga->localizacao() }}
                                        </span>
                                    @endif
                                    @if ($c->vaga?->projetoNome)
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                            <span class="line-clamp-1">{{ $c->vaga->projetoNome }}</span>
                                        </span>
                                    @endif
                                    @if ($c->enviadaEm)
                                        <span>Enviada em {{ $c->enviadaEm->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            </div>
                            <svg class="mt-1 size-4 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-0.5 group-hover:text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </div>
                        <x-andamento-inscricao :andamento="$c->andamento()" class="mt-5" />
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.public>
