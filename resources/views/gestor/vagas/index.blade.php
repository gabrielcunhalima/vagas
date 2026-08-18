{{-- $vagas: LengthAwarePaginator<array>. $status: string. $busca: string --}}
<x-layouts.internal title="Autorizar vagas" page-title="Autorizar vagas" breadcrumb="Gestor">
    <div class="flex flex-wrap gap-1.5">
        @foreach (['aguardando_autorizacao' => 'Aguardando', 'ativa' => 'Autorizadas', 'recusada' => 'Recusadas'] as $valor => $label)
            <a href="{{ route('gestor.vagas.index', array_filter(['status' => $valor, 'busca' => $busca])) }}" class="cursor-pointer rounded-full px-3.5 py-1.5 text-xs font-semibold transition-colors {{ $status === $valor ? 'bg-primary text-primary-foreground' : 'bg-card text-muted-foreground ring-1 ring-foreground/10 hover:text-foreground' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('gestor.vagas.index') }}" class="relative mt-4">
        <svg class="pointer-events-none absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <x-ui.input name="busca" value="{{ $busca }}" placeholder="Buscar por título, descrição ou projeto…" class="bg-card pl-8" />
        <input type="hidden" name="status" value="{{ $status }}">
    </form>

    <div class="mt-4 overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10">
        @if ($vagas->isEmpty())
            <x-empty-state title="Nenhuma vaga aqui" :description="$status === 'aguardando_autorizacao' ? 'Nenhuma vaga aguardando autorização no momento.' : 'Nenhuma vaga encontrada com este filtro.'">
                <x-slot:icon><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><path d="M12 3c1 1.5 3 2 5 2a3.5 3.5 0 0 1 0 7c0 2-1.5 3.5-3 5-1.5-1.5-3-3-3-5a3.5 3.5 0 0 1 0-7c2 0 4-.5 5-2Z"/></svg></x-slot:icon>
            </x-empty-state>
        @else
            <x-ui.table>
                <x-ui.table-header>
                    <x-ui.table-row>
                        <x-ui.table-head>Vaga</x-ui.table-head>
                        <x-ui.table-head>Coordenador</x-ui.table-head>
                        <x-ui.table-head>Área</x-ui.table-head>
                        <x-ui.table-head>Status</x-ui.table-head>
                        <x-ui.table-head>Encerramento</x-ui.table-head>
                        <x-ui.table-head class="w-24" />
                    </x-ui.table-row>
                </x-ui.table-header>
                <x-ui.table-body>
                    @foreach ($vagas as $v)
                        <x-ui.table-row>
                            <x-ui.table-cell class="max-w-72">
                                <div class="flex items-center gap-2">
                                    <x-badges.tipo :tipo="$v['tipo']" />
                                    <a href="{{ route('gestor.vagas.show', $v['id']) }}" class="truncate font-medium hover:text-primary">{{ $v['titulo'] }}</a>
                                </div>
                            </x-ui.table-cell>
                            <x-ui.table-cell class="text-muted-foreground">{{ $v['coordenador']['name'] ?? 'N/A' }}</x-ui.table-cell>
                            <x-ui.table-cell class="text-muted-foreground">{{ $v['area'] }}</x-ui.table-cell>
                            <x-ui.table-cell><x-badges.status-vaga :status="$v['status']" /></x-ui.table-cell>
                            <x-ui.table-cell class="text-muted-foreground">{{ \Illuminate\Support\Carbon::parse($v['data_encerramento'])->format('d/m/Y') }}</x-ui.table-cell>
                            <x-ui.table-cell class="text-right">
                                <x-ui.button tag="a" href="{{ route('gestor.vagas.show', $v['id']) }}" size="sm" variant="outline" class="gap-1.5">
                                    {{ $v['status'] === 'aguardando_autorizacao' ? 'Analisar' : 'Ver' }}
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </x-ui.button>
                            </x-ui.table-cell>
                        </x-ui.table-row>
                    @endforeach
                </x-ui.table-body>
            </x-ui.table>
        @endif
    </div>

    <x-pagination :paginator="$vagas" class="mt-4" />
</x-layouts.internal>
