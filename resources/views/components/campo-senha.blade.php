@props(['name', 'id' => null, 'autocomplete' => 'current-password', 'required' => false, 'forca' => false, 'autofocus' => false])
@php
    $id = $id ?? $name;
    $forcaAlvo = $forca ? "{$id}-forca" : null;
@endphp
<div class="relative">
    <x-ui.input
        id="{{ $id }}"
        name="{{ $name }}"
        type="password"
        class="h-10 pr-10"
        :autocomplete="$autocomplete"
        :required="$required"
        :autofocus="$autofocus"
        :data-senha-forca-input="$forcaAlvo"
    />
    <button type="button" data-senha-toggle="{{ $id }}" class="absolute inset-y-0 right-0 flex w-10 cursor-pointer items-center justify-center text-muted-foreground transition-colors hover:text-foreground" aria-label="Mostrar senha">
        <svg data-icon-mostrar class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
        <svg data-icon-ocultar class="hidden size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/><path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/><path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/><path d="m2 2 20 20"/></svg>
    </button>
</div>
@if ($forca)
    <div id="{{ $id }}-forca" data-senha-forca-output class="hidden"></div>
@endif
