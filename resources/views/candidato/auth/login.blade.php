{{-- $redirect: ?string --}}
<x-layouts.auth
    title="Entrar"
    headline="Sua próxima oportunidade começa aqui."
    sub="Estágios, bolsas e empregos em projetos administrados pela FAPEU"
>
    <h1 class="text-2xl font-bold tracking-tight">Bem-vindo</h1>
    <p class="mt-1 text-sm text-muted-foreground">Entre para se candidatar e acompanhar suas candidaturas.</p>

    <form method="POST" action="{{ route('candidato.login.post') }}" class="mt-8 flex flex-col gap-5">
        @csrf
        <input type="hidden" name="redirect" value="{{ $redirect }}">

        <x-field label="E-mail" name="email">
            <x-ui.input id="email" name="email" type="email" class="h-10" value="{{ old('email') }}" autocomplete="username" autofocus required />
        </x-field>

        <x-field label="Senha" name="password">
            <x-campo-senha name="password" autocomplete="current-password" required />
        </x-field>

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <input type="checkbox" id="remember" name="remember" value="1" class="size-4 rounded border-input text-primary focus-visible:ring-3 focus-visible:ring-ring/50">
                <x-ui.label for="remember" class="font-normal text-muted-foreground">Manter conectado</x-ui.label>
            </div>

            <a href="{{ route('candidato.senha.request') }}" class="text-sm font-medium text-primary hover:underline">Esqueci minha senha</a>
        </div>

        <x-ui.button type="submit" class="h-10 w-full">Entrar</x-ui.button>
    </form>

    <div class="mt-8 rounded-lg bg-accent/60 px-4 py-3 text-center text-sm">
        Novo por aqui?
        <a href="{{ $redirect ? route('candidato.registro', ['redirect' => $redirect]) : route('candidato.registro') }}" class="font-semibold text-primary hover:underline">Crie sua conta gratuita</a>
    </div>

    <!-- <p class="mt-6 text-center text-xs text-muted-foreground">
        Coordenador ou gestor?
        <a href="{{ route('login') }}" class="font-medium hover:text-foreground hover:underline">Acesse o painel</a>
    </p> -->
</x-layouts.auth>
