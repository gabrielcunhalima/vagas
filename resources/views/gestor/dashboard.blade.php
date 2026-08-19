{{-- $stats: array. $vagasPendentes: Collection<array> --}}
<x-layouts.internal title="Dashboard" page-title="Dashboard" breadcrumb="Gestor">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Aguardando autorização" value="{{ $stats['aguardando_aut'] }}" tone="amber" href="{{ route('gestor.vagas.index', ['status' => 'aguardando_autorizacao']) }}">
            <x-slot:icon><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></x-slot:icon>
        </x-stat-card>
        <x-stat-card label="Vagas ativas no portal" value="{{ $stats['total_ativas'] }}" tone="primary">
            <x-slot:icon><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/></svg></x-slot:icon>
        </x-stat-card>
        <x-stat-card label="Autorizadas por mim" value="{{ $stats['autorizadas'] }}" tone="blue" href="{{ route('gestor.vagas.index', ['status' => 'ativa']) }}">
            <x-slot:icon><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><path d="M12 3c1 1.5 3 2 5 2a3.5 3.5 0 0 1 0 7c0 2-1.5 3.5-3 5-1.5-1.5-3-3-3-5a3.5 3.5 0 0 1 0-7c2 0 4-.5 5-2Z"/></svg></x-slot:icon>
        </x-stat-card>
        <x-stat-card label="Recusadas por mim" value="{{ $stats['recusadas'] }}" tone="red" href="{{ route('gestor.vagas.index', ['status' => 'recusada']) }}">
            <x-slot:icon><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></svg></x-slot:icon>
        </x-stat-card>
    </div>

    <section class="mt-6 overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10">
        <div class="flex items-center justify-between border-b px-5 py-3.5">
            <h2 class="text-sm font-bold">Vagas aguardando sua autorização</h2>
            <x-ui.button tag="a" href="{{ route('gestor.vagas.index') }}" variant="ghost" size="sm" class="gap-1.5 text-muted-foreground">
                Ver todas
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </x-ui.button>
        </div>

        @if ($vagasPendentes->isEmpty())
            <x-empty-state title="Tudo em dia" description="Nenhuma vaga aguardando autorização no momento.">
                <x-slot:icon><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><path d="M12 3c1 1.5 3 2 5 2a3.5 3.5 0 0 1 0 7c0 2-1.5 3.5-3 5-1.5-1.5-3-3-3-5a3.5 3.5 0 0 1 0-7c2 0 4-.5 5-2Z"/></svg></x-slot:icon>
            </x-empty-state>
        @else
            <x-ui.table>
                <x-ui.table-header>
                    <x-ui.table-row>
                        <x-ui.table-head>Vaga</x-ui.table-head>
                        <x-ui.table-head>Coordenador</x-ui.table-head>
                        <x-ui.table-head>Área</x-ui.table-head>
                        <x-ui.table-head>Enviada em</x-ui.table-head>
                        <x-ui.table-head class="text-right">Ação</x-ui.table-head>
                    </x-ui.table-row>
                </x-ui.table-header>
                <x-ui.table-body>
                    @foreach ($vagasPendentes as $v)
                        <x-ui.table-row>
                            <x-ui.table-cell class="max-w-72">
                                <div class="flex items-center gap-2">
                                    <x-badges.tipo :tipo="$v['tipo']" />
                                    <span class="truncate font-medium">{{ $v['titulo'] }}</span>
                                </div>
                            </x-ui.table-cell>
                            <x-ui.table-cell class="text-muted-foreground">{{ $v['coordenador']['name'] ?? 'N/A' }}</x-ui.table-cell>
                            <x-ui.table-cell class="text-muted-foreground">{{ $v['area'] }}</x-ui.table-cell>
                            <x-ui.table-cell class="text-muted-foreground">{{ \Illuminate\Support\Carbon::parse($v['created_at'])->format('d/m/Y') }}</x-ui.table-cell>
                            <x-ui.table-cell class="text-right">
                                <x-ui.button tag="a" href="{{ route('gestor.vagas.show', $v['id']) }}" size="sm" variant="outline" class="gap-1.5">
                                    Analisar
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </x-ui.button>
                            </x-ui.table-cell>
                        </x-ui.table-row>
                    @endforeach
                </x-ui.table-body>
            </x-ui.table>
        @endif
    </section>
</x-layouts.internal>
