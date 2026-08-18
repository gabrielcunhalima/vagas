{{-- $candidaturas: iterable de arrays (CandidaturaController::candidaturaResumo). $mostrarVaga: bool --}}
@props(['candidaturas', 'mostrarVaga' => false])
@if (count($candidaturas) === 0)
    <x-empty-state title="Nenhuma candidatura encontrada" description="Ajuste os filtros ou aguarde novas candidaturas.">
        <x-slot:icon>
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </x-slot:icon>
    </x-empty-state>
@else
    <x-ui.table>
        <x-ui.table-header>
            <x-ui.table-row>
                <x-ui.table-head>Candidato</x-ui.table-head>
                <x-ui.table-head>Curso</x-ui.table-head>
                @if ($mostrarVaga)
                    <x-ui.table-head>Vaga</x-ui.table-head>
                @endif
                <x-ui.table-head>Status</x-ui.table-head>
                <x-ui.table-head>Recebida em</x-ui.table-head>
                <x-ui.table-head class="w-12" />
            </x-ui.table-row>
        </x-ui.table-header>
        <x-ui.table-body>
            @foreach ($candidaturas as $c)
                <x-ui.table-row>
                    <x-ui.table-cell class="max-w-56">
                        <a href="{{ route('coord.candidaturas.show', [$c['vaga_id'], $c['id']]) }}" class="block truncate font-medium hover:text-primary">
                            @if ($c['acesso_expirado'])
                                <span class="italic text-muted-foreground">Candidato não identificado</span>
                            @else
                                {{ $c['nome'] }}
                            @endif
                        </a>
                        <div class="flex items-center gap-1.5">
                            <span class="truncate text-xs text-muted-foreground">{{ $c['acesso_expirado'] ? 'Dados não disponíveis' : $c['email'] }}</span>
                            @if (!$c['acesso_expirado'] && $c['pcd'])
                                <x-ui.badge variant="secondary" class="h-6 gap-0.5 px-1.5 text-[0.725rem]">
                                    <svg class="size-2.5!" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="16" cy="4" r="1"/><path d="m18 19 1-7-6 1"/><path d="m5 8 3-3 5.5 3-2.36 3.5"/><path d="M4.24 14.5a5 5 0 0 0 6.88 6"/><path d="M13.76 17.5a5 5 0 0 0-6.88-6"/></svg>
                                    PcD
                                </x-ui.badge>
                            @endif
                        </div>
                    </x-ui.table-cell>
                    <x-ui.table-cell class="max-w-40 truncate text-muted-foreground">{{ $c['cursos'] }}</x-ui.table-cell>
                    @if ($mostrarVaga)
                        <x-ui.table-cell class="max-w-48 truncate text-muted-foreground">{{ $c['vaga']['titulo'] ?? 'N/A' }}</x-ui.table-cell>
                    @endif
                    <x-ui.table-cell><x-badges.status-candidatura :status="$c['status']" /></x-ui.table-cell>
                    <x-ui.table-cell class="text-muted-foreground">{{ \Illuminate\Support\Carbon::parse($c['created_at'])->format('d/m/Y') }}</x-ui.table-cell>
                    <x-ui.table-cell>
                        <a href="{{ route('coord.candidaturas.show', [$c['vaga_id'], $c['id']]) }}" class="inline-flex size-7 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-muted hover:text-foreground" aria-label="Ver candidatura">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </x-ui.table-cell>
                </x-ui.table-row>
            @endforeach
        </x-ui.table-body>
    </x-ui.table>
@endif
