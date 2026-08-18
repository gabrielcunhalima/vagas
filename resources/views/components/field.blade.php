@props(['label' => null, 'name' => null, 'hint' => null, 'success' => null, 'required' => false])
<div {{ $attributes->merge(['class' => 'flex flex-col gap-1.5']) }}>
    @if ($label)
        <x-ui.label for="{{ $name }}">
            {{ $label }}
            @if ($required)<span class="-ml-0.5 text-destructive">*</span>@endif
        </x-ui.label>
    @endif

    {{ $slot }}

    @if ($name && $errors->has($name))
        <p class="text-xs font-medium text-destructive">{{ $errors->first($name) }}</p>
    @elseif ($success)
        <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ $success }}</p>
    @elseif ($hint)
        <p class="text-xs text-muted-foreground">{{ $hint }}</p>
    @endif
</div>
