# Design System — Portal de Vagas FAPEU

Sistema de design do Portal de Vagas, construído sobre **React + Inertia + Tailwind CSS v4 + shadcn/ui**. Este documento é a fonte de verdade para tokens, componentes e padrões de página.

---

## 1. Stack

| Camada        | Tecnologia                                        |
|---------------|---------------------------------------------------|
| Backend       | Laravel 13 (controllers → `Inertia::render`)      |
| Frontend      | React 19 + Inertia.js v2                          |
| CSS           | Tailwind CSS v4 (`@theme` em `resources/css/app.css`) |
| Componentes   | shadcn/ui (estilo radix-nova) em `resources/js/Components/ui/` |
| Ícones        | `lucide-react` (exclusivamente — sem CDNs)        |
| Rotas no JS   | Ziggy (`route('nome.da.rota')` global)            |
| Tipografia    | DM Sans (Google Fonts), única família             |
| Build         | Vite 8                                            |

**Não usar:** Bootstrap (removido), Bootstrap Icons (removido), CSS custom fora de tokens.

## 2. Filosofia

- **Minimalista e direto** — só o necessário para o usuário fluir; sem frases decorativas.
- **CTA primário do portal público é a candidatura** — “Candidatar-se” sempre visível e em `primary`.
- **Dois temas com paletas próprias** (não é inversão): light "papel" e dark "floresta à noite".
- **Elevação por contorno**: cartões usam `ring-1 ring-foreground/10` + `hover:shadow-md`, nunca sombras pesadas.

## 3. Paletas (tokens em `resources/css/app.css`)

O toggle adiciona/remove a classe `.dark` no `<html>` (script inline em `partials/theme-script.blade.php`, persistido em `localStorage`).

### Light — "Papel"

| Token                  | Valor     | Uso                                  |
|------------------------|-----------|--------------------------------------|
| `--background`         | `#F6F8F7` | Fundo global (branco esverdeado)     |
| `--foreground`         | `#1A2420` | Texto                                |
| `--card` / `--popover` | `#FFFFFF` | Superfícies                          |
| `--primary`            | `#0D9571` | Verde FAPEU — CTAs, links, foco      |
| `--secondary`          | `#EDF3F0` | Botões secundários, chips            |
| `--muted`              | `#F0F4F2` | Fundos discretos / `#5F6E67` texto   |
| `--accent`             | `#E1F3EC` | Washes de destaque / `#0B6E54` texto |
| `--destructive`        | `#DC2626` | Erros e ações destrutivas            |
| `--border` / `--input` | `#E2E9E5` / `#D8E1DC` | Bordas             |
| `--brand-deep`         | `#074635` | Verde institucional profundo         |
| `--sidebar`            | `#074635` | Sidebar interna (verde FAPEU)        |

### Dark — "Floresta à noite"

| Token                  | Valor     | Observação                            |
|------------------------|-----------|---------------------------------------|
| `--background`         | `#0E1411` | Verde-carvão profundo (não é preto)   |
| `--foreground`         | `#E4EBE7` |                                       |
| `--card`               | `#151D18` | Popover um passo acima: `#1A231E`     |
| `--primary`            | `#34D399` | Esmeralda viva / fg `#05301F`         |
| `--secondary` / `--muted` | `#1E2823` / `#1C2621` | fg `#8CA195`       |
| `--accent`             | `#12352A` | fg `#7CEBC3`                          |
| `--destructive`        | `#F87171` |                                       |
| `--border` / `--input` | `#26312A` / `#2C3831` |                           |
| `--sidebar`            | `#101914` | Levemente destacada do fundo          |

### Raio e fonte

- `--radius: 0.625rem` (10px) — cartões usam `rounded-xl`, inputs `rounded-lg`.
- `--font-sans` e `--font-heading`: DM Sans.

## 4. Logo

Componente único: [Logo.jsx](resources/js/Components/Logo.jsx), arte `public/imagens/fapeulogobranca.png`.

- **Dark / fundos escuros fixos** (`white` prop): imagem branca original.
- **Light**: tingida com `--primary` via **CSS mask** (cor sempre fiel ao token).

### 4.1 Lockup do nome do sistema (padrão FAPEU, reaplicar em todo novo sistema)

Ao lado da logo no header, o nome do sistema segue sempre a mesma estrutura de duas linhas — **nunca** um `<span>` único com o nome inteiro no mesmo peso:

1. **Linha 1 — categoria do sistema**: "Portal de", "Sistema de", "Portal do(a)"… — minúsculo por extenso, mas exibido em `uppercase`, pequeno, peso médio, cor **muted** (nunca a cor de texto principal).
2. **Linha 2 — nome do sistema**: a palavra/expressão que identifica o sistema em si (ex.: "Vagas") — grande, `font-bold`, cor de texto principal (`--foreground`). É o elemento com mais peso visual do lockup.

