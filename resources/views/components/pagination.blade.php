@props(['paginator'])
@if ($paginator && $paginator->lastPage() > 1)
    @php
        $links = $paginator->linkCollection();
        $paginas = $links->slice(1, -1);
    @endphp
    <nav {{ $attributes->merge(['class' => 'flex flex-wrap items-center justify-between gap-3']) }} aria-label="Paginação">
        <p class="text-xs text-muted-foreground">
            {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }}
        </p>
        <div class="flex items-center gap-0.5">
            @if ($paginator->previousPageUrl())
                <a href="{{ $paginator->previousPageUrl() }}" aria-label="Página anterior" class="inline-flex size-8 shrink-0 items-center justify-center rounded-[min(var(--radius-md),12px)] text-sm font-medium transition-colors hover:bg-muted hover:text-foreground">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            @else
                <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-[min(var(--radius-md),12px)] opacity-40">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </span>
            @endif

            @foreach ($paginas as $link)
                @if ($link['url'] === null)
                    <span class="px-1.5 text-sm text-muted-foreground">…</span>
                @elseif ($link['active'])
                    <span class="pointer-events-none inline-flex h-8 min-w-7 shrink-0 items-center justify-center rounded-[min(var(--radius-md),12px)] bg-secondary px-2 text-[0.925rem] font-semibold text-secondary-foreground">{{ $link['label'] }}</span>
                @else
                    <a href="{{ $link['url'] }}" class="inline-flex h-8 min-w-7 shrink-0 items-center justify-center rounded-[min(var(--radius-md),12px)] px-2 text-[0.925rem] font-medium transition-colors hover:bg-muted hover:text-foreground">{{ $link['label'] }}</a>
                @endif
            @endforeach

            @if ($paginator->nextPageUrl())
                <a href="{{ $paginator->nextPageUrl() }}" aria-label="Próxima página" class="inline-flex size-8 shrink-0 items-center justify-center rounded-[min(var(--radius-md),12px)] text-sm font-medium transition-colors hover:bg-muted hover:text-foreground">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            @else
                <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-[min(var(--radius-md),12px)] opacity-40">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            @endif
        </div>
    </nav>
@endif
