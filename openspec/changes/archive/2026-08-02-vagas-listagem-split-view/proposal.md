## Why

Hoje a home é uma lista de cards grandes onde cada vaga custa uma navegação inteira: o candidato clica, sai da listagem, lê o detalhe em `/vagas/{id}`, volta com o botão do navegador e perde a posição da rolagem e o contexto dos filtros. Comparar três vagas parecidas significa três idas e voltas.

O padrão consolidado para busca de vagas (LinkedIn, Gupy, Indeed) resolve isso com *split view*: uma lista compacta à esquerda e o detalhe da vaga selecionada à direita, na mesma tela. O candidato varre a lista, clica, lê e decide sem nunca perder os filtros aplicados. É o formato que o dono do produto pediu, com referência visual explícita ao LinkedIn.

## What Changes

- **Listagem pública vira split view de três colunas**: `filtros | lista compacta | painel de detalhe`. A coluna de detalhe é nova; filtros e lista passam a dividir o espaço restante à esquerda dela.
- **Largura da página passa a 10-80-10** na home/listagem: 10% de margem de cada lado, 80% de conteúdo — substituindo o container `max-w-6xl` **apenas nesta página**. Navbar, footer e demais páginas públicas seguem em `max-w-6xl` (decisão do dono do produto).
- **Itens de lista colados, não cards soltos**: a lista deixa de usar `VagaCard` (card flutuante com `gap-4` entre eles) e passa a ser uma lista contínua, com itens encostados um no outro separados apenas por divisória, exibindo somente **cargo, localização, projeto e tempo para expirar**. Remuneração, carga horária, modalidade, área e descrição saem do item de lista.
- **Item selecionado tem estado visual próprio** e a primeira vaga da página vem selecionada por padrão, para o painel nunca aparecer vazio.
- **Painel de detalhe à direita** apresenta o que ficou de fora da lista: resumo/descrição da vaga, remuneração, requisitos e demais informações da vaga selecionada, com o botão **"Candidatar-se"** levando para `inscricao.create` (cadastro do currículo).
- **Página de detalhe (`Show.jsx`) fica inacessível pela interface, mas preservada**: o arquivo e a rota `vagas.publicas.show` continuam existindo e respondendo por URL direta (e-mails de alerta e links compartilhados seguem funcionando); nenhum elemento da listagem navega mais para lá. Não é remoção — é desligamento dos pontos de entrada na UI.
- **Back-end passa a enviar os campos de detalhe na listagem**: a listagem precisa de `projeto_nome`, `requisitos` e demais campos hoje exclusivos de `vagaCompleta()` para o painel renderizar sem uma segunda requisição por clique.
- **Comportamento em telas estreitas**: split view de duas colunas não cabe no mobile; a lista ocupa a tela e o detalhe aparece sobreposto ao ser selecionado, com retorno explícito para a lista.

Não é escopo: alterar o fluxo de inscrição em si, o hero da home, a navbar/footer, o conjunto de filtros disponíveis, ou qualquer campo novo no banco.

**Conflito sinalizado**: a change pendente `redesign-detalhe-vaga` reescreve exatamente a `Show.jsx` que aqui deixa de ser alcançável pela interface. Por decisão do dono do produto ela fica **parada como está** — não é implementada nem arquivada por esta change. Se for retomada depois, precisará ser reavaliada contra o painel lateral.

## Capabilities

### New Capabilities

- `vagas-listagem-publica`: a listagem pública de vagas — organização espacial da página (largura, colunas), quais informações aparecem em cada item da lista, como o candidato seleciona uma vaga e o que o painel de detalhe apresenta, como se chega à candidatura, e como tudo isso se comporta em telas estreitas e nos estados de lista vazia / filtro aplicado / paginação.

### Modified Capabilities

Nenhuma — `openspec/specs/` está vazio; a única outra capability declarada no repositório (`vaga-detalhe-publica`) existe apenas como delta da change pendente `redesign-detalhe-vaga`, que não é tocada aqui.

## Impact

- **Front-end alterado**: [Index.jsx](resources/js/Pages/Publico/Vagas/Index.jsx) — reescrita da composição da página (container 10-80-10, terceira coluna, estado de seleção).
- **Front-end criado**: componentes de apoio em [resources/js/components/](resources/js/components/) para o item de lista compacto e para o painel de detalhe.
- **Front-end preservado**: [Show.jsx](resources/js/Pages/Publico/Vagas/Show.jsx) e [VagaCard.jsx](resources/js/components/VagaCard.jsx) continuam no repositório — o `VagaCard` segue em uso na seção "Vagas relacionadas" da `Show.jsx`, apenas deixa de aparecer na listagem.
- **Back-end alterado**: [VagaPublicaController::index()](app/Http/Controllers/Vagas/VagaPublicaController.php) — os itens paginados passam a carregar os campos de detalhe; `show()` e a rota permanecem intactos.
- **Rotas**: nenhuma rota criada ou removida; `vagas.publicas.show` mantida e não referenciada pela UI.
- **Documentação**: seções 6 e 8 do [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md) descrevem "Listagem pública: grid `[260px_1fr]`… cards à direita" e o `VagaCard` como card público padrão — ambas precisam refletir o novo padrão de listagem.
- **Restrições de design herdadas**: regras 8, 9 e 10 do DESIGN_SYSTEM (sem `hover:-translate-y-*`, tipo de vaga só por cor, sem filete lateral colorido), tokens de cor apenas, ícones só de `lucide-react`, selects sempre shadcn.
