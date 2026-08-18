{{-- $vaga: ?Vaga. $areas: array<string>. $cursos: array<string> --}}
@php
    $editando = (bool) $vaga;
    $cursosDesejados = old('curso_desejado', $vaga?->curso_desejado ?? []);
@endphp
<x-layouts.internal :title="$editando ? 'Editar vaga' : 'Nova vaga'" :page-title="$editando ? 'Editar vaga' : 'Nova vaga'" breadcrumb="Coordenador / Vagas">
    <a href="{{ route('coord.vagas.index') }}" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7M19 12H5"/></svg>
        Minhas vagas
    </a>

    @if ($editando && $vaga?->status === 'recusada' && $vaga?->motivo_recusa)
        <div class="mt-4 rounded-xl border border-destructive/30 bg-destructive/5 p-4">
            <p class="text-sm font-semibold text-destructive">Vaga recusada pelo gestor</p>
            <p class="mt-1 text-sm text-muted-foreground">{{ $vaga?->motivo_recusa }}</p>
            <p class="mt-1.5 text-xs text-muted-foreground">Ajuste a vaga e envie novamente para autorização.</p>
        </div>
    @endif

    <form method="POST" action="{{ $editando ? route('coord.vagas.update', $vaga) : route('coord.vagas.store') }}" class="mt-4 grid items-start gap-5 xl:grid-cols-[1fr_320px]">
        @csrf
        @if ($editando) @method('PUT') @endif

        <div class="flex min-w-0 flex-col gap-5">
            <section class="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                <h2 class="text-sm font-bold tracking-tight">Identificação</h2>
                <div class="mt-4 flex flex-col gap-4">
                    <x-field label="Título da vaga" name="titulo" required>
                        <x-ui.input id="titulo" name="titulo" value="{{ old('titulo', $vaga?->titulo ?? '') }}" placeholder="Ex.: Estágio em Administração, Projeto X" :autofocus="!$editando" />
                    </x-field>
                    <x-field label="Descrição" name="descricao" required>
                        <x-ui.textarea id="descricao" name="descricao" rows="5" placeholder="Atividades, contexto do projeto, o que a pessoa vai fazer…">{{ old('descricao', $vaga?->descricao ?? '') }}</x-ui.textarea>
                    </x-field>
                    <x-field label="Requisitos" name="requisitos" required>
                        <x-ui.textarea id="requisitos" name="requisitos" rows="4" placeholder="Um requisito por linha…">{{ old('requisitos', $vaga?->requisitos ?? '') }}</x-ui.textarea>
                    </x-field>
                    <x-field label="Diferenciais (desejáveis)" name="requisitos_desejaveis">
                        <x-ui.textarea id="requisitos_desejaveis" name="requisitos_desejaveis" rows="3">{{ old('requisitos_desejaveis', $vaga?->requisitos_desejaveis ?? '') }}</x-ui.textarea>
                    </x-field>
                    <x-field label="Benefícios" name="beneficios">
                        <x-ui.textarea id="beneficios" name="beneficios" rows="3" placeholder="Ex.: Vale-transporte, auxílio-alimentação…">{{ old('beneficios', $vaga?->beneficios ?? '') }}</x-ui.textarea>
                    </x-field>
                </div>
            </section>

            <section class="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                <h2 class="text-sm font-bold tracking-tight">Projeto</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-[1fr_200px]">
                    <x-field label="Nome do projeto" name="projeto_nome">
                        <x-ui.input id="projeto_nome" name="projeto_nome" value="{{ old('projeto_nome', $vaga?->projeto_nome ?? '') }}" />
                    </x-field>
                    <x-field label="Código" name="projeto_codigo">
                        <x-ui.input id="projeto_codigo" name="projeto_codigo" value="{{ old('projeto_codigo', $vaga?->projeto_codigo ?? '') }}" />
                    </x-field>
                </div>
            </section>

            <section class="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                <h2 class="text-sm font-bold tracking-tight">Local de trabalho</h2>
                <p class="mt-0.5 text-xs text-muted-foreground">Para vagas remotas, o endereço é opcional.</p>
                <div class="mt-4 grid gap-4 sm:grid-cols-6">
                    <x-field label="CEP" name="cep" class="sm:col-span-2">
                        <x-ui.input id="cep" name="cep" inputmode="numeric" value="{{ old('cep', $vaga?->cep ?? '') }}" placeholder="00000-000" data-cep-input data-cep-url="{{ url('/api/cep') }}" data-cep-logradouro="logradouro" data-cep-bairro="bairro" data-cep-cidade="cidade" data-cep-estado="estado" />
                        <p data-cep-hint-for="cep" class="text-xs text-muted-foreground"></p>
                    </x-field>
                    <x-field label="Cidade" name="cidade" class="sm:col-span-3">
                        <x-ui.input id="cidade" name="cidade" value="{{ old('cidade', $vaga?->cidade ?? '') }}" />
                    </x-field>
                    <x-field label="UF" name="estado" class="sm:col-span-1">
                        <select id="estado" name="estado" class="h-9 w-full rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
                            <option value="">UF</option>
                            @foreach (['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                <option value="{{ $uf }}" @selected(old('estado', $vaga?->estado ?? '') === $uf)>{{ $uf }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Bairro" name="bairro" class="sm:col-span-2">
                        <x-ui.input id="bairro" name="bairro" value="{{ old('bairro', $vaga?->bairro ?? '') }}" />
                    </x-field>
                    <x-field label="Logradouro" name="logradouro" class="sm:col-span-2">
                        <x-ui.input id="logradouro" name="logradouro" value="{{ old('logradouro', $vaga?->logradouro ?? '') }}" />
                    </x-field>
                    <x-field label="Número" name="numero" class="sm:col-span-2">
                        <x-ui.input id="numero" name="numero" value="{{ old('numero', $vaga?->numero ?? '') }}" />
                    </x-field>
                    <x-field label="Complemento" name="complemento" class="sm:col-span-3">
                        <x-ui.input id="complemento" name="complemento" value="{{ old('complemento', $vaga?->complemento ?? '') }}" />
                    </x-field>
                    <x-field label="Referência do local" name="local_trabalho" class="sm:col-span-3">
                        <x-ui.input id="local_trabalho" name="local_trabalho" value="{{ old('local_trabalho', $vaga?->local_trabalho ?? '') }}" placeholder="Ex.: Campus UFSC, Trindade" />
                    </x-field>
                </div>
            </section>
        </div>

        <div class="flex flex-col gap-5 xl:sticky xl:top-20">
            <section class="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                <h2 class="text-sm font-bold tracking-tight">Classificação</h2>
                <div class="mt-4 flex flex-col gap-4">
                    @if ($editando)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted-foreground">Status atual</span>
                            <x-badges.status-vaga :status="$vaga?->status" />
                        </div>
                    @endif
                    <x-field label="Tipo" name="tipo" required>
                        <select id="tipo" name="tipo" class="h-9 w-full rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
                            <option value="">Selecione</option>
                            @foreach (\App\Models\Vagas\Vaga::$tiposLabel as $v => $l)
                                <option value="{{ $v }}" @selected(old('tipo', $vaga?->tipo ?? '') === $v)>{{ $l }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Área" name="area" required>
                        <select id="area" name="area" class="h-9 w-full rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
                            <option value="">Selecione</option>
                            @foreach ($areas as $a)
                                <option value="{{ $a }}" @selected(old('area', $vaga?->area ?? '') === $a)>{{ $a }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Modalidade" name="modalidade" required>
                        @php $modalidadeAtual = old('modalidade', $vaga?->modalidade ?? 'presencial'); @endphp
                        <select id="modalidade" name="modalidade" class="h-9 w-full rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
                            @foreach (\App\Models\Vagas\Vaga::$modalidadesLabel as $v => $l)
                                <option value="{{ $v }}" @selected($modalidadeAtual === $v)>{{ $l }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Cursos desejados" name="curso_desejado" hint="Vazio = aberto a qualquer curso.">
                        <div data-multi-select data-multi-select-name="curso_desejado" class="flex flex-col gap-2">
                            <input type="text" list="cursos-datalist" data-multi-select-input placeholder="Adicionar curso…" class="h-9 w-full rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
                            <datalist id="cursos-datalist">
                                @foreach ($cursos as $c)
                                    <option value="{{ $c }}"></option>
                                @endforeach
                            </datalist>
                            <div data-multi-select-chips class="flex flex-wrap gap-1.5">
                                @foreach ($cursosDesejados as $curso)
                                    <span class="inline-flex items-center gap-1 rounded-4xl bg-secondary py-0.5 pl-2.5 pr-1 text-xs font-medium text-secondary-foreground">
                                        {{ $curso }}
                                        <button type="button" class="cursor-pointer rounded-full p-0.5 hover:bg-foreground/10" aria-label="Remover {{ $curso }}" onclick="this.closest('span').remove()">
                                            <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                        </button>
                                        <input type="hidden" name="curso_desejado[]" value="{{ $curso }}">
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </x-field>
                </div>
            </section>

            <section class="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                <h2 class="text-sm font-bold tracking-tight">Condições</h2>
                <div class="mt-4 flex flex-col gap-4">
                    <div class="grid grid-cols-2 items-end gap-3">
                        <x-field label="Remuneração (R$)" name="remuneracao">
                            <x-ui.input id="remuneracao" name="remuneracao" type="number" min="0" step="0.01" value="{{ old('remuneracao', $vaga?->remuneracao ?? '') }}" placeholder="Mín." />
                        </x-field>
                        <x-field label="Até (R$)" name="remuneracao_max">
                            <x-ui.input id="remuneracao_max" name="remuneracao_max" type="number" min="0" step="0.01" value="{{ old('remuneracao_max', $vaga?->remuneracao_max ?? '') }}" placeholder="Máx." />
                        </x-field>
                    </div>
                    <x-field label="Carga horária semanal" name="carga_horaria">
                        <x-ui.input id="carga_horaria" name="carga_horaria" type="number" min="1" max="44" value="{{ old('carga_horaria', $vaga?->carga_horaria ?? '') }}" placeholder="Ex.: 20" />
                    </x-field>
                    <x-field label="Inscrições até" name="data_encerramento" required>
                        <x-ui.input id="data_encerramento" name="data_encerramento" type="date" value="{{ old('data_encerramento', $vaga?->data_encerramento?->format('Y-m-d') ?? '') }}" />
                    </x-field>
                    <label class="flex items-center justify-between gap-3 text-sm">
                        <span class="font-medium">Notificar novas candidaturas por e-mail</span>
                        <input type="checkbox" name="notificar_email" value="1" @checked(old('notificar_email', $vaga?->notificar_email ?? true)) class="size-4 rounded border-input text-primary focus-visible:ring-3 focus-visible:ring-ring/50">
                    </label>
                </div>
            </section>

            <div class="flex flex-col gap-2">
                <x-ui.button type="submit" name="acao" value="publicar" class="h-10 w-full gap-1.5">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 3 3 9-3 9 19-9Z"/><path d="M6 12h16"/></svg>
                    Enviar para autorização
                </x-ui.button>
                <x-ui.button type="submit" name="acao" value="rascunho" variant="outline" class="h-10 w-full gap-1.5">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7M7 3v4a1 1 0 0 0 1 1h7"/></svg>
                    Salvar como rascunho
                </x-ui.button>
                <p class="text-center text-xs text-muted-foreground">A vaga só aparece no portal após autorização do gestor.</p>
            </div>
        </div>
    </form>
</x-layouts.internal>
