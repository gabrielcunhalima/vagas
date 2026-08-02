## Purpose

Define como a página pública de detalhe de uma vaga apresenta as informações da oportunidade a um candidato visitante, de modo que ele consiga fazer a triagem (remuneração, local, jornada, prazo e elegibilidade) em poucos segundos antes de ler o conteúdo longo e decidir se candidatar.

## ADDED Requirements

### Requirement: Cabeçalho de identificação da vaga

A página SHALL exibir, no topo, a identificação da vaga: os selos de classificação (vaga nova, tipo de contratação e modalidade), o título da vaga e uma linha de contexto com a área e o local. O projeto vinculado SHALL aparecer nessa linha somente quando a vaga tiver projeto informado.

O tipo de contratação SHALL ser comunicado apenas por cor e texto no selo, nunca por ícone próprio.

#### Scenario: Vaga com projeto vinculado

- **WHEN** o candidato abre a página de uma vaga que possui nome de projeto
- **THEN** o cabeçalho exibe os selos de classificação, o título, a área, o local e a identificação do projeto

#### Scenario: Vaga sem projeto vinculado

- **WHEN** o candidato abre a página de uma vaga sem nome de projeto
- **THEN** o cabeçalho exibe os selos, o título, a área e o local, e nenhum rótulo ou espaço vazio referente a projeto é renderizado

#### Scenario: Vaga publicada recentemente

- **WHEN** a vaga se enquadra no critério de "nova"
- **THEN** o selo de vaga nova aparece antes dos demais selos de classificação

### Requirement: Fatos rápidos acima da dobra

A página SHALL apresentar, imediatamente abaixo do cabeçalho e antes do conteúdo textual longo, um bloco de fatos rápidos com remuneração, carga horária, modalidade/local e prazo de inscrição. Esse bloco SHALL ser visível sem rolagem horizontal e SHALL estar presente em todas as larguras de tela, independentemente do painel lateral de candidatura.

Cada fato SHALL ter rótulo e valor legíveis de forma independente, sem depender de contexto de outro fato para ser compreendido.

#### Scenario: Vaga com todos os fatos preenchidos

- **WHEN** a vaga possui remuneração, carga horária, modalidade e data de encerramento
- **THEN** os quatro fatos são exibidos no bloco de fatos rápidos com seus rótulos e valores

#### Scenario: Vaga sem remuneração informada

- **WHEN** a vaga não possui valor de remuneração
- **THEN** o fato de remuneração exibe o texto de remuneração a combinar, em vez de valor vazio ou zero

#### Scenario: Vaga com faixa salarial

- **WHEN** a vaga possui remuneração mínima e máxima
- **THEN** o fato de remuneração exibe a faixa completa formatada em moeda brasileira

#### Scenario: Vaga sem carga horária informada

- **WHEN** a vaga não possui carga horária
- **THEN** o fato de carga horária é omitido do bloco, sem deixar rótulo órfão nem célula vazia

#### Scenario: Visualização em tela estreita

- **WHEN** o candidato abre a página em uma tela de celular
- **THEN** os fatos rápidos permanecem visíveis e legíveis, reorganizados verticalmente, sem exigir rolagem horizontal da página

### Requirement: Seções de conteúdo hierarquizadas

A página SHALL apresentar o conteúdo textual da vaga em seções distintas e rotuladas — descrição da vaga, requisitos obrigatórios, requisitos desejáveis e benefícios —, cada uma com título próprio e separação visual clara entre elas.

Seções cujo campo de origem estiver vazio SHALL ser inteiramente omitidas, incluindo seu título.

O texto de cada seção SHALL preservar as quebras de linha originais informadas pelo publicador da vaga. Quando o texto vier estruturado em linhas ou marcadores, a página SHALL renderizá-lo como lista, e não como parágrafo corrido.

#### Scenario: Vaga sem requisitos desejáveis

- **WHEN** a vaga não possui requisitos desejáveis preenchidos
- **THEN** a seção de requisitos desejáveis não é renderizada, nem seu título

#### Scenario: Vaga sem benefícios

- **WHEN** a vaga não possui benefícios preenchidos
- **THEN** a seção de benefícios não é renderizada, nem seu título

#### Scenario: Requisitos informados em múltiplas linhas

- **WHEN** o campo de requisitos contém várias linhas ou itens marcados
- **THEN** a seção de requisitos exibe cada linha como um item de lista distinto

#### Scenario: Descrição informada em parágrafo único

- **WHEN** o campo de descrição contém um texto corrido sem quebras
- **THEN** a seção exibe o texto como parágrafo, sem inserir marcadores de lista

### Requirement: Destaque dos cursos desejados

Quando a vaga informar cursos desejados, a página SHALL exibi-los como um bloco próprio de elegibilidade, posicionado antes do conteúdo textual longo ou no topo dele, e não ao final da página. Cada curso SHALL ser apresentado como um item visualmente discreto e individualizado.

Quando a vaga não informar nenhum curso desejado, o bloco SHALL ser omitido.

#### Scenario: Vaga com cursos desejados

- **WHEN** a vaga possui uma ou mais entradas em cursos desejados
- **THEN** a página exibe o bloco de cursos com cada curso individualizado, antes do conteúdo textual longo

#### Scenario: Vaga sem cursos desejados

- **WHEN** a vaga não possui cursos desejados
- **THEN** nenhum bloco de cursos é renderizado

### Requirement: Local de trabalho estruturado

