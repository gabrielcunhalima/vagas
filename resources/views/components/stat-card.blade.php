@props(['label', 'value', 'tone' => 'primary', 'href' => null])
@php
    $tones = [
        'primary' => 'bg-primary/10 text-primary',
        'amber' => 'bg-amber-500/12 text-amber-600 dark:text-amber-400',
        'blue' => 'bg-blue-500/12 text-blue-600 dark:text-blue-400',
        'sky' => 'bg-sky-500/12 text-sky-600 dark:text-sky-400',
        'violet' => 'bg-violet-500/12 text-violet-600 dark:text-violet-400',
        'red' => 'bg-red-500/12 text-red-600 dark:text-red-400',
        'neutral' => 'bg-muted text-muted-foreground',
    ];
    $tag = $href ? 'a' : 'div';
@endphp
<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => 'flex items-center gap-3.5 rounded-xl bg-card p-4 ring-1 ring-foreground/10 transition-shadow ' . ($href ? 'hover:shadow-md hover:ring-primary/35' : '')]) }}>
    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg {{ $tones[$tone] }}">
        {{ $icon ?? '' }}
    </div>
    <div class="min-w-0">
        <div class="text-2xl font-bold leading-none tracking-tight">{{ $value }}</div>
        <div class="mt-1 truncate text-xs font-medium text-muted-foreground">{{ $label }}</div>
    </div>
</{{ $tag }}>
