@props(['size' => 'default'])
@php
    $sizes = ['default' => 'size-8', 'sm' => 'size-6', 'lg' => 'size-10'];
@endphp
<span {{ $attributes->merge(['class' => 'relative flex shrink-0 overflow-hidden rounded-full select-none ring-1 ring-border ' . $sizes[$size]]) }}>{{ $slot }}</span>
