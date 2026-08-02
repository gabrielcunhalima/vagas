# vagas-listagem-publica Specification

## Purpose
Define como o candidato encontra, compara e escolhe uma vaga na listagem pública do portal: a organização espacial da página, o que cada item da lista mostra, como a vaga selecionada é apresentada em detalhe na mesma tela e como daí se chega à candidatura.
## Requirements
### Requirement: Largura de conteúdo 10-80-10 na listagem

A listagem pública de vagas SHALL ocupar 80% da largura da janela, com 10% de margem livre de cada lado, em telas largas. Essa largura SHALL valer apenas para a listagem — as demais páginas públicas e os elementos comuns de navegação (cabeçalho e rodapé) mantêm a largura máxima já em uso no portal.

#### Scenario: Tela larga

- **WHEN** o candidato abre a listagem pública em uma janela larga
- **THEN** o conteúdo da listagem ocupa 80% da largura da janela, centralizado, com 10% de espaço livre à esquerda e 10% à direita

#### Scenario: Cabeçalho e rodapé inalterados

- **WHEN** o candidato abre a listagem pública em uma janela larga
- **THEN** o cabeçalho e o rodapé do portal permanecem na mesma largura máxima que apresentam nas demais páginas públicas, sem acompanhar a largura da listagem

#### Scenario: Outras páginas públicas inalteradas

- **WHEN** o candidato navega para qualquer outra página pública do portal
- **THEN** a largura de conteúdo dessa página permanece exatamente como antes desta mudança

### Requirement: Listagem em três colunas

Em telas largas, a listagem SHALL apresentar simultaneamente três áreas na mesma tela, na ordem: filtros, lista de vagas e detalhe da vaga selecionada. As três áreas SHALL ser visíveis ao mesmo tempo, sem exigir navegação entre páginas.

#### Scenario: Três áreas simultâneas

- **WHEN** o candidato abre a listagem em tela larga e há ao menos uma vaga no resultado
- **THEN** os filtros, a lista de vagas e o detalhe de uma vaga aparecem juntos na mesma tela

#### Scenario: Filtros continuam funcionando

- **WHEN** o candidato aplica, altera ou limpa um filtro
- **THEN** a lista é atualizada com o novo resultado e a página permanece na listagem, sem perder a posição de rolagem

### Requirement: Item de lista compacto e contínuo

Cada vaga na lista SHALL ser exibida como um item de uma lista contínua — itens encostados uns nos outros, sem espaçamento vertical entre eles — e SHALL exibir exclusivamente: título do cargo, localização, projeto e tempo restante para o encerramento das inscrições. Nenhuma outra informação da vaga SHALL aparecer no item de lista.

#### Scenario: Conteúdo do item

- **WHEN** a lista exibe uma vaga cujos dados estão todos preenchidos
- **THEN** o item mostra título do cargo, localização, nome do projeto e tempo restante para expirar, e não mostra descrição, remuneração, carga horária, modalidade nem área

#### Scenario: Itens colados

- **WHEN** a lista exibe duas ou mais vagas
- **THEN** os itens aparecem imediatamente adjacentes, separados apenas por uma divisória, sem espaço vazio entre eles

#### Scenario: Vaga sem projeto associado

- **WHEN** a lista exibe uma vaga que não tem projeto informado
- **THEN** o item omite o projeto por completo, sem rótulo órfão nem espaço reservado

#### Scenario: Prazo próximo do fim

- **WHEN** a lista exibe uma vaga cujo encerramento está próximo
- **THEN** o tempo restante recebe tratamento visual de urgência, com o mesmo critério de urgência já usado no portal

### Requirement: Seleção de vaga na lista

Clicar em um item da lista SHALL selecionar aquela vaga e apresentar seu detalhe na área de detalhe, sem recarregar a página nem alterar o resultado da lista. Exatamente uma vaga SHALL estar selecionada enquanto houver resultados, e o item selecionado SHALL ser visualmente distinguível dos demais.

#### Scenario: Clique seleciona

- **WHEN** o candidato clica em um item da lista
- **THEN** o detalhe daquela vaga passa a ser exibido na área de detalhe e a lista permanece inalterada, com os mesmos itens e a mesma posição de rolagem

#### Scenario: Item selecionado é distinguível

- **WHEN** uma vaga está selecionada
- **THEN** seu item na lista tem tratamento visual distinto dos itens não selecionados

#### Scenario: Seleção inicial

- **WHEN** a listagem é aberta e há ao menos uma vaga no resultado
- **THEN** a primeira vaga da página vem selecionada e seu detalhe já aparece, sem exigir clique

#### Scenario: Seleção sobrevive à mudança de resultado

- **WHEN** o candidato aplica um filtro, faz uma busca ou altera a ordenação e a vaga selecionada continua presente no novo resultado
- **THEN** ela permanece selecionada e o detalhe exibido continua sendo o dela

#### Scenario: Seleção perdida na mudança de resultado

- **WHEN** o candidato aplica um filtro, faz uma busca, altera a ordenação ou troca de página e a vaga selecionada não está no novo resultado, que tem ao menos uma vaga
- **THEN** a primeira vaga do novo resultado passa a ser a selecionada e o detalhe é atualizado para ela

#### Scenario: Seleção acessível por teclado

- **WHEN** o candidato navega pela lista usando o teclado
- **THEN** cada item é alcançável e acionável pelo teclado, com indicação visível de foco

### Requirement: Painel de detalhe da vaga selecionada

