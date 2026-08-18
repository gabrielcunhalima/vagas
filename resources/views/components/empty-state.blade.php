@props(['title', 'description' => null])
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center px-6 py-16 text-center']) }}>
    <div class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
        @isset($icon)
            {{ $icon }}
        @else
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m13.5 8.5-5 5"/><path d="m8.5 8.5 5 5"/><circle cx="10" cy="10" r="7"/><path d="m21 21-4.3-4.3"/></svg>
        @endisset
    </div>
    <h3 class="mt-4 text-sm font-semibold">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 max-w-sm text-sm text-muted-foreground">{{ $description }}</p>
    @endif
    @if (trim($slot))
        <div class="mt-5 flex flex-wrap items-center justify-center gap-2">{{ $slot }}</div>
    @endif
</div>
