## MODIFIED Requirements

### Requirement: Painel de detalhe da vaga selecionada

A área de detalhe SHALL apresentar as informações da vaga selecionada que não cabem no item de lista. Ela SHALL conter, no mínimo: um resumo rápido da vaga, a descrição completa, a remuneração e os requisitos. Informações complementares disponíveis na vaga (carga horária, modalidade, tipo, benefícios, requisitos desejáveis, cursos desejados, local de trabalho, prazo de inscrição) SHALL ser apresentadas quando existirem e omitidas por completo quando ausentes, sem deixar rótulos órfãos ou blocos vazios. Em tela larga, a área de detalhe SHALL sempre exibir seu conteúdo por completo, sem barra de rolagem própria que esconda parte da vaga.

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

- **WHEN** o candidato rola a lista de vagas em tela larga e o detalhe da vaga selecionada cabe inteiro na área visível
- **THEN** o detalhe permanece visível, acompanhando a rolagem

#### Scenario: Detalhe mais longo que a tela

- **WHEN** o detalhe da vaga selecionada é mais alto que a área visível
- **THEN** todo o conteúdo do detalhe é exibido sem barra de rolagem própria, e o candidato rola a página para ver o restante do conteúdo

### Requirement: Candidatura a partir do painel de detalhe

A área de detalhe SHALL oferecer um botão "Candidatar-se" que leva o candidato ao cadastro de currículo da vaga selecionada. Enquanto o painel de detalhe estiver visível na tela, esse botão SHALL permanecer alcançável sem exigir rolagem até o fim do texto, acompanhando o cabeçalho fixo do painel.

#### Scenario: Botão leva ao cadastro

- **WHEN** o candidato clica em "Candidatar-se" com uma vaga selecionada
- **THEN** ele é levado à tela de cadastro de currículo daquela vaga

#### Scenario: Botão alcançável em detalhe longo

- **WHEN** a vaga selecionada tem um detalhe mais longo que a área visível e o painel de detalhe ainda está em vista na tela
- **THEN** o botão "Candidatar-se" permanece visível no cabeçalho do painel, sem exigir rolagem até o fim do texto

#### Scenario: Item de lista não candidata direto

- **WHEN** o candidato clica em qualquer parte de um item da lista
- **THEN** ele apenas seleciona a vaga; a candidatura só é iniciada pelo botão da área de detalhe
