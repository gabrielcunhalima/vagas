@props(['tipo'])
@php
    $classes = [
        'estagio' => 'bg-violet-500/12 text-violet-700 dark:bg-violet-400/10 dark:text-violet-300',
        'emprego' => 'bg-blue-500/12 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300',
        'bolsa' => 'bg-emerald-500/12 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
    ];
@endphp
<x-ui.badge class="{{ $classes[$tipo] ?? '' }}">{{ \App\Models\Vagas\Vaga::$tiposLabel[$tipo] ?? $tipo }}</x-ui.badge>
