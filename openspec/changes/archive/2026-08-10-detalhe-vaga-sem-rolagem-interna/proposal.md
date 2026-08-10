## Why

No painel de detalhe da vaga (coluna fixa em telas largas), o conteúdo é limitado a uma altura máxima com rolagem própria (`max-h-[calc(100dvh-6rem)] overflow-y-auto`). Isso faz aparecer uma barra de rolagem vertical dentro do painel e esconde parte do conteúdo da vaga (requisitos, diferenciais, benefícios, cursos, local de trabalho) atrás dessa rolagem interna. O usuário quer que o painel sempre mostre o conteúdo completo da vaga, sem barra de rolagem própria.

## What Changes

- Remove a altura máxima e a rolagem interna (`max-h-[calc(100dvh-6rem)] overflow-y-auto`) do painel de detalhe na coluna fixa (`xl:`), para que ele cresça naturalmente com o conteúdo da vaga e nunca corte informação atrás de uma barra de rolagem própria.
- O painel continua `sticky` em relação à rolagem da página: acompanha a lista enquanto cabe na tela e, quando o conteúdo da vaga é mais alto que a janela, passa a rolar junto com a página (sem barra própria) — deixando de ter rolagem independente da lista.
- **BREAKING**: em vagas com detalhe mais longo que a tela, o botão "Candidatar-se" deixa de ficar sempre alcançável sem rolagem — ele acompanha o cabeçalho `sticky` do painel enquanto o painel está em vista, mas some quando o painel rola para fora da tela junto com a página.
- O painel exibido no Sheet (telas estreitas) não é afetado: continua com sua própria rolagem, pois ocupa uma área de altura fixa (bottom sheet).

## Capabilities

### Modified Capabilities
- `vagas-listagem-publica`: os requisitos "Painel de detalhe da vaga selecionada" e "Candidatura a partir do painel de detalhe" mudam — o painel deixa de ter rolagem interna própria em telas largas e passa a exibir sempre o conteúdo completo, acompanhando a rolagem da página.

## Impact

- `resources/js/Pages/Publico/Vagas/Index.jsx`: classe do painel de detalhe na coluna fixa (`xl:`).
- `resources/js/Components/VagaDetalhePainel.jsx`: comentário/comportamento do cabeçalho `sticky`, hoje descrito como dependente da rolagem interna do painel.
- `openspec/specs/vagas-listagem-publica/spec.md`: cenários "Detalhe mais longo que a tela" e "Botão alcançável em detalhe longo".
