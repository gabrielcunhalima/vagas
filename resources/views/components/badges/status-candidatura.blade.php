@props(['status'])
@php
    $classes = [
        'recebida' => 'bg-sky-500/15 text-sky-700 dark:bg-sky-400/10 dark:text-sky-300',
        'em_analise' => 'bg-amber-500/15 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300',
        'entrevista' => 'bg-blue-500/12 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300',
        'aprovado' => 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
        'reprovado' => 'bg-red-500/12 text-red-700 dark:bg-red-400/10 dark:text-red-300',
    ];
@endphp
<x-ui.badge class="{{ $classes[$status] ?? 'bg-muted text-muted-foreground' }}">
    {{ \App\Models\Vagas\Candidatura::$statusLabel[$status] ?? $status }}
</x-ui.badge>
