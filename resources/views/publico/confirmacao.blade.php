{{-- $vaga: App\Support\Drhflow\VagaDrhflow. $nome: ?string --}}
<x-layouts.public title="Candidatura enviada">
    <div class="mx-auto flex w-full max-w-lg flex-col items-center px-4 pt-16 text-center">
        <div class="flex size-16 items-center justify-center rounded-full bg-accent">
            <svg class="size-8 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/></svg>
        </div>

        <h1 class="mt-6 text-2xl font-bold tracking-tight">Candidatura enviada!</h1>
        <p class="mt-2 text-sm leading-relaxed text-muted-foreground">
            {{ $nome ? Str::before($nome, ' ') . ', sua' : 'Sua' }}
            candidatura para <span class="font-semibold text-foreground">{{ $vaga->titulo }}</span> foi recebida.
            Enviamos um e-mail de confirmação. O RH entrará em contato pelos dados informados.
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <x-ui.button tag="a" href="{{ route('vagas.publicas.index') }}">Ver mais vagas</x-ui.button>
            <x-ui.button tag="a" href="{{ route('candidatura.consulta') }}" variant="outline" class="gap-1.5">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                Acompanhar candidatura
            </x-ui.button>
        </div>

        <p class="mt-8 text-xs text-muted-foreground">
            Dica: crie uma conta para acompanhar suas candidaturas em um só lugar.
            <a href="{{ route('candidato.registro') }}" class="font-medium text-primary hover:underline">Criar conta</a>
        </p>
    </div>
</x-layouts.public>
