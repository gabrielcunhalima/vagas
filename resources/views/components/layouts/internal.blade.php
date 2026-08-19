@props(['title' => null, 'pageTitle' => null, 'breadcrumb' => null])
@php
    $user = auth()->user();
    $perfil = $user?->perfil;
    $atual = request()->route()?->getName() ?? '';
    $ativo = fn (string $padrao) => str_ends_with($padrao, '.*')
        ? str_starts_with($atual, substr($padrao, 0, -2))
        : $atual === $padrao;
@endphp
<x-layouts.app :title="$title">
    <aside class="fixed inset-y-0 left-0 z-40 hidden w-64 lg:block">
        @include('components.layouts.partials.internal-sidebar', ['user' => $user, 'perfil' => $perfil, 'ativo' => $ativo])
    </aside>

    <div data-sheet data-side="left" class="fixed inset-0 z-50 hidden lg:hidden">
        <div data-sheet-backdrop class="absolute inset-0 bg-black/50"></div>
        <div data-sheet-panel class="absolute inset-y-0 left-0 w-72 overflow-y-auto border-sidebar-border">
            @include('components.layouts.partials.internal-sidebar', ['user' => $user, 'perfil' => $perfil, 'ativo' => $ativo])
        </div>
    </div>

    <div class="flex min-h-dvh flex-col lg:pl-64">
        <div class="sticky top-0 z-30 flex items-center justify-between gap-3 border-b bg-background/85 px-4 py-3 backdrop-blur-md sm:px-6">
            <div class="flex items-center gap-3">
                <x-ui.button tag="button" variant="outline" size="icon-sm" class="lg:hidden" data-sheet-trigger="menu-interno" aria-label="Abrir menu">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                </x-ui.button>
                <div>
                    <h1 class="text-sm font-bold tracking-tight sm:text-base">{{ $pageTitle ?? 'Painel' }}</h1>
                    @if ($breadcrumb)
                        <p class="text-xs text-muted-foreground">{{ $breadcrumb }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                {{ $topbarActions ?? '' }}
                <x-ui.button tag="button" variant="ghost" size="icon" class="text-muted-foreground" data-tema-toggle aria-label="Alternar tema claro/escuro">
                    <svg class="size-4 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                    <svg class="hidden size-4 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                </x-ui.button>
            </div>
        </div>

        <div class="flex-1 px-4 py-6 sm:px-6">{{ $slot }}</div>
    </div>
</x-layouts.app>