A área de detalhe SHALL apresentar as informações da vaga selecionada que não cabem no item de lista. Ela SHALL conter, no mínimo: um resumo rápido da vaga, a descrição completa, a remuneração e os requisitos. Informações complementares disponíveis na vaga (carga horária, modalidade, tipo, benefícios, requisitos desejáveis, cursos desejados, local de trabalho, prazo de inscrição) SHALL ser apresentadas quando existirem e omitidas por completo quando ausentes, sem deixar rótulos órfãos ou blocos vazios.

#### Scenario: Informações mínimas presentes

- **WHEN** uma vaga está selecionada
- **THEN** a área de detalhe exibe o título do cargo, um resumo rápido da vaga, a descrição completa, a remuneração e os requisitos

#### Scenario: Campos opcionais ausentes

- **WHEN** a vaga selecionada não possui benefícios, requisitos desejáveis, cursos desejados nem carga horária informados
- **THEN** a área de detalhe não exibe rótulo, título de seção nem espaço reservado para nenhum desses campos

#### Scenario: Remuneração não informada

- **WHEN** a vaga selecionada não tem remuneração informada
- **THEN** a área de detalhe apresenta o tratamento de remuneração indefinida já usado no portal, em vez de um valor vazio

#### Scenario: Detalhe acompanha a rolagem da lista

- **WHEN** o candidato rola a lista de vagas em tela larga
- **THEN** o detalhe da vaga selecionada permanece visível

#### Scenario: Detalhe mais longo que a tela

- **WHEN** o detalhe da vaga selecionada é mais alto que a área disponível
- **THEN** o detalhe pode ser rolado por conta própria, sem que a lista de vagas seja arrastada junto

### Requirement: Candidatura a partir do painel de detalhe

A área de detalhe SHALL oferecer um botão "Candidatar-se" que leva o candidato ao cadastro de currículo da vaga selecionada. Esse botão SHALL estar sempre alcançável enquanto uma vaga estiver selecionada, mesmo que o detalhe seja longo.

#### Scenario: Botão leva ao cadastro

- **WHEN** o candidato clica em "Candidatar-se" com uma vaga selecionada
- **THEN** ele é levado à tela de cadastro de currículo daquela vaga

#### Scenario: Botão alcançável em detalhe longo

- **WHEN** a vaga selecionada tem um detalhe mais longo que a área visível
- **THEN** o botão "Candidatar-se" continua alcançável sem que o candidato precise rolar até o fim do texto

#### Scenario: Item de lista não candidata direto

- **WHEN** o candidato clica em qualquer parte de um item da lista
- **THEN** ele apenas seleciona a vaga; a candidatura só é iniciada pelo botão da área de detalhe

### Requirement: Comportamento em telas estreitas

Em telas estreitas, onde as três colunas não cabem lado a lado, a listagem SHALL priorizar a lista de vagas e apresentar o detalhe da vaga selecionada sobreposto à lista, com um retorno explícito para a lista.

#### Scenario: Lista ocupa a tela

- **WHEN** o candidato abre a listagem em tela estreita
- **THEN** a lista de vagas ocupa a área de conteúdo e o detalhe não é exibido junto dela

#### Scenario: Seleção abre o detalhe

- **WHEN** o candidato toca em um item da lista em tela estreita
- **THEN** o detalhe daquela vaga é apresentado sobreposto, com o botão "Candidatar-se" acessível

#### Scenario: Retorno à lista

- **WHEN** o candidato aciona o retorno a partir do detalhe sobreposto em tela estreita
- **THEN** o detalhe é fechado, a lista reaparece com os mesmos resultados e a posição de rolagem anterior

#### Scenario: Filtros em tela estreita

- **WHEN** o candidato abre a listagem em tela estreita
- **THEN** os filtros continuam acessíveis, sem exigir rolagem por toda a lista de vagas para alcançá-los

### Requirement: Estado de lista vazia

Quando nenhum resultado atender aos filtros, a listagem SHALL apresentar o estado vazio no lugar da lista e SHALL não apresentar área de detalhe.

#### Scenario: Nenhum resultado

- **WHEN** os filtros aplicados não retornam nenhuma vaga
- **THEN** a listagem exibe a mensagem de nenhuma vaga encontrada com as ações de limpar filtros e criar alerta, e nenhum detalhe de vaga é exibido

#### Scenario: Filtros permanecem visíveis

- **WHEN** os filtros aplicados não retornam nenhuma vaga
- **THEN** a área de filtros continua visível com os valores aplicados, permitindo ajustá-los sem recarregar a página

### Requirement: Página de detalhe sem ponto de entrada na interface

A página de detalhe de vaga em endereço próprio SHALL continuar respondendo a acessos diretos por URL, para não quebrar links já distribuídos em e-mails de alerta e compartilhamentos. Nenhum elemento da listagem pública SHALL navegar para ela.

#### Scenario: Acesso direto continua funcionando

- **WHEN** alguém acessa diretamente a URL de detalhe de uma vaga aberta
- **THEN** a página de detalhe é exibida normalmente, como antes desta mudança

#### Scenario: Listagem não leva ao detalhe em página própria

- **WHEN** o candidato clica em qualquer item ou elemento da listagem pública
- **THEN** ele não é levado para a página de detalhe em endereço próprio

#### Scenario: Vaga encerrada por URL direta

- **WHEN** alguém acessa diretamente a URL de detalhe de uma vaga que não está mais aberta
- **THEN** o comportamento é o mesmo de antes desta mudança

