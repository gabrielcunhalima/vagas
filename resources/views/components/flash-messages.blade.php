{{-- As chaves em português (sucesso/aviso/erro) vêm dos controllers do painel
     interno (herança do antigo HandleInertiaRequests::share(), que fazia o
     mesmo de-para); success/info/error são as usadas pelo restante do app. --}}
@php
    $mensagens = [
        'success' => session('sucesso') ?? session('success'),
        'info' => session('aviso') ?? session('info'),
        'error' => session('erro') ?? session('error'),
    ];
@endphp
@if (collect($mensagens)->filter()->isNotEmpty())
    <div class="pointer-events-none fixed inset-x-4 top-4 z-100 flex flex-col items-end gap-2 sm:inset-x-auto sm:right-4" aria-live="polite">
        @foreach ($mensagens as $tipo => $mensagem)
            @if ($mensagem)
                <div
                    data-flash-toast
                    data-flash-tipo="{{ $tipo }}"
                    class="pointer-events-auto flex w-full max-w-sm items-start gap-2 rounded-lg bg-card px-3 py-2.5 text-sm shadow-lg ring-1 ring-foreground/10 {{ match ($tipo) {
                        'error' => 'text-destructive',
                        'success' => 'text-emerald-600 dark:text-emerald-400',
                        default => 'text-foreground',
                    } }}"
                >
                    <span class="flex-1">{{ $mensagem }}</span>
                    <button type="button" data-flash-close class="shrink-0 cursor-pointer text-muted-foreground hover:text-foreground" aria-label="Fechar">
                        &times;
                    </button>
                </div>
            @endif
        @endforeach
    </div>
@endif
