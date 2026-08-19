## Context

O formulário de criação de conta ([resources/js/Pages/Candidato/Auth/Registro.jsx](resources/js/Pages/Candidato/Auth/Registro.jsx)) hoje usa um `Link` do Inertia para `route('politica.privacidade')`, que renderiza a página completa [resources/js/Pages/Publico/PoliticaPrivacidade.jsx](resources/js/Pages/Publico/PoliticaPrivacidade.jsx) dentro de `PublicLayout`. O projeto já tem um componente `Dialog` (Radix, via [resources/js/components/ui/dialog.jsx](resources/js/components/ui/dialog.jsx)) usado em outras telas (ex.: `Coord/Candidaturas/Show.jsx`, `Candidato/Perfil/Edit.jsx`) com `DialogContent` limitado a `sm:max-w-sm` por padrão.

Ver proposal.md - Why/What Changes para a motivação e o escopo.

## Goals / Non-Goals

**Goals:**
- Abrir o conteúdo da Política de Privacidade em um pop-up a partir do formulário de criação de conta, sem navegação.
- Reutilizar o texto já existente da política sem duplicá-lo entre a página e o pop-up.

**Non-Goals:**
- Alterar o link "Política de Privacidade" do rodapé ([PublicLayout.jsx](resources/js/Layouts/PublicLayout.jsx)) ou de qualquer outro ponto fora do formulário de criação de conta — esses continuam navegando para a página dedicada.
- Alterar o conteúdo textual da política.
- Remover ou aposentar a rota/página `/politica-privacidade`.

## Decisions

**Extrair o conteúdo da política para um componente compartilhado.** O corpo de `PoliticaPrivacidade.jsx` (a partir do bloco de tópicos) passa a viver em um componente de conteúdo puro (ex.: `resources/js/components/PoliticaPrivacidadeConteudo.jsx`), sem depender de `PublicLayout`. A página pública passa a montar esse conteúdo dentro do `PublicLayout` normalmente; o pop-up do cadastro monta o mesmo componente dentro do `DialogContent`. Alternativa descartada: duplicar o texto no pop-up — rejeitada por criar duas fontes de verdade que podem divergir.

**Usar o `Dialog` já existente, com `DialogContent` mais largo.** O padrão `sm:max-w-sm` do `Dialog` é estreito demais para um texto longo com títulos e listas; o `DialogContent` usado no pop-up recebe uma classe de largura maior (ex.: `sm:max-w-2xl`) e altura com rolagem interna (`max-h-[80vh] overflow-y-auto`), seguindo o padrão já usado em outros diálogos de conteúdo extenso do projeto. Alternativa descartada: criar um componente de modal próprio — rejeitada por duplicar o que o `Dialog` shadcn já resolve (foco, Esc, clique fora, acessibilidade).

**Gatilho é um `button`/`DialogTrigger`, não mais um `Link`.** Como o link deixa de navegar, ele passa a ser um elemento interativo que abre o `Dialog` (`DialogTrigger asChild`), mantendo a mesma aparência visual (texto sublinhado, cor primária) para não alterar a percepção do usuário sobre o que é clicável.

## Risks / Trade-offs

- [Extrair o conteúdo para um componente compartilhado toca a página pública existente, ainda que sem mudar seu comportamento] → Mitigação: a extração é mecânica (mover JSX, sem alterar texto ou estrutura visual); a página pública continua renderizando exatamente o mesmo HTML dentro do `PublicLayout`.
- [Pop-up com texto longo pode ficar com rolagem ruim em telas pequenas] → Mitigação: usar `max-h-[80vh] overflow-y-auto` no `DialogContent`, mesmo padrão de rolagem interna já usado em outros diálogos de conteúdo extenso do projeto.
