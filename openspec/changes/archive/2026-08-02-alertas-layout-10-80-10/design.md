## Context

Ver [proposal.md](proposal.md) — Why.

Estado atual de [Alertas.jsx](resources/js/Pages/Publico/Alertas.jsx): a página é um `div` `max-w-2xl` com o cabeçalho (h1 + descrição) acima e um único `<form>` em `flex flex-col gap-6` dentro de um card (`rounded-xl bg-card p-6 ring-1 ring-foreground/10`). Os grupos de seleção são renderizados por `GrupoCheckbox`, um componente local que já aceita a prop `colunas` para controlar quantas opções cabem por linha.

Restrições que moldam a solução:

- A largura 10-80-10 já existe no portal, mas como constante local de [Index.jsx:22](resources/js/Pages/Publico/Vagas/Index.jsx#L22) (`CONTAINER = 'mx-auto w-full px-4 lg:w-4/5 lg:px-0'`). Esta mudança cria o segundo consumidor.
- O cabeçalho do portal é sticky com `h-16`; a convenção do repositório para colunas que acompanham a rolagem é `xl:sticky xl:top-20`.
- A ordem do DOM define a ordem de tabulação. A ordem visual empilhada exigida pela spec (e-mail → preferências → LGPD → envio) precisa ser a própria ordem do DOM; resolver por `order-*` do CSS deixaria o teclado saltando do e-mail direto para o botão de envio, pulando as preferências.

## Goals / Non-Goals

**Goals:**

- Uma única fonte para a largura 10-80-10, consumida pela listagem e pela página de alertas.
- Ordem do DOM igual à ordem de leitura e de tabulação em telas estreitas, sem `order-*` para compensar.
- Reaproveitar `GrupoCheckbox` como está, mudando apenas as props de layout.
- Nenhuma alteração no `useForm`, no payload ou no tratamento de erros.

**Non-Goals:**

- Promover `GrupoCheckbox` a componente compartilhado em `resources/js/components/` — ele só tem um consumidor.
- Introduzir `Secao` ou qualquer outro wrapper de formulário longo aqui: a página tem um envio só e não é um formulário longo no sentido do DESIGN_SYSTEM.
- Repensar o conteúdo dos grupos (quais áreas, tipos e modalidades existem) — vêm do controller e ficam como estão.

## Decisions

### 1. Extrair a largura 10-80-10 para `lib/`

Criar `CONTAINER_LARGO` (ou nome equivalente) em [resources/js/lib/](resources/js/lib/) e passar `Index.jsx` e `Alertas.jsx` a consumi-lo.

**Por quê:** com dois consumidores, a string duplicada vira duas verdades — e a próxima página que adotar a largura copiaria de qualquer um dos dois. `lib/` já abriga constantes e helpers de apresentação (`enums.js`, `format.js`).

**Alternativa considerada:** duplicar a string literal em `Alertas.jsx`. Descartada — é exatamente o tipo de duplicação que faz a listagem e os alertas divergirem numa refatoração futura, quebrando o cenário "Mesma largura da listagem" da spec.

### 2. Grid de duas colunas com posicionamento explícito, a partir de `xl`

O `<form>` vira o grid:

```
grid gap-x-10 gap-y-8 xl:grid-cols-[320px_minmax(0,1fr)] xl:grid-rows-[auto_1fr]
```

com três blocos irmãos, nesta ordem no DOM:

| Bloco | Conteúdo | Colocação em `xl` |
|---|---|---|
| **A** | h1, descrição, campo de e-mail | `xl:col-start-1 xl:row-start-1` |
| **B** | áreas de interesse, tipos, modalidades | `xl:col-start-2 xl:row-start-1 xl:row-span-2` |
| **C** | consentimento LGPD + botão "Ativar alertas" | `xl:col-start-1 xl:row-start-2` |

**Por quê o posicionamento explícito:** é o que permite A e C ficarem empilhados na coluna esquerda em `xl` sem ficarem adjacentes no DOM. A ordem do DOM (A, B, C) já é a ordem empilhada exigida abaixo de `xl`, então o layout estreito não precisa de nenhuma regra — é o fluxo natural do grid de coluna única.

**Por quê `xl` e não `lg`:** em `lg` (1024px) o container 10-80-10 dá ~820px; descontando a coluna de 320px e o gap, sobrariam ~450px para as preferências — menos do que as duas colunas de áreas que a página já tem hoje em `sm`. A listagem quebra as três colunas no mesmo ponto, então os dois layouts públicos largos passam a mudar de forma juntos.

**Alternativas consideradas:**

- **Uma `<div>` por coluna, com A e C juntos na coluna esquerda.** Mais simples de ler, mas em telas estreitas o botão de envio apareceria antes dos grupos de preferência. Descartada por contrariar o requisito de empilhamento.
- **`order-*` do CSS para reordenar em telas estreitas.** Corrigiria o visual mas não a ordem de tabulação: o teclado iria do e-mail ao envio pulando as preferências.

### 3. Sticky no bloco C, via wrapper interno

`xl:grid-rows-[auto_1fr]` faz a linha 2 esticar até a altura de B (que atravessa as duas linhas). O bloco C ocupa toda essa área, e o sticky vai num `div` **dentro** de C: `xl:sticky xl:top-20`.

**Por quê o wrapper:** um item de grid esticado já preenche sua própria área — `position: sticky` nele não teria por onde deslizar. O elemento sticky precisa ser menor que o bloco que o contém.

**Por quê C e não a coluna inteira:** o requisito de spec é que consentimento e envio permaneçam visíveis durante a rolagem das preferências; o h1 e o e-mail podem sair de vista. Prender a coluna inteira exigiria juntar A e C, que é justamente o que a decisão 2 evita.

**Alternativa considerada:** botão fixo no rodapé da janela em telas largas. Descartada — não é um padrão em uso no portal e competiria com o rodapé do `PublicLayout`.

### 4. Preferências: áreas em 3 colunas, tipos e modalidades lado a lado

Dentro de B:

- **Áreas de interesse**: `colunas="sm:grid-cols-2 xl:grid-cols-3"` — 13 áreas em 5 linhas.
- **Tipos** e **Modalidades**: irmãos dentro de um `grid gap-8 sm:grid-cols-2`, cada um mantendo `sm:grid-cols-3` para suas 3 opções.

**Por quê:** os três grupos têm volumes muito diferentes (13 / 3 / 3). Empilhar os dois pequenos deixaria a coluna direita com duas linhas quase vazias; lado a lado eles fecham o bloco na mesma altura das áreas.

### 5. Card único, com o divisor na coluna de preferências

Mantém-se um único card cobrindo os 80%. A separação visual entre as duas áreas vem de `xl:border-l xl:pl-10` **no bloco B**.

**Por quê em B:** B é um elemento só e atravessa as duas linhas, então produz um filete contínuo. A borda equivalente em A e C seriam dois segmentos com o `gap-y` entre eles, deixando uma falha no meio da linha.

Este filete separa duas áreas de conteúdo e não marca seleção — a regra 10 do DESIGN_SYSTEM (seleção nunca por filete lateral) continua respeitada.

### 6. Registrar o padrão no DESIGN_SYSTEM

A linha "Listagem pública (split view)" afirma que a listagem é a "única página em **10-80-10**". Passa a citar as duas páginas, e a página de alertas ganha entrada própria em "Padrões de página" descrevendo o grid aside + preferências.

## Risks / Trade-offs

- **O posicionamento explícito em grid é frágil a edições futuras** (inserir um quarto bloco sem ajustar `row-start` quebra o layout em `xl`) → comentar no JSX que a ordem do DOM serve ao empilhamento e que as classes `col-start`/`row-start` são o que reconstitui as duas colunas.
- **`xl:grid-rows-[auto_1fr]` só estica a linha 2 enquanto B for o bloco mais alto.** Se as preferências encolherem (poucas áreas cadastradas), a linha 2 fica curta e o sticky perde curso — mas nesse caso a página também não rola, então o requisito continua satisfeito na prática.
- **O h1 passa a ficar dentro do card**, na coluna esquerda, em vez de acima dele → nenhuma mudança de nível de heading nem de conteúdo; o documento continua com um único h1.
- **Entre `lg` e `xl` a página fica em coluna única na largura 10-80-10** (~820px), mais larga que os `max-w-2xl` de hoje, com os grupos de checkbox esticados → aceitável; é a mesma faixa em que a listagem também abandona as três colunas.
- **Alterar `Index.jsx` para consumir a constante extraída toca uma página fora do escopo funcional desta mudança** → a alteração é uma substituição literal por literal idêntico, coberta pelos cenários de largura já existentes na spec da listagem.

## Migration Plan

Mudança puramente de apresentação, sem dados, rotas ou contratos envolvidos. Deploy pelo pipeline normal (build de assets); rollback é reverter o commit.
