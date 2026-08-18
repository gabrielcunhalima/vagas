@props(['andamento', 'rotulo' => null])
@php
    $classes = [
        'recebida' => 'bg-sky-500/15 text-sky-700 dark:bg-sky-400/10 dark:text-sky-300',
        'entrevista_marcada' => 'bg-blue-500/12 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300',
        'avaliacao_concluida' => 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
    ];
@endphp
<x-ui.badge class="{{ $classes[$andamento] ?? 'bg-muted text-muted-foreground' }}">{{ $rotulo ?? $andamento }}</x-ui.badge>
