{{-- $token: string. $email: string --}}
<x-layouts.auth
    title="Redefinir senha"
    headline="Sua próxima oportunidade começa aqui."
    sub="Estágios, bolsas e empregos em projetos administrados pela FAPEU"
>
    <h1 class="text-2xl font-bold tracking-tight">Crie uma nova senha</h1>
    <p class="mt-1 text-sm text-muted-foreground">Escolha uma senha forte para proteger sua conta.</p>

    <form method="POST" action="{{ route('candidato.senha.update') }}" class="mt-8 flex flex-col gap-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <x-field label="E-mail" name="email">
            <x-ui.input id="email" name="email" type="email" class="h-10" value="{{ old('email', $email) }}" placeholder="seu@email.com" autocomplete="username" :autofocus="!$email" required />
        </x-field>

        <x-field label="Nova senha" name="password">
            <x-campo-senha name="password" autocomplete="new-password" forca :autofocus="(bool) $email" />
        </x-field>

        <x-field label="Confirmar nova senha" name="password_confirmation">
            <x-campo-senha name="password_confirmation" id="password_confirmation" autocomplete="new-password" />
        </x-field>

        <x-ui.button type="submit" class="h-10 w-full">Redefinir senha</x-ui.button>
    </form>

    <p class="mt-8 text-center text-sm text-muted-foreground">
        Lembrou sua senha?
        <a href="{{ route('candidato.login') }}" class="font-semibold text-primary hover:underline">Entrar</a>
    </p>
</x-layouts.auth>
