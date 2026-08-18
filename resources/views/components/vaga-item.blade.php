{{-- $vaga: App\Support\Drhflow\VagaDrhflow. Link para a página própria da
     vaga (funciona sem JS); com JS, o clique é interceptado para selecionar
     no painel ao lado (>= xl) ou abrir o off-canvas (< xl) sem navegar. --}}
@props(['vaga', 'selecionada' => false])
<a
    href="{{ route('vagas.publicas.show', $vaga->codigo) }}"
    data-vaga-item
    data-vaga-id="{{ $vaga->codigo }}"
    aria-current="{{ $selecionada ? 'true' : 'false' }}"
    class="block w-full cursor-pointer px-4 py-3.5 text-left transition-colors outline-none focus-visible:inset-ring-2 focus-visible:inset-ring-ring {{ $selecionada ? 'bg-accent' : 'hover:bg-muted/60' }}"
>
    <h3 class="text-sm font-semibold leading-snug tracking-tight {{ $selecionada ? 'text-primary' : '' }}">{{ $vaga->titulo }}</h3>

    <div class="mt-1.5 flex flex-col gap-1 text-xs text-muted-foreground">
        <span class="inline-flex items-center gap-1.5">
            <svg class="size-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
            {{ $vaga->localizacao() ?? 'A definir' }}
        </span>

        @if ($vaga->projetoNome)
            <span class="inline-flex items-center gap-1.5">
                <svg class="size-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                <span class="line-clamp-1">{{ $vaga->projetoNome }}</span>
            </span>
        @endif

        <span class="inline-flex items-center gap-1.5 {{ $vaga->diasRestantes() !== null && $vaga->diasRestantes() <= 5 ? 'font-semibold text-primary' : '' }}">
            <svg class="size-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
            {{ $vaga->prazoInscricaoLabel() }}
        </span>
    </div>
</a>