Markup de referência (Tailwind):

```jsx
<Link href={route('home')} className="flex shrink-0 items-center gap-3">
    <Logo className="h-9" />
    <span className="hidden flex-col leading-none sm:flex">
        <span className="text-[0.65rem] font-medium uppercase tracking-wider text-muted-foreground">
            Portal de
        </span>
        <span className="text-[1.25rem] font-bold tracking-tight text-foreground">
            Vagas
        </span>
    </span>
</Link>
```

Regras fixas do lockup:

- Linha 1: `text-[0.65rem]` (~10.4px), `font-medium`, `uppercase`, `tracking-wider`, `text-muted-foreground`.
- Linha 2: `text-[1.25rem]` (20px), `font-bold`, `tracking-tight`, `text-foreground`. É o único valor que muda de sistema para sistema — troque apenas o texto ("Vagas", "RH", "Financeiro"…), nunca a hierarquia de tamanho/peso/cor.
- Container do texto: `flex flex-col leading-none` (duas linhas coladas, sem espaçamento vertical extra); `hidden sm:flex` para esconder em telas muito estreitas (a logo sozinha já identifica a marca).
- `gap-3` entre a logo e o bloco de texto (não usar `gap-2` ou `gap-2.5` — o texto em duas linhas precisa de mais respiro que um texto de uma linha só).
- Nunca aplicar `font-bold` às duas linhas, nem deixá-las do mesmo tamanho — a hierarquia (categoria pequena/muted → nome grande/bold) é o que torna o lockup reconhecível entre sistemas.

## 5. Cores de status (definidas em `Components/badges.jsx`)

Washes de opacidade sobre a paleta Tailwind — funcionam nos dois temas:

| Domínio      | Status → cor                                                            |
|--------------|-------------------------------------------------------------------------|
| Tipo de vaga | estágio → violet · CLT → blue · bolsa → emerald                         |
| Vaga         | rascunho/inativa → muted · aguardando → amber · ativa → emerald · encerrada → slate · recusada → red |
| Candidatura  | recebida → sky · em análise → amber · entrevista → blue · aprovado → emerald · reprovado → red |

Padrão de classe: `bg-{cor}-500/12 text-{cor}-700 dark:bg-{cor}-400/10 dark:text-{cor}-300`.

**Tipo de vaga é comunicado apenas por cor, nunca por ícone** (regra do dono do produto). No `VagaCard` a cor do tipo aparece em duas camadas: gradiente-wash diagonal (`from-{cor}-500/10 → transparente`) e hover (ring + sombra tingida + título). **Filete/borda lateral colorida é proibido** (regra do dono do produto).

## 6. Componentes do projeto (`resources/js/Components/`)

| Componente               | Uso                                                        |
|--------------------------|------------------------------------------------------------|
| `Field`                  | Label + controle + erro/hint (todo campo de formulário)    |
| `badges.jsx`             | `TipoBadge`, `ModalidadeBadge`, `StatusVagaBadge`, `StatusCandidaturaBadge`, `NovaBadge` |
| `VagaCard`               | Card público de vaga — identidade cromática por tipo (gradiente-wash, sem ícone e sem filete), sombra de flutuação, CTA "Candidatar-se". **Usado só em "Vagas relacionadas"** desde o split view da listagem |
| `VagaListaItem`          | Item da lista compacta da listagem pública — cargo, localização, projeto e prazo; `<button>` que seleciona (nunca navega) |
| `VagaDetalhePainel`      | Detalhe da vaga selecionada — cabeçalho sticky com CTA, resumo rápido e seções; serve a coluna (xl) e o `Sheet` (abaixo de xl) |
| `Pagination`             | Recebe o paginator do Laravel (`links`, `from`, `to`…)     |
| `EmptyState`             | Ícone + título + descrição + ações                         |
| `StatCard`               | Métrica de dashboard (ícone + valor + label, tons por prop)|
| `CurriculoDropzone`      | Upload PDF com drag-and-drop (máx. 5 MB)                   |
| `CandidaturaTimeline`    | Progresso Recebida → Em análise → Entrevista → Resultado   |
| `candidaturas-list.jsx`  | `ContadoresStatus` (chips) + `CandidaturasTabela` (painel) |
| `ThemeToggle`            | Sol/lua, ghost button                                      |
| `FlashMessages`          | Flash da sessão → toasts (sonner)                          |
| `Logo`                   | Ver seção 4                                                |

## 7. Layouts (`resources/js/Layouts/`)

### `PublicLayout`
Navbar sticky theme-aware (`bg-background/85 backdrop-blur`) com: logo, links (Vagas, Alertas, Acompanhar candidatura), ThemeToggle e auth do candidato — visitante vê **Entrar** (ghost) + **Criar conta** (primary); logado vê dropdown com iniciais. Menu mobile em `Sheet`. Footer `bg-card` com 3 colunas.

