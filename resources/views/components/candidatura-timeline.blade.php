{{-- Etapas visuais: Recebida → Em análise → Entrevista → Resultado (status próprio, caminho legado coordenador/gestor) --}}
@props(['status'])
@php
    $indicePorStatus = ['recebida' => 0, 'em_analise' => 1, 'entrevista' => 2, 'aprovado' => 3, 'reprovado' => 3];
    $atual = $indicePorStatus[$status] ?? 0;
    $reprovado = $status === 'reprovado';
    $aprovado = $status === 'aprovado';
    $etapas = ['Recebida', 'Em análise', 'Entrevista', $reprovado ? 'Não selecionado' : ($aprovado ? 'Aprovado' : 'Resultado')];
@endphp
<ol {{ $attributes->merge(['class' => 'flex w-full items-start']) }}>
    @foreach ($etapas as $i => $etapa)
        @php
            $concluida = $i < $atual || $aprovado;
            $corrente = $i === $atual && !$aprovado;
            $finalNegativo = $reprovado && $i === 3;
        @endphp
        <li class="flex items-start {{ $i > 0 ? 'flex-1' : '' }}">
            @if ($i > 0)
                <span class="mx-1.5 mt-3 h-px flex-1 {{ $i <= $atual ? ($finalNegativo ? 'bg-destructive/50' : 'bg-primary') : 'bg-border' }}"></span>
            @endif
            <span class="flex w-14 flex-col items-center gap-1.5 sm:w-20">
                <span class="flex size-6 items-center justify-center rounded-full text-[0.775rem] font-bold transition-colors {{ $finalNegativo ? 'bg-destructive/15 text-destructive' : ($concluida ? 'bg-primary text-primary-foreground' : ($corrente ? 'bg-primary/15 text-primary ring-2 ring-primary/40' : 'bg-muted text-muted-foreground')) }}">
                    @if ($finalNegativo)
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    @elseif ($concluida)
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    @else
                        {{ $i + 1 }}
                    @endif
                </span>
                <span class="text-center text-[0.775rem] leading-tight sm:text-xs {{ $finalNegativo ? 'font-semibold text-destructive' : (($corrente || $concluida) ? 'font-semibold text-foreground' : 'text-muted-foreground') }}">{{ $etapa }}</span>
            </span>
        </li>
    @endforeach
</ol>
