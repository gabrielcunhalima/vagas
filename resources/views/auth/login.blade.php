<x-layouts.auth
    title="Entrar no painel"
    headline="Divulgue vagas. Encontre as pessoas certas."
    sub="Painel de coordenadores e gestores dos projetos administrados pela FAPEU."
>
    <h1 class="text-2xl font-bold tracking-tight">Acessar painel</h1>
    <p class="mt-1 text-sm text-muted-foreground">Acesso restrito a coordenadores e gestores.</p>

    <form method="POST" action="{{ route('login.post') }}" class="mt-8 flex flex-col gap-5">
        @csrf
        <x-field label="E-mail" name="email">
            <x-ui.input id="email" name="email" type="email" class="h-10" value="{{ old('email') }}" placeholder="voce@fapeu.org.br" autocomplete="username" autofocus required />
        </x-field>

        <x-field label="Senha" name="password">
            <x-campo-senha name="password" autocomplete="current-password" required />
        </x-field>

        <div class="flex items-center gap-2">
            <input type="checkbox" id="remember" name="remember" value="1" class="size-4 rounded border-input text-primary focus-visible:ring-3 focus-visible:ring-ring/50">
            <x-ui.label for="remember" class="font-normal text-muted-foreground">Manter conectado</x-ui.label>
        </div>

        <x-ui.button type="submit" class="h-10 w-full">Entrar</x-ui.button>
    </form>

    <p class="mt-8 text-center text-sm text-muted-foreground">
        É candidato?
        <a href="{{ route('candidato.login') }}" class="font-semibold text-primary hover:underline">Entre por aqui</a>
    </p>
</x-layouts.auth>
