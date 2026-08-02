## Context

Ver [proposal.md](proposal.md) — Why. O que molda o desenho:

- A listagem é `Pages/Publico/Vagas/Index.jsx`, renderizada por `VagaPublicaController::index()` com um paginator Laravel (`vagas`, 12 por página) cujos itens passam por `vagaResumo()` — que **não** inclui `projeto_nome` nem `requisitos`. Os campos de detalhe só existem em `vagaCompleta()`, usada apenas em `show()`.
- A página tem hoje hero full-bleed + `mx-auto max-w-6xl px-4` e um grid `lg:grid-cols-[260px_1fr]` (filtros sticky + cards).
- Filtros já aplicam via `router.get(..., { preserveState: true, preserveScroll: true })` — a instância do componente React **sobrevive** à troca de filtro, então qualquer estado de seleção precisa se reconciliar sozinho com o novo resultado.
- Regras de design vinculantes ([DESIGN_SYSTEM.md](DESIGN_SYSTEM.md) §9): sem `hover:-translate-y-*` (8), tipo de vaga só por cor e nunca por ícone (9), **filete/borda lateral colorida proibido** (10), cores só por token (4), ícones só `lucide-react` (3), selects sempre shadcn (7).
- Componentes shadcn já disponíveis em `components/ui/`: `sheet`, `separator`, `button`, `badge`, `select` — não é preciso adicionar dependência nova.
- A rota `vagas.publicas.show` é alvo de links externos já distribuídos (e-mails de alerta) e por isso permanece viva.

## Goals / Non-Goals

**Goals:**

- Trocar navegação por seleção: ler o detalhe sem sair da listagem nem perder filtros e rolagem.
- Zero requisição adicional por clique — o detalhe já vem no payload da página.
- Reconciliação de seleção sem `useEffect`: o estado derivado resolve filtro, busca, ordenação e paginação com a mesma regra.
- Manter `Show.jsx` e `VagaCard.jsx` funcionando como estão, sem edições.

**Non-Goals:**

- Rolagem infinita na lista — a paginação atual permanece.
- Deep-link da vaga selecionada na URL da listagem.
- Redesenho do hero, dos filtros disponíveis ou do fluxo de inscrição.
- Aplicar 10-80-10 fora da listagem.

## Decisions

### Decisão 1 — Container 10-80-10 local à página, sem `max-w`

O container da listagem passa a ser `mx-auto w-full px-4 lg:w-4/5 lg:px-0` e o `max-w-6xl` é removido **apenas** em `Index.jsx` (hero e área de conteúdo, para as duas partes da página alinharem). `PublicLayout` não é tocado — cabeçalho e rodapé continuam em `max-w-6xl`, o que é a decisão do dono do produto e também evita mexer em todas as demais páginas públicas.

Abaixo de `lg` o percentual é abandonado em favor de `w-full px-4`: 80% de uma tela de 390px deixaria 78px de margem inútil.

*Alternativas descartadas:* (a) container 80% em `PublicLayout` — mudaria todas as páginas públicas de uma vez, escopo recusado; (b) `max-w-[1600px]` sobre os 80% — descaracteriza o 10-80-10 pedido em monitores grandes, que é justamente onde a terceira coluna respira.

### Decisão 2 — Grid de três colunas a partir de `xl`

Três larguras: `xl:grid-cols-[240px_minmax(340px,420px)_1fr]` — filtros com largura fixa (240px, um passo mais estreito que os 260px atuais para devolver espaço), lista com faixa controlada (não encolhe abaixo de 340px nem estica além de 420px — item compacto não se beneficia de largura) e detalhe consumindo o resto.

O corte é `xl` (1280px) e não `lg` (1024px): em 1024px as duas primeiras colunas mais os gaps já comem ~620px e sobrariam ~400px para o detalhe, largura em que o painel fica pior que o `Sheet`. Entre `lg` e `xl` a página fica em duas colunas (`lg:grid-cols-[240px_1fr]`, como hoje) e o detalhe vai para o `Sheet` (Decisão 7). Abaixo de `lg`, coluna única com filtros empilhados acima da lista, como hoje.

