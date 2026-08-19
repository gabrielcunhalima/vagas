## Context

Ver [proposal.md](proposal.md) — Why, para a motivação.

Estado atual relevante:

- O portal é Laravel + Inertia + React, estilizado com **Tailwind v4** ([resources/css/app.css](resources/css/app.css) já usa a sintaxe CSS-first: `@import 'tailwindcss'` + blocos `@theme`).
- A escala tipográfica em uso é a padrão do Tailwind, definida em `node_modules/tailwindcss/theme.css` como as variáveis `--text-xs` … `--text-9xl`, cada uma acompanhada de um `--text-*--line-height` **sem unidade** (`calc(1.25 / 0.875)` e afins).
- A maior parte do texto da SPA vem dos 13 degraus nomeados. Distribuição: `text-sm` 171, `text-xs` 77, `text-2xl` 20, `text-base` 14, `text-lg` 5, `text-xl` 4, `text-3xl` 4, `text-5xl` 3, `text-4xl` 3, `text-6xl` 1.
- **Correção a uma afirmação anterior deste documento:** existem 15 tamanhos arbitrários (`text-[0.65rem]` e afins) em 10 arquivos. A varredura original os deixou passar por um erro de expressão regular, e a conclusão "nenhum tamanho escapa do tema" era falsa. A lista completa está na Decisão 7. Nenhum arquivo define tamanho por `style={{ fontSize }}` — isso permanece verdadeiro.
- Não existe CSS solto em `public/`; todo estilo passa pelo Vite.
- Os e-mails transacionais em [resources/views/emails/vagas/](resources/views/emails/vagas/) são a única exceção: são HTML com CSS inline, fora do alcance do Tailwind. São 14 declarações de `font-size` em 5 arquivos.

A consequência prática: existe um ponto único de alteração que cobre 100% da interface, e um segundo ponto pequeno e enumerável para os e-mails.

## Goals / Non-Goals

**Goals:**

- Uma única edição declarativa em `app.css` reajusta toda a interface — nenhuma edição em componente para atingir o efeito principal.
- O acréscimo é de 2px absolutos por degrau, preservando os intervalos entre degraus.
- A escala continua em `rem`, preservando a acessibilidade (respeito ao tamanho de fonte do navegador).
- Correções de layout só onde o texto maior de fato quebrar algo concreto.

**Non-Goals:**

- Não é um redesenho da hierarquia tipográfica: nenhum elemento troca de degrau (`text-sm` não vira `text-base` em lugar nenhum).
- Não se altera escala de espaçamento, raio de borda, ou a altura nominal de componentes shadcn — exceto onde uma quebra visual concreta exigir.
- Não se muda a família tipográfica (DM Sans permanece), peso, ou `letter-spacing`.
- Não se introduz alternância de tamanho pelo usuário (controle A- / A+). Se isso for desejado depois, a base criada aqui é o pré-requisito, mas não é escopo.

## Decisions

### 1. Override das variáveis `--text-*` do tema, em vez de escalar a raiz

**Escolhido:** redefinir os 13 degraus num bloco `@theme` próprio em `app.css`.

**Alternativa rejeitada — `html { font-size: 18px }`:** no Tailwind v4 a escala de espaçamento, os raios e as dimensões (`h-9`, `p-4`, `gap-2`) também são derivados de `rem`. Mudar a raiz ampliaria tudo em 12,5% — viraria um zoom da interface inteira, não "fontes 2px maiores". Também não é um acréscimo de 2px: seria +1,5px no `text-xs` e +6px no `text-5xl`.

**Alternativa rejeitada — buscar e substituir as classes nos 72 arquivos** (`text-sm` → `text-base` etc.): quebra a hierarquia, colide nos degraus (dois degraus diferentes viram o mesmo), é irreversível sem outro varrimento, e deixa a próxima mudança tipográfica igualmente cara.

### 2. Acréscimo absoluto de 0.125rem por degrau

`0.125rem` = 2px na raiz padrão de 16px. Aplicado igualmente a todos os degraus, o que preserva as diferenças entre eles. Tabela completa:

| Degrau | Antes | Depois | px (raiz 16) |
|---|---|---|---|
| `text-xs` | `0.75rem` | `0.875rem` | 12 → 14 |
| `text-sm` | `0.875rem` | `1rem` | 14 → 16 |
| `text-base` | `1rem` | `1.125rem` | 16 → 18 |
| `text-lg` | `1.125rem` | `1.25rem` | 18 → 20 |
| `text-xl` | `1.25rem` | `1.375rem` | 20 → 22 |
| `text-2xl` | `1.5rem` | `1.625rem` | 24 → 26 |
| `text-3xl` | `1.875rem` | `2rem` | 30 → 32 |
| `text-4xl` | `2.25rem` | `2.375rem` | 36 → 38 |
| `text-5xl` | `3rem` | `3.125rem` | 48 → 50 |
| `text-6xl` | `3.75rem` | `3.875rem` | 60 → 62 |
| `text-7xl` | `4.5rem` | `4.625rem` | 72 → 74 |
| `text-8xl` | `6rem` | `6.125rem` | 96 → 98 |
| `text-9xl` | `8rem` | `8.125rem` | 128 → 130 |

