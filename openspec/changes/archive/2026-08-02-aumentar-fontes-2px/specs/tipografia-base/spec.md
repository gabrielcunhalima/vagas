## Purpose

Define a escala tipográfica única do portal de vagas: quais tamanhos de texto existem, como se relacionam entre si, como o entrelinhamento acompanha o tamanho, e a regra de que todo texto da interface tira seu tamanho dessa escala — nunca de um valor solto no componente.

## ADDED Requirements

### Requirement: Escala tipográfica 2px acima do padrão

Todos os tamanhos nomeados da escala tipográfica do portal SHALL medir 2px a mais que o tamanho padrão equivalente do framework de estilo. O acréscimo SHALL ser de 2px absolutos em cada degrau — não um percentual — de modo que a diferença entre degraus vizinhos permaneça exatamente a mesma de antes.

A escala SHALL ser expressa em unidade relativa ao tamanho de fonte raiz do navegador, para que a preferência de tamanho de fonte configurada pelo usuário continue sendo respeitada.

#### Scenario: Texto de corpo padrão

- **WHEN** um elemento da interface usa o degrau base da escala
- **THEN** ele é renderizado a 18px em um navegador com tamanho de fonte raiz padrão, e não a 16px

#### Scenario: Menor degrau da escala

- **WHEN** um elemento usa o menor degrau da escala (o usado em badges, legendas e rótulos auxiliares)
- **THEN** ele é renderizado a 14px, e não a 12px

#### Scenario: Degraus permanecem distintos e ordenados

- **WHEN** dois degraus vizinhos quaisquer da escala são comparados
- **THEN** o degrau maior continua estritamente maior que o menor, e a diferença em px entre eles é idêntica à que existia antes desta mudança

#### Scenario: Preferência de fonte do navegador respeitada

- **WHEN** o usuário aumenta o tamanho de fonte padrão do navegador
- **THEN** todos os textos do portal crescem proporcionalmente, como cresciam antes desta mudança

### Requirement: Entrelinhamento proporcional ao tamanho

O entrelinhamento de cada degrau da escala SHALL ser definido como razão do tamanho da fonte, não como medida fixa, de modo que cresça junto com o texto e preserve a proporção de leitura de cada degrau.

#### Scenario: Entrelinhamento cresce com a fonte

- **WHEN** um degrau da escala tem seu tamanho aumentado
- **THEN** a altura de linha desse degrau aumenta na mesma proporção, mantendo a razão altura-de-linha ÷ tamanho-de-fonte inalterada

#### Scenario: Blocos de texto longo permanecem legíveis

- **WHEN** o candidato lê a descrição completa de uma vaga
- **THEN** as linhas do parágrafo não se sobrepõem nem ficam visualmente coladas

### Requirement: Escala como fonte primária de tamanho de texto

Todo texto renderizado na interface do portal SHALL obter seu tamanho de um degrau nomeado da escala, exceto por um conjunto pequeno e deliberado de tamanhos fora da escala usados onde nenhum degrau serve — rótulos miúdos de badge, contadores e o lockup de identidade do cabeçalho.

Todo tamanho fora da escala SHALL acompanhar a escala: quando a escala é deslocada, cada valor fora dela é deslocado na mesma medida absoluta, de modo que a relação visual com os degraus vizinhos permaneça constante.

#### Scenario: Alteração da escala propaga para toda a interface

- **WHEN** um degrau da escala tem seu valor alterado em um único lugar
- **THEN** todo texto da interface que usa aquele degrau muda de tamanho junto, em todas as telas, sem edição adicional em componentes

#### Scenario: Tamanhos fora da escala acompanham o deslocamento

- **WHEN** a escala é deslocada em uma medida absoluta
- **THEN** todo tamanho de fonte fora da escala é deslocado na mesma medida, e nenhum texto da interface permanece no tamanho anterior

#### Scenario: Nenhum tamanho por estilo em linha