Para vagas presenciais ou híbridas com endereço informado, a página SHALL exibir o local de trabalho decomposto em partes legíveis — logradouro com número e complemento, bairro, cidade e estado, e CEP — em vez de uma única linha concatenada. Partes não informadas SHALL ser omitidas sem deixar separadores soltos.

Para vagas remotas, o bloco de endereço SHALL ser omitido.

#### Scenario: Vaga presencial com endereço completo

- **WHEN** a vaga é presencial e possui logradouro, número, bairro, cidade, estado e CEP
- **THEN** a página exibe o local de trabalho com essas partes separadas e identificáveis

#### Scenario: Vaga presencial com endereço parcial

- **WHEN** a vaga possui apenas cidade e estado informados
- **THEN** a página exibe somente cidade e estado, sem vírgulas, hifens ou rótulos referentes às partes ausentes

#### Scenario: Vaga remota

- **WHEN** a modalidade da vaga é remota
- **THEN** o bloco de local de trabalho não é renderizado

### Requirement: Painel de candidatura e prazo

A página SHALL oferecer um painel de candidatura contendo o prazo de inscrição e a ação principal de candidatar-se. O prazo SHALL indicar visualmente a urgência quando restarem poucos dias para o encerramento, e SHALL informar a data de encerramento nos demais casos.

O painel SHALL permanecer acessível enquanto o candidato percorre o conteúdo da página, sem que ele precise voltar ao topo para se candidatar.

O painel SHALL NOT repetir integralmente os dados já apresentados no bloco de fatos rápidos.

#### Scenario: Vaga com prazo confortável

- **WHEN** faltam mais de cinco dias para o encerramento das inscrições
- **THEN** o painel exibe a data de encerramento em tratamento visual neutro

#### Scenario: Vaga próxima do encerramento

- **WHEN** faltam cinco dias ou menos para o encerramento das inscrições
- **THEN** o painel exibe a contagem de dias restantes em tratamento visual de urgência

#### Scenario: Candidato aciona a candidatura

- **WHEN** o candidato aciona a ação principal do painel
- **THEN** ele é levado à página de inscrição da vaga correspondente

#### Scenario: Rolagem do conteúdo em tela larga

- **WHEN** o candidato rola o conteúdo longo da vaga em uma tela larga
- **THEN** o painel de candidatura continua visível na lateral

#### Scenario: Rolagem do conteúdo em tela estreita

- **WHEN** o candidato rola o conteúdo longo da vaga em uma tela de celular
- **THEN** a ação de candidatar-se permanece acessível de forma fixa na tela, sem exigir retorno ao topo

### Requirement: Compartilhamento e alerta de vagas

A página SHALL permitir que o candidato copie o link da vaga e o compartilhe por WhatsApp, e SHALL oferecer um caminho para o cadastro de alertas de vagas semelhantes.

A cópia do link SHALL produzir confirmação visível de sucesso ou de falha.

#### Scenario: Cópia do link bem-sucedida

- **WHEN** o candidato aciona a cópia do link e a operação é concluída
- **THEN** a página exibe uma confirmação de que o link foi copiado

#### Scenario: Cópia do link indisponível

- **WHEN** o candidato aciona a cópia do link e a operação falha
- **THEN** a página exibe uma mensagem informando que não foi possível copiar

#### Scenario: Compartilhamento por WhatsApp

- **WHEN** o candidato aciona o compartilhamento por WhatsApp
- **THEN** abre-se o WhatsApp com uma mensagem contendo o título da vaga e o endereço da página

### Requirement: Navegação de retorno e vagas relacionadas

A página SHALL oferecer um caminho explícito de retorno à listagem de vagas. Quando existirem vagas relacionadas, a página SHALL exibi-las ao final, após o conteúdo da vaga atual.

Quando não houver vagas relacionadas, a seção SHALL ser omitida.

#### Scenario: Vaga com relacionadas disponíveis

- **WHEN** existem vagas ativas relacionadas à vaga aberta
- **THEN** elas são exibidas ao final da página, cada uma levando à sua própria página de detalhe

#### Scenario: Vaga sem relacionadas

- **WHEN** não existem vagas relacionadas
- **THEN** a seção de vagas relacionadas não é renderizada, nem seu título

#### Scenario: Retorno à listagem

- **WHEN** o candidato aciona o caminho de retorno
- **THEN** ele é levado à listagem pública de vagas

### Requirement: Preservação do contrato de dados existente

A página SHALL ser construída exclusivamente sobre os dados já fornecidos pelo back-end para a rota pública de detalhe da vaga. Nenhuma informação adicional SHALL ser requisitada ao servidor, e nenhum campo interno não exposto publicamente SHALL ser apresentado.

#### Scenario: Conjunto de dados inalterado

- **WHEN** a página de detalhe é renderizada
- **THEN** ela utiliza somente os campos públicos da vaga e a lista de vagas relacionadas já enviados pela rota, sem chamadas extras ao servidor

### Requirement: Suporte a tema claro e escuro

Toda a apresentação da página SHALL funcionar nos dois temas do portal, usando exclusivamente os tokens de cor do sistema de design, sem cores fixas fora da paleta.

#### Scenario: Alternância de tema

- **WHEN** o candidato alterna entre tema claro e escuro
- **THEN** todos os blocos da página — fatos rápidos, seções, cursos, endereço, painel e relacionadas — permanecem legíveis e com contraste adequado