Os degraus `7xl`–`9xl` não são usados hoje, mas entram na tabela para que a escala permaneça internamente consistente caso venham a ser usados.

Efeito colateral aceito: `text-xs` (14px) passa a medir o que `text-sm` media, e `text-sm` (16px) o que `text-base` media. Como toda a escala desloca junto, a hierarquia relativa entre elementos da tela não muda — é exatamente o efeito pedido.

### 3. Não redefinir os `--text-*--line-height`

As alturas de linha padrão do Tailwind são **razões sem unidade** (`calc(1.25 / 0.875)` ≈ 1.43). Sobrescrever apenas o tamanho faz a altura de linha crescer na mesma proporção automaticamente — `text-sm` sai de 20px para ~22,9px de entrelinhamento. Isso satisfaz o requisito de entrelinhamento proporcional sem 13 variáveis extras para manter em sincronia.

Sobrescrever o tamanho não apaga o `--text-*--line-height` correspondente: são variáveis independentes no mesmo namespace, e redefinir uma não zera a outra.

### 4. Bloco `@theme` separado, não `@theme inline`

O `@theme inline` existente em [app.css](resources/css/app.css#L96) serve às cores, que precisam ser inlinadas porque referenciam variáveis que trocam entre `:root` e `.dark`. Os tamanhos de fonte são literais estáticos e não dependem de tema. Usar um `@theme` comum mantém `--text-sm` e companhia emitidos como custom properties no `:root`, disponíveis para CSS manual, o que `inline` suprimiria.

O bloco novo fica **depois** do `@theme inline`, em seção própria comentada, para que a intenção seja óbvia em leituras futuras.

### 5. E-mails: acréscimo manual, unidade preservada

As 14 declarações são editadas uma a uma, mantendo a unidade original de cada uma (`px` continua `px`, `rem` continua `rem`). Converter `rem` → `px` "para segurança em cliente de e-mail" seria uma mudança de comportamento não pedida, num código que já vem funcionando com `rem`.

Valores em `rem` que não são múltiplos limpos (`0.82rem`, `0.78rem`, `0.9rem`, `0.95rem`, `1.15rem`) recebem `+0.125rem` e ficam com decimais irregulares (`0.945rem`, `0.905rem`, …). É feio mas correto, e preserva a hierarquia que o autor original escolheu. Arredondar seria alterar a hierarquia por conta própria.

### 6. Alturas de controle sobem por cálculo, não por impressão

A premissa original — "corrigir só o que a passada visual mostrar quebrado" — não se sustentou: a caixa de conteúdo de vários controles é calculável e prova o corte antes de qualquer screenshot.

| Controle | Altura | Menos preenchimento e bordas | Linha do texto | Cabe? |
|---|---|---|---|---|
| `badge` `h-5` | 20px | 20 − 4 (`py-0.5`) − 2 = **14px** | `text-xs` 14px × 1.333 = **18.7px** | não |
| `select` `h-8` | 32px | 32 − 16 (`py-2`) − 2 = **14px** | `text-sm` 16px × 1.429 = **22.9px** | não |
| `input` `h-8` | 32px | 32 − 8 (`py-1`) − 2 = **22px** | `text-base` 18px × 1.5 = **27px** (mobile) | não |
| `button` `h-8` | 32px | 32 − 2 = **30px** | `text-sm` = 22.9px | sim, com folga curta |
| `tabs` `h-8` | 32px | ~27px | `text-sm` = 22.9px | sim |
| `table th` `h-10` | 40px | 40px | `text-sm` = 22.9px | sim, folgado |
| `textarea` `min-h-16` | auto (`field-sizing-content`) | — | — | sim |

Botão, campo, seletor e grupo de entrada compartilham `h-8` **de propósito**, para alinharem lado a lado. Corrigir só os que provam corte (badge e seletor) quebraria esse alinhamento. Por isso a família inteira de 32px sobe para 36px (`h-9`), incluindo os que já cabiam.

Subir só o degrau `default` faria o `lg` (`h-9`, 36px) empatar com ele. Então a escada inteira de cada controle sobe 4px: `xs` 24→28, `sm` 28→32, `default` 32→36, `lg` 36→40, e os equivalentes quadrados de botão de ícone. O badge sobe 6px (20→26), o mínimo que comporta 18.7px de linha mais o preenchimento existente.

Superfícies que ainda exigem passada visual, porque dependem de conteúdo real e não de aritmética:

- `h-*` fixo em botões, inputs, selects e triggers de aba em [resources/js/Components/ui/](resources/js/Components/ui/)
- badges e chips de status, onde `text-xs` a 14px dentro de padding apertado é o caso mais provável de estouro
- a listagem pública de três colunas ([Publico/Vagas/Index.jsx](resources/js/Pages/Publico/Vagas/Index.jsx)) — coluna de lista estreita, alto risco de truncamento novo
- tabelas de candidaturas ([Coord/Candidaturas/](resources/js/Pages/Coord/Candidaturas/), [Coord/Candidaturas/Show.jsx](resources/js/Pages/Coord/Candidaturas/Show.jsx) concentra 7 usos de `text-xs`)
- sidebar do layout interno ([Layouts/InternalLayout.jsx](resources/js/Layouts/InternalLayout.jsx)) — largura fixa com rótulos de menu
- títulos display responsivos ([Vagas/Index.jsx:120](resources/js/Pages/Publico/Vagas/Index.jsx#L120) e [FazendaRessacada.jsx:149](resources/js/Pages/Publico/FazendaRessacada.jsx#L149) usam `text-3xl`/`4xl`/`5xl` encadeados por breakpoint) — verificar quebra de linha em mobile

### 7. Tamanhos arbitrários recebem o mesmo acréscimo absoluto

Os 15 valores fora da escala recebem `+0.125rem` cada, preservando exatamente a relação que tinham com os degraus vizinhos. Não são convertidos para degraus nomeados: `0.65rem` (10,4px) não corresponde a nenhum degrau, e forçá-lo para `text-xs` seria redesenhar a hierarquia por conta própria.

| Arquivo | De | Para |
|---|---|---|
| [candidaturas-list.jsx:74](resources/js/Components/candidaturas-list.jsx#L74) | `0.6rem` | `0.725rem` |
| [PublicLayout.jsx:113](resources/js/Layouts/PublicLayout.jsx#L113) | `0.6rem` | `0.725rem` |
| [CandidaturaTimeline.jsx:45,65](resources/js/Components/CandidaturaTimeline.jsx#L45) | `0.65rem` | `0.775rem` |
| [InternalLayout.jsx:51](resources/js/Layouts/InternalLayout.jsx#L51) | `0.65rem` | `0.775rem` |
| [PublicLayout.jsx:89](resources/js/Layouts/PublicLayout.jsx#L89) | `0.65rem` | `0.775rem` |
| [Registro.jsx:85](resources/js/Pages/Candidato/Auth/Registro.jsx#L85) | `0.68rem` | `0.805rem` |
| [PasswordStrengthMeter.jsx:43](resources/js/Components/PasswordStrengthMeter.jsx#L43) | `0.7rem` | `0.825rem` |
| [FazendaRessacada.jsx:166,236,343](resources/js/Pages/Publico/FazendaRessacada.jsx#L166) | `0.7rem` | `0.825rem` |
| [ui/button.jsx:27](resources/js/Components/ui/button.jsx#L27) | `0.8rem` | `0.925rem` |
| [Vagas/Show.jsx:25,96](resources/js/Pages/Publico/Vagas/Show.jsx#L25) | `1.125rem` | `1.25rem` |
| [PublicLayout.jsx:90](resources/js/Layouts/PublicLayout.jsx#L90) | `1.25rem` | `1.375rem` |

Três desses valores — `0.65rem` e `1.25rem` nos dois layouts — formam o **lockup de identidade do cabeçalho**, padrão declarado no [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md) para replicação nos demais sistemas FAPEU. Alterá-los foi decisão explícita do usuário, ciente de que o portal passa a divergir dos outros sistemas até a replicação. O `DESIGN_SYSTEM.md` é atualizado no mesmo commit para que o padrão documentado e o código não se contradigam.

## Risks / Trade-offs

- **Texto cortado em controle de altura fixa** → a passada visual cobre explicitamente botões, campos, abas e badges; onde houver corte, aumenta-se a altura/padding do componente, nunca se reduz a fonte.
- **Truncamento novo em coluna estreita** (listagem de três colunas, células de tabela) → inspecionar essas telas com dados reais, não com placeholder curto; corrigir com largura de coluna ou quebra de linha.
- **Densidade de informação cai** — cabe menos conteúdo por tela, mais rolagem nas listas → é a contrapartida inerente ao que foi pedido; não se mitiga reduzindo fonte. Se incomodar em alguma tela específica, a decisão é do usuário e vira outra mudança.
- **Nenhum teste automatizado detecta regressão tipográfica** → a verificação é necessariamente visual e manual; por isso a passada por telas nomeadas está listada como tarefa explícita, não como "conferir se está ok".
- **`text-xs` a 14px pode parecer grande demais para badges e legendas** → risco real, mas é o pedido literal e uniforme; alterar só esse degrau quebraria a regra de acréscimo constante e a hierarquia. Se depois se quiser recuar apenas o `text-xs`, é uma linha na tabela.
- **E-mails: edição manual em 5 arquivos pode deixar declaração para trás** → ao final, uma varredura por `font-size` nos templates confirma que nenhum valor antigo sobrou.

## Migration Plan

Não há migração de dados nem mudança de contrato. O deploy é o build de assets normal.

- **Aplicar:** editar `app.css` e os templates de e-mail → `npm run build` → deploy pelo pipeline existente.
- **Reverter:** remover o bloco `@theme` de tipografia e reverter os templates. Como a mudança principal está concentrada num bloco contíguo, o rollback é um `git revert` limpo. Ajustes de layout feitos junto vêm no mesmo commit e revertem juntos.
- **Faseamento:** desnecessário. É uma mudança visual atômica; entregar metade das telas com escala nova seria pior que entregar tudo.
