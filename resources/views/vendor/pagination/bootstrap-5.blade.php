@if ($paginator->hasPages())
<style>
    .pagination .page-link:hover { background:#f0faf7; border-color:#0D9571 !important; }
    .pagination .page-item.disabled .page-link { background:#F8F9FA; }
</style>
<nav aria-label="Paginação">
    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2">

        {{-- Contagem de resultados --}}
        <div style="font-size:0.82rem;color:#6C757D;">
            Exibindo <strong>{{ $paginator->firstItem() }}</strong> a <strong>{{ $paginator->lastItem() }}</strong>
            de <strong>{{ $paginator->total() }}</strong> resultado{{ $paginator->total() !== 1 ? 's' : '' }}
        </div>

        {{-- Botões de página --}}
        <ul class="pagination mb-0" style="gap:4px;">

            {{-- Anterior --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link" style="border-radius:8px;border:1px solid #E9ECEF;color:#CACACA;padding:6px 12px;font-size:0.82rem;">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"
                       style="border-radius:8px;border:1px solid #e9ecef;color:#0D9571;padding:6px 12px;font-size:0.82rem;transition:all 0.2s;">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Páginas --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link" style="border-radius:8px;border:1px solid #E9ECEF;color:#CACACA;padding:6px 10px;font-size:0.82rem;">
                            {{ $element }}
                        </span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link"
                                      style="border-radius:8px;background:#0D9571;border-color:#0D9571;color:#fff;padding:6px 12px;font-size:0.82rem;font-weight:600;box-shadow:0 2px 8px rgba(13,149,113,0.35);">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}"
                                   style="border-radius:8px;border:1px solid #E9ECEF;color:#3E3E3F;padding:6px 12px;font-size:0.82rem;transition:all 0.2s;">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Próximo --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"
                       style="border-radius:8px;border:1px solid #E9ECEF;color:#0D9571;padding:6px 12px;font-size:0.82rem;transition:all 0.2s;">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link" style="border-radius:8px;border:1px solid #E9ECEF;color:#CACACA;padding:6px 12px;font-size:0.82rem;">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            @endif

        </ul>
    </div>
</nav>
@endif
