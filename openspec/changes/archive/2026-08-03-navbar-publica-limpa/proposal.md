## Why

O header público atual mistura a marca ("Portal de Vagas") com atalhos secundários (Vagas, Alertas) que já são redundantes ou pouco relevantes ali — a logo já leva para a listagem de vagas, e Alertas já aparece em destaque na própria listagem, na página de vaga e no rodapé. Além disso, o lockup do nome do sistema usa a classe `text-foreground-vagas`, que não existe no CSS (não há token `foreground-vagas` definido), então a palavra "Vagas" perde a cor de texto principal e o lockup não sai como documentado na seção 4.1 do DESIGN_SYSTEM.md. O resultado é uma barra mais cheia e menos hierárquica do que deveria.

## What Changes

- Remove os botões de atalho "Vagas" e "Alertas" da navbar de desktop — a logo/lockup já navega para a listagem (rota `home` = mesma controller de `vagas.publicas.index`), e Alertas continua acessível pelo banner da listagem, pela página de vaga e pelo rodapé.
- Corrige o lockup "Portal de Vagas": troca `text-foreground-vagas` (classe inexistente) por `text-foreground`, conforme o padrão de duas linhas já documentado (categoria pequena/uppercase/muted em cima, nome grande/bold embaixo).
- Mantém, sem alteração de posição ou comportamento: `ThemeToggle`, os botões "Entrar"/"Criar conta" (visitante deslogado) e o dropdown de conta (candidato logado).
- O menu mobile (Sheet/hambúrguer) não muda — continua com os links completos de navegação (Vagas, Alertas, conta), já que é uma gaveta expandida e não a barra fixa que este change simplifica.
- Sem mudanças de rota, API ou dados. Puramente visual/estrutural no header público.

## Capabilities

### New Capabilities
- `navegacao-publica`: comportamento e composição do header fixo (navbar) exibido em todas as páginas públicas do portal — o que aparece na barra, em que ordem, e o que cada estado (visitante x candidato logado) mostra.

### Modified Capabilities
- (nenhuma — nenhuma spec existente descreve o header hoje)

## Impact

- `resources/js/Layouts/PublicLayout.jsx`: markup e classes do header (bloco `<header>`), usado por todas as páginas públicas via `PublicLayout`.
- `DESIGN_SYSTEM.md` seção 4.1 / regra 11: nenhuma mudança de regra, apenas correção de uma implementação que estava fora do padrão já documentado.
- Nenhum impacto em backend, rotas ou banco de dados.
