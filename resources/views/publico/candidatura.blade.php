{{-- $vaga: App\Support\Drhflow\VagaDrhflow. $perfil: array (conferência, não edição).
     $completude: array (candidato->estadoCompletude()). --}}
<x-layouts.public :title="'Candidatura: ' . $vaga->titulo">
    <div class="mx-auto w-full max-w-6xl px-4 pt-8">
        <h1 class="text-2xl font-bold tracking-tight">Candidatar-se</h1>
        <p class="mt-1 text-sm text-muted-foreground">Confira os dados que o RH vai receber e responda às perguntas desta vaga.</p>

        <div class="mt-6 grid items-start gap-6 lg:grid-cols-[1fr_320px]">
            @if ($completude['completo'])
                <form method="POST" action="{{ route('inscricao.store', $vaga->codigo) }}" class="flex min-w-0 flex-col gap-5">
                    @csrf
                    {{-- Honeypot anti-spam: invisível para humanos --}}
                    <input type="text" name="_honeypot" value="" class="absolute -left-[9999px] size-px opacity-0" tabindex="-1" autocomplete="off" aria-hidden="true">

                    <section class="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
                        <h2 class="text-sm font-bold tracking-tight">Seus dados</h2>
                        <p class="mt-0.5 text-xs text-muted-foreground">É isto que o RH desta vaga vai ver. Alterar aqui altera os dados da sua conta e vale para todas as suas candidaturas.</p>

                        <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                            @php
                                $endereco = ($perfil['cidade'] ?? null) && ($perfil['estado'] ?? null) ? "{$perfil['cidade']}/{$perfil['estado']}" : ($perfil['cidade'] ?? null);
                            @endphp
                            @foreach ([
                                'Nome completo' => $perfil['nome'] ?? null,
                                'Nome social' => $perfil['nome_social'] ?? null,
                                'E-mail' => $perfil['email'] ?? null,
                                'CPF' => $perfil['cpf_formatado'] ?? null,
                                'Telefone' => $perfil['telefone'] ?? null,
                                'Nacionalidade' => $perfil['nacionalidade'] ?? null,
                                'Cidade' => $endereco,
                                'LinkedIn' => $perfil['linkedin'] ?? null,
                                'Disponibilidade' => $perfil['disponibilidade'] ?? null,
                            ] as $rotulo => $valor)
                                @if ($valor)
                                    <div class="min-w-0">
                                        <dt class="text-xs text-muted-foreground">{{ $rotulo }}</dt>
                                        <dd class="truncate text-sm font-medium">{{ $valor }}</dd>
                                    </div>
                                @endif
                            @endforeach

                            @if (!empty($perfil['formacoes']))
                                <div class="min-w-0 sm:col-span-2">
                                    <dt class="text-xs text-muted-foreground">Formação</dt>
                                    <dd class="mt-1 flex flex-col gap-2">
                                        @foreach ($perfil['formacoes'] as $formacao)
                                            <div class="text-sm">
                                                <span class="font-medium">{{ $formacao['curso'] }}</span>
                                                @if ($formacao['instituicao'])
                                                    <span class="text-muted-foreground"> — {{ $formacao['instituicao'] }}</span>
                                                @endif
                                                <div class="text-xs text-muted-foreground">
                                                    {{ collect([
                                                        \App\Models\CandidatoFormacao::$niveisLabel[$formacao['nivel_escolaridade']] ?? $formacao['nivel_escolaridade'],
                                                        $formacao['situacao_curso'] === 'cursando'
                                                            ? 'Cursando' . ($formacao['semestre'] ? " — {$formacao['semestre']}" : '')
                                                            : ($formacao['situacao_curso'] === 'concluido' ? 'Concluído' : null),
                                                        $formacao['previsao_conclusao'] ? \Illuminate\Support\Carbon::parse($formacao['previsao_conclusao'])->format('d/m/Y') : null,
                                                    ])->filter()->implode(' · ') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </dd>
                                </div>
                            @endif

                            @if (!empty($perfil['outras_formacoes_mec']))
                                <div class="min-w-0 sm:col-span-2">
                                    <dt class="text-xs text-muted-foreground">Outras formações reconhecidas pelo MEC</dt>
                                    <dd class="mt-0.5 whitespace-pre-line text-sm font-medium">{{ $perfil['outras_formacoes_mec'] }}</dd>
                                </div>
                            @endif
                            @if (!empty($perfil['outros_cursos']))
                                <div class="min-w-0 sm:col-span-2">
                                    <dt class="text-xs text-muted-foreground">Outros cursos, palestras, etc.</dt>
                                    <dd class="mt-0.5 whitespace-pre-line text-sm font-medium">{{ $perfil['outros_cursos'] }}</dd>
                                </div>
                            @endif
                        </dl>

                        <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t pt-4">
                            <span class="inline-flex min-w-0 items-center gap-2 text-sm">
                                <svg class="size-4 shrink-0 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
                                <span class="truncate font-medium">{{ $perfil['curriculo_nome'] }}</span>
                            </span>
                            <x-ui.button tag="a" href="{{ route('candidato.perfil.edit') }}" variant="outline" size="sm" class="gap-1.5">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                Editar meus dados
                            </x-ui.button>
                        </div>
                    </section>

                    <section class="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
                        <h2 class="text-sm font-bold tracking-tight">Sobre esta vaga</h2>
                        <div class="mt-4 grid gap-4">
                            <x-field label="Carta de apresentação" name="carta_apresentacao" hint="Opcional. Conte por que você se interessou por esta vaga.">
                                <x-ui.textarea id="carta_apresentacao" name="carta_apresentacao" rows="5">{{ old('carta_apresentacao') }}</x-ui.textarea>
                            </x-field>

                            <x-field label="Você tem vínculo de parentesco ou relação pessoal com alguém da equipe desta vaga?" name="conflito_interesse" required>
                                <select id="conflito_interesse" name="conflito_interesse" data-conflito-select class="h-9 w-full rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
                                    <option value="" @selected(old('conflito_interesse') === null)>Selecione</option>
                                    <option value="0" @selected(old('conflito_interesse') === '0')>Não</option>
                                    <option value="1" @selected(old('conflito_interesse') === '1')>Sim</option>
                                </select>
                            </x-field>

                            <div data-conflito-detalhe class="{{ old('conflito_interesse') === '1' ? '' : 'hidden' }}">
                                <x-field label="Descreva a relação" name="conflito_interesse_detalhe" required>
                                    <x-ui.textarea id="conflito_interesse_detalhe" name="conflito_interesse_detalhe" rows="3">{{ old('conflito_interesse_detalhe') }}</x-ui.textarea>
                                </x-field>
                            </div>
                        </div>
                    </section>

                    <div class="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                        <label class="flex items-start gap-2.5">
                            <input type="checkbox" name="codigo_conduta_aceite" value="1" @checked(old('codigo_conduta_aceite')) class="mt-0.5 size-4 rounded border-input text-primary focus-visible:ring-3 focus-visible:ring-ring/50">
                            <span class="text-sm leading-relaxed">
                                Li e aceito o
                                <a href="https://fapeu.org.br/codigoconduta" target="_blank" rel="noreferrer" class="font-semibold text-primary hover:underline">Código de Conduta da FAPEU</a>
                                para este processo seletivo. <span class="text-destructive">*</span>
                            </span>
                        </label>
                        <x-input-error for="codigo_conduta_aceite" class="mt-2" />
                    </div>

                    <x-ui.button type="submit" size="lg" class="h-11 w-full gap-2 px-6 sm:w-auto sm:self-end">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/><path d="m21.854 2.147-10.94 10.939"/></svg>
                        Enviar candidatura
                    </x-ui.button>
                </form>
            @else
                @php $pendentes = array_values($completude['pendencias']); @endphp
                <div class="rounded-xl bg-card p-5 ring-1 ring-amber-500/30 sm:p-6">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 size-5 shrink-0 text-amber-600 dark:text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4M12 17h.01"/></svg>
                        <div class="min-w-0">
                            <h2 class="text-sm font-bold tracking-tight">Complete seu perfil para se candidatar</h2>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Faltam {{ count($pendentes) }} {{ count($pendentes) === 1 ? 'informação' : 'informações' }} para você concorrer a
                                <span class="font-medium text-foreground">{{ $vaga->titulo }}</span>.
                            </p>
                        </div>
                    </div>

                    <x-ui.progress :value="$completude['progresso']" class="mt-4 h-1.5" />

                    <ul class="mt-3 flex flex-wrap gap-1.5">
                        @foreach ($pendentes as $rotulo)
                            <li class="rounded-full bg-muted px-2.5 py-1 text-xs font-medium text-muted-foreground">{{ $rotulo }}</li>
                        @endforeach
                    </ul>

                    <x-ui.button tag="a" href="{{ route('candidato.perfil.edit') }}" class="mt-5">Completar meu perfil</x-ui.button>
                </div>
            @endif

            <aside class="order-first lg:order-none lg:sticky lg:top-20">
                <div class="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                    <div class="flex flex-wrap gap-2">
                        <x-badges.tipo-admissao :tipo="$vaga->tipo" :codigo="$vaga->tipoCodigo" />
                    </div>
                    <h2 class="mt-3 font-semibold leading-snug">{{ $vaga->titulo }}</h2>
                    <div class="mt-3 flex flex-col gap-2 text-sm text-muted-foreground">
                        @if ($vaga->localizacao())
                            <span class="inline-flex items-center gap-2">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ $vaga->localizacao() }}
                            </span>
                        @endif
                        @if ($vaga->projetoNome)
                            <span class="line-clamp-2 text-xs">Projeto — {{ $vaga->projetoNome }}</span>
                        @endif
                        <span class="inline-flex items-center gap-2 {{ $vaga->diasRestantes() !== null && $vaga->diasRestantes() <= 5 ? 'font-semibold text-primary' : '' }}">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                            {{ $vaga->prazoInscricaoLabel() }}
                        </span>
                        <span class="font-medium text-foreground">{{ $vaga->remuneracaoFormatada() ?? 'A combinar' }}</span>
                    </div>
                    <a href="{{ route('vagas.publicas.show', $vaga->codigo) }}" class="mt-4 inline-block text-xs font-medium text-primary hover:underline">Ver descrição completa da vaga</a>
                </div>
            </aside>
        </div>
    </div>
</x-layouts.public>
