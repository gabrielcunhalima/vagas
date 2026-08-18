## Context

O app é hoje uma SPA Inertia.js + React: `resources/views/app.blade.php` só carrega `@inertia`/`@inertiaHead` e um único bundle `resources/js/app.jsx`; toda página vive em `resources/js/Pages/**` (25 arquivos), com 3 layouts (`AuthLayout`, `InternalLayout`, `PublicLayout`), ~40 componentes (incluindo uma cópia inteira de shadcn/radix-ui em `Components/ui` e `components/ui` — duplicados por causa de um problema de case já registrado no histórico do repo) e libs (`lib/format.js`, `lib/cpf.js`, `lib/enums.js`, `lib/utils.js`, `hooks/useMediaQuery.js`).

Controllers chamam `Inertia::render('Pasta/Pagina', [...])`; navegação usa `<Link>`/`router.get/post` do Inertia (visita assíncrona, sem reload de página, com `preserveState`/`preserveScroll`); testes Feature usam helpers próprios (`assertComponenteInertia`, `assertVeInertia`, `assertNaoVeInertia`, `propsInertia` em `tests/TestCase.php`) que leem o payload JSON de `data-page` em vez de HTML — o próprio comentário desses helpers registra que eles substituíram, numa migração anterior, `assertViewIs`/`assertViewHas`/`assertSee` do Laravel puro. Esta mudança é o caminho inverso.

O ponto de maior complexidade de interação é `Publico/Vagas/Index.jsx`: layout mestre-detalhe responsivo (lista + painel de detalhe na mesma resposta, sem fetch adicional ao selecionar), filtros que reaplicam a busca a cada mudança de select (via visita Inertia com `preserveState`), painel de detalhe que vira um off-canvas (Sheet) abaixo do breakpoint `xl`, e paginação. Outros pontos de estado no cliente: `CurriculoDropzone` (arrastar/soltar + preview de arquivo antes do submit), `PasswordStrengthMeter` (feedback de força ao digitar), `ThemeToggle` (dark/light via classe em `<html>` + `localStorage`, sem servidor).

Ver proposal.md — Why/What Changes para motivação e lista completa de remoções/adições.

## Goals / Non-Goals

**Goals:**
- Eliminar toda dependência de React/Inertia/JSX do projeto; o app volta a ser Laravel MPA clássico.
- Preservar 1:1 o comportamento hoje validado: URLs, nomes de rota, dados exibidos, regras de acesso/middleware, e-mails (já Blade, não mudam).
- Preservar as interações que hoje dependem de estado client-side (filtros auto-aplicados, dropzone, medidor de senha, tema, master-detail de vagas, paginação) usando apenas JS puro (sem framework de componentes, sem build de JSX).
- Manter Tailwind CSS como camada de estilo, migrando classes 1:1 dos componentes React para os componentes/partials Blade.
- Manter Vite como bundler (ele já compila Tailwind e pode compilar JS puro), removendo apenas os plugins/dependências específicas de React/Inertia.

**Non-Goals:**
- Não introduzir um novo framework JS (Alpine.js, Stimulus, htmx, Livewire, Vue etc.) — o pedido explícito é JavaScript puro (vanilla).
- Não mudar regras de negócio, policies, middleware, schema de banco ou a integração com o DRHFlow (SQL Server).
- Não reescrever os e-mails (`resources/views/emails/**`), que já são Blade e não usam Inertia/React.
- Não otimizar/redesenhar visualmente as telas além do necessário para reproduzir o layout atual em Blade+Tailwind.

## Decisions

### 1. Estrutura de views: 1 view Blade por página Inertia, layouts viram Blade layouts
Cada `resources/js/Pages/<Grupo>/<Nome>.jsx` vira `resources/views/<grupo-kebab>/<nome-kebab>.blade.php`, seguindo a convenção padrão do Laravel (`resources/views/candidato/candidaturas/index.blade.php` em vez de `Candidato/Candidaturas/Index`). Os três layouts React (`AuthLayout`, `InternalLayout`, `PublicLayout`) viram `resources/views/layouts/{auth,internal,public}.blade.php`, usando `@extends`/`@section`/slots nomeados para `pageTitle`, `breadcrumb` e `topbarActions` (hoje props/children do componente React).
- **Alternativa considerada**: manter nomes/estrutura de pastas idênticos aos do Inertia (`Pages/Candidato/...`) para minimizar diff. Rejeitada: não é convenção Blade e confundiria o próximo desenvolvedor sobre por que "Pages" existe sem framework de páginas.

### 2. Controllers trocam `Inertia::render` por `view()`, mantendo o mesmo array de dados
Cada `Inertia::render('X/Y', $props)` vira `view('x.y', $props)`. Como os `$props` já são os dados que a view precisa (nenhum é específico de serialização Inertia), a troca é mecânica na maioria dos controllers. Onde a página hoje decide UI só a partir de props (ex.: `indisponivel`, `filtros`), a view Blade recebe as mesmas variáveis e decide com `@if`.
- **Risco**: alguns controllers podem depender de comportamento Inertia implícito (ex.: `303` em redirects de `PUT/PATCH/DELETE` feitos via visita Inertia, ou merge parcial de props). Precisa ser conferido controller a controller nas tasks (ver Riscos).

