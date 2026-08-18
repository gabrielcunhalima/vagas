## ADDED Requirements

### Requirement: Filtros sustentados pela origem das vagas

Os filtros oferecidos na listagem pública SHALL corresponder a informações que a origem das vagas realmente possui. O portal SHALL NOT oferecer filtro cujo critério não exista no registro da vaga.

Os filtros disponíveis SHALL ser: busca textual por cargo e atividades, tipo de contratação, escolaridade exigida, município, UF, faixa de remuneração e projeto.

#### Scenario: Filtro por tipo de contratação

- **WHEN** o candidato filtra por um tipo de contratação
- **THEN** a lista passa a exibir somente as vagas cujo tipo de admissão corresponde ao escolhido

#### Scenario: Filtro por município

- **WHEN** o candidato filtra por um município
- **THEN** a lista passa a exibir somente as vagas daquele município

#### Scenario: Busca textual

- **WHEN** o candidato digita um termo na busca
- **THEN** a lista passa a exibir as vagas cujo cargo ou descrição de atividades contenham o termo

#### Scenario: Nenhum filtro sem lastro na origem

- **WHEN** o candidato abre os filtros da listagem
- **THEN** não há filtro por área, por modalidade nem por curso desejado

## MODIFIED Requirements

### Requirement: Painel de detalhe da vaga selecionada

A área de detalhe SHALL apresentar as informações da vaga selecionada que não cabem no item de lista. Ela SHALL conter, no mínimo: um resumo rápido da vaga, a descrição das atividades, a remuneração e os requisitos exigidos. Informações complementares disponíveis na vaga (carga horária, tipo de contratação, escolaridade exigida, experiência exigida, benefícios, documentação necessária, localização, projeto, prazo de inscrição) SHALL ser apresentadas quando existirem e omitidas por completo quando ausentes, sem deixar rótulos órfãos ou blocos vazios. Em tela larga, a área de detalhe SHALL sempre exibir seu conteúdo por completo, sem barra de rolagem própria que esconda parte da vaga.

#### Scenario: Informações mínimas presentes

- **WHEN** uma vaga está selecionada
- **THEN** a área de detalhe exibe o título do cargo, um resumo rápido da vaga, a descrição das atividades, a remuneração e os requisitos exigidos

#### Scenario: Campos opcionais ausentes

- **WHEN** a vaga selecionada não possui benefícios, documentação necessária, projeto nem carga horária informados
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
