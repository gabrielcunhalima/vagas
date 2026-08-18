@props(['variant' => 'default'])
@php
    $variants = [
        'default' => 'bg-primary text-primary-foreground',
        'secondary' => 'bg-secondary text-secondary-foreground',
        'destructive' => 'bg-destructive/10 text-destructive dark:bg-destructive/20',
        'outline' => 'border-border text-foreground',
        'ghost' => 'hover:bg-muted hover:text-muted-foreground',
    ];
    $base = 'inline-flex h-6.5 w-fit shrink-0 items-center justify-center gap-1 overflow-hidden rounded-4xl border border-transparent px-2 py-0.5 text-xs font-medium whitespace-nowrap [&>svg]:pointer-events-none [&>svg]:size-3!';
@endphp
<span {{ $attributes->merge(['class' => trim("$base {$variants[$variant]}")]) }}>{{ $slot }}</span>
