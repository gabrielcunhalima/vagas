## Context

A hero da listagem vive inteira em [Index.jsx:106-168](resources/js/Pages/Publico/Vagas/Index.jsx#L106-L168): uma `<section>` isolada com a foto em `absolute inset-0`, dois overlays (`bg-black/50` e `bg-gradient-to-r from-brand-deep/80 to-transparent`) e um único filho de conteúdo — `<div className="max-w-2xl">` com título, contagem, `<form>` de busca e o card de alerta empilhados.

Duas restrições moldam a solução:

- **O gradiente é lateral.** `from-brand-deep/80 to-transparent` escurece a esquerda e deixa a direita apenas com o `bg-black/50`. O card vai justamente para a metade menos escurecida, então a legibilidade dele depende da sua própria superfície (`bg-white/10` + `ring-white/25` + `backdrop-blur-sm`), não do gradiente.
- **O card já é responsivo.** Hoje ele é `flex-col sm:flex-row` — vertical no mobile, horizontal a partir de `sm`. Como coluna lateral ele precisa voltar a ser vertical em `lg+`, o que significa um terceiro estado, não uma troca de estado.

Ver [proposal.md](openspec/changes/hero-card-alerta-lateral/proposal.md) para a motivação e [specs/vagas-listagem-publica/spec.md](openspec/changes/hero-card-alerta-lateral/specs/vagas-listagem-publica/spec.md) para os requisitos.

## Goals / Non-Goals

**Goals:**

- Hero em duas colunas a partir de `lg`, com o card na direita e a busca intacta à esquerda.
- Menos altura vertical sem apertar o conteúdo nem quebrar o ritmo do título → busca.
- Um só componente de card servindo os três estados (vertical estreito, horizontal médio, vertical lateral), sem duplicar markup.

**Non-Goals:**

- Extrair o card para um componente próprio em `resources/js/components/` — ele é usado uma vez só; extrair agora só adiciona indireção.
- Mexer na foto, nos overlays ou no `CONTAINER` (10-80-10) da hero.
- Alterar a hero de outras páginas públicas ou o split-screen de auth.

## Decisions

### 1. Grid de duas colunas com coluna direita de largura fixa

`grid lg:grid-cols-[minmax(0,1fr)_360px] lg:items-center lg:gap-12` no filho do `CONTAINER`, com o `max-w-2xl` atual saindo do wrapper e passando a viver na coluna esquerda.

- **Por que largura fixa na direita e não `1fr_1fr`:** o container é 80% da janela — em 1920px isso dá 1536px, e metade disso deixaria o card com ~750px de largura para três linhas de texto. Uma coluna de ~360px mantém o card com proporção de cartão e devolve o espaço extra ao título/busca, que escalam melhor.
- **Por que `items-center` e não `items-end`:** alinhado ao centro, o card fica na altura da busca — a associação visual ("busque agora ou seja avisado depois") é imediata. Alinhado embaixo, ele descolaria do formulário quando o título quebrasse em duas linhas.
- **Alternativa descartada:** posicionar o card com `absolute right-0` sobre a foto. Sairia do fluxo, exigiria altura mínima manual na hero e brigaria com o `CONTAINER` percentual em cada breakpoint.

### 2. Breakpoint `lg`, alinhado ao resto da página

A coluna dupla entra em `lg`, o mesmo ponto em que o `CONTAINER` vira `w-4/5` e em que a listagem abaixo passa a três colunas. Abaixo de `lg` a hero volta a ser uma pilha: título, busca, card — exatamente o comportamento atual.

- **Por que não `md`:** em ~768px, uma coluna de 360px deixaria menos de 400px para o campo de busca, que é a ação primária da página.

### 3. Card com três estados via cascata de utilitários

`flex-col sm:flex-row sm:items-center lg:flex-col lg:items-start`, com o botão em `w-full sm:w-auto lg:w-full` e o `max-w-xl` removido (a largura passa a ser a da coluna do grid).

- **Por que reaproveitar as classes em vez de renderizar dois cards:** duas árvores de markup significam dois lugares para o texto divergir; a spec exige texto idêntico nas duas posições. A cascata `lg:` reverte o `sm:` no ponto certo e mantém uma única fonte.
- **Ícone e tipografia:** inalterados — `Bell` em `size-5` dentro do círculo `size-10`, título `text-base font-semibold`, descrição `text-sm text-white/80`.

### 4. Altura: `py-16 lg:py-24` → `py-10 lg:py-14`

- **Por que esses valores:** o card empilhado somava `mt-6` + ~104px de altura à pilha. Tirando-o do fluxo em `lg+`, a coluna esquerda passa a ter três blocos (título, contagem, busca). `py-14` (56px) mantém respiro sobre a foto sem que a hero domine a dobra; abaixo de `lg` o card continua empilhado, por isso `py-10` em vez de algo mais agressivo.
- **Piso de altura:** a coluna direita (~176px com o card vertical) passa a ser o elemento mais alto em telas largas e define a altura da hero na prática. Vale conferir se a hero não fica *mais alta* que antes em `lg` estreito (~1024px) — se ficar, o ajuste é reduzir o `gap` da coluna, não o padding.

### 5. Sem hover-lift no card

O card mantém `transition-colors hover:bg-white/15` — só cor, sem transform. Regra 8 do [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md).

## Risks / Trade-offs

- **Contraste do card sobre a área clara da foto** → a região direita tem só `bg-black/50` sob o card; o card já traz `bg-white/10` + `ring-white/25` + `backdrop-blur-sm`, que é a mesma superfície que hoje o sustenta. Verificar na foto real (`home-hero.jpg`) em `lg` e `2xl`; se o texto branco perder legibilidade, a correção é subir o `bg-white/10` para `/15`, não mexer no overlay da seção.
- **Hero mais alta em `lg` estreito** → ~~medir antes de fechar~~ **não se confirmou**: 568px → 316px em 1024px e 568px → 341px em 1440px. O `lg:gap-12` ficou como estava.
- **Título com quebra ruim na coluna estreita** → **confirmou-se**: em 1024px, `lg:text-5xl` quebrava "Encontre sua próxima oportunidade" em três linhas. Mitigação aplicada — o passo para `text-5xl` foi movido de `lg` para `xl`, então entre 1024px e 1279px o título fica em `text-4xl` e volta a duas linhas.
- **Card menos proeminente que hoje** → empilhado logo abaixo da busca, ele está no caminho natural do olhar; lateral, é mais fácil de ignorar. Trade-off aceito em troca da hero mais baixa; o estado vazio da listagem continua oferecendo "criar alerta" como segunda porta.

## Migration Plan

Mudança puramente visual em um arquivo, sem estado persistido nem contrato de API. Rollback é reverter o commit.
