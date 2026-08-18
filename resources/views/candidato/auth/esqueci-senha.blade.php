<x-layouts.auth
    :title="session('success') ? 'Verifique seu e-mail' : 'Esqueci minha senha'"
    headline="Sua próxima oportunidade começa aqui."
    sub="Estágios, bolsas e empregos em projetos administrados pela FAPEU"
>
    @if (session('success'))
        <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-accent">
            <svg class="size-6 text-accent-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/><path d="m16 19 2 2 4-4"/></svg>
        </div>
        <h1 class="mt-5 text-2xl font-bold tracking-tight">Verifique seu e-mail</h1>
        <p class="mt-2 text-sm leading-relaxed text-muted-foreground">{{ session('success') }}</p>

        <a href="{{ route('candidato.login') }}" class="mt-8 inline-block text-sm font-semibold text-primary hover:underline">Voltar para o login</a>
    @else
        <h1 class="text-2xl font-bold tracking-tight">Esqueceu sua senha?</h1>
        <p class="mt-1 text-sm text-muted-foreground">Informe seu e-mail cadastrado e enviaremos um link para você criar uma nova senha.</p>

        <form method="POST" action="{{ route('candidato.senha.email') }}" class="mt-8 flex flex-col gap-5">
            @csrf
            <x-field label="E-mail" name="email">
                <x-ui.input id="email" name="email" type="email" class="h-10" value="{{ old('email') }}" placeholder="seu@email.com" autocomplete="username" autofocus required />
            </x-field>

            <x-ui.button type="submit" class="h-10 w-full">Enviar link de recuperação</x-ui.button>
        </form>

        <p class="mt-8 text-center text-sm text-muted-foreground">
            Lembrou sua senha?
            <a href="{{ route('candidato.login') }}" class="font-semibold text-primary hover:underline">Entrar</a>
        </p>
    @endif
</x-layouts.auth>
