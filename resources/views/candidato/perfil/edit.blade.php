{{-- $candidato: array (dados + tem_curriculo, curriculo_nome_original, formacoes, possui_acessibilidade, acessibilidade_detalhe) --}}
@php
$completude = auth('candidato')->user()->estadoCompletude();
$ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
$disponibilidades = ['Imediata', '15 dias', '30 dias', '60 dias'];
$rotulosPendencia = [
'nome' => 'Nome completo',
'nacionalidade' => 'Nacionalidade',
'telefone' => 'Telefone',
'formacao' => 'Formação acadêmica',
'possui_acessibilidade' => 'Necessidade de acessibilidade',
'curriculo' => 'Currículo em PDF',
];
@endphp
<x-layouts.public title="Meus dados">
    <div class="mx-auto w-full max-w-3xl px-4 pt-10">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Meus dados</h1>
            </div>
            <x-ui.button tag="a" href="{{ route('candidato.perfil.exportar') }}" variant="outline" size="sm" class="gap-1.5">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                    <polyline points="7 10 12 15 17 10" />
                    <line x1="12" x2="12" y1="15" y2="3" />
                </svg>
                Exportar meus dados
            </x-ui.button>
        </div>

        <div class="mt-5 rounded-xl bg-card p-5 ring-1 ring-foreground/10">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-sm font-bold tracking-tight">
                    @if ($completude['completo'])
                    <span class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-500">
                        <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21.801 10A10 10 0 1 1 17 3.335" />
                            <path d="m9 11 3 3L22 4" />
                        </svg>
                        Perfil completo — você já pode se candidatar
                    </span>
                    @else
                    Complete seu perfil para se candidatar
                    @endif
                </h2>
                <span class="text-xs text-muted-foreground">{{ $completude['atendidos'] }} de {{ $completude['total'] }}</span>
            </div>

            <x-ui.progress :value="$completude['progresso']" class="mt-3 h-1.5" />

            <ul class="mt-3 flex flex-wrap gap-1.5">
                @foreach ($rotulosPendencia as $chave => $rotulo)
                @php $completo = !array_key_exists($chave, $completude['pendencias']); @endphp
                <li class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium transition-colors {{ $completo ? 'bg-emerald-500/10 text-emerald-600 ring-1 ring-emerald-500/25 dark:text-emerald-500' : 'bg-muted text-muted-foreground' }}">
                    @if ($completo)
                    <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    @endif
                    {{ $rotulo }}
                </li>
                @endforeach
            </ul>
        </div>

        <form method="POST" action="{{ route('candidato.perfil.update') }}" enctype="multipart/form-data" class="mt-7 flex flex-col gap-5">
            @csrf
            @method('PUT')

            <section class="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
                <h2 class="text-sm font-bold tracking-tight">Dados pessoais</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <x-field label="Nome completo" name="nome" required class="sm:col-span-2">
                        <x-ui.input id="nome" name="nome" value="{{ old('nome', $candidato['nome']) }}" required />
                    </x-field>
                    <x-field label="E-mail" name="email" required>
                        <x-ui.input id="email" name="email" type="email" value="{{ old('email', $candidato['email']) }}" required />
                    </x-field>
                    <x-field label="CPF" name="cpf" required>
                        <x-ui.input id="cpf" name="cpf" inputmode="numeric" value="{{ old('cpf', $candidato['cpf'] ? \App\Support\Cpf::mascara($candidato['cpf']) : '') }}" data-cpf-input required />
                    </x-field>
                    <x-field label="Telefone" name="telefone" hint="Formato: (48) 99999-9999">
                        <x-ui.input id="telefone" name="telefone" inputmode="numeric" value="{{ old('telefone', $candidato['telefone']) }}" data-telefone-input />
                    </x-field>
                    <x-field label="LinkedIn" name="linkedin" hint="Ex.: https://linkedin.com/in/voce">
                        <x-ui.input id="linkedin" name="linkedin" type="url" value="{{ old('linkedin', $candidato['linkedin']) }}" />
                    </x-field>
                    <x-field label="Nome social" name="nome_social">
                        <x-ui.input id="nome_social" name="nome_social" value="{{ old('nome_social', $candidato['nome_social']) }}" />
                    </x-field>
                    <x-field label="Nacionalidade" name="nacionalidade" required>
                        <x-ui.input id="nacionalidade" name="nacionalidade" value="{{ old('nacionalidade', $candidato['nacionalidade']) }}" />
                    </x-field>
                </div>
            </section>

            <section class="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
                <h2 class="text-sm font-bold tracking-tight">Formação</h2>
                <p class="mt-0.5 text-xs text-muted-foreground">Adicione quantas formações desejar. Ao menos uma completa é exigida para se candidatar.</p>

                <div class="mt-4 flex flex-col gap-4" data-formacoes-lista>
                    @foreach ($candidato['formacoes'] as $i => $formacao)
                    @include('candidato.perfil._formacao', ['index' => $i, 'formacao' => $formacao])
                    @endforeach
                </div>
                <template data-formacao-template>@include('candidato.perfil._formacao', ['index' => '__INDEX__', 'formacao' => []])</template>
                <button type="button" data-adicionar-formacao class="mt-4 inline-flex h-8 cursor-pointer items-center gap-1 rounded-[min(var(--radius-md),12px)] border border-border bg-background px-2.5 text-[0.925rem] font-medium hover:bg-muted">
                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M12 5v14" />
                    </svg>
                    Adicionar formação
                </button>

                <div class="mt-5 grid gap-4 border-t pt-5">
                    <x-field label="Outras Formações Superiores reconhecidas pelo MEC" name="outras_formacoes_mec">
                        <x-ui.textarea id="outras_formacoes_mec" name="outras_formacoes_mec" rows="3">{{ old('outras_formacoes_mec', $candidato['outras_formacoes_mec']) }}</x-ui.textarea>
                    </x-field>
                    <x-field label="Especializações" name="outros_cursos" hint="Ex.: cursos de extensão, palestras, workshops, etc.">
                        <x-ui.textarea id="outros_cursos" name="outros_cursos" rows="3">{{ old('outros_cursos', $candidato['outros_cursos']) }}</x-ui.textarea>
                    </x-field>
                </div>
            </section>

            <section class="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
                <h2 class="text-sm font-bold tracking-tight">Acessibilidade</h2>
                <p class="mt-0.5 text-xs text-muted-foreground">Usado apenas para garantir condições adequadas no processo seletivo.</p>
                @php $possuiAcessibilidade = old('possui_acessibilidade', $candidato['possui_acessibilidade'] === null ? '' : ($candidato['possui_acessibilidade'] ? '1' : '0')); @endphp
                <div class="mt-4 grid gap-4">
                    <x-field label="Você precisa de alguma adaptação de acessibilidade?" name="possui_acessibilidade" required>
                        <select id="possui_acessibilidade" name="possui_acessibilidade" class="h-9 w-full rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
                            <option value="" @selected($possuiAcessibilidade==='' )>Selecione</option>
                            <option value="0" @selected($possuiAcessibilidade==='0' )>Não</option>
                            <option value="1" @selected($possuiAcessibilidade==='1' )>Sim</option>
                        </select>
                    </x-field>
                    <div data-mostrar-se="possui_acessibilidade=1" class="{{ $possuiAcessibilidade === '1' ? '' : 'hidden' }}">
                        <x-field label="Qual adaptação você precisa?" name="acessibilidade_detalhe" required>
                            <x-ui.textarea id="acessibilidade_detalhe" name="acessibilidade_detalhe" rows="3">{{ old('acessibilidade_detalhe', $candidato['acessibilidade_detalhe']) }}</x-ui.textarea>
                        </x-field>
                    </div>
                </div>
            </section>

            <section class="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
                <h2 class="text-sm font-bold tracking-tight">Endereço</h2>
                <p class="mt-0.5 text-xs text-muted-foreground">Informe o CEP para preenchimento automático.</p>
                <div class="mt-4 grid gap-4 sm:grid-cols-6">
                    <x-field label="CEP" name="cep" class="sm:col-span-2">
                        <x-ui.input id="cep" name="cep" inputmode="numeric" value="{{ old('cep', $candidato['cep']) }}" data-cep-input data-cep-url="{{ url('/api/cep') }}" data-cep-logradouro="logradouro" data-cep-bairro="bairro" data-cep-cidade="cidade" data-cep-estado="estado" />
                        <p data-cep-hint-for="cep" class="text-xs text-muted-foreground"></p>
                    </x-field>
                    <x-field label="Cidade" name="cidade" class="sm:col-span-3">
                        <x-ui.input id="cidade" name="cidade" value="{{ old('cidade', $candidato['cidade']) }}" />
                    </x-field>
                    <x-field label="UF" name="estado" class="sm:col-span-1">
                        <select id="estado" name="estado" class="h-9 w-full rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
                            <option value="">UF</option>
                            @foreach ($ufs as $uf)
                            <option value="{{ $uf }}" @selected(old('estado', $candidato['estado'])===$uf)>{{ $uf }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Bairro" name="bairro" class="sm:col-span-3">
                        <x-ui.input id="bairro" name="bairro" value="{{ old('bairro', $candidato['bairro']) }}" />
                    </x-field>
                    <x-field label="Logradouro" name="logradouro" class="sm:col-span-3">
                        <x-ui.input id="logradouro" name="logradouro" value="{{ old('logradouro', $candidato['logradouro']) }}" />
                    </x-field>
                    <x-field label="Número" name="numero" class="sm:col-span-2">
                        <x-ui.input id="numero" name="numero" value="{{ old('numero', $candidato['numero']) }}" />
                    </x-field>
                    <x-field label="Complemento" name="complemento" class="sm:col-span-4">
                        <x-ui.input id="complemento" name="complemento" value="{{ old('complemento', $candidato['complemento']) }}" />
                    </x-field>
                </div>
            </section>

            <section class="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
                <h2 class="text-sm font-bold tracking-tight">Preferências</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <x-field label="Pretensão salarial (R$)" name="pretensao_salarial">
                        <x-ui.input id="pretensao_salarial" name="pretensao_salarial" type="number" min="0" step="0.01" value="{{ old('pretensao_salarial', $candidato['pretensao_salarial']) }}" />
                    </x-field>
                    <x-field label="Disponibilidade para início" name="disponibilidade">
                        <select id="disponibilidade" name="disponibilidade" class="h-9 w-full rounded-lg border border-input bg-transparent px-2.5 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm dark:bg-input/30">
                            <option value="">Selecione</option>
                            @foreach ($disponibilidades as $d)
                            <option value="{{ $d }}" @selected(old('disponibilidade', $candidato['disponibilidade'])===$d)>{{ $d }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <div class="flex flex-col gap-3 sm:col-span-2">
                        <label class="flex items-center gap-3">
                            <input type="checkbox" id="pcd" name="pcd" value="1" @checked(old('pcd', $candidato['pcd'])) class="size-4 rounded border-input text-primary focus-visible:ring-3 focus-visible:ring-ring/50">
                            <span class="text-sm font-medium">Sou pessoa com deficiência (PcD)</span>
                        </label>
                        <div data-mostrar-se="pcd=1" class="{{ old('pcd', $candidato['pcd']) ? '' : 'hidden' }}">
                            <x-field label="Tipo de deficiência" name="pcd_tipo">
                                <x-ui.input id="pcd_tipo" name="pcd_tipo" value="{{ old('pcd_tipo', $candidato['pcd_tipo']) }}" />
                            </x-field>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
                <h2 class="text-sm font-bold tracking-tight">Currículo</h2>
                <p class="mt-0.5 text-xs text-muted-foreground">
                    Usado como padrão nas suas candidaturas. Fica guardado por {{ \App\Models\CandidatoCurriculo::RETENCAO_MESES }} meses a partir do envio ou da sua última candidatura; depois disso, é removido automaticamente.
                </p>

                <div class="mt-4">
                    @if ($candidato['tem_curriculo'])
                    <div class="mb-4 flex flex-wrap items-center gap-3 rounded-lg bg-muted/50 px-3 py-2.5 ring-1 ring-foreground/10">
                        <svg class="size-4 shrink-0 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                            <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                        </svg>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-medium">{{ $candidato['curriculo_nome_original'] }}</div>
                            <div class="text-xs text-muted-foreground">Guardado até {{ $candidato['curriculo_expira_em'] }}</div>
                        </div>
                        <div class="flex flex-wrap items-center gap-1">
                            <x-ui.button tag="button" type="button" variant="ghost" size="sm" data-dialog-trigger="visualizar-curriculo" class="gap-1.5">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                Visualizar
                            </x-ui.button>
                            <x-ui.button tag="a" href="{{ route('candidato.perfil.curriculo.download') }}" variant="ghost" size="sm" class="gap-1.5">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="7 10 12 15 17 10" />
                                    <line x1="12" x2="12" y1="15" y2="3" />
                                </svg>
                                Baixar
                            </x-ui.button>
                            <x-ui.button tag="button" type="button" variant="destructive" size="sm" data-dialog-trigger="remover-curriculo" class="gap-1.5">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14Z" />
                                </svg>
                                Remover
                            </x-ui.button>
                        </div>
                    </div>
                    @endif

                    <x-field name="curriculo" :hint="$candidato['tem_curriculo'] ? 'Enviar um novo arquivo substitui o atual.' : null">
                        <div data-curriculo-dropzone>
                            <input type="file" name="curriculo" id="curriculo" accept="application/pdf,.pdf" data-curriculo-input>
                            <div data-curriculo-empty class="hidden flex-col items-center gap-1.5 rounded-lg border border-dashed border-input px-4 py-6 text-center transition-colors hover:border-primary/50 hover:bg-accent/40 {{ $errors->has('curriculo') ? 'border-destructive' : '' }}" tabindex="0" role="button">
                                <svg class="size-5 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242" />
                                    <path d="M12 12v9" />
                                    <path d="m16 16-4-4-4 4" />
                                </svg>
                                <span class="text-sm font-medium">Arraste o PDF ou clique para selecionar</span>
                                <span class="text-xs text-muted-foreground">Somente PDF · máx. 5 MB</span>
                            </div>
                            <div data-curriculo-preview class="hidden flex items-center gap-3 rounded-lg bg-muted/50 px-3 py-2.5 ring-1 ring-foreground/10">
                                <svg class="size-4 shrink-0 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                    <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                                </svg>
                                <span class="min-w-0 flex-1 truncate text-sm font-medium" data-curriculo-filename></span>
                                <span class="shrink-0 text-xs text-muted-foreground" data-curriculo-filesize></span>
                                <button type="button" data-curriculo-remove class="shrink-0 cursor-pointer rounded-md p-1 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground" aria-label="Remover arquivo">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 6 6 18M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </x-field>
                </div>
            </section>

            <x-ui.button type="submit" class="h-10 gap-1.5 sm:self-end sm:px-8">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" />
                    <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7M7 3v4a1 1 0 0 0 1 1h7" />
                </svg>
                Salvar alterações
            </x-ui.button>
        </form>

        <form method="POST" action="{{ route('candidato.perfil.senha') }}" class="mt-10 rounded-xl bg-card p-5 ring-1 ring-foreground/10 sm:p-6">
            @csrf
            @method('PUT')
            <h2 class="text-sm font-bold tracking-tight">Alterar senha</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                <x-field label="Senha atual" name="senha_atual">
                    <x-ui.input id="senha_atual" name="senha_atual" type="password" autocomplete="current-password" required />
                </x-field>
                <x-field label="Nova senha" name="password">
                    <x-ui.input id="password_senha" name="password" type="password" autocomplete="new-password" required />
                </x-field>
                <x-field label="Confirmar nova senha" name="password_confirmation">
                    <x-ui.input id="password_confirmation_senha" name="password_confirmation" type="password" autocomplete="new-password" required />
                </x-field>
            </div>
            <x-ui.button type="submit" variant="outline" class="mt-4">Atualizar senha</x-ui.button>
        </form>

        <div class="mt-10 rounded-xl border border-destructive/30 bg-destructive/5 p-5 sm:p-6">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 size-5 shrink-0 text-destructive" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
                    <path d="M12 9v4M12 17h.01" />
                </svg>
                <div class="flex-1">
                    <h2 class="text-sm font-bold tracking-tight text-destructive">Excluir minha conta</h2>
                    <p class="mt-1 text-sm leading-relaxed text-muted-foreground">Seus dados pessoais e candidaturas serão anonimizados de forma irreversível, conforme a LGPD.</p>

                    <x-ui.button tag="button" type="button" variant="destructive" size="sm" data-dialog-trigger="excluir-conta" class="mt-4 gap-1.5">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14Z" />
                        </svg>
                        Excluir conta
                    </x-ui.button>
                </div>
            </div>
        </div>
    </div>

    @if ($candidato['tem_curriculo'])
    {{-- O PDF só é pedido ao abrir (data-src): o arquivo vem do servidor de
             arquivos, e carregá-lo junto com a página atrasaria "Meus dados". --}}
    <dialog data-dialog="visualizar-curriculo" class="w-full max-w-4xl rounded-xl">
        <div class="flex h-[85dvh] flex-col bg-card ring-1 ring-foreground/10">
            <div class="flex items-center gap-3 border-b px-4 py-3">
                <h2 class="min-w-0 flex-1 truncate text-sm font-bold tracking-tight">{{ $candidato['curriculo_nome_original'] }}</h2>
                <x-ui.button tag="a" href="{{ route('candidato.perfil.curriculo.visualizar') }}" target="_blank" rel="noopener" variant="outline" size="sm" class="gap-1.5">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h6v6M10 14 21 3M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                    </svg>
                    Abrir em nova aba
                </x-ui.button>
                <button type="button" data-dialog-close class="cursor-pointer rounded-md p-1.5 text-muted-foreground hover:bg-muted" aria-label="Fechar">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <iframe data-src="{{ route('candidato.perfil.curriculo.visualizar') }}" title="Currículo" class="min-h-0 w-full flex-1 bg-muted"></iframe>
        </div>
    </dialog>
    @endif

    <dialog data-dialog="remover-curriculo" class="w-full max-w-md rounded-xl">
        <form method="POST" action="{{ route('candidato.perfil.curriculo.remover') }}" class="bg-card p-6 ring-1 ring-foreground/10">
            @csrf
            @method('DELETE')
            <h2 class="text-base font-bold tracking-tight">Remover currículo?</h2>
            <p class="mt-2 text-sm text-muted-foreground">O arquivo será excluído do seu perfil. Candidaturas já enviadas não são afetadas.</p>
            <div class="mt-5 flex justify-end gap-2">
                <x-ui.button type="button" variant="outline" data-dialog-close>Cancelar</x-ui.button>
                <x-ui.button type="submit" variant="destructive">Remover</x-ui.button>
            </div>
        </form>
    </dialog>

    <dialog data-dialog="excluir-conta" class="w-full max-w-md rounded-xl">
        <form method="POST" action="{{ route('candidato.excluir') }}" class="bg-card p-6 ring-1 ring-foreground/10">
            @csrf
            @method('DELETE')
            <h2 class="text-base font-bold tracking-tight">Excluir conta permanentemente?</h2>
            <p class="mt-2 text-sm text-muted-foreground">Esta ação é irreversível. Seus dados e candidaturas serão anonimizados e você perderá o acesso à conta.</p>

            <div class="mt-4 flex flex-col gap-4">
                <label class="flex items-start gap-2.5">
                    <input type="checkbox" name="confirmar_exclusao" value="1" class="mt-0.5 size-4 rounded border-input text-primary focus-visible:ring-3 focus-visible:ring-ring/50">
                    <span class="text-sm">Entendo que esta ação não pode ser desfeita.</span>
                </label>
                <x-input-error for="confirmar_exclusao" />
                <x-field label="Confirme sua senha" name="password">
                    <x-ui.input id="senha_exclusao" name="password" type="password" autocomplete="current-password" required />
                </x-field>
            </div>

            <div class="mt-5 flex justify-end gap-2">
                <x-ui.button type="button" variant="outline" data-dialog-close>Cancelar</x-ui.button>
                <x-ui.button type="submit" variant="destructive">Excluir definitivamente</x-ui.button>
            </div>
        </form>
    </dialog>
</x-layouts.public>