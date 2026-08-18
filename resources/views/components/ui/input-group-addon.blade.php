@props(['align' => 'inline-start'])
<div {{ $attributes->merge(['class' => 'flex h-auto items-center justify-center gap-2 py-1.5 text-sm font-medium text-muted-foreground select-none [&>svg:not([class*="size-"])]:size-4 ' . ($align === 'inline-start' ? 'order-first pl-2' : 'order-last pr-2')]) }}>{{ $slot }}</div>
