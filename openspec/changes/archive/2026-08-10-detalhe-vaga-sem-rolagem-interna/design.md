## Context

O painel de detalhe (`resources/js/Components/VagaDetalhePainel.jsx`) é usado em dois lugares de `resources/js/Pages/Publico/Vagas/Index.jsx`:

- Coluna fixa (`xl:` para cima): `className="hidden max-h-[calc(100dvh-6rem)] min-w-0 overflow-y-auto rounded-xl ring-1 ring-foreground/10 xl:sticky xl:top-20 xl:block"` — é essa a área com a barra de rolagem vertical do print do usuário.
- Sheet (abaixo de `xl:`): `<div className="h-full overflow-y-auto">` dentro de um `SheetContent` de altura fixa (`h-[92dvh]`) — aqui a rolagem própria é necessária porque o Sheet tem altura fixa por natureza; não é o alvo da mudança.

Dentro do próprio `VagaDetalhePainel`, o cabeçalho (badges, título, botão "Candidatar-se") usa `sticky top-0` — hoje isso funciona porque o `overflow-y-auto` do container pai faz esse container ser o "scroll container" mais próximo, então o cabeçalho gruda no topo do painel enquanto o conteúdo rola por dentro dele. Ver proposal.md - Why.

## Goals / Non-Goals

**Goals:**
- Eliminar a rolagem interna do painel de detalhe na coluna fixa, para que todo o conteúdo da vaga fique sempre visível sem barra própria.
- Manter o botão "Candidatar-se" alcançável sem rolar até o fim do texto, enquanto o painel estiver em vista.

**Non-Goals:**
- Mudar o comportamento do Sheet em telas estreitas (continua com rolagem própria de altura fixa).
- Mudar o conteúdo, ordem ou condições de exibição das seções da vaga.

## Decisions

**Remover `max-h-[calc(100dvh-6rem)]` e `overflow-y-auto` da coluna fixa, mantendo `xl:sticky xl:top-20`.**
Sem altura máxima nem `overflow-y-auto`, o painel cresce com o conteúdo da vaga. O `sticky` continua acompanhando a rolagem da página enquanto o painel cabe na janela; quando o conteúdo é mais alto que a janela, o navegador simplesmente para de "grudar" o painel no topo assim que sua borda inferior alcança o fim da área visível, e ele passa a rolar junto com o restante da página — sem precisar de nenhuma lógica adicional.
Alternativa considerada: manter `overflow-y-auto` mas aumentar a altura máxima (ex. `100dvh` sem o `-6rem`). Rejeitada porque ainda existiria um limite arbitrário e, em vagas muito longas, a barra de rolagem interna voltaria a aparecer — o pedido do usuário é que ela nunca apareça.

**Manter o cabeçalho `sticky top-0` dentro de `VagaDetalhePainel`.**
Ao remover o `overflow-y-auto` do pai, o "scroll container" mais próximo do cabeçalho passa a ser a página inteira (o mesmo scroller do `xl:sticky` externo). Nesse cenário, o cabeçalho continua grudado no topo do painel enquanto ele está em vista — na prática, o efeito visual de "botão sempre alcançável enquanto o painel está na tela" se mantém, só que agora resolvido por CSS puro (sticky aninhado) em vez de por rolagem interna. Não há necessidade de remover ou reescrever essa parte do componente.
Alternativa considerada: remover o `sticky` do cabeçalho já que a rolagem interna que o motivava deixa de existir. Rejeitada porque isso tornaria o botão "Candidatar-se" inalcançável sem rolar até o fim em vagas longas, contrariando o requisito de acessibilidade do botão.

**Atualizar o comentário de topo de `VagaDetalhePainel.jsx`.**
O comentário atual descreve o cabeçalho `sticky` como dependente da "própria rolagem do painel", o que deixa de ser verdade. Ajustar o texto para refletir que o cabeçalho agora gruda em relação à rolagem da página enquanto o painel (que cresce com o conteúdo) está em vista.

## Risks / Trade-offs

- **[Risco]** Em vagas com descrição muito longa, o painel de detalhe pode ficar visualmente muito mais alto que a lista de vagas ao lado, gerando uma coluna assimétrica. → **Mitigação**: aceito conscientemente — é a troca pedida pelo usuário (mostrar tudo, sem cortar) em vez de rolagem interna; a lista continua rolando de forma independente.
- **[Risco]** Uma vez que o painel role para fora da tela junto com a página (conteúdo muito mais longo que a janela), o botão "Candidatar-se" deixa de estar visível até o candidato rolar de volta. → **Mitigação**: é o comportamento padrão esperado de conteúdo que rola com a página; documentado como mudança **BREAKING** no proposal.md.
