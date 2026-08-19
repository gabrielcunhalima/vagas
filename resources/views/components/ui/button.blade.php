@props(['variant' => 'default', 'size' => 'default', 'tag' => 'button', 'type' => 'button'])
@php
    $variants = [
        'default' => 'bg-primary text-primary-foreground hover:bg-primary/80',
        'outline' => 'border-border bg-background hover:bg-muted hover:text-foreground dark:border-input dark:bg-input/30 dark:hover:bg-input/50',
        'secondary' => 'bg-secondary text-secondary-foreground hover:bg-[color-mix(in_oklch,var(--secondary),var(--foreground)_5%)]',
        'ghost' => 'hover:bg-muted hover:text-foreground dark:hover:bg-muted/50',
        'destructive' => 'bg-destructive/10 text-destructive hover:bg-destructive/20 dark:bg-destructive/20 dark:hover:bg-destructive/30',
        'link' => 'text-primary underline-offset-4 hover:underline',
    ];
    $sizes = [
        'default' => 'h-9 gap-1.5 px-2.5',
        'xs' => 'h-7 gap-1 rounded-[min(var(--radius-md),10px)] px-2 text-xs',
        'sm' => 'h-8 gap-1 rounded-[min(var(--radius-md),12px)] px-2.5 text-[0.925rem]',
        'lg' => 'h-10 gap-1.5 px-2.5',
        'icon' => 'size-9',
        'icon-xs' => 'size-7 rounded-[min(var(--radius-md),10px)]',
        'icon-sm' => 'size-8 rounded-[min(var(--radius-md),12px)]',
        'icon-lg' => 'size-10',
    ];
    $base = 'inline-flex shrink-0 cursor-pointer items-center justify-center gap-1.5 rounded-lg border border-transparent bg-clip-padding text-sm font-medium whitespace-nowrap transition-all outline-none select-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 active:translate-y-px disabled:pointer-events-none disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-3 aria-invalid:ring-destructive/20 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*="size-"])]:size-4';
    $classes = trim("$base {$variants[$variant]} {$sizes[$size]}");
@endphp
@if ($tag === 'a')
    <a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
