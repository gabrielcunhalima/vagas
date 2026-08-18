@props(['variant' => 'default'])
@php
    $variants = [
        'default' => 'bg-card text-card-foreground',
        'destructive' => 'bg-card text-destructive [&_[data-slot=alert-description]]:text-destructive/90',
    ];
    $base = 'relative grid w-full gap-0.5 rounded-lg border px-2.5 py-2 text-left text-sm has-[>svg]:grid-cols-[auto_1fr] has-[>svg]:gap-x-2 [&>svg]:row-span-2 [&>svg]:translate-y-0.5 [&>svg]:text-current [&>svg:not([class*="size-"])]:size-4';
@endphp
<div role="alert" {{ $attributes->merge(['class' => trim("$base {$variants[$variant]}")]) }}>{{ $slot }}</div>
