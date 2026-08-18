{{-- $contadores: array. $ativo: ?string. $formId: id do <form> de filtro a submeter (campo "status"). --}}
@props(['contadores', 'ativo' => null, 'formId'])
@php
    $opcoes = ['' => "Todas ({$contadores['todos']})"];
    foreach (\App\Models\Vagas\Candidatura::$statusLabel as $valor => $label) {
        $quantidade = $contadores[$valor] ?? 0;
        $opcoes[$valor] = "{$label} ({$quantidade})";
    }
@endphp
<div class="flex flex-wrap gap-1.5">
    @foreach ($opcoes as $valor => $label)
        <button
            type="submit"
            form="{{ $formId }}"
            name="status"
            value="{{ $valor }}"
            class="cursor-pointer rounded-full px-3.5 py-1.5 text-xs font-semibold transition-colors {{ ($ativo ?? '') === $valor ? 'bg-primary text-primary-foreground' : 'bg-card text-muted-foreground ring-1 ring-foreground/10 hover:text-foreground' }}"
        >
            {{ $label }}
        </button>
    @endforeach
</div>
