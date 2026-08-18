## Context

`Button` (`resources/js/Components/ui/button.jsx`) é um único elemento `inline-flex items-center justify-center gap-*`; ícone (`data-icon="inline-start|inline-end"`) e texto são filhos diretos e irmãos desse flex container — não há wrapper separado para o texto. Hoje o desbalanceamento é atenuado só por uma pequena redução de padding no lado do ícone (`has-data-[icon=inline-end]:pr-2`, `has-data-[icon=inline-start]:pl-2`), insuficiente frente à largura real de ícone+gap (~20-24px), então o texto renderiza deslocado do centro. Ver proposal.md - Why.

## Goals / Non-Goals

**Goals:**
- O rótulo de texto de um botão ícone+texto fica opticamente centralizado no botão, para qualquer tamanho (`xs`/`sm`/`default`/`lg`), qualquer posição de ícone (`inline-start`/`inline-end`) e independente da largura real do ícone (inclusive quando o ícone é trocado em runtime, como `Save` → `Loader2` durante envio).
- Zero mudança nas ~25 telas que já consomem `<Button data-icon=...>` — o ajuste fica isolado no componente compartilhado.
- Nenhuma regressão para botões só-texto ou só-ícone (`size="icon*"`).

**Non-Goals:**
- Suporte a RTL (o portal é pt-BR, LTR apenas).
- Redesenho de tokens visuais do botão (cor, radius, tamanhos) — só o alinhamento interno.
- Tratar o caso de ícone nos dois lados (`inline-start` **e** `inline-end` simultâneos) de forma diferente — esse caso já é simétrico por natureza e fica fora de escopo.

## Decisions

**1. Técnica de centralização: spacer-espelho invisível (clone do ícone), não posicionamento absoluto nem padding fixo por tamanho.**
`Button()` usa `React.Children` para detectar, entre os filhos, um único elemento com prop `data-icon`. Quando encontrado (e há outros filhos além dele, ou seja, existe texto a centralizar), renderiza um clone invisível (`aria-hidden`, `pointer-events-none`, mesma largura) desse ícone do lado oposto do texto. Como o clone reflete o ícone realmente renderizado, ele acompanha automaticamente qualquer largura (`size-3`/`size-3.5`/`size-4` por variante) e qualquer troca de ícone em runtime — sem precisar de tabela de valores por tamanho.
- *Alternativa: aumentar/recalibrar a compensação de padding por tamanho.* Rejeitada — exige conhecer a largura exata do ícone por variante e quebra assim que o ícone muda (ex.: `Loader2` durante `processing`) ou é sobrescrito com `className="size-*"`.
- *Alternativa: `position: absolute` no ícone dentro de um gutter reservado.* Rejeitada — exige centralização vertical manual (`top-1/2 -translate-y-1/2`), interage mal com o efeito de clique (`active:not-aria-[haspopup]:translate-y-px`) e com o ring de foco, e ainda precisaria da mesma reserva simétrica de espaço.
- *Alternativa: tabela de padding por `data-icon`+`size` via `:has()`.* Rejeitada — hardcoded e frágil a qualquer ícone fora do padrão.

**2. Superfície da mudança: só `button.jsx`.**
Toda a lógica fica no componente compartilhado; nenhum call-site precisa passar props novas. O contrato existente (`data-icon="inline-start|inline-end"` no elemento do ícone) é mantido.

**3. Padding volta a ser simétrico.**
As classes `has-data-[icon=inline-end]:pr-2` / `has-data-[icon=inline-start]:pl-2` são removidas de `buttonVariants`. Com o spacer-espelho fazendo o balanceamento, padding desigual entre os lados voltaria a descentralizar o texto (a centralização do flex ocorre dentro da content-box, que já exclui o padding).

**4. Escopo da espelhagem.**
Só se aplica quando há exatamente um filho com `data-icon` mais outro conteúdo (texto). Botões `size="icon*"` (sem texto) e botões com ícone nos dois lados ficam inalterados.

**5. `asChild` (Slot) exige olhar um nível abaixo.**
Quando `asChild={true}`, o filho direto de `Button` é o único elemento a ser fundido pelo `Slot.Root` (ex.: `<a>`/`<Link>`), e é *dentro dele* que o ícone e o texto aparecem como filhos (padrão usado em ~15 telas, ex. `Coord/Dashboard.jsx`, `VagaCard.jsx`, `Candidato/Perfil/Edit.jsx` "Exportar/Baixar"). A detecção original (só nos filhos diretos de `Button`) não alcançava esse caso e deixava esses botões sem correção — o próprio bug que esta change resolve. `Button` agora detecta `asChild` com exatamente um filho elemento e aplica a mesma lógica de spacer-espelho um nível abaixo, clonando esse filho com o novo conteúdo. Continua isolado em `button.jsx`.

## Risks / Trade-offs

- [Risco] Clonar o elemento do ícone para o spacer duplica qualquer efeito colateral que ele tivesse → [Mitigação] ícones passados via `data-icon` são sempre SVGs presentacionais do `lucide-react`, sem handlers (convenção já existente, DESIGN_SYSTEM.md regra "Ícones só de lucide-react"); o clone também recebe `aria-hidden` e `pointer-events-none` por garantia.
- [Risco] Percorrer `React.Children` a cada render adiciona custo → [Mitigação] são no máximo 2-3 filhos por botão, sem medição de layout nem `ResizeObserver`; custo desprezível.
- [Risco] Rótulos longos poderiam quebrar linha e deslocar o spacer para outra linha → [Mitigação] `buttonVariants` já inclui `whitespace-nowrap` na base, então botões nunca quebram linha hoje.

## Migration Plan

Mudança de um arquivo só (`button.jsx`), já compartilhado por todas as telas. Sem migração de dados. Verificação: conferir visualmente uma amostra representativa das ~25 telas listadas no Impact da proposal (botão "Salvar alterações" em `Candidato/Perfil/Edit`, formulário de vaga em `Coord/Vagas/Form`, diálogos com "Cancelar"/"Remover", banners de alerta) nos temas claro e escuro. Rollback é reverter o arquivo único, sem efeito em nenhum outro lugar.
