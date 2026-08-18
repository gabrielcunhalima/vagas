@props(['value' => 0])
<div {{ $attributes->merge(['class' => 'relative flex h-1 w-full items-center overflow-x-hidden rounded-full bg-muted']) }}>
    <div class="h-full flex-1 bg-primary transition-all" style="transform: translateX(-{{ 100 - (int) $value }}%)"></div>
</div>
