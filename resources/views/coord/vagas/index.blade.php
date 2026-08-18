{{-- $vagas: LengthAwarePaginator<Vaga>. $areas: array<string>. $filtros: array --}}
@php
    $filtros = $filtros ?? [];
@endphp
<x-layouts.internal title="Minhas vagas" page-title="Minhas vagas" breadcrumb="Coordenador">
    <x-slot:topbarActions>
        <x-ui.button tag="a" href="{{ route('coord.vagas.create') }}" size="sm" class="gap-1.5">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8"/></svg>
            Nova vaga
        </x-ui.button>
    </x-slot:topbarActions>

    <form id="filtro-coord-vagas" method="GET" action="{{ route('coord.vagas.index') }}" data-vagas-filtro class="flex flex-wrap items-center gap-2 rounded-xl bg-card p-3 ring-1 ring-foreground/10">
        <div class="relative min-w-52 flex-1">
            <svg class="pointer-events-none absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <x-ui.input name="busca" value="{{ $filtros['busca'] ?? '' }}" placeholder="Buscar por título, descrição, projeto…" class="pl-8" />
        </div>

        <select name="status" data-auto-apply class="h-9 w-44 rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
            <option value="">Todos os status</option>
            @foreach (\App\Models\Vagas\Vaga::$statusLabel as $v => $l)
                <option value="{{ $v }}" @selected(($filtros['status'] ?? '') === $v)>{{ $l }}</option>
            @endforeach
        </select>

        <select name="tipo" data-auto-apply class="h-9 w-40 rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
            <option value="">Todos os tipos</option>
            @foreach (\App\Models\Vagas\Vaga::$tiposLabel as $v => $l)
                <option value="{{ $v }}" @selected(($filtros['tipo'] ?? '') === $v)>{{ $l }}</option>
            @endforeach
        </select>

        <select name="area" data-auto-apply class="h-9 w-48 rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
            <option value="">Todas as áreas</option>
            @foreach ($areas as $a)
                <option value="{{ $a }}" @selected(($filtros['area'] ?? '') === $a)>{{ $a }}</option>
            @endforeach
        </select>
    </form>

    <div class="mt-4 overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10">
        @if ($vagas->isEmpty())
            <x-empty-state title="Nenhuma vaga encontrada" description="Ajuste os filtros ou crie uma nova vaga.">
                <x-slot:icon>
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </x-slot:icon>
                <x-ui.button tag="a" href="{{ route('coord.vagas.create') }}" size="sm">Nova vaga</x-ui.button>
            </x-empty-state>
        @else
            <x-ui.table>
                <x-ui.table-header>
                    <x-ui.table-row>
                        <x-ui.table-head>Vaga</x-ui.table-head>
                        <x-ui.table-head>Área</x-ui.table-head>
                        <x-ui.table-head>Status</x-ui.table-head>
                        <x-ui.table-head>Encerramento</x-ui.table-head>
                        <x-ui.table-head class="text-center">Candidaturas</x-ui.table-head>
                        <x-ui.table-head class="w-12" />
                    </x-ui.table-row>
                </x-ui.table-header>
                <x-ui.table-body>
                    @foreach ($vagas as $v)
                        @php $podeEditar = !in_array($v->status, ['ativa', 'encerrada']); @endphp
                        <x-ui.table-row>
                            <x-ui.table-cell class="max-w-72">
                                <div class="flex items-center gap-2">
                                    <x-badges.tipo :tipo="$v->tipo" />
                                    @if ($podeEditar)
                                        <a href="{{ route('coord.vagas.edit', $v) }}" class="truncate font-medium hover:text-primary">{{ $v->titulo }}</a>
                                    @else
                                        <span class="truncate font-medium">{{ $v->titulo }}</span>
                                    @endif
                                </div>
                            </x-ui.table-cell>
                            <x-ui.table-cell class="text-muted-foreground">{{ $v->area }}</x-ui.table-cell>
                            <x-ui.table-cell><x-badges.status-vaga :status="$v->status" /></x-ui.table-cell>
                            <x-ui.table-cell class="text-muted-foreground">{{ $v->data_encerramento?->format('d/m/Y') }}</x-ui.table-cell>
                            <x-ui.table-cell class="text-center">
                                <a href="{{ route('coord.candidaturas.index', $v) }}" class="font-semibold text-primary hover:underline">{{ $v->candidaturas_count }}</a>
                            </x-ui.table-cell>
                            <x-ui.table-cell>
                                <details class="group relative">
                                    <summary class="flex size-8 cursor-pointer list-none items-center justify-center rounded-md hover:bg-muted [&::-webkit-details-marker]:hidden" aria-label="Ações">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                                    </summary>
                                    <div class="absolute right-0 z-10 mt-1 min-w-52 rounded-lg bg-popover p-1 text-sm text-popover-foreground shadow-lg ring-1 ring-foreground/10">
                                        <a href="{{ route('coord.candidaturas.index', $v) }}" class="flex items-center gap-2 rounded-md px-3 py-2 hover:bg-muted">
                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                            Ver candidaturas
                                        </a>
                                        @if ($podeEditar)
                                            <a href="{{ route('coord.vagas.edit', $v) }}" class="flex items-center gap-2 rounded-md px-3 py-2 hover:bg-muted">
                                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                                Editar
                                            </a>
                                        @endif
                                        @if ($v->status === 'rascunho')
                                            <form method="POST" action="{{ route('coord.vagas.submeter', $v) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left hover:bg-muted">
                                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 3 3 9-3 9 19-9Z"/><path d="M6 12h16"/></svg>
                                                    Enviar para autorização
                                                </button>
                                            </form>
                                        @endif
                                        @if ($v->status === 'ativa')
                                            <form method="POST" action="{{ route('coord.vagas.desativar', $v) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left hover:bg-muted">
                                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v10"/><path d="M18.4 6.6a9 9 0 1 1-12.77.04"/></svg>
                                                    Desativar
                                                </button>
                                            </form>
                                        @endif
                                        @if ($v->status === 'inativa')
                                            <form method="POST" action="{{ route('coord.vagas.reativar', $v) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left hover:bg-muted">
                                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v10"/><path d="M18.4 6.6a9 9 0 1 1-12.77.04"/></svg>
                                                    Reativar
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('coord.vagas.notificacao', $v) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left hover:bg-muted">
                                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/></svg>
                                                {{ $v->notificar_email ? 'Silenciar notificações' : 'Ativar notificações' }}
                                            </button>
                                        </form>
                                        <div class="my-1 h-px bg-border"></div>
                                        <button type="button" data-dialog-trigger="excluir-vaga-{{ $v->id }}" class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-destructive hover:bg-destructive/10">
                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14Z"/></svg>
                                            Excluir
                                        </button>
                                    </div>
                                </details>

                                <dialog data-dialog="excluir-vaga-{{ $v->id }}" class="w-full max-w-md rounded-xl">
                                    <form method="POST" action="{{ route('coord.vagas.destroy', $v) }}" class="bg-card p-6 text-left ring-1 ring-foreground/10">
                                        @csrf @method('DELETE')
                                        <h2 class="text-base font-bold tracking-tight">Excluir vaga?</h2>
                                        <p class="mt-2 text-sm text-muted-foreground">A vaga <span class="font-semibold text-foreground">"{{ $v->titulo }}"</span> será removida do portal. Esta ação não pode ser desfeita.</p>
                                        <div class="mt-5 flex justify-end gap-2">
                                            <x-ui.button type="button" variant="outline" data-dialog-close>Cancelar</x-ui.button>
                                            <x-ui.button type="submit" variant="destructive">Excluir</x-ui.button>
                                        </div>
                                    </form>
                                </dialog>
                            </x-ui.table-cell>
                        </x-ui.table-row>
                    @endforeach
                </x-ui.table-body>
            </x-ui.table>
        @endif
    </div>

    <x-pagination :paginator="$vagas" class="mt-4" />
</x-layouts.internal>
