## Context

O header público vive inteiro em `resources/js/Layouts/PublicLayout.jsx`, usado por todas as páginas via Inertia. Ver proposal.md - Why para a motivação (atalhos redundantes + classe de cor quebrada no lockup). O menu mobile (`MenuMobile`, componente `Sheet`) é renderizado à parte e não muda neste change.

## Goals / Non-Goals

**Goals:**
- Remover os botões "Vagas" e "Alertas" do `<nav>` de desktop dentro do `<header>`.
- Corrigir `text-foreground-vagas` → `text-foreground` na segunda linha do lockup.
- Preservar 100% do comportamento de `ThemeToggle`, `Entrar`/`Criar conta` e do dropdown de conta (mesma posição relativa, mesmas rotas).

**Non-Goals:**
- Não altera o menu mobile (`MenuMobile`) nem o rodapé — ambos já cobrem Vagas/Alertas.
- Não altera rotas, controllers ou dados (`auth.candidato`).
- Não introduz um novo componente de header; a mudança é local ao JSX existente.

## Decisions

- **Remover o `<nav>` de atalhos em vez de escondê-lo condicionalmente**: os links "Vagas" e "Alertas" (linhas 149-156 do `PublicLayout.jsx` atual) somem tanto para visitante quanto para candidato logado. Alternativa considerada — manter só para um dos dois estados — foi descartada porque o pedido do usuário é por uma barra "mais limpa" em geral, e o próprio lockup já linka para a listagem.
- **Corrigir a classe de cor no lugar, sem introduzir um novo token**: `text-foreground` já existe e é o valor documentado na seção 4.1 do DESIGN_SYSTEM.md; não há necessidade de um token "foreground-vagas" dedicado.
- **Não tornar o lockup visível abaixo do breakpoint `sm`**: mantém `hidden sm:flex` como está — em telas muito estreitas a logo sozinha já identifica a marca (regra já documentada), e o pedido do usuário foi por estrutura, não por visibilidade em todas as larguras.
- **Navbar em 10-80-10 (`CONTAINER_LARGO`) em vez de `max-w-6xl`** (pedido direto do usuário, 2026-08-03): a listagem de vagas e a página de alertas já usam `CONTAINER_LARGO` (`lib/layout.js`); a navbar em `max-w-6xl` ficava desalinhada com o conteúdo dessas duas páginas em telas largas. Trade-off aceito: o rodapé e as páginas mais estreitas (detalhe de vaga, candidatura, política, FazendaRessacada) continuam em `max-w-6xl` e agora desalinham com a navbar nessas telas — não foi pedido migrá-las também.
- **Lockup "Portal de Vagas" em uma linha só em vez de duas** (pedido direto do usuário, 2026-08-03): substitui o padrão de duas linhas empilhadas (seção 4.1/regra 11 do DESIGN_SYSTEM.md, vigente até então) por categoria + nome lado a lado (`flex items-baseline gap-1.5`). Mantém a distinção categoria muted/nome bold, só muda de empilhado para inline. DESIGN_SYSTEM.md e a memória de preferências de UI foram atualizados para não recriar o formato antigo em telas futuras.
- **Controles do lado direito maiores** (pedido direto do usuário, 2026-08-03): `ThemeToggle` ganhou um `size` prop (default `icon`, aqui usado como `icon-lg`) e um `iconClassName` prop para o tamanho do ícone, em vez de forçar o tamanho via `className` — evita depender da ordem de merge de classes Tailwind e não afeta os outros usos de `ThemeToggle` (`InternalLayout`, `AuthLayout`), que continuam no tamanho pequeno original. Dropdown de conta e botões "Entrar"/"Criar conta" foram ajustados para a mesma altura (~40px/`h-10`) para ficarem visualmente alinhados com o `ThemeToggle` maior.

## Risks / Trade-offs

- [Usuários de desktop perdem um atalho de 1 clique para "Alertas"] → Mitigação: Alertas já tem CTA de destaque na listagem de vagas e na página de vaga, então o caminho continua a no máximo 2 cliques a partir da home.
- [Divergência visual entre desktop (sem atalhos) e mobile (com atalhos no drawer)] → Aceitável: é o padrão comum de navbar responsiva (links secundários migram para o menu em telas largas quando a barra é simplificada); não há inconsistência de rota ou destino, só de onde o link mora.
- [Navbar em 10-80-10 desalinha com páginas ainda em `max-w-6xl`] → Aceito pelo usuário; migrar essas páginas para `CONTAINER_LARGO` fica para um change futuro, se pedido.
