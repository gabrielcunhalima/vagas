## Why

Botões que combinam ícone (`data-icon="inline-start"` ou `inline-end"`) com texto centralizam o bloco ícone+texto como um todo, e não o texto em si. O rótulo visível fica deslocado do centro geométrico do botão — visível em CTAs de destaque como "Salvar alterações" (`Candidato/Perfil/Edit.jsx`) — o que lê como um defeito de acabamento em um componente usado em dezenas de telas.

## What Changes

- O componente `Button` (`resources/js/Components/ui/button.jsx`) passa a centralizar o **texto** do botão no centro do botão quando há ícone, e não o grupo ícone+texto.
- Aplica-se a todas as combinações já suportadas: ícone à esquerda (`inline-start`), ícone à direita (`inline-end`), e todos os tamanhos (`xs`, `sm`, `default`, `lg`).
- Botões sem ícone (só texto) continuam centralizados como já estão hoje — sem mudança de comportamento para eles.
- Sem mudança de API do componente: nenhuma prop nova, nenhum `data-icon` novo a ser passado pelas ~25 telas que já usam o padrão.

## Capabilities

### New Capabilities
- `botao-icone-texto`: define que, quando um botão combina ícone e rótulo de texto, o rótulo de texto SHALL aparecer centralizado no botão — não o bloco ícone+texto — para todos os tamanhos e posições de ícone suportados.

### Modified Capabilities
(nenhuma — não há spec existente cobrindo alinhamento de ícone+texto em botão)

## Impact

- `resources/js/Components/ui/button.jsx`: ajuste de layout interno (classes utilitárias) para centralizar o texto em vez do grupo ícone+texto.
- Nenhuma mudança de código nas ~25 telas que consomem `<Button>` com `data-icon` — o ajuste é isolado no componente compartilhado.
- Puramente visual/CSS: não afeta rotas, dados, permissões ou lógica de submit dos formulários que usam esses botões.
