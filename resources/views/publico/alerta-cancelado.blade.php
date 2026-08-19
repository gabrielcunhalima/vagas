<x-layouts.public title="Alertas cancelados">
    <div class="mx-auto flex w-full max-w-md flex-col items-center px-4 pt-16 text-center">
        <div class="flex size-14 items-center justify-center rounded-full bg-muted">
            <svg class="size-6 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.7 3A6 6 0 0 1 18 8c0 4.499 1.411 5.956 2.738 7.326A1 1 0 0 1 20 17H4a1 1 0 0 1-.732-1.674C4.591 13.956 6 12.499 6 8"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/><path d="m2 2 20 20"/></svg>
        </div>
        <h1 class="mt-5 text-2xl font-bold tracking-tight">Alertas cancelados</h1>
        <p class="mt-2 text-sm leading-relaxed text-muted-foreground">Você não receberá mais e-mails de novas vagas. Se mudar de ideia, é só ativar novamente.</p>
        <div class="mt-7 flex gap-3">
            <x-ui.button tag="a" href="{{ route('alertas.create') }}" variant="outline">Reativar alertas</x-ui.button>
            <x-ui.button tag="a" href="{{ route('vagas.publicas.index') }}">Ver vagas</x-ui.button>
        </div>
    </div>
</x-layouts.public>
