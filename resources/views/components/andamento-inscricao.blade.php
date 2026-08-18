{{-- Três etapas fixas do andamento de uma inscrição no DRHFlow (ver InscricaoDrhflow::andamento()). --}}
@props(['andamento'])
@php
    $etapas = [
        'recebida' => 'Recebida',
        'entrevista_marcada' => 'Entrevista',
        'avaliacao_concluida' => 'Avaliação',
    ];
    $chaves = array_keys($etapas);
    $atual = max(0, array_search($andamento, $chaves, true) ?: 0);
@endphp
<ol {{ $attributes->merge(['class' => 'flex w-full items-start']) }}>
    @foreach ($etapas as $chave => $rotulo)
        @php
            $concluida = $loop->index < $atual;
            $corrente = $loop->index === $atual;
        @endphp
        <li class="flex items-start {{ $loop->first ? '' : 'flex-1' }}">
            @unless ($loop->first)
                <span class="mx-1.5 mt-3 h-px flex-1 {{ $loop->index <= $atual ? 'bg-primary' : 'bg-border' }}"></span>
            @endunless
            <span class="flex w-16 flex-col items-center gap-1.5 sm:w-24">
                <span class="flex size-6 items-center justify-center rounded-full text-[0.775rem] font-bold transition-colors {{ $concluida ? 'bg-primary text-primary-foreground' : ($corrente ? 'bg-primary/15 text-primary ring-2 ring-primary/40' : 'bg-muted text-muted-foreground') }}">
                    @if ($concluida)
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    @else
                        {{ $loop->index + 1 }}
                    @endif
                </span>
                <span class="text-center text-[0.775rem] leading-tight sm:text-xs {{ $corrente || $concluida ? 'font-semibold text-foreground' : 'text-muted-foreground' }}">{{ $rotulo }}</span>
            </span>
        </li>
    @endforeach
</ol>
