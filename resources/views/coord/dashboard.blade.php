{{-- $stats: array. $vagasRecentes: Collection<Vaga>. $candidaturasRecentes: Collection<array> --}}
<x-layouts.internal title="Dashboard" page-title="Dashboard" breadcrumb="Coordenador">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Vagas criadas" value="{{ $stats['total_vagas'] }}" tone="neutral" href="{{ route('coord.vagas.index') }}">
            <x-slot:icon><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></x-slot:icon>
        </x-stat-card>
        <x-stat-card label="Vagas ativas" value="{{ $stats['vagas_ativas'] }}" tone="primary" href="{{ route('coord.vagas.index', ['status' => 'ativa']) }}">
            <x-slot:icon><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/></svg></x-slot:icon>
        </x-stat-card>
        <x-stat-card label="Aguardando autorização" value="{{ $stats['aguardando_aut'] }}" tone="amber" href="{{ route('coord.vagas.index', ['status' => 'aguardando_autorizacao']) }}">
            <x-slot:icon><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></x-slot:icon>
        </x-stat-card>
        <x-stat-card label="Rascunhos" value="{{ $stats['vagas_rascunho'] }}" tone="neutral" href="{{ route('coord.vagas.index', ['status' => 'rascunho']) }}">
            <x-slot:icon><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7M7 3v4a1 1 0 0 0 1 1h7"/></svg></x-slot:icon>
        </x-stat-card>
        <x-stat-card label="Candidaturas recebidas" value="{{ $stats['total_candidatos'] }}" tone="blue" href="{{ route('coord.candidaturas.todas') }}">
            <x-slot:icon><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></x-slot:icon>
        </x-stat-card>
        <x-stat-card label="Novas (sem análise)" value="{{ $stats['candidatos_novos'] }}" tone="sky" href="{{ route('coord.candidaturas.todas', ['status' => 'recebida']) }}">
            <x-slot:icon><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></x-slot:icon>
        </x-stat-card>
        <x-stat-card label="Entrevistas hoje" value="{{ $stats['entrevistas_hoje'] }}" tone="violet" href="{{ route('coord.candidaturas.todas', ['status' => 'entrevista']) }}">
            <x-slot:icon><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><circle cx="18" cy="16" r="2"/><path d="M18 16v-1.5"/></svg></x-slot:icon>
        </x-stat-card>
    </div>

    <div class="mt-6 grid items-start gap-6 xl:grid-cols-[3fr_2fr]">
        <section class="overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10">
            <div class="flex items-center justify-between border-b px-5 py-3.5">
                <h2 class="text-sm font-bold">Vagas recentes</h2>
                <x-ui.button tag="a" href="{{ route('coord.vagas.index') }}" variant="ghost" size="sm" class="gap-1.5 text-muted-foreground">
                    Ver todas
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </x-ui.button>
            </div>

            @if ($vagasRecentes->isEmpty())
                <x-empty-state title="Nenhuma vaga criada" description="Crie sua primeira vaga para começar a receber candidaturas.">
                    <x-slot:icon><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></x-slot:icon>
                    <x-ui.button tag="a" href="{{ route('coord.vagas.create') }}" size="sm">Nova vaga</x-ui.button>
                </x-empty-state>
            @else
                <x-ui.table>
                    <x-ui.table-header>
                        <x-ui.table-row>
                            <x-ui.table-head>Vaga</x-ui.table-head>
                            <x-ui.table-head>Status</x-ui.table-head>
                            <x-ui.table-head>Encerramento</x-ui.table-head>
                            <x-ui.table-head class="text-right">Candidaturas</x-ui.table-head>
                        </x-ui.table-row>
                    </x-ui.table-header>
                    <x-ui.table-body>
                        @foreach ($vagasRecentes as $v)
                            <x-ui.table-row>
                                <x-ui.table-cell class="max-w-72">
                                    <div class="flex items-center gap-2">
                                        <x-badges.tipo :tipo="$v->tipo" />
                                        <span class="truncate font-medium">{{ $v->titulo }}</span>
                                    </div>
                                </x-ui.table-cell>
                                <x-ui.table-cell><x-badges.status-vaga :status="$v->status" /></x-ui.table-cell>
                                <x-ui.table-cell class="text-muted-foreground">{{ $v->data_encerramento?->format('d/m/Y') }}</x-ui.table-cell>
                                <x-ui.table-cell class="text-right">
                                    <a href="{{ route('coord.candidaturas.index', $v) }}" class="font-semibold text-primary hover:underline">{{ $v->candidaturas_count }}</a>
                                </x-ui.table-cell>
                            </x-ui.table-row>
                        @endforeach
                    </x-ui.table-body>
                </x-ui.table>
            @endif
        </section>

        <section class="overflow-hidden rounded-xl bg-card ring-1 ring-foreground/10">
            <div class="flex items-center justify-between border-b px-5 py-3.5">
                <h2 class="text-sm font-bold">Últimas candidaturas</h2>
                <x-ui.button tag="a" href="{{ route('coord.candidaturas.todas') }}" variant="ghost" size="sm" class="gap-1.5 text-muted-foreground">
                    Ver todas
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </x-ui.button>
            </div>

            @if ($candidaturasRecentes->isEmpty())
                <x-empty-state title="Nenhuma candidatura ainda" description="Assim que alguém se candidatar às suas vagas, aparecerá aqui.">
                    <x-slot:icon><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></x-slot:icon>
                </x-empty-state>
            @else
                <ul class="divide-y">
                    @foreach ($candidaturasRecentes as $c)
                        <li>
                            <a href="{{ route('coord.candidaturas.show', [$c['vaga_id'], $c['id']]) }}" class="flex items-center justify-between gap-3 px-5 py-3 transition-colors hover:bg-muted/50">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-medium">{{ $c['nome'] }}</div>
                                    <div class="truncate text-xs text-muted-foreground">{{ $c['vaga']['titulo'] ?? '' }} · {{ \Illuminate\Support\Carbon::parse($c['created_at'])->format('d/m/Y') }}</div>
                                </div>
                                <x-badges.status-candidatura :status="$c['status']" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</x-layouts.internal>
