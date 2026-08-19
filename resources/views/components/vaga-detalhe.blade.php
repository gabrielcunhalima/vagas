{{-- $vaga: App\Support\Drhflow\VagaDrhflow. $comVoltar: mostra o link "Voltar
     para a lista" (usado dentro do off-canvas mobile). Único parcial para as
     duas apresentações — coluna fixa (>= xl) e conteúdo do off-canvas (< xl) —
     para não existirem duas versões do detalhe. --}}
@props(['vaga', 'comVoltar' => false])
<article {{ $attributes->merge(['class' => 'bg-card']) }}>
    <div class="sticky top-0 z-1 border-b bg-card px-5 pb-4 pt-5">
        @if ($comVoltar)
            <button type="button" data-sheet-close class="mb-3 inline-flex cursor-pointer items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7M19 12H5"/></svg>
                Voltar para a lista
            </button>
        @endif

        <div class="flex flex-wrap items-center gap-2">
            <x-badges.tipo-admissao :tipo="$vaga->tipo" :codigo="$vaga->tipoCodigo" />
            @if ($vaga->isNova())
                <x-badges.nova />
            @endif
        </div>

        <h2 class="mt-3 text-xl font-bold leading-tight tracking-tight">{{ $vaga->titulo }}</h2>

        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-muted-foreground">
            <span>{{ $vaga->localizacao() ?? 'A definir' }}</span>
            @if ($vaga->projetoNome)
                <span aria-hidden>·</span>
                <span>Projeto — {{ $vaga->projetoNome }}</span>
            @endif
        </div>

        <x-ui.button tag="a" href="{{ route('inscricao.create', $vaga->codigo) }}" size="lg" class="mt-4 h-11 w-full gap-2 px-8 text-base font-semibold sm:w-auto sm:min-w-52">
            Candidatar-se
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </x-ui.button>
    </div>

    <div class="max-w-4xl px-5 pb-6 pt-5">
        <div class="grid grid-cols-2 gap-x-4 gap-y-3.5 rounded-xl bg-muted/50 p-4">
            <div class="flex min-w-0 flex-col gap-0.5">
                <span class="flex items-start gap-1.5 text-xs leading-tight text-muted-foreground">
                    <svg class="mt-px size-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 14a2 2 0 0 1 0 4h-2v-4h2Z"/><path d="M9 10a2 2 0 0 0 0 4h6"/><circle cx="12" cy="12" r="10"/></svg>
                    <span class="min-w-0">Remuneração</span>
                </span>
                <span class="text-sm font-semibold">{{ $vaga->remuneracaoFormatada() ?? 'A combinar' }}</span>
            </div>
            @if ($vaga->cargaHoraria)
                <div class="flex min-w-0 flex-col gap-0.5">
                    <span class="flex items-start gap-1.5 text-xs leading-tight text-muted-foreground">
                        <svg class="mt-px size-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span class="min-w-0">Carga horária</span>
                    </span>
                    <span class="text-sm font-semibold">{{ $vaga->cargaHoraria }}</span>
                </div>
            @endif
            @if ($vaga->escolaridade)
                <div class="flex min-w-0 flex-col gap-0.5">
                    <span class="flex items-start gap-1.5 text-xs leading-tight text-muted-foreground">
                        <svg class="mt-px size-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6M6 12.5V16a6 3 0 0 0 12 0v-3.5"/></svg>
                        <span class="min-w-0">Escolaridade</span>
                    </span>
                    <span class="text-sm font-semibold">{{ $vaga->escolaridade }}</span>
                </div>
            @endif
            @if ($vaga->localizacao())
                <div class="flex min-w-0 flex-col gap-0.5">
                    <span class="flex items-start gap-1.5 text-xs leading-tight text-muted-foreground">
                        <svg class="mt-px size-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                        <span class="min-w-0">Localização</span>
                    </span>
                    <span class="text-sm font-semibold">{{ $vaga->localizacao() }}</span>
                </div>
            @endif
            @if ($vaga->experiencia)
                <div class="flex min-w-0 flex-col gap-0.5">
                    <span class="flex items-start gap-1.5 text-xs leading-tight text-muted-foreground">
                        <svg class="mt-px size-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        <span class="min-w-0">Experiência</span>
                    </span>
                    <span class="text-sm font-semibold">{{ $vaga->experiencia }}</span>
                </div>
            @endif
            <div class="flex min-w-0 flex-col gap-0.5">
                <span class="flex items-start gap-1.5 text-xs leading-tight text-muted-foreground">
                    <svg class="mt-px size-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                    <span class="min-w-0">Inscrições até</span>
                </span>
                <span class="text-sm font-semibold {{ $vaga->diasRestantes() !== null && $vaga->diasRestantes() <= 5 ? 'text-primary' : '' }}">{{ $vaga->dataEncerramentoFormatada() ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="mt-6 flex flex-col gap-6">
            @if ($vaga->descricao)
                <section>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-muted-foreground">Sobre a vaga</h3>
                    <div class="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{{ $vaga->descricao }}</div>
                </section>
            @endif
            @if ($vaga->requisitos)
                <section>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-muted-foreground">Requisitos</h3>
                    <div class="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{{ $vaga->requisitos }}</div>
                </section>
            @endif
            @if ($vaga->beneficios)
                <section>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-muted-foreground">Benefícios</h3>
                    <div class="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{{ $vaga->beneficios }}</div>
                </section>
            @endif
            @if ($vaga->documentacao)
                <section>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-muted-foreground">Documentação necessária</h3>
                    <div class="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{{ $vaga->documentacao }}</div>
                </section>
            @endif
            @if ($vaga->horario)
                <section>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-muted-foreground">Horário</h3>
                    <div class="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{{ $vaga->horario }}</div>
                </section>
            @endif
            @if ($vaga->projetoNome)
                <section>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-muted-foreground">Projeto</h3>
                    <div class="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{{ $vaga->projetoNome }}</div>
                </section>
            @endif
        </div>
    </div>
</article>
