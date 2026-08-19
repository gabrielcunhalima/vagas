@props(['for'])
@if ($errors->has($for))
    <p {{ $attributes->merge(['class' => 'text-xs font-medium text-destructive']) }}>{{ $errors->first($for) }}</p>
@endif
