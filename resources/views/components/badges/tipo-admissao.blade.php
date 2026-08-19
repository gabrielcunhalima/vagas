{{-- Tipo de contratação das vagas do DRHFlow: rótulo já vem traduzido do
     servidor (domínio VagaDrhflow); `codigo` só define a cor —
     A autônomo, N celetista, U bolsista, T estagiário. --}}
@props(['tipo', 'codigo'])
@if ($tipo)
    @php
        $classes = [
            'T' => 'bg-violet-500/12 text-violet-700 dark:bg-violet-400/10 dark:text-violet-300',
            'N' => 'bg-blue-500/12 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300',
            'U' => 'bg-emerald-500/12 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
            'A' => 'bg-amber-500/15 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300',
        ];
    @endphp
    <x-ui.badge class="{{ $classes[$codigo] ?? '' }}">{{ $tipo }}</x-ui.badge>
@endif
