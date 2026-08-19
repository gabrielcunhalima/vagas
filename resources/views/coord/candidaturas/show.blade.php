{{-- $vaga: array (id,titulo,status). $candidatura: array (CandidaturaController::show). $proximosStatus: array.
     $acessoExpirado: bool. $motivoExpiracao: ?string --}}
@php $c = $candidatura; @endphp
<x-layouts.internal :title="'Candidatura: ' . ($c['nome'] ?? 'sem acesso')" page-title="Candidatura" breadcrumb="Coordenador / Candidaturas">
    <a href="{{ route('coord.candidaturas.index', $vaga['id']) }}" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7M19 12H5"/></svg>
        Candidaturas de "{{ $vaga['titulo'] }}"
    </a>

    <div class="mt-4 flex flex-wrap items-start justify-between gap-4 rounded-xl bg-card p-5 ring-1 ring-foreground/10">
        <div class="flex items-center gap-4">
            <div class="flex size-12 items-center justify-center rounded-full bg-primary/10">
                <svg class="size-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-lg font-bold tracking-tight">{{ $acessoExpirado ? 'Candidato não identificado' : $c['nome'] }}</h1>
                    <x-badges.status-candidatura :status="$c['status']" />
                    @if (!$acessoExpirado && !empty($c['pcd']))
                        <x-ui.badge variant="secondary" class="gap-1">
                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="16" cy="4" r="1"/><path d="m18 19 1-7-6 1"/><path d="m5 8 3-3 5.5 3-2.36 3.5"/><path d="M4.24 14.5a5 5 0 0 0 6.88 6"/><path d="M13.76 17.5a5 5 0 0 0-6.88-6"/></svg>
                            PcD{{ !empty($c['pcd_tipo']) ? ", {$c['pcd_tipo']}" : '' }}
                        </x-ui.badge>
                    @endif
                </div>
                <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-muted-foreground">
                    @unless ($acessoExpirado)
                        <a href="mailto:{{ $c['email'] }}" class="inline-flex items-center gap-1 hover:text-foreground">
                            <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            {{ $c['email'] }}
                        </a>
                        @if (!empty($c['telefone']))
                            <span class="inline-flex items-center gap-1">
                                <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/></svg>
                                {{ $c['telefone'] }}
                            </span>
                        @endif
                    @endunless
                    <span>Recebida em {{ \Illuminate\Support\Carbon::parse($c['created_at'])->format('d/m/Y') }}</span>
                    @if (!$acessoExpirado && !empty($c['perfil_atualizado_em']))
                        <span>Dados atualizados em {{ \Illuminate\Support\Carbon::parse($c['perfil_atualizado_em'])->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if (!$acessoExpirado && !empty($c['linkedin']))
                <x-ui.button tag="a" href="{{ $c['linkedin'] }}" target="_blank" rel="noreferrer" variant="outline" size="sm" class="gap-1.5">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6M10 14 21 3M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                    LinkedIn
                </x-ui.button>
            @endif
            @if (!$acessoExpirado && !empty($c['tem_curriculo']))
                <x-ui.button tag="a" href="{{ route('coord.candidaturas.curriculo', [$vaga['id'], $c['id']]) }}" size="sm" class="gap-1.5">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                    Currículo
                </x-ui.button>
            @endif
        </div>
    </div>

    <div class="mt-5 grid items-start gap-5 xl:grid-cols-[1fr_340px]">
        <div class="flex min-w-0 flex-col gap-5">
            @if ($acessoExpirado)
                <section class="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 size-5 shrink-0 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <div>
                            <h2 class="text-sm font-bold">Dados pessoais não disponíveis</h2>
                            <p class="mt-1 text-sm text-muted-foreground">{{ $motivoExpiracao }}</p>
                        </div>
                    </div>
                </section>
            @else
                <section class="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
                    <h2 class="text-sm font-bold">Dados do candidato</h2>
                    <dl class="mt-4 grid gap-x-6 gap-y-4 sm:grid-cols-2">
                        @foreach ([
                            'CPF' => $c['cpf_formatado'] ?? null,
                            'Pretensão salarial' => isset($c['pretensao_salarial']) ? ('R$ ' . number_format($c['pretensao_salarial'], 2, ',', '.')) : null,
                            'Disponibilidade' => $c['disponibilidade'] ?? null,
                            'Endereço' => $c['endereco_completo'] ?? null,
                        ] as $rotulo => $valor)
                            @if ($valor)
                                <div class="flex flex-col gap-0.5">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">{{ $rotulo }}</dt>
                                    <dd class="text-sm">{{ $valor }}</dd>
                                </div>
                            @endif
                        @endforeach

                        @if (!empty($c['formacoes']))
                            <div class="flex flex-col gap-0.5 sm:col-span-2">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Formação</dt>
                                <dd class="mt-1 flex flex-col gap-2">
                                    @foreach ($c['formacoes'] as $formacao)
                                        <div class="text-sm">
                                            <span class="font-medium">{{ $formacao['curso'] }}</span>
                                            @if (!empty($formacao['instituicao']))
                                                <span class="text-muted-foreground"> — {{ $formacao['instituicao'] }}</span>
                                            @endif
                                            <div class="text-xs text-muted-foreground">
                                                {{ collect([
                                                    \App\Models\CandidatoFormacao::$niveisLabel[$formacao['nivel_escolaridade']] ?? $formacao['nivel_escolaridade'],
                                                    $formacao['situacao_curso'] === 'cursando' ? 'Cursando' . (!empty($formacao['semestre']) ? " — {$formacao['semestre']}" : '') : ($formacao['situacao_curso'] === 'concluido' ? 'Concluído' : null),
                                                    !empty($formacao['previsao_conclusao']) ? \Illuminate\Support\Carbon::parse($formacao['previsao_conclusao'])->format('d/m/Y') : null,
                                                ])->filter()->implode(' · ') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </dd>
                            </div>
                        @endif
                        @if (!empty($c['outras_formacoes_mec']))
                            <div class="flex flex-col gap-0.5 sm:col-span-2">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Outras formações reconhecidas pelo MEC</dt>
                                <dd class="whitespace-pre-line text-sm">{{ $c['outras_formacoes_mec'] }}</dd>
                            </div>
                        @endif
                        @if (!empty($c['outros_cursos']))
                            <div class="flex flex-col gap-0.5 sm:col-span-2">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Outros cursos, palestras, etc.</dt>
                                <dd class="whitespace-pre-line text-sm">{{ $c['outros_cursos'] }}</dd>
                            </div>
                        @endif
                    </dl>
                </section>
            @endif

            <section class="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
                <h2 class="text-sm font-bold">Respostas desta vaga</h2>
                <dl class="mt-4 grid gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div class="flex flex-col gap-0.5">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Conflito de interesse</dt>
                        <dd class="text-sm">{{ $c['conflito_interesse'] ? 'Sim' : 'Não' }}</dd>
                    </div>
                    @if (!empty($c['codigo_conduta_aceito_em']))
                        <div class="flex flex-col gap-0.5">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Código de conduta aceito em</dt>
                            <dd class="text-sm">{{ \Illuminate\Support\Carbon::parse($c['codigo_conduta_aceito_em'])->format('d/m/Y') }}</dd>
                        </div>
                    @endif
                    @if ($c['conflito_interesse'] && !empty($c['conflito_interesse_detalhe']))
                        <div class="flex flex-col gap-0.5">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Relação declarada</dt>
                            <dd class="text-sm">{{ $c['conflito_interesse_detalhe'] }}</dd>
                        </div>
                    @endif
                </dl>

                @if (!empty($c['carta_apresentacao']))
                    <div class="mt-5 border-t pt-5">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Carta de apresentação</h3>
                        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed">{{ $c['carta_apresentacao'] }}</p>
                    </div>
                @endif
            </section>

            @if (!empty($c['eventos']))
                <section class="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
                    <h2 class="text-sm font-bold">Histórico do processo</h2>
                    <ol class="mt-4 flex flex-col gap-3">
                        @foreach ($c['eventos'] as $ev)
                            <li class="flex gap-3 text-sm">
                                <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-primary"></span>
                                <div class="min-w-0">
                                    <p class="font-medium">{{ $ev['descricao'] }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ \Illuminate\Support\Carbon::parse($ev['ocorrido_em'])->format('d/m/Y') }}
                                        {{ $ev['autor'] ? " · {$ev['autor']}" : '' }}
                                        {{ $ev['curriculo'] ? " · currículo: {$ev['curriculo']}" : '' }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif

            <form method="POST" action="{{ route('coord.candidaturas.updateStatus', [$vaga['id'], $c['id']]) }}" class="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="{{ $c['status'] }}">
                <h2 class="text-sm font-bold">Observações internas</h2>
                <p class="mt-0.5 text-xs text-muted-foreground">Visíveis apenas para a equipe. O candidato nunca vê este campo.</p>
                <x-ui.textarea rows="4" name="observacoes_internas" class="mt-3">{{ $c['observacoes_internas'] ?? '' }}</x-ui.textarea>
                <x-ui.button type="submit" variant="outline" size="sm" class="mt-3 gap-1.5">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7M7 3v4a1 1 0 0 0 1 1h7"/></svg>
                    Salvar observações
                </x-ui.button>
            </form>
        </div>

        <aside class="flex flex-col gap-4 xl:sticky xl:top-20">
            <div class="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                <h2 class="text-sm font-bold">Processo seletivo</h2>
                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-muted-foreground">Status atual</span>
                    <x-badges.status-candidatura :status="$c['status']" />
                </div>

                @if (!empty($c['entrevista_data']))
                    <div class="mt-4 rounded-lg bg-accent/60 p-3.5 text-sm ring-1 ring-primary/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-accent-foreground">Entrevista</p>
                        <div class="mt-1.5 flex flex-col gap-1">
                            <span class="inline-flex items-center gap-2">
                                <svg class="size-3.5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                                {{ \Illuminate\Support\Carbon::parse($c['entrevista_data'])->format('d/m/Y H:i') }}
                            </span>
                            @if (!empty($c['entrevista_local']))
                                <span class="inline-flex items-center gap-2">
                                    <svg class="size-3.5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                                    {{ $c['entrevista_local'] }}
                                </span>
                            @endif
                            @if (!empty($c['entrevista_observacoes']))
                                <p class="text-xs text-muted-foreground">{{ $c['entrevista_observacoes'] }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                @if (count($proximosStatus) > 0)
                    <div class="mt-4 flex flex-col gap-2">
                        @if (in_array('em_analise', $proximosStatus))
                            <form method="POST" action="{{ route('coord.candidaturas.updateStatus', [$vaga['id'], $c['id']]) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="em_analise">
                                <x-ui.button type="submit" variant="outline" class="w-full gap-1.5">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                    Mover para análise
                                </x-ui.button>
                            </form>
                        @endif

                        @if (in_array('entrevista', $proximosStatus))
                            <x-ui.button type="button" variant="outline" data-dialog-trigger="agendar-entrevista" class="w-full gap-1.5">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><circle cx="18" cy="16" r="2"/><path d="M18 16v-1.5"/></svg>
                                Convidar para entrevista
                            </x-ui.button>
                        @endif

                        @if (in_array('aprovado', $proximosStatus))
                            <x-ui.button type="button" data-dialog-trigger="aprovar-candidato" class="w-full gap-1.5">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/></svg>
                                Aprovar candidato
                            </x-ui.button>
                        @endif

                        @if (in_array('reprovado', $proximosStatus))
                            <x-ui.button type="button" variant="destructive" data-dialog-trigger="reprovar-candidato" class="w-full gap-1.5">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></svg>
                                Reprovar
                            </x-ui.button>
                        @endif
                    </div>
                @else
                    <p class="mt-4 text-xs text-muted-foreground">Processo concluído, não há mais transições possíveis.</p>
                @endif
            </div>
        </aside>
    </div>

    <dialog data-dialog="agendar-entrevista" class="w-full max-w-md rounded-xl">
        <form method="POST" action="{{ route('coord.candidaturas.updateStatus', [$vaga['id'], $c['id']]) }}" class="bg-card p-6 ring-1 ring-foreground/10">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="entrevista">
            <h2 class="text-base font-bold tracking-tight">Agendar entrevista</h2>
            <p class="mt-1 text-sm text-muted-foreground">O candidato receberá um convite por e-mail com data e local.</p>
            <div class="mt-4 flex flex-col gap-4">
                <x-field label="Data e hora" name="entrevista_data" required>
                    <x-ui.input id="entrevista_data" name="entrevista_data" type="datetime-local" required />
                </x-field>
                <x-field label="Local (ou link da chamada)" name="entrevista_local" required>
                    <x-ui.input id="entrevista_local" name="entrevista_local" required />
                </x-field>
                <x-field label="Observações" name="entrevista_observacoes">
                    <x-ui.textarea id="entrevista_observacoes" name="entrevista_observacoes" rows="3"></x-ui.textarea>
                </x-field>
            </div>
            <div class="mt-5 flex justify-end gap-2">
                <x-ui.button type="button" variant="ghost" data-dialog-close>Cancelar</x-ui.button>
                <x-ui.button type="submit">Agendar e notificar</x-ui.button>
            </div>
        </form>
    </dialog>

    <dialog data-dialog="aprovar-candidato" class="w-full max-w-md rounded-xl">
        <form method="POST" action="{{ route('coord.candidaturas.updateStatus', [$vaga['id'], $c['id']]) }}" class="bg-card p-6 ring-1 ring-foreground/10">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="aprovado">
            <h2 class="text-base font-bold tracking-tight">Aprovar {{ !empty($c['nome']) ? \Illuminate\Support\Str::before($c['nome'], ' ') : 'este candidato' }}?</h2>
            <p class="mt-2 text-sm text-muted-foreground">O candidato será notificado da aprovação por e-mail. Esta decisão é final.</p>
            <div class="mt-5 flex justify-end gap-2">
                <x-ui.button type="button" variant="outline" data-dialog-close>Cancelar</x-ui.button>
                <x-ui.button type="submit">Aprovar e notificar</x-ui.button>
            </div>
        </form>
    </dialog>

    <dialog data-dialog="reprovar-candidato" class="w-full max-w-md rounded-xl">
        <form method="POST" action="{{ route('coord.candidaturas.updateStatus', [$vaga['id'], $c['id']]) }}" class="bg-card p-6 ring-1 ring-foreground/10">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="reprovado">
            <h2 class="text-base font-bold tracking-tight">Reprovar {{ !empty($c['nome']) ? \Illuminate\Support\Str::before($c['nome'], ' ') : 'este candidato' }}?</h2>
            <p class="mt-2 text-sm text-muted-foreground">O candidato será notificado por e-mail de que não seguirá no processo. Esta decisão é final.</p>
            <div class="mt-5 flex justify-end gap-2">
                <x-ui.button type="button" variant="outline" data-dialog-close>Cancelar</x-ui.button>
                <x-ui.button type="submit" variant="destructive">Reprovar e notificar</x-ui.button>
            </div>
        </form>
    </dialog>
</x-layouts.internal>
