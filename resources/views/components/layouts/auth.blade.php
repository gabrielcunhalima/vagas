@props(['title' => null, 'headline' => null, 'sub' => null, 'wide' => false])
<x-layouts.app :title="$title">
    <div class="grid min-h-dvh {{ $wide ? 'lg:grid-cols-[2fr_3fr]' : 'lg:grid-cols-[1.1fr_1fr]' }}">
        <div class="relative hidden overflow-hidden lg:block">
            <img src="{{ asset('imagens/auth-hero.jpg') }}" alt="" class="absolute inset-0 size-full object-cover">
            <div class="absolute inset-0 bg-black/50"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-brand-deep/85 via-transparent to-black/20"></div>

            <div class="relative z-10 flex h-full flex-col justify-between p-10">
                <a href="{{ route('home') }}" class="w-fit">
                    <x-logo white class="h-30" />
                </a>

                <div class="max-w-md">
                    <h2 class="text-3xl font-bold leading-tight tracking-tight text-white xl:text-4xl">{{ $headline }}</h2>
                    @if ($sub)
                        <p class="mt-3 text-sm leading-relaxed text-white/80">{{ $sub }}</p>
                    @endif
                </div>

                <p class="text-xs text-white/60">© {{ date('Y') }} FAPEU, Portal de Vagas</p>
            </div>
        </div>

        <div class="flex flex-col">
            <div class="flex items-center justify-between px-5 py-4">
                <a href="{{ route('home') }}" class="lg:invisible">
                    <x-logo class="h-8" />
                </a>
                <x-ui.button tag="button" variant="ghost" size="default" class="text-muted-foreground" data-tema-toggle aria-label="Alternar tema claro/escuro">
                    <svg class="size-4 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                    <svg class="hidden size-4 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                </x-ui.button>
            </div>

            <div class="flex flex-1 items-start justify-center px-5 pb-12 pt-4 sm:items-center sm:pt-0">
                <div class="w-full {{ $wide ? 'max-w-2xl' : 'max-w-sm' }}">{{ $slot }}</div>
            </div>
        </div>
    </div>
</x-layouts.app>