- **WHEN** o código da interface é inspecionado em busca de tamanho de fonte definido por atributo de estilo em linha no React
- **THEN** não existe nenhuma ocorrência — todo tamanho vem de classe utilitária, seja de degrau nomeado ou de valor explícito

### Requirement: Integridade visual sob a escala aumentada

Nenhum elemento da interface SHALL apresentar texto cortado, sobreposto, transbordando seu contêiner, ou truncado onde antes aparecia inteiro, em consequência do aumento da escala. Onde o texto maior não couber no espaço existente, o espaço SHALL ser ajustado para acomodá-lo — a escala não é reduzida para caber.

#### Scenario: Controles de altura fixa

- **WHEN** um botão, campo de formulário, badge, aba ou item de menu contendo texto é renderizado
- **THEN** o texto aparece inteiro e verticalmente centralizado, sem corte no topo nem na base

#### Scenario: Altura mínima comporta a linha de texto

- **WHEN** um controle de altura fixa é medido
- **THEN** sua altura descontados preenchimento e bordas é maior ou igual à altura da linha do degrau tipográfico que ele usa

#### Scenario: Telas densas em desktop e mobile

- **WHEN** a listagem pública de vagas, uma tabela de candidaturas, a barra lateral ou o formulário de vaga é aberto, em largura de desktop e de celular
- **THEN** nenhum texto transborda seu contêiner e nenhuma coluna ou rótulo passa a ser truncado que antes aparecia por extenso

#### Scenario: Ambos os temas

- **WHEN** qualquer tela é vista no tema claro e no tema escuro
- **THEN** o resultado tipográfico é idêntico entre os dois temas, já que o tamanho de texto não depende do tema

### Requirement: Controles de formulário mantêm alinhamento entre si

Os controles que compartilham uma altura nominal — botão, campo de texto, seletor e grupo de entrada — SHALL continuar tendo a mesma altura entre si após qualquer ajuste feito para acomodar a escala, de modo que permaneçam alinhados quando dispostos lado a lado.

A escada de tamanhos de um mesmo controle SHALL permanecer estritamente crescente: nenhum tamanho nomeado pode passar a ter a mesma altura de outro.

#### Scenario: Controles lado a lado

- **WHEN** um botão, um campo de texto e um seletor no tamanho padrão são dispostos na mesma linha
- **THEN** os três têm exatamente a mesma altura e suas bordas superior e inferior coincidem

#### Scenario: Escada de tamanhos preservada

- **WHEN** os tamanhos nomeados de um controle são comparados do menor ao maior
- **THEN** cada um é estritamente mais alto que o anterior, sem empates

### Requirement: Tipografia dos e-mails transacionais acompanha a escala

Os e-mails transacionais enviados pelo portal SHALL ter seus tamanhos de fonte aumentados em 2px na mesma medida da interface, preservando a hierarquia relativa entre título, corpo, caixa de informação, botão e rodapé.

O aumento SHALL ser aplicado sem alterar a unidade de medida já usada em cada declaração, para não mudar a forma como os clientes de e-mail interpretam o estilo.

#### Scenario: Corpo do e-mail

- **WHEN** um candidato ou gestor abre qualquer e-mail transacional do portal
- **THEN** o texto do corpo está 2px maior que antes desta mudança

#### Scenario: Hierarquia preservada

- **WHEN** um e-mail transacional com título de cabeçalho, subtítulo de seção e corpo é aberto
- **THEN** o título do cabeçalho continua maior que o subtítulo de seção, e o subtítulo continua maior que o corpo

#### Scenario: Nenhuma relação de tamanho se inverte

- **WHEN** os tamanhos renderizados de dois elementos quaisquer do e-mail são comparados antes e depois do aumento
- **THEN** a ordem entre eles é a mesma nos dois casos, inclusive onde uma regra de elemento já sobrepunha uma regra de contêiner
