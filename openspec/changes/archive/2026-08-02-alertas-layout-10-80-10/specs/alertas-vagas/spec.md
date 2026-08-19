## Purpose

Define como o candidato assina alertas de vagas por e-mail: a largura da página, a separação entre o que o identifica (e-mail, consentimento e envio) e o que ele escolhe receber (áreas, tipos e modalidades), e como essa separação se comporta conforme a largura da tela.

## ADDED Requirements

### Requirement: Largura de conteúdo 10-80-10 na página de alertas

A página de criação de alertas de vagas SHALL ocupar 80% da largura da janela, com 10% de margem livre de cada lado, em telas largas — a mesma largura de conteúdo da listagem pública. Os elementos comuns de navegação (cabeçalho e rodapé) SHALL manter a largura máxima já em uso no portal, sem acompanhar a largura da página.

#### Scenario: Tela larga

- **WHEN** o candidato abre a página de alertas em uma janela larga
- **THEN** o conteúdo da página ocupa 80% da largura da janela, centralizado, com 10% de espaço livre à esquerda e 10% à direita

#### Scenario: Mesma largura da listagem

- **WHEN** o candidato vem da listagem pública para a página de alertas em uma janela larga
- **THEN** as duas páginas apresentam o conteúdo na mesma largura, sem salto lateral perceptível entre elas

#### Scenario: Cabeçalho e rodapé inalterados

- **WHEN** o candidato abre a página de alertas em uma janela larga
- **THEN** o cabeçalho e o rodapé do portal permanecem na mesma largura máxima que apresentam nas demais páginas públicas

### Requirement: Identificação e preferências em áreas separadas

Em telas largas, a página de alertas SHALL apresentar duas áreas lado a lado. À esquerda, em área estreita, o título da página, o campo de e-mail, o consentimento LGPD e a ação de envio. À direita, em área larga, os grupos de preferência: áreas de interesse, tipos de vaga e modalidades. Nenhum grupo de preferência SHALL aparecer na área esquerda, e nenhum campo de identificação, consentimento ou envio SHALL aparecer na área direita.

#### Scenario: Duas áreas simultâneas

- **WHEN** o candidato abre a página de alertas em uma janela larga
- **THEN** a área de identificação e envio e a área de preferências aparecem lado a lado na mesma tela, sem sobreposição entre elas

#### Scenario: Divisão do conteúdo respeitada

- **WHEN** a página é exibida em duas áreas
- **THEN** o campo de e-mail, o consentimento LGPD e o botão de envio estão na área esquerda, e os três grupos de preferência estão na área direita

### Requirement: Envio alcançável sem rolar as preferências

Em telas largas, a ação de envio SHALL permanecer visível e acionável enquanto o candidato percorre os grupos de preferência, sem exigir que ele role de volta ao início ou até o fim da página.

#### Scenario: Rolagem pelas preferências

- **WHEN** o candidato rola a página para ver os grupos de preferência em uma janela larga
- **THEN** o botão de envio continua visível e acionável na área esquerda

#### Scenario: Consentimento acompanha o envio

- **WHEN** o botão de envio está visível durante a rolagem
- **THEN** o consentimento LGPD e seu texto também estão visíveis, para que o candidato não conceda o consentimento às cegas

### Requirement: Preferências ocupam a largura ganha

Na área de preferências em telas largas, cada grupo SHALL distribuir suas opções em mais de uma opção por linha, de modo que nenhum grupo se apresente como uma coluna única. Os rótulos das opções SHALL permanecer inteiramente legíveis, sem truncamento, e a página SHALL não produzir rolagem horizontal.

#### Scenario: Opções distribuídas

- **WHEN** o candidato abre a página de alertas em uma janela larga
- **THEN** as áreas de interesse, os tipos de vaga e as modalidades aparecem distribuídos em várias opções por linha, ocupando a largura da área de preferências

#### Scenario: Rótulos íntegros

- **WHEN** os grupos de preferência são exibidos em qualquer largura de tela
- **THEN** cada rótulo de opção aparece por inteiro, sem corte ou reticências, e a página não rola horizontalmente

### Requirement: Empilhamento em telas estreitas

Em telas onde as duas áreas não cabem lado a lado, o conteúdo SHALL empilhar em coluna única, em largura total, na ordem: título e e-mail, grupos de preferência, consentimento LGPD e, por último, a ação de envio. Nenhum conteúdo SHALL ser perdido, cortado ou sobreposto na troca entre as duas apresentações.

#### Scenario: Ordem empilhada

- **WHEN** o candidato abre a página de alertas em uma janela estreita
- **THEN** ele encontra, de cima para baixo, o título e o campo de e-mail, depois os grupos de preferência, depois o consentimento LGPD e por último o botão de envio

#### Scenario: Transição entre apresentações

- **WHEN** a janela é redimensionada entre a largura em que as duas áreas cabem lado a lado e a largura em que não cabem
- **THEN** todo o conteúdo permanece presente e legível, sem sobreposição e sem espaço vazio no lugar de um bloco

### Requirement: Campos, validação e envio preservados

A página SHALL coletar exatamente os mesmos dados de antes desta mudança — e-mail, áreas de interesse, tipos de vaga, modalidades e consentimento LGPD — com a mesma obrigatoriedade, os mesmos rótulos e textos de apoio, e um único envio. As mensagens de erro de validação SHALL aparecer junto do campo a que se referem, em qualquer das duas apresentações.

#### Scenario: Mesmo conjunto de dados

- **WHEN** o candidato preenche e envia o formulário de alertas
- **THEN** os dados enviados e o resultado são os mesmos de antes desta mudança

#### Scenario: Erro junto do campo

- **WHEN** o envio é recusado por validação, em qualquer largura de tela
- **THEN** a mensagem de erro aparece junto do campo correspondente, e não em outra área da página

#### Scenario: E-mail já conhecido

- **WHEN** a página é aberta com o e-mail do candidato já conhecido
- **THEN** o campo de e-mail vem preenchido e não editável, como antes desta mudança

#### Scenario: Deixar preferências em branco

- **WHEN** o candidato envia o formulário sem marcar nenhuma área de interesse
- **THEN** o alerta é criado cobrindo todas as áreas, como antes desta mudança
