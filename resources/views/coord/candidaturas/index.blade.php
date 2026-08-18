{{-- $vaga: Vaga. $candidaturas: LengthAwarePaginator (itens já resumidos). $contadores: array. $filtros: array --}}
<x-layouts.internal :title="'Candidaturas: ' . $vaga->titulo" page-title="Candidaturas da vaga" breadcrumb="Coordenador / Vagas">
    <a href="{{ route('coord.vagas.index') }}" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7M19 12H5"/></svg>
        Minhas vagas
    </a>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-card p-5 ring-1 ring-foreground/10">
        <div class="min-w-0">
            <h1 class="truncate text-lg font-bold tracking-tight">{{ $vaga->titulo }}</h1>
            <p class="mt-0.5 text-xs text-muted-foreground">Inscrições até {{ $vaga->data_encerramento?->format('d/m/Y') }}</p>
        </div>
        <x-badges.status-vaga :status="$vaga->status" />
    </div>

    <form id="filtro-candidaturas" method="GET" action="{{ route('coord.candidaturas.index', $vaga) }}" data-vagas-filtro class="hidden"></form>

    <div class="mt-5">
        <x-contadores-status :contadores="$contadores" :ativo="$filtros['status'] ?? null" form-id="filtro-candidaturas" />
    </div>

    <div class="relative mt-4">
        <svg class="pointer-events-none absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <x-ui.input name="busca" form="filtro-candidaturas" value="{{ $filtros['busca'] ?? '' }}" placeholder="Buscar por nome, e-mail, CPF ou curso…" class="bg-card pl-8" />
    </div>

    <div class="mt-4 overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10">
        <x-candidaturas-tabela :candidaturas="$candidaturas->items()" />
    </div>

    <x-pagination :paginator="$candidaturas" class="mt-4" />
</x-layouts.internal>
