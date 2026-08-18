{{-- $areas: array<string>. $modalidades/$tipos: array código => rótulo.
     $email: string. $alerta: ?array (areas, modalidades, tipos, ativo). --}}
@php
    $container = 'mx-auto w-full px-4 lg:w-4/5 lg:px-0';
    $selecionados = fn (string $campo) => old($campo, $alerta[$campo] ?? []);
@endphp
<x-layouts.public title="Alertas de vagas">
    <div class="{{ $container }} py-10">
        <form method="POST" action="{{ route('alertas.store') }}" class="grid gap-x-10 gap-y-8 rounded-xl bg-card p-6 ring-1 ring-foreground/10 xl:grid-cols-[320px_minmax(0,1fr)] xl:grid-rows-[auto_1fr] xl:p-8">
            @csrf

            <div class="flex flex-col gap-6 xl:col-start-1 xl:row-start-1">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Alertas de vagas</h1>
                    <p class="mt-1 text-sm text-muted-foreground">Receba por e-mail as novas vagas do seu interesse, assim que forem publicadas.</p>
                </div>

                <div>
                    <span class="text-sm font-medium">Enviaremos para</span>
                    <p class="mt-1 flex items-center gap-2 rounded-lg bg-muted px-3 py-2 text-sm">
                        <svg class="size-4 shrink-0 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        <span class="truncate">{{ $email }}</span>
                    </p>
                    <p class="mt-1.5 text-xs text-muted-foreground">
                        Para receber em outro endereço, altere o e-mail em
                        <a href="{{ route('candidato.perfil.edit') }}" class="font-medium text-primary hover:underline">Meus dados</a>.
                    </p>
                </div>
            </div>

            <div class="flex flex-col gap-6 xl:col-start-2 xl:row-span-2 xl:row-start-1 xl:border-l xl:pl-10">
                <div>
                    <h3 class="text-sm font-semibold">Áreas de interesse</h3>
                    <p class="mt-0.5 text-xs text-muted-foreground">Deixe em branco para receber vagas de todas as áreas.</p>
                    <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($areas as $area)
                            @php $ativo = in_array($area, $selecionados('areas')); @endphp
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border px-3 py-2 text-sm transition-colors {{ $ativo ? 'border-primary bg-accent/50 font-medium' : 'border-input hover:bg-muted/50' }}">
                                <input type="checkbox" name="areas[]" value="{{ $area }}" @checked($ativo) class="size-4 rounded border-input text-primary focus-visible:ring-3 focus-visible:ring-ring/50">
                                {{ $area }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 sm:gap-8">
                    <div>
                        <h3 class="text-sm font-semibold">Tipos de vaga</h3>
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            @foreach ($tipos as $codigo => $rotulo)
                                @php $ativo = in_array($codigo, $selecionados('tipos')); @endphp
                                <label class="flex cursor-pointer items-center gap-2 rounded-lg border px-3 py-2 text-sm transition-colors {{ $ativo ? 'border-primary bg-accent/50 font-medium' : 'border-input hover:bg-muted/50' }}">
                                    <input type="checkbox" name="tipos[]" value="{{ $codigo }}" @checked($ativo) class="size-4 rounded border-input text-primary focus-visible:ring-3 focus-visible:ring-ring/50">
                                    {{ $rotulo }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="sm:border-l sm:pl-8">
                        <h3 class="text-sm font-semibold">Modalidades</h3>
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            @foreach ($modalidades as $codigo => $rotulo)
                                @php $ativo = in_array($codigo, $selecionados('modalidades')); @endphp
                                <label class="flex cursor-pointer items-center gap-2 rounded-lg border px-3 py-2 text-sm transition-colors {{ $ativo ? 'border-primary bg-accent/50 font-medium' : 'border-input hover:bg-muted/50' }}">
                                    <input type="checkbox" name="modalidades[]" value="{{ $codigo }}" @checked($ativo) class="size-4 rounded border-input text-primary focus-visible:ring-3 focus-visible:ring-ring/50">
                                    {{ $rotulo }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="xl:col-start-1 xl:row-start-2">
                <div class="flex flex-col gap-5 border-t pt-6 xl:sticky xl:top-20 xl:border-t-0 xl:pt-0">
                    <x-ui.button type="submit" class="h-10 gap-1.5">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19.3 14.8C20.1 15.9 21 17 21 17H3s3-2.5 3-9c0-3.3 2.7-6 6-6 .3 0 .6 0 .9.1M15 4.5c.9-.1 1.8.1 2.6.6"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/><circle cx="18" cy="8" r="3"/></svg>
                        {{ ($alerta['ativo'] ?? false) ? 'Salvar preferências' : 'Ativar alertas' }}
                    </x-ui.button>

                    <p class="text-xs text-muted-foreground">Você pode cancelar a qualquer momento pelo link presente em cada e-mail, sem precisar entrar na conta.</p>
                </div>
            </div>
        </form>
    </div>
</x-layouts.public>
