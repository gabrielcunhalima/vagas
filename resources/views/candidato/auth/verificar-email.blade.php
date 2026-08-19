@php $candidato = auth('candidato')->user(); @endphp
<x-layouts.app title="Confirme seu e-mail">
    <div class="flex min-h-dvh flex-col items-center justify-center bg-background px-4 py-10">
        <a href="{{ route('home') }}">
            <x-logo class="h-10" />
        </a>

        <div class="mt-8 w-full max-w-md rounded-xl bg-card p-8 text-center ring-1 ring-foreground/10">
            <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-accent">
                <svg class="size-6 text-accent-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/><path d="m16 19 2 2 4-4"/></svg>
            </div>

            <h1 class="mt-5 text-xl font-bold tracking-tight">Confirme seu e-mail</h1>
            <p class="mt-2 text-sm leading-relaxed text-muted-foreground">
                Enviamos um link de confirmação para
                <span class="font-semibold text-foreground">{{ $candidato->email }}</span>. Confirme para acessar sua conta e se candidatar às vagas.
            </p>

            <form method="POST" action="{{ route('candidato.verification.send') }}" class="mt-6">
                @csrf
                <x-ui.button type="submit" class="w-full">Reenviar e-mail de confirmação</x-ui.button>
            </form>

            <p class="mt-4 text-xs text-muted-foreground">Verifique também a caixa de spam.</p>
        </div>

        <form method="POST" action="{{ route('candidato.logout') }}" class="mt-6">
            @csrf
            <button type="submit" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Sair da conta</button>
        </form>
    </div>
</x-layouts.app>
