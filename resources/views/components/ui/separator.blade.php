@props(['orientation' => 'horizontal'])
<div
    role="separator"
    {{ $attributes->merge(['class' => 'shrink-0 bg-border ' . ($orientation === 'vertical' ? 'w-px self-stretch' : 'h-px w-full')]) }}
></div>