### `InternalLayout`
Sidebar fixa 256px com tokens `--sidebar*` (verde FAPEU no light, verde-carvão no dark), seções por perfil (Coordenador / Gestor / Sistema), usuário + logout no rodapé. Topbar sticky com título, breadcrumb, `topbarActions` e ThemeToggle. Mobile: sidebar vira `Sheet`.

### `AuthLayout`
Split-screen: painel esquerdo com **foto (`auth-hero.jpg`) + overlay preto 50%** + gradiente `brand-deep` + texto branco (headline por página); formulário à direita (`max-w-sm`, ou `max-w-2xl` com `wide` — usado no wizard).

## 8. Padrões de página

- **Hero público (home)**: foto `home-hero.jpg` + `bg-black/50` + gradiente lateral `brand-deep/80`, título branco, busca em destaque, contagem de vagas.
- **Listagem pública (split view)**: única página em **10-80-10** (`mx-auto w-full px-4 lg:w-4/5 lg:px-0`, sem `max-w-6xl`) — navbar e footer seguem em `max-w-6xl`. Grid `xl:[240px_minmax(340px,420px)_1fr]`: filtros sticky, lista compacta e painel de detalhe. Os itens da lista ficam **colados** (`divide-y`, sem `gap`) e mostram só cargo, localização, projeto e prazo; clicar seleciona (não navega) e o painel à direita traz resumo rápido, descrição, requisitos e o CTA "Candidatar-se". Seleção marcada por `bg-accent` + título em `primary` — nunca por filete lateral (regra 10). Entre `lg` e `xl`, e no mobile, o detalhe vai para um `Sheet` inferior com o mesmo painel.
- **Detalhe de vaga (`/vagas/{id}`)**: grid `[1fr_330px]` — conteúdo em seções (label uppercase muted + texto `whitespace-pre-line`), aside sticky com prazo, remuneração e CTA grande. **Sem ponto de entrada na interface** desde o split view: a rota segue viva para acesso direto (e-mails de alerta, links compartilhados) e é alcançada também pelos links "Ver vaga" da área do candidato e da tela de candidatura.
- **Formulários longos**: seções em cards (`Secao`), grid responsivo `sm:grid-cols-2`, CEP com autofill ViaCEP no blur, máscaras de CPF/telefone/CEP (`lib/cpf.js`).
- **Wizard (registro)**: stepper horizontal (desktop) / barra de progresso (mobile), validação por etapa no cliente, checagem AJAX de CPF, força de senha em 5 regras, salto automático para a etapa com erro do servidor.
- **Painel interno**: chips de contadores por status, tabela em card, ações em `DropdownMenu`, confirmações destrutivas em `AlertDialog`, formulários contextuais em `Dialog` (entrevista, recusa).
- **Datas/moeda**: sempre via `lib/format.js` (pt-BR, UTC-safe). Enums/labels via `lib/enums.js`.

## 9. Regras para novos desenvolvimentos

1. Página nova = componente em `Pages/` + `Inertia::render` no controller com **props curadas** (nunca passar o model inteiro em páginas públicas/candidato — `observacoes_internas` é interno).
2. Todo campo de formulário dentro de `Field` com `error={errors.campo}`.
3. Ícones só de `lucide-react`; em botões, usar `data-icon="inline-start|inline-end"`.
4. Cores: somente tokens (`bg-primary`, `text-muted-foreground`…) ou washes de status da seção 5 — nunca hex solto em página.
5. Confirmação destrutiva = `AlertDialog`; nunca `window.confirm`.
6. Feedback de sucesso/erro = flash da sessão (vira toast automaticamente).
7. Selects sempre shadcn `Select` (item com check à direita; padding padrão já ajustado no ui/select).
8. **Nunca aplicar translate de "levantar" no hover** (`hover:-translate-y-*`) — hover comunica apenas via sombra/ring/cor. (Regra do dono do produto.)
9. **Tipo de vaga (Estágio/CLT/Bolsa) nunca é diferenciado por ícone** — só por cor (badge, wash). (Regra do dono do produto.)
10. **Nunca usar filete/borda lateral colorida em cards** — proibido. Elevação do card público de vaga: sombra suave de flutuação (`shadow-lg shadow-black/[0.07]` + ring), intensificada no hover. (Regra do dono do produto.)
11. **Nome do sistema ao lado da logo sempre segue o lockup de duas linhas da seção 4.1** — categoria pequena/uppercase/muted em cima, nome do sistema grande/bold embaixo. Padrão a replicar em todos os sistemas FAPEU ao criar/editar um header. (Regra do dono do produto.)

---

*Última atualização: julho de 2026 — migração completa Blade/Bootstrap → Inertia/React/shadcn.*
