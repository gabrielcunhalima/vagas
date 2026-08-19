@php
    $cores = [
        'verde' => 'bg-emerald-500/12 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
        'azul' => 'bg-blue-500/12 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300',
        'violeta' => 'bg-violet-500/12 text-violet-700 dark:bg-violet-400/10 dark:text-violet-300',
        'laranja' => 'bg-orange-500/12 text-orange-700 dark:bg-orange-400/10 dark:text-orange-300',
        'teal' => 'bg-teal-500/12 text-teal-700 dark:bg-teal-400/10 dark:text-teal-300',
    ];

    $icone = fn (string $path, string $class = 'size-5') => '<svg class="' . $class . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';

    $especies = [
        ['nome' => 'Ostra-do-Pacífico', 'cientifico' => 'Crassostrea gigas', 'cor' => 'verde', 'texto' => 'Principal espécie de maricultura do estado. Santa Catarina responde por mais de 95% da produção nacional, e a UFSC contribui com genética e manejo de ponta.', 'pills' => ['Maricultura', 'Espécie-chave']],
        ['nome' => 'Camarão Vannamei', 'cientifico' => 'Litopenaeus vannamei', 'cor' => 'azul', 'texto' => 'Espécie dominante na carcinicultura mundial. Pesquisas focam em nutrição, genética de resistência a doenças e sistemas biofloc para produção sustentável.', 'pills' => ['Carcinicultura', 'Biofloc']],
        ['nome' => 'Mexilhão-perna-perna', 'cientifico' => 'Perna perna', 'cor' => 'violeta', 'texto' => 'Espécie nativa cultivada em long-lines na Baía Sul de Florianópolis. Alvo de estudos sobre qualidade microbiológica, sanidade e rastreabilidade.', 'pills' => ['Maricultura', 'Nativa']],
        ['nome' => 'Vieira', 'cientifico' => 'Nodipecten nodosus', 'cor' => 'laranja', 'texto' => 'Molusco de alto valor de mercado. Pesquisas em reprodução controlada e larvicultura viabilizam o fornecimento de sementes para produtores catarinenses.', 'pills' => ['Maricultura', 'Alto valor']],
        ['nome' => 'Tilápia-do-Nilo', 'cientifico' => 'Oreochromis niloticus', 'cor' => 'verde', 'texto' => 'Estudada em sistemas de recirculação de água (RAS) e aquaponia. Pesquisas avaliam bem-estar animal, nutrição funcional e integração com horticultura.', 'pills' => ['Piscicultura', 'RAS']],
        ['nome' => 'Microalgas', 'cientifico' => 'Nannochloropsis, Chaetoceros spp.', 'cor' => 'teal', 'texto' => 'Produzidas como alimento vivo para larvas de moluscos e camarões. Também investigadas como fonte de ômega-3, pigmentos e biocombustíveis de terceira geração.', 'pills' => ['Ficicultura', 'Biocombustível']],
    ];

    $timeline = [
        ['ano' => '1983', 'titulo' => 'Fundação da Estação', 'texto' => 'Implantação da Estação de Aquicultura Marinha da UFSC com foco inicial no cultivo experimental de ostras e mexilhões na Baía Norte de Florianópolis.'],
        ['ano' => '1991', 'titulo' => 'Laboratório de Camarões Marinhos', 'texto' => 'Criação do LCM, Laboratório de Camarões Marinhos, que se tornaria referência internacional em pesquisa com camarões peneídeos e desenvolvimento do sistema Biofloc.'],
        ['ano' => '2000', 'titulo' => 'Reconhecimento nacional', 'texto' => 'O Departamento de Aquicultura passa a integrar o Sistema Nacional de Pesquisa Agropecuária, ampliando parcerias com Embrapa, EPAGRI e órgãos federais.'],
        ['ano' => '2010', 'titulo' => 'Programa de Pós-Graduação', 'texto' => 'Consolidação do PPGAQUI, Programa de Pós-Graduação em Aquicultura, com conceito CAPES 5, formando mestres e doutores para o setor produtivo e academia.'],
        ['ano' => 'Hoje', 'titulo' => 'Polo de Inovação Aquícola', 'texto' => 'Mais de 200 pesquisadores ativos, projetos financiados por CNPq, FINEP e FAPESC, e parcerias com empresas do setor produtivo nacional e internacional.'],
    ];

    $parceiros = [
        ['nome' => 'UFSC', 'desc' => 'Universidade Federal de Santa Catarina'],
        ['nome' => 'CNPq', 'desc' => 'Conselho Nacional de Pesquisa'],
        ['nome' => 'FAPESC', 'desc' => 'Fundação de Amparo à Pesquisa de SC'],
        ['nome' => 'FINEP', 'desc' => 'Financiadora de Estudos e Projetos'],
        ['nome' => 'EPAGRI', 'desc' => 'Empresa de Pesquisa Agropecuária de SC'],
        ['nome' => 'MAPA', 'desc' => 'Ministério da Agricultura, Pecuária e Abastecimento'],
    ];