*Alternativa descartada:* três frações elásticas (`0.8fr 1.2fr 2fr`) — em monitor ultralargo a coluna da lista ficaria com centenas de pixels de espaço morto à direita do texto.

### Decisão 3 — `index()` passa a usar `vagaCompleta()`

O paginator da listagem troca `vagaResumo()` por `vagaCompleta()`. O painel renderiza a vaga selecionada direto do array já em memória — sem requisição por clique, sem estado de carregamento, sem tratamento de erro de rede.

Custo: o payload da página cresce de ~12 resumos para ~12 registros completos (a diferença são os textos de `descricao`, `requisitos`, `requisitos_desejaveis`, `beneficios` e os campos de endereço). Na ordem de dezenas de KB por página, comprimido pelo servidor — aceitável para 12 itens fixos por página.

`vagaCompleta()` já é o conjunto de campos exposto publicamente em `show()`, então nada de novo passa a ser público (`observacoes_internas` continua fora). `show()` e `relacionadas` não mudam.

*Alternativas descartadas:* (a) buscar o detalhe sob demanda com `router.reload({ only: [...] })` — adiciona latência perceptível em cada clique, estado de carregando e um caminho de erro, para economizar dezenas de KB; (b) criar um `vagaListagem()` intermediário — mais uma projeção para manter em sincronia sem ganho real.

### Decisão 4 — Seleção como estado derivado, não sincronizado

```
const [selecionadaId, setSelecionadaId] = useState(null);
const selecionada = vagas.data.find((v) => v.id === selecionadaId) ?? vagas.data[0] ?? null;
```

Uma única expressão cobre todos os casos da spec: seleção inicial (id `null` cai na primeira), seleção que sobrevive ao filtro (o `find` acha), seleção que não sobrevive (cai na primeira do novo resultado) e resultado vazio (`null`, e a página mostra o estado vazio). Como `preserveState: true` mantém a instância do componente viva entre filtros, um `useEffect` de sincronização teria que observar a identidade da página inteira e ainda renderizaria um quadro com a seleção velha — o derivado não tem esse intervalo.

### Decisão 5 — Item de lista: `<button>` em lista com `divide-y`, seleção sem filete

Novo componente `components/VagaListaItem.jsx`: um `<button type="button">` com `w-full text-left`, dentro de um container `rounded-xl ring-1 ring-foreground/10 divide-y overflow-hidden bg-card`. `divide-y` entrega os itens colados separados só por divisória, sem `gap`, e `<button>` entrega foco e acionamento por teclado de graça (requisito de acessibilidade da spec).

Estado selecionado: `bg-accent` + título em `text-primary font-semibold`. **Sem barra/filete lateral colorido** — é o recurso que o LinkedIn usa para marcar o item ativo e é exatamente o que a regra 10 do DESIGN_SYSTEM proíbe. Hover: só `bg-muted/60`, sem translate (regra 8).

Conteúdo, e nada além dele: título (`titulo`), localização (`localVaga(vaga)`), projeto (`projeto_nome`, omitido inteiro quando ausente) e prazo (`diasRestantes` → `text-destructive font-semibold` quando ≤ 5 dias, senão `Inscrições até {formatDate}`, mesmo critério do `VagaCard`).

`VagaCard.jsx` **não é alterado nem removido** — segue em uso na seção "Vagas relacionadas" de `Show.jsx`, e é lá que o link para `vagas.publicas.show` continua fazendo sentido (a página só é alcançável por URL direta de qualquer forma).

### Decisão 6 — Painel: aside sticky com rolagem própria e cabeçalho fixo interno

Novo componente `components/VagaDetalhePainel.jsx`, usado nas duas apresentações (coluna à direita em `xl`, conteúdo do `Sheet` abaixo disso) para não haver duas versões do detalhe divergindo.

