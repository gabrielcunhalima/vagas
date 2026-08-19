@props(['white' => false])
@php
    $src = asset('imagens/fapeulogobranca.png');
@endphp
@if ($white)
    <img src="{{ $src }}" alt="FAPEU" {{ $attributes->merge(['class' => 'w-auto flex-none']) }}>
@else
    <span
        role="img"
        aria-label="FAPEU"
        {{ $attributes->merge(['class' => 'inline-block flex-none bg-primary dark:hidden']) }}
        style="aspect-ratio: 291 / 497; mask-image: url('{{ $src }}'); -webkit-mask-image: url('{{ $src }}'); mask-size: contain; -webkit-mask-size: contain; mask-repeat: no-repeat; -webkit-mask-repeat: no-repeat; mask-position: center; -webkit-mask-position: center;"
    ></span>
    <img src="{{ $src }}" alt="FAPEU" {{ $attributes->merge(['class' => 'hidden w-auto flex-none dark:block']) }}>
@endif