@endphp
<x-layouts.public title="Fazenda Ressacada: Projeto UFSC">
    <section class="bg-brand-deep">
        <div class="mx-auto w-full max-w-6xl px-4 py-16 lg:py-20">
            <div class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-white">
                {!! $icone('<path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6M6 12.5V16a6 3 0 0 0 12 0v-3.5"/>', 'size-3.5') !!}
                Projeto Universitário · UFSC
            </div>
            <h1 class="mt-5 text-4xl font-bold tracking-tight text-white sm:text-5xl">Fazenda Ressacada</h1>
            <p class="mt-4 max-w-xl text-sm leading-relaxed text-white/80 sm:text-base">
                Estação de Aquicultura da Universidade Federal de Santa Catarina, referência nacional em pesquisa, ensino e extensão no cultivo sustentável de organismos aquáticos.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                @foreach ([['+40', 'Anos de pesquisa'], ['12 ha', 'Área total'], ['6', 'Espécies cultivadas'], ['200+', 'Pesquisadores']] as [$num, $label])
                    <div class="min-w-28 rounded-xl border border-white/15 bg-white/10 px-5 py-3 text-center">
                        <div class="text-xl font-bold text-white">{{ $num }}</div>
                        <div class="mt-0.5 text-[0.825rem] text-white/65">{{ $label }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto w-full max-w-6xl px-4 py-14">
        <div class="grid items-center gap-10 lg:grid-cols-2">
            <div>
                <div class="text-xs font-bold uppercase tracking-widest text-primary">Sobre o projeto</div>
                <h2 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Uma estação de referência em aquicultura sustentável</h2>
                <div class="mt-4 flex flex-col gap-3 text-sm leading-relaxed text-muted-foreground">
                    <p>Localizada em Florianópolis, a Fazenda Ressacada é uma Unidade de Pesquisa, Ensino e Extensão vinculada ao Centro de Ciências Agrárias (CCA) da Universidade Federal de Santa Catarina (UFSC).</p>
                    <p>Fundada na década de 1980, a estação atua como laboratório vivo onde estudantes de graduação, pós-graduação e pesquisadores desenvolvem estudos sobre o cultivo de camarões, ostras, mexilhões, peixes e algas marinhas, contribuindo para o desenvolvimento científico e tecnológico da aquicultura brasileira.</p>
                    <p>Além da pesquisa, a Fazenda Ressacada promove ações de extensão junto às comunidades de maricultores e aquicultores de Santa Catarina, transferindo tecnologia e boas práticas que fortalecem a produção regional.</p>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ([
                    ['icone' => '<path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7Z"/>', 'cor' => 'verde', 'titulo' => 'Maricultura', 'texto' => 'Cultivo de ostras, mexilhões e vieiras em ambiente marinho controlado.'],
                    ['icone' => '<path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/>', 'cor' => 'azul', 'titulo' => 'Carcinicultura', 'texto' => 'Pesquisa com camarão-branco do Pacífico em sistemas intensivos e superintensivos.'],
                    ['icone' => '<path d="M6.5 12c.94-3.46 4.94-6 8.5-6 3.56 0 6.06 2.54 7 6-.94 3.47-3.44 6-7 6s-7.56-2.53-8.5-6Z"/><path d="M18 12v.5"/><path d="M16 17.93a9.77 9.77 0 0 1 0-11.86"/><path d="M7 10.67C7 8 5.58 5.97 2.73 5.5c-1 1.5-1 5 .27 6.5-1.27 1.5-1.27 5-.27 6.5C5.58 18.03 7 16 7 13.33"/><path d="M10.46 7.26C10.2 5.88 9.17 4.24 8 3h5.8a2 2 0 0 1 1.98 1.67l.23 1.4"/><path d="m16.01 17.93-.23 1.4A2 2 0 0 1 13.8 21H9.5a5.96 5.96 0 0 0 1.49-3.98"/>', 'cor' => 'violeta', 'titulo' => 'Piscicultura', 'texto' => 'Estudos com espécies nativas e exóticas em sistemas de recirculação de água (RAS).'],
                    ['icone' => '<path d="M12 5a3 3 0 1 1 3 3m-3-3a3 3 0 1 0-3 3m3-3v2M9 8a3 3 0 1 0 3 3M9 8H7m5 3a3 3 0 1 0 3 3m-3-3v-2m3 5a3 3 0 1 0-3 3m3-3h2m-5 3a3 3 0 1 1-3-3"/>', 'cor' => 'laranja', 'titulo' => 'Algas Marinhas', 'texto' => 'Cultivo de microalgas e macroalgas para alimentação, biocombustíveis e cosméticos.'],
                ] as $card)
                    <div class="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                        <div class="flex size-10 items-center justify-center rounded-lg {{ $cores[$card['cor']] }}">
                            {!! $icone($card['icone']) !!}
                        </div>
                        <h3 class="mt-3 text-sm font-bold">{{ $card['titulo'] }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-muted-foreground">{{ $card['texto'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="border-y bg-card/50">
        <div class="mx-auto w-full max-w-6xl px-4 py-14">
            <div class="mx-auto max-w-xl text-center">
                <div class="text-xs font-bold uppercase tracking-widest text-primary">Biodiversidade</div>
                <h2 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Espécies cultivadas na Fazenda</h2>
                <p class="mt-3 text-sm text-muted-foreground">A estação mantém cultivos ativos de diversas espécies, integrando produção, pesquisa e material didático para as turmas do AQI.</p>
            </div>
            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($especies as $e)
                    <div class="rounded-xl bg-card p-5 ring-1 ring-foreground/10">
                        <h3 class="text-sm font-bold">{{ $e['nome'] }}</h3>
                        <p class="text-xs italic text-muted-foreground">{{ $e['cientifico'] }}</p>
                        <p class="mt-2.5 text-sm leading-relaxed text-muted-foreground">{{ $e['texto'] }}</p>
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach ($e['pills'] as $pill)
                                <span class="rounded-full px-2.5 py-0.5 text-[0.825rem] font-semibold {{ $cores[$e['cor']] }}">{{ $pill }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto w-full max-w-6xl px-4 py-14">
        <div class="grid gap-10 lg:grid-cols-[2fr_3fr]">
            <div>
                <div class="text-xs font-bold uppercase tracking-widest text-primary">Histórico</div>
                <h2 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Mais de quatro décadas de ciência aquícola</h2>
                <p class="mt-4 text-sm leading-relaxed text-muted-foreground">Da implantação pioneira nos anos 1980 à liderança atual em pesquisa aquícola nacional, a Fazenda Ressacada consolidou-se como polo de excelência científica e transferência de tecnologia para o setor produtivo.</p>
                <div class="mt-6 rounded-xl bg-card p-4 ring-1 ring-foreground/10">
                    <div class="flex items-center gap-2 text-sm font-semibold">
                        {!! $icone('<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>', 'size-4 text-primary') !!}
                        Localização
                    </div>
                    <p class="mt-1.5 text-sm text-muted-foreground">Rodovia Admar Gonzaga, 1.346, Itacorubi<br>Florianópolis, SC, CEP 88034-001</p>
                </div>
            </div>
            <ol class="relative flex flex-col gap-7 border-l pl-6">
                @foreach ($timeline as $t)
                    <li class="relative">
                        <span class="absolute -left-[31px] flex size-2.5 items-center justify-center rounded-full bg-primary ring-4 ring-background"></span>
                        <div class="text-xs font-bold uppercase tracking-wide text-primary">{{ $t['ano'] }}</div>
                        <h3 class="mt-0.5 text-sm font-bold">{{ $t['titulo'] }}</h3>
                        <p class="mt-1 text-sm leading-relaxed text-muted-foreground">{{ $t['texto'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="border-y bg-card/50">
        <div class="mx-auto w-full max-w-6xl px-4 py-14">
            <div class="text-center">
                <div class="text-xs font-bold uppercase tracking-widest text-primary">Linhas de pesquisa</div>
                <h2 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">O que a Fazenda Ressacada investiga</h2>
            </div>
            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['icone' => '<path d="M6 3h12M6 21h12M8 3c0 4-4 6-4 9s4 5 4 9M16 3c0 4 4 6 4 9s-4 5-4 9"/>', 'cor' => 'verde', 'titulo' => 'Genética e Melhoramento', 'texto' => 'Seleção de linhagens resistentes a patógenos e com maior eficiência de conversão alimentar.'],
                    ['icone' => '<path d="M9 3h6l1 7H8Z"/><path d="M8 10v9a3 3 0 0 0 3 3h2a3 3 0 0 0 3-3v-9"/>', 'cor' => 'azul', 'titulo' => 'Nutrição e Alimentação', 'texto' => 'Desenvolvimento de rações funcionais com ingredientes regionais sustentáveis e avaliação de aditivos naturais.'],
                    ['icone' => '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5l7 7Z"/>', 'cor' => 'violeta', 'titulo' => 'Sanidade Aquícola', 'texto' => 'Diagnóstico e controle de enfermidades, uso racional de antibióticos e desenvolvimento de vacinas.'],
                    ['icone' => '<path d="M10 10v.2A3 3 0 0 1 8.9 16H5a3 3 0 0 1-1-5.83V10a3 3 0 0 1 6 0Z"/><path d="M7 16v6M13 19v3M12 19h8.3a1 1 0 0 0 .7-1.7L18 14h.3a1 1 0 0 0 .7-1.7L16 9h.2a1 1 0 0 0 .8-1.7L13 3l-1.4 1.5"/>', 'cor' => 'laranja', 'titulo' => 'Sustentabilidade', 'texto' => 'Avaliação de impacto ambiental, sistemas integrados multitróficos (IMTA) e certificações ambientais.'],
                ] as $card)
                    <div class="rounded-xl bg-card p-5 text-center ring-1 ring-foreground/10">
                        <div class="mx-auto flex size-10 items-center justify-center rounded-lg {{ $cores[$card['cor']] }}">
                            {!! $icone($card['icone']) !!}
                        </div>
                        <h3 class="mt-3 text-sm font-bold">{{ $card['titulo'] }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-muted-foreground">{{ $card['texto'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto w-full max-w-6xl px-4 py-14">
        <div class="grid items-center gap-8 rounded-2xl bg-brand-deep p-8 sm:p-10 lg:grid-cols-[3fr_1fr]">
            <div>
                <p class="text-lg font-semibold leading-relaxed text-white sm:text-xl">
                    &ldquo;A Fazenda Ressacada representa a ponte entre o conhecimento gerado na universidade e a realidade do aquicultor brasileiro, formando profissionais capazes de transformar ciência em produção sustentável.&rdquo;
                </p>
                <p class="mt-3 text-sm text-white/65">Departamento de Aquicultura · Centro de Ciências Agrárias · UFSC</p>
            </div>
            <div class="flex flex-col items-center gap-2 rounded-xl bg-white/10 px-6 py-6 text-center">
                {!! $icone('<path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"/><circle cx="12" cy="8" r="6"/>', 'size-9 text-white') !!}
                <span class="text-base font-bold text-white">Conceito CAPES 5</span>
                <span class="text-xs text-white/65">Programa de Pós-Graduação</span>
            </div>
        </div>
    </section>

    <section class="border-y bg-card/50">
        <div class="mx-auto w-full max-w-6xl px-4 py-14">
            <div class="text-center">
                <div class="text-xs font-bold uppercase tracking-widest text-primary">Parceiros e fomento</div>
                <h2 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Quem apoia a Fazenda Ressacada</h2>
            </div>
            <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($parceiros as $p)
                    <div class="rounded-xl bg-card p-4 text-center ring-1 ring-foreground/10">
                        {!! $icone('<rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4M8 6h.01M16 6h.01M12 6h.01M12 10h.01M12 14h.01M16 10h.01M16 14h.01M8 10h.01M8 14h.01"/>', 'mx-auto size-6 text-primary') !!}
                        <div class="mt-2 text-sm font-bold">{{ $p['nome'] }}</div>
                        <div class="mt-0.5 text-[0.825rem] leading-snug text-muted-foreground">{{ $p['desc'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto w-full max-w-6xl px-4 py-14">
        <div class="grid items-center gap-10 lg:grid-cols-2">
            <div>
                <div class="text-xs font-bold uppercase tracking-widest text-primary">Venha conhecer</div>
                <h2 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Localização e contato</h2>
                <p class="mt-4 text-sm leading-relaxed text-muted-foreground">A Fazenda Ressacada está aberta para visitas técnicas agendadas, recebe estagiários e alunos de iniciação científica vinculados à UFSC e a outras instituições parceiras.</p>
                <ul class="mt-6 flex flex-col gap-4">
                    @foreach ([
                        ['icone' => '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>', 'titulo' => 'Endereço', 'texto' => "Rodovia Admar Gonzaga, 1.346, Bairro Ressacada\nFlorianópolis, SC, CEP 88034-001"],
                        ['icone' => '<circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20M2 12h20"/>', 'titulo' => 'Departamento', 'texto' => "Departamento de Aquicultura (AQI)\nCentro de Ciências Agrárias, UFSC"],
                        ['icone' => '<rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>', 'titulo' => 'Vagas e estágios', 'texto' => 'Oportunidades vinculadas ao projeto são divulgadas neste portal.'],
                    ] as $item)
                        <li class="flex items-start gap-3">
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-accent">
                                {!! $icone($item['icone'], 'size-4 text-accent-foreground') !!}
                            </div>
                            <div>
                                <div class="text-sm font-semibold">{{ $item['titulo'] }}</div>
                                <p class="whitespace-pre-line text-sm text-muted-foreground">{{ $item['texto'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <x-ui.button tag="a" href="{{ route('vagas.publicas.index') }}" class="mt-7 gap-1.5">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    Ver vagas do projeto
                </x-ui.button>
            </div>
            <div class="flex flex-col items-center gap-2 rounded-2xl border border-dashed p-10 text-center">
                <svg class="size-10 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                <div class="font-semibold">Fazenda Ressacada, UFSC</div>
                <div class="text-sm text-muted-foreground">Florianópolis, Santa Catarina</div>
                <x-ui.button tag="a" href="https://maps.google.com/?q=Fazenda+Ressacada+UFSC+Florianopolis" target="_blank" rel="noreferrer" variant="outline" size="sm" class="mt-2 gap-1.5">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6M10 14 21 3M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                    Abrir no Google Maps
                </x-ui.button>
            </div>
        </div>
    </section>
</x-layouts.public>