Como coluna: `xl:sticky xl:top-20` com `max-h-[calc(100dvh-6rem)] overflow-y-auto` — o painel rola por conta própria e a lista não é arrastada junto (requisito da spec). Dentro dele, um cabeçalho `sticky top-0` com fundo opaco reúne título, identificação e o botão **Candidatar-se** (`route('inscricao.create', vaga.id)`), o que resolve "CTA alcançável em detalhe longo" sem barra flutuante em desktop.

Ordem do conteúdo: cabeçalho (badges de tipo/modalidade + título + área/local/projeto + CTA) → **resumo rápido** em grade de fatos (remuneração via `faixaSalarial`, carga horária, modalidade, prazo) → Sobre a vaga (`descricao`) → Requisitos → Diferenciais → Benefícios → Cursos desejados (chips) → Local de trabalho (`endereco_completo`, omitido quando `modalidade === 'remoto'`). Toda seção de campo vazio é omitida com título e tudo.

O "breve resumo da vaga" pedido é essa grade de fatos — não existe campo de resumo separado no banco, e inventar um truncamento de `descricao` logo acima da própria `descricao` seria repetição.

### Decisão 7 — Telas estreitas: `Sheet` inferior reaproveitando o mesmo painel

Abaixo de `xl`, selecionar abre um `Sheet side="bottom"` (shadcn, já no projeto) em ~92dvh com o mesmo `VagaDetalhePainel` dentro. O `Sheet` só é montado abaixo de `xl`; acima disso o clique apenas troca a seleção da coluna. O `Sheet` dá fechamento por gesto, `Esc`, botão e clique fora — os quatro caminhos de "retorno à lista" — e preserva a rolagem da lista por baixo sem código extra.

*Alternativa descartada:* navegar para `Show.jsx` no mobile — reintroduziria na interface exatamente o ponto de entrada que esta change desliga, e criaria duas experiências diferentes de detalhe para manter.

### Decisão 8 — Sem deep-link da seleção

A vaga selecionada não vai para a URL da listagem. Compartilhamento continua tendo endereço próprio: `/vagas/{id}` segue vivo. Adicionar `?vaga=` implicaria sincronizar histórico do navegador, botão voltar e estado derivado — custo desproporcional para um caminho que já tem solução.

## Risks / Trade-offs

- **80% sem teto em monitor ultralargo (3440px+) deixa linhas de texto muito longas no painel** → a coluna do painel recebe `max-w-4xl` no conteúdo textual interno; o container da página continua fiel ao 10-80-10 pedido.
- **Payload da listagem cresce (Decisão 3)** → limitado a 12 itens por página, campos idênticos aos já expostos em `show()`; se virar problema, a troca para carregamento sob demanda é local ao `index()` e ao painel.
- **Entre `lg` e `xl` o candidato não vê o split view** e cai no mesmo caminho do mobile (Decisão 2) → é a faixa onde o painel ficaria estreito demais para valer; se na verificação visual em 1280px o detalhe ainda parecer apertado, o corte sobe, não desce.
- **`Show.jsx` fica sem cobertura de uso real** (viva por URL direta, invisível na navegação) → regressões nela passam despercebidas; a change `redesign-detalhe-vaga` fica parada e sinalizada no proposal para essa decisão ser retomada conscientemente.
- **Perda de contexto ao trocar de página da paginação** (a seleção pula para a primeira vaga da nova página) → comportamento intencional e coberto por cenário na spec; alternativa seria manter o detalhe de uma vaga fora da lista visível, o que é pior.
- **`divide-y` + `bg-accent` no item selecionado pode achatar a divisória adjacente** → verificar visualmente nos dois temas com item selecionado no meio, no topo e no fim da lista.

## Migration Plan

Mudança de apresentação com uma alteração de projeção no controller; sem migration de banco, sem mudança de rota, sem alteração de contrato externo. Deploy é o build normal do Vite. Rollback = reverter o commit: `Show.jsx`, `VagaCard.jsx` e a rota `vagas.publicas.show` permanecem intactos durante todo o processo, então a listagem antiga volta a funcionar sem nenhum passo adicional.