### 3. Formulários voltam a ser submissões HTML clássicas; interações "sem reload" viram `fetch` pontual em JS puro
- Formulários de criar/editar/excluir (login, cadastro, perfil, vaga, candidatura, status) usam `<form method="POST">` padrão do Laravel (com `@csrf` e `@method('PUT'|'PATCH'|'DELETE')`), erros de validação e flash messages via `session('errors')`/`session('status')`, exatamente como qualquer app Blade — o comportamento de mostrar erro por campo já existe nos componentes de erro (basta reimplementar `<x-input-error>` em vez do `errors.campo` do Inertia.
- Interações que hoje evitam reload de página inteira sem ser navegação (troca de filtro nos selects de `/vagas`, mudança de ordenação, seleção de vaga no master-detail) são reimplementadas com `fetch()` para a mesma rota GET, substituindo `<body>`/o trecho relevante via `document.querySelector(...).outerHTML = ...` (progressive enhancement): sem JS, o clique em "Buscar"/troca de filtro ainda funciona como submit de formulário GET normal (fallback correto); com JS, o mesmo request é feito via fetch e só o HTML da lista+detalhe é trocado, preservando scroll — reproduzindo o `preserveState`/`preserveScroll` de hoje.
  - Para isso, os controllers que hoje só respondem a Inertia (`VagaPublicaController::index`) passam a suportar responder com a página completa OU, quando a requisição vier com um header próprio (`X-Requested-With: fetch` ou `Accept: text/vnd.turbo-stream+html`-like custom header), retornar só a `view` parcial da lista+detalhe (um Blade `@include` renderizado isoladamente). Isso evita duplicar o Blade de listagem entre "página cheia" e "fragmento".
- **Alternativa considerada**: sempre fazer full page reload nos filtros (mais simples, zero JS). Rejeitada: quebra a preferência já validada com o usuário de "filtros auto-aplicam" com boa UX (perderia scroll/estado do painel selecionado a cada troca). O fetch parcial é o meio-termo que preserva a experiência sem framework.

### 4. Componentes de UI (shadcn/radix) viram Blade components + CSS/JS Tailwind "a mão"
`Components/ui/*` (button, input, select, dialog, sheet, dropdown-menu, tabs, switch, checkbox, radio-group, popover, command, table, badge, alert, alert-dialog, card, avatar, label, progress, separator, textarea, input-group) são reimplementados como Blade components em `resources/views/components/ui/*.blade.php`, com as mesmas classes Tailwind (o design visual não muda). Onde o componente React só formata estilo sem estado (button, badge, card, alert, table, separator, label, input, textarea) a tradução é direta.
Onde o componente hoje depende de estado/portal do Radix (`select`, `dialog`, `sheet`, `dropdown-menu`, `popover`, `command`, `tabs`, `switch`, `checkbox`, `radio-group`), a reimplementação usa, nesta ordem de preferência:
1. **Elemento HTML nativo equivalente** quando cobre o caso (`<select>` estilizado para os selects de filtro simples; `<dialog>` para confirmações modais; `<details>/<summary>` para dropdowns simples).
2. **JS puro mínimo** (um arquivo por widget em `resources/js/ui/*.js`, sem dependência entre eles) só quando o nativo não cobre a interação hoje usada (ex.: o Sheet mobile do painel de detalhe de vaga, que precisa abrir/fechar por classe CSS e travar scroll do body).
- **Alternativa considerada**: portar Radix UI tal como está (ele é framework-agnostic em teoria). Rejeitada: Radix é distribuído como pacotes React (`@radix-ui/react-*`); não há build "vanilla" oficial, então mantê-lo contradiz a remoção de React.

### 5. Tema claro/escuro, medidor de senha e dropzone: JS puro 1:1
Esses três já são JS relativamente simples hoje (ver Context) e não dependem de Inertia nem de Radix — só de React para orquestrar estado local. Viram scripts vanilla anexados por `data-*` attributes / `document.querySelectorAll` num arquivo por widget (`resources/js/tema.js`, `resources/js/senha-forca.js`, `resources/js/curriculo-dropzone.js`), carregados globalmente via `@vite` no layout base. Comportamento preservado: classe `dark` em `<html>` + `localStorage('theme')`; barra + checklist de regras ao digitar senha; drag&drop + preview de arquivo + limpar.

### 6. `route()`/Ziggy em JS: removido, URLs viem prontas do Blade
Hoje o JS usa `route('nome.rota', params)` via `ziggy-js`. Sem Inertia/SPA, cada link já é gerado no Blade (`{{ route('nome.rota', $params) }}`) e embutido no HTML (`href`, `action`, ou `data-url` para os poucos casos de fetch em JS). `ziggy-js` e o helper global `route()` no client são removidos; nenhum JS precisa montar URL de rota nomeada.

### 7. Testes: remove os helpers de asserção Inertia, volta para asserções Blade padrão
`tests/TestCase.php` perde `propsInertia`/`assertComponenteInertia`/`assertVeInertia`/`assertNaoVeInertia`/`propsInertiaJson`. Os call-sites (`assertComponenteInertia($r, 'Publico/Vagas/Index')` → `assertViewIs('publico.vagas.index')`; `assertVeInertia($r, 'X')` → `assertSee('X')`; `assertNaoVeInertia` → `assertDontSee`) são atualizados teste a teste. Isso é mecânico mas tem volume (`VagaPublicaTest.php` sozinho tem ~20 ocorrências; `InscricaoTest`, `CandidaturaTriagemTest`, `ExclusaoContaTest`, `RetencaoContasInativasTest` também usam os helpers — confirmar no grep de tasks).

### 8. Pipeline de build: mantém Vite, remove plugin/deps de React
`vite.config.js` perde `laravel-vite-plugin`'s config específica de refresh React e `@vitejs/plugin-react`; mantém `@tailwindcss/vite`. Entry point deixa de ser `resources/js/app.jsx` e passa a ser um ou mais entries JS puro (`resources/js/app.js` com os widgets vanilla) + `resources/css/app.css` (Tailwind). `resources/views/app.blade.php` deixa de ter `@inertia`/`@inertiaHead`/`@viteReactRefresh` e vira um layout Blade real (ou é substituído pelos layouts descritos na Decisão 1).

## Risks / Trade-offs

- **[Risco] Paridade visual/comportamental perdida em componentes Radix complexos (Select multi-estado, Command/combobox, Sheet)** → Mitigação: revisar cada tela que usa esses componentes durante a implementação e validar visualmente (a skill `run`/checagem manual no browser) antes de considerar a tarefa concluída; priorizar HTML nativo (`<select>`, `<dialog>`) sempre que a interação permitir.
- **[Risco] Regressão silenciosa em filtros/paginação que hoje dependem de `preserveState` do Inertia** → Mitigação: o fallback é sempre "funciona sem JS" (form GET clássico); o fetch parcial é só progressive enhancement, então na pior hipótese o usuário perde a suavidade da troca mas não a funcionalidade.
- **[Risco] Volume de trabalho grande e risco de regressão ampla numa única mudança (25 páginas + 40 componentes + testes)** → Mitigação: tasks.md quebra a migração por módulo (público → candidato → coordenador → gestor → limpeza final), cada um testável isoladamente antes de seguir para o próximo; testes Feature existentes (reescritos incrementalmente) servem de rede de segurança por módulo.
- **[Risco] Duplicação `Components/` vs `components/` (case-only) já é uma fonte de bug conhecida no histórico do projeto (commit "Corrige o case da pasta de componentes")** → Mitigação: a migração cria a árvore `resources/views/components/**` do zero (não copia arquivo por arquivo), então o problema de case não se repete; o diretório `resources/js/Components` e `resources/js/components` inteiros são apagados no fim.
- **[Trade-off] JS puro para widgets como Sheet/dropdown custom exige mais código boilerplate por widget do que uma lib como Radix** → Aceito conscientemente: é exatamente o pedido do usuário (sem framework de componentes React).

## Migration Plan

1. **Fundação**: novo pipeline Vite (sem React/Inertia), `app.blade.php`/layouts base, componentes Blade de UI genéricos (button, input, card, badge, etc.), widgets JS puro (tema, senha, dropzone).
2. **Público** (`/`, `/vagas`, `/vagas/{id}`, `/politica-privacidade`, `/fazenda-ressacada`, `/candidatura/*`, `/alertas*`): maior superfície de teste automatizado hoje (`VagaPublicaTest`, `InscricaoTest`); migrar e reescrever testes desse módulo antes de seguir.
3. **Candidato** (`/minha-conta/**`: login, cadastro, recuperação de senha, perfil, minhas candidaturas): reaproveita os widgets de senha/dropzone da fundação.
4. **Coordenador** (`/coord/**`) e **Gestor** (`/gestor/**`): telas internas, sidebar/layout interno.
5. **Limpeza final**: remover `resources/js/Pages`, `Components`, `Layouts`, `hooks`, `lib` (JSX), dependências React/Inertia/shadcn do `package.json`, `laravel-vite-plugin` config de Inertia, `Vite::prefetch` em `AppServiceProvider` (é otimização de bundle SPA; reavaliar se ainda faz sentido para assets JS puro), e conferir que `composer.json` não tem `inertiajs/inertia-laravel` (checar nas tasks).
Rollback: cada etapa é um conjunto de commits sobre a mesma branch; como o comportamento observável (URLs, dados, regras) não muda, reverter é reverter os commits daquela etapa sem efeito em dados/migrations (não há migration de banco nesta mudança).

## Open Questions

- Nenhuma pendência bloqueante identificada; ambiguidades relevantes (estratégia de fetch parcial vs full reload, tratamento dos componentes Radix complexos) já foram resolvidas nas Decisões 3 e 4 acima.
