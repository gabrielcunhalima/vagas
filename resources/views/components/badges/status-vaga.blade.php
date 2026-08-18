@props(['status'])
@php
    $classes = [
        'rascunho' => 'bg-muted text-muted-foreground',
        'aguardando_autorizacao' => 'bg-amber-500/15 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300',
        'ativa' => 'bg-emerald-500/15 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
        'encerrada' => 'bg-slate-500/15 text-slate-700 dark:bg-slate-400/10 dark:text-slate-300',
        'recusada' => 'bg-red-500/12 text-red-700 dark:bg-red-400/10 dark:text-red-300',
        'inativa' => 'bg-muted text-muted-foreground',
    ];
@endphp
<x-ui.badge class="{{ $classes[$status] ?? 'bg-muted text-muted-foreground' }}">
    {{ \App\Models\Vagas\Vaga::$statusLabel[$status] ?? $status }}
</x-ui.badge>
