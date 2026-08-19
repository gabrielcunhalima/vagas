{{-- $candidaturas: LengthAwarePaginator (itens resumidos). $vagas: Collection<Vaga>. $contadores: array. $filtros: array --}}
<x-layouts.internal title="Candidaturas" page-title="Candidaturas" breadcrumb="Coordenador">
    <form id="filtro-todas-candidaturas" method="GET" action="{{ route('coord.candidaturas.todas') }}" data-vagas-filtro class="hidden"></form>

    <x-contadores-status :contadores="$contadores" :ativo="$filtros['status'] ?? null" form-id="filtro-todas-candidaturas" />

    <div class="mt-4 flex flex-wrap items-center gap-2 rounded-xl bg-card p-3 ring-1 ring-foreground/10">
        <div class="relative min-w-52 flex-1">
            <svg class="pointer-events-none absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <x-ui.input name="busca" form="filtro-todas-candidaturas" value="{{ $filtros['busca'] ?? '' }}" placeholder="Buscar por nome, e-mail, CPF ou curso…" class="pl-8" />
        </div>

        <select name="vaga_id" form="filtro-todas-candidaturas" data-auto-apply class="h-9 w-64 rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
            <option value="">Todas as vagas</option>
            @foreach ($vagas as $v)
                <option value="{{ $v->id }}" @selected((string) ($filtros['vaga_id'] ?? '') === (string) $v->id)>{{ $v->titulo }}</option>
            @endforeach
        </select>
    </div>

    <div class="mt-4 overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10">
        <x-candidaturas-tabela :candidaturas="$candidaturas->items()" mostrar-vaga />
    </div>

    <x-pagination :paginator="$candidaturas" class="mt-4" />
</x-layouts.internal>
