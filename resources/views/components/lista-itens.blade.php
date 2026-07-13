@props(['texto', 'classe' => ''])

@php
    $itens = array_filter(
        array_map('trim', preg_split('/\r\n|\r|\n/', $texto ?? '')),
        fn($linha) => $linha !== ''
    );
@endphp

@if(count($itens))
<ul class="lista-itens {{ $classe }}" style="margin:0;padding-left:1.25rem;display:flex;flex-direction:column;gap:0.35rem;">
    @foreach($itens as $item)
        <li style="font-size:0.9rem;line-height:1.6;color:#3E3E3F;">{{ $item }}</li>
    @endforeach
</ul>
@endif
