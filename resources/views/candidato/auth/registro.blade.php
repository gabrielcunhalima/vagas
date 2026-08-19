{{-- $redirect: ?string --}}
<x-layouts.auth
    title="Criar conta"
    headline="Sua próxima oportunidade pode estar aqui."
    sub="Crie sua conta em um passo."
>
    <h1 class="text-2xl font-bold tracking-tight">Criar conta</h1>
    <p class="mt-1 text-sm text-muted-foreground">
        Já tem conta?
        <a href="{{ route('candidato.login') }}" class="font-semibold text-primary hover:underline">Entrar</a>
    </p>

    <form method="POST" action="{{ route('candidato.registro.post', $redirect ? ['redirect' => $redirect] : []) }}" class="mt-6">
        @csrf
        <div class="flex flex-col gap-5 rounded-xl bg-card p-6 ring-1 ring-foreground/10">
            <x-field label="CPF" name="cpf" required>
                <x-ui.input
                    id="cpf"
                    name="cpf"
                    inputmode="numeric"
                    class="h-10"
                    value="{{ old('cpf') }}"
                    autofocus
                    data-cpf-input
                    data-cpf-check-url="{{ route('candidato.registro.verificar-cpf') }}"
                />
                <p data-cpf-feedback-for="cpf" class="text-xs text-muted-foreground"></p>

                <div class="mt-0.5 rounded-lg border border-destructive/30 bg-destructive/5 p-3 text-xs" data-cpf-duplicado-hint-for="cpf" hidden>
                    <p class="font-medium text-destructive">Este CPF já está cadastrado.</p>
                    <p class="mt-1 text-muted-foreground">Entre com o e-mail da sua conta. Se não lembrar qual usou, recupere o acesso pela senha.</p>
                    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1">
                        <a href="{{ route('candidato.login') }}" class="font-semibold text-primary hover:underline">Entrar</a>
                        <a href="{{ route('candidato.senha.request') }}" class="font-semibold text-primary hover:underline">Esqueci minha senha</a>
                    </div>
                </div>
            </x-field>

            <x-field label="E-mail" name="email" required>
                <x-ui.input id="email" name="email" type="email" class="h-10" value="{{ old('email') }}" autocomplete="username" />
            </x-field>

            <x-field label="Senha" name="password" required>
                <x-campo-senha name="password" autocomplete="new-password" forca />
            </x-field>

            <x-field label="Confirmar senha" name="password_confirmation" required>
                <x-campo-senha name="password_confirmation" id="password_confirmation" autocomplete="new-password" />
            </x-field>

            <div class="flex flex-col gap-1.5">
                <label class="flex items-start gap-2.5">
                    <input type="checkbox" name="lgpd_consentimento" value="1" @checked(old('lgpd_consentimento')) class="mt-0.5 size-4 rounded border-input text-primary focus-visible:ring-3 focus-visible:ring-ring/50">
                    <span class="text-sm leading-relaxed">
                        Autorizo o tratamento dos meus dados pessoais para processos seletivos, conforme a
                        <button type="button" data-dialog-trigger="politica-privacidade" class="font-semibold text-primary hover:underline">Política de Privacidade</button>
                        (LGPD).
                    </span>
                </label>
                <x-input-error for="lgpd_consentimento" />
            </div>
        </div>

        <x-ui.button type="submit" class="mt-5 h-10 w-full">Criar conta</x-ui.button>

        <p class="mt-4 text-center text-xs text-muted-foreground">
            Depois de criar a conta você já pode navegar pelas vagas. Os dados do currículo são pedidos só quando for se candidatar.
        </p>
    </form>

    <dialog data-dialog="politica-privacidade" class="w-full max-w-2xl rounded-xl">
        <div class="max-h-[80vh] overflow-y-auto bg-card p-6 ring-1 ring-foreground/10">
            <div class="flex justify-end">
                <button type="button" data-dialog-close class="rounded-md p-1.5 text-muted-foreground hover:bg-muted" aria-label="Fechar">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <x-politica-privacidade-conteudo />
        </div>
    </dialog>
</x-layouts.auth>
