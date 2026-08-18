{{-- $vaga: array (VagaController::showGestor) --}}
<x-layouts.internal :title="'Vaga: ' . $vaga['titulo']" page-title="Análise de vaga" breadcrumb="Gestor / Autorizar vagas">
    <a href="{{ route('gestor.vagas.index') }}" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7M19 12H5"/></svg>
        Autorizar vagas
    </a>

    <header class="mt-4">
        <div class="flex flex-wrap items-center gap-2">
            <x-badges.tipo :tipo="$vaga['tipo']" />
            <x-badges.modalidade :modalidade="$vaga['modalidade']" />
            <x-badges.status-vaga :status="$vaga['status']" />
        </div>
        <h1 class="mt-3 max-w-3xl text-2xl font-bold leading-tight tracking-tight">{{ $vaga['titulo'] }}</h1>
        <p class="mt-1 text-sm text-muted-foreground">
            {{ $vaga['area'] }} · enviada em {{ \Illuminate\Support\Carbon::parse($vaga['created_at'])->format('d/m/Y') }}
            @if (!empty($vaga['coordenador']))
                · por {{ $vaga['coordenador']['name'] }}
            @endif
        </p>
    </header>

    @if ($vaga['status'] === 'recusada' && !empty($vaga['motivo_recusa']))
        <div class="mt-4 max-w-3xl rounded-xl border border-destructive/30 bg-destructive/5 p-4">
            <p class="text-sm font-semibold text-destructive">Motivo da recusa</p>
            <p class="mt-1 text-sm text-muted-foreground">{{ $vaga['motivo_recusa'] }}</p>
        </div>
    @endif

    <div class="mt-6 grid items-start gap-6 xl:grid-cols-[1fr_330px]">
        <article class="flex min-w-0 flex-col gap-7 rounded-xl bg-card p-6 ring-1 ring-foreground/10">
            <section>
                <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Descrição</h2>
                <div class="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{{ $vaga['descricao'] }}</div>
            </section>
            <section>
                <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Requisitos</h2>
                <div class="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{{ $vaga['requisitos'] }}</div>
            </section>
            @if (!empty($vaga['requisitos_desejaveis']))
                <section>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Diferenciais</h2>
                    <div class="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{{ $vaga['requisitos_desejaveis'] }}</div>
                </section>
            @endif
            @if (!empty($vaga['beneficios']))
                <section>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Benefícios</h2>
                    <div class="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{{ $vaga['beneficios'] }}</div>
                </section>
            @endif
            @if (!empty($vaga['curso_desejado']))
                <section>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Cursos desejados</h2>
                    <div class="mt-2.5 flex flex-wrap gap-1.5">
                        @foreach ($vaga['curso_desejado'] as $curso)
                            <x-ui.badge variant="secondary">{{ $curso }}</x-ui.badge>
                        @endforeach
                    </div>
                </section>
            @endif
            @if (!empty($vaga['endereco_completo']))
                <section>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Local de trabalho</h2>
                    <div class="mt-2.5 whitespace-pre-line text-sm leading-relaxed">{{ $vaga['endereco_completo'] }}</div>
                </section>
            @endif
        </article>

        <aside class="flex flex-col gap-4 xl:sticky xl:top-20">
            <div class="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                <h2 class="text-sm font-bold">Detalhes</h2>
                <div class="mt-3 flex flex-col gap-2.5">
                    @php
                        $faixa = $vaga['remuneracao'] ? 'R$ ' . number_format($vaga['remuneracao'], 2, ',', '.') : null;
                        if ($faixa && !empty($vaga['remuneracao_max'])) {
                            $faixa .= ' a R$ ' . number_format($vaga['remuneracao_max'], 2, ',', '.');
                        }
                    @endphp
                    @foreach ([
                        'Remuneração' => $faixa ?? 'A combinar',
                        'Carga horária' => !empty($vaga['carga_horaria']) ? "{$vaga['carga_horaria']}h/sem" : null,
                        'Inscrições até' => \Illuminate\Support\Carbon::parse($vaga['data_encerramento'])->format('d/m/Y'),
                        'Projeto' => $vaga['projeto_nome'] ?? null,
                        'Código' => $vaga['projeto_codigo'] ?? null,
                    ] as $label => $valor)
                        @if ($valor)
                            <div class="flex items-start justify-between gap-3 text-sm">
                                <span class="shrink-0 text-muted-foreground">{{ $label }}</span>
                                <span class="text-right font-medium">{{ $valor }}</span>
                            </div>
                        @endif
                    @endforeach

                    @if (!empty($vaga['coordenador']))
                        <div class="my-1 border-t"></div>
                        <div class="flex items-start justify-between gap-3 text-sm">
                            <span class="shrink-0 text-muted-foreground">Coordenador</span>
                            <span class="text-right font-medium">{{ $vaga['coordenador']['name'] }}</span>
                        </div>
                        <div class="flex items-start justify-between gap-3 text-sm">
                            <span class="shrink-0 text-muted-foreground">E-mail</span>
                            <a href="mailto:{{ $vaga['coordenador']['email'] }}" class="text-right font-medium text-primary hover:underline">{{ $vaga['coordenador']['email'] }}</a>
                        </div>
                    @endif
                </div>
            </div>

            @if ($vaga['status'] === 'aguardando_autorizacao')
                <div class="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                    <h2 class="flex items-center gap-2 text-sm font-bold">
                        <svg class="size-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><path d="M12 3c1 1.5 3 2 5 2a3.5 3.5 0 0 1 0 7c0 2-1.5 3.5-3 5-1.5-1.5-3-3-3-5a3.5 3.5 0 0 1 0-7c2 0 4-.5 5-2Z"/></svg>
                        Decisão
                    </h2>
                    <p class="mt-1.5 text-xs leading-relaxed text-muted-foreground">Ao autorizar, a vaga é publicada no portal e os alertas de candidatos compatíveis são disparados.</p>
                    <div class="mt-4 flex flex-col gap-2">
                        <x-ui.button type="button" data-dialog-trigger="autorizar-vaga" class="gap-1.5">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/></svg>
                            Autorizar e publicar
                        </x-ui.button>
                        <x-ui.button type="button" variant="destructive" data-dialog-trigger="recusar-vaga" class="gap-1.5">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></svg>
                            Recusar
                        </x-ui.button>
                    </div>
                </div>
            @endif
        </aside>
    </div>

    <dialog data-dialog="autorizar-vaga" class="w-full max-w-md rounded-xl">
        <form method="POST" action="{{ route('gestor.vagas.autorizar', $vaga['id']) }}" class="bg-card p-6 ring-1 ring-foreground/10">
            @csrf @method('PATCH')
            <h2 class="text-base font-bold tracking-tight">Autorizar esta vaga?</h2>
            <p class="mt-2 text-sm text-muted-foreground">"{{ $vaga['titulo'] }}" será publicada imediatamente no portal público e os candidatos com alertas compatíveis serão notificados por e-mail.</p>
            <div class="mt-5 flex justify-end gap-2">
                <x-ui.button type="button" variant="outline" data-dialog-close>Cancelar</x-ui.button>
                <x-ui.button type="submit">Autorizar</x-ui.button>
            </div>
        </form>
    </dialog>

    <dialog data-dialog="recusar-vaga" class="w-full max-w-md rounded-xl">
        <form method="POST" action="{{ route('gestor.vagas.recusar', $vaga['id']) }}" class="bg-card p-6 ring-1 ring-foreground/10">
            @csrf @method('PATCH')
            <h2 class="text-base font-bold tracking-tight">Recusar vaga</h2>
            <p class="mt-2 text-sm text-muted-foreground">O coordenador será notificado por e-mail com o motivo abaixo e poderá ajustar a vaga e reenviá-la.</p>
            <div class="mt-4">
                <x-field label="Motivo da recusa" name="motivo_recusa" required>
                    <x-ui.textarea id="motivo_recusa" name="motivo_recusa" rows="4" placeholder="Explique o que precisa ser ajustado (mín. 10 caracteres)…" required minlength="10"></x-ui.textarea>
                </x-field>
            </div>
            <div class="mt-5 flex justify-end gap-2">
                <x-ui.button type="button" variant="ghost" data-dialog-close>Cancelar</x-ui.button>
                <x-ui.button type="submit" variant="destructive">Recusar vaga</x-ui.button>
            </div>
        </form>
    </dialog>
</x-layouts.internal>
