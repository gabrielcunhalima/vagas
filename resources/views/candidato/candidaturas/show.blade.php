{{-- $candidatura: InscricaoDrhflow. $cartaApresentacao, $conflitoInteresse, $conflitoInteresseDetalhe,
     $curriculoNome, $temCurriculo: dados próprios do portal (InscricaoComplemento) --}}
@php $vaga = $candidatura->vaga; @endphp
<x-layouts.public :title="'Candidatura: ' . ($vaga->titulo ?? '')">
    <div class="mx-auto w-full max-w-3xl px-4 pt-10">
        <a href="{{ route('candidato.candidaturas.index') }}" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7M19 12H5"/></svg>
            Minhas candidaturas
        </a>

        <header class="mt-5">
            <div class="flex flex-wrap items-center gap-2">
                @if ($vaga)
                    <x-badges.tipo-admissao :tipo="$vaga->tipo" :codigo="$vaga->tipoCodigo" />
                @endif
                <x-andamento-badge :andamento="$candidatura->andamento()" :rotulo="$candidatura->andamentoRotulo()" />
            </div>
            <h1 class="mt-3 text-2xl font-bold leading-tight tracking-tight">{{ $vaga->titulo ?? 'Vaga encerrada' }}</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                @if ($candidatura->enviadaEm)
                    Candidatura enviada em {{ $candidatura->enviadaEm->format('d/m/Y') }}
                @endif
                @if ($vaga)
                    @if ($candidatura->enviadaEm) · @endif
                    <a href="{{ route('vagas.publicas.show', $vaga->codigo) }}" class="inline-flex items-center gap-1 font-medium text-primary hover:underline">
                        Ver vaga
                        <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6M10 14 21 3M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                    </a>
                @endif
            </p>
        </header>

        <div class="mt-7 rounded-xl bg-card p-6 ring-1 ring-foreground/10">
            <h2 class="text-sm font-bold">Andamento do processo</h2>
            <x-andamento-inscricao :andamento="$candidatura->andamento()" class="mt-5" />
            @if ($candidatura->andamento() === 'avaliacao_concluida')
                <p class="mt-5 text-sm text-muted-foreground">Sua avaliação foi concluída. O RH entrará em contato com o resultado do processo seletivo.</p>
            @endif
        </div>

        @if ($candidatura->entrevistaData)
            <div class="mt-4 rounded-xl bg-accent/60 p-5 ring-1 ring-primary/20">
                <h2 class="text-sm font-bold text-accent-foreground">Entrevista agendada</h2>
                <div class="mt-3 flex flex-col gap-2 text-sm">
                    <span class="inline-flex items-center gap-2">
                        <svg class="size-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><circle cx="18" cy="16" r="2"/><path d="M18 16v-1.5"/></svg>
                        {{ $candidatura->entrevistaData->format('d/m/Y') }}
                    </span>
                    @if ($candidatura->entrevistaHora)
                        <span class="inline-flex items-center gap-2">
                            <svg class="size-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ $candidatura->entrevistaHora }}
                        </span>
                    @endif
                    @if ($candidatura->entrevistaLocal)
                        <span class="inline-flex items-center gap-2">
                            <svg class="size-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $candidatura->entrevistaLocal }}
                        </span>
                    @endif
                </div>
            </div>
        @endif

        <div class="mt-4 rounded-xl bg-card p-6 ring-1 ring-foreground/10">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-sm font-bold">Envio</h2>
                @if ($temCurriculo)
                    <x-ui.button tag="a" href="{{ route('candidato.candidaturas.curriculo', $candidatura->cdVagaEmprego) }}" variant="outline" size="sm" class="gap-1.5">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                        Currículo enviado
                    </x-ui.button>
                @endif
            </div>

            <dl class="mt-5 grid gap-x-6 gap-y-4 sm:grid-cols-2">
                @if ($vaga && $vaga->localizacao())
                    <div class="flex flex-col gap-0.5">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Local</dt>
                        <dd class="text-sm">{{ $vaga->localizacao() }}</dd>
                    </div>
                @endif
                @if ($vaga?->projetoNome)
                    <div class="flex flex-col gap-0.5">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Projeto</dt>
                        <dd class="text-sm">{{ $vaga->projetoNome }}</dd>
                    </div>
                @endif
                @if ($curriculoNome)
                    <div class="flex flex-col gap-0.5">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Currículo enviado</dt>
                        <dd class="text-sm">{{ $curriculoNome }}</dd>
                    </div>
                @endif
                @if ($conflitoInteresse !== null)
                    <div class="flex flex-col gap-0.5">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Conflito de interesse</dt>
                        <dd class="text-sm">{{ $conflitoInteresse ? ($conflitoInteresseDetalhe ?: 'Declarado') : 'Não declarado' }}</dd>
                    </div>
                @endif
            </dl>

            @if ($cartaApresentacao)
                <div class="mt-5 border-t pt-5">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Carta de apresentação</h3>
                    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed">{{ $cartaApresentacao }}</p>
                </div>
            @endif
        </div>

        <p class="mt-4 text-center text-xs text-muted-foreground">
            Seus dados pessoais ficam no seu
            <a href="{{ route('candidato.perfil.edit') }}" class="font-medium text-primary hover:underline">perfil</a>.
            O que você atualizar lá vale para os processos em andamento.
        </p>
    </div>
</x-layouts.public>
