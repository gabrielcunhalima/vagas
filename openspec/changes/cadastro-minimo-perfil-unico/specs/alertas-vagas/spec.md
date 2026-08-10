## ADDED Requirements

### Requirement: Criar ou alterar alerta exige conta autenticada e verificada

O portal SHALL exigir que o candidato esteja autenticado e com o e-mail verificado para criar ou alterar um alerta de vaga. Todo alerta SHALL pertencer a uma conta.

A exigência de verificação existe porque o alerta origina envio de e-mail: sem ela, seria possível inscrever o endereço de terceiros contra a vontade deles.

#### Scenario: Visitante não autenticado aciona os alertas

- **WHEN** um visitante sem sessão acessa a página de alertas
- **THEN** o portal apresenta as opções de entrar ou criar conta
- **AND** após autenticar-se, o candidato retorna à configuração do alerta

#### Scenario: Candidato com e-mail não verificado

- **WHEN** um candidato autenticado com e-mail não verificado tenta ativar um alerta
- **THEN** o portal informa que a verificação é necessária
- **AND** oferece o reenvio do e-mail de verificação

#### Scenario: Alerta sem perfil completo

- **WHEN** um candidato autenticado e verificado, com perfil incompleto, cria um alerta
- **THEN** o alerta é criado normalmente

### Requirement: E-mail do alerta é o da conta

O e-mail de destino do alerta SHALL ser o da conta autenticada. O portal SHALL NOT aceitar um endereço de destino informado pelo candidato.

Quando o candidato alterar o e-mail da conta, os alertas SHALL passar a ser enviados para o novo endereço.

#### Scenario: Destino não é escolhido pelo candidato

- **WHEN** o candidato configura um alerta
- **THEN** o portal apresenta o e-mail da conta como destino, sem permitir informar outro

#### Scenario: E-mail da conta alterado

- **WHEN** o candidato altera o e-mail da conta e existe alerta ativo
- **THEN** os envios seguintes vão para o novo endereço

### Requirement: Um alerta por conta

Cada conta SHALL ter no máximo um alerta. Configurar um alerta quando já existe um SHALL atualizar as preferências do alerta existente.

#### Scenario: Reconfiguração de alerta existente

- **WHEN** um candidato que já tem alerta ativo altera as áreas de interesse
- **THEN** o alerta existente passa a valer com as novas preferências, sem criar um segundo alerta

### Requirement: Cancelamento do recebimento sem autenticação

O portal SHALL permitir que o destinatário cancele o recebimento de alertas a partir do link presente no e-mail, sem exigir autenticação.

#### Scenario: Cancelamento pelo link do e-mail

- **WHEN** o destinatário aciona o link de cancelamento recebido por e-mail
- **THEN** o alerta é desativado e o portal confirma o cancelamento
- **AND** nenhuma autenticação é solicitada

#### Scenario: Reativação após cancelamento

- **WHEN** um candidato cujo alerta foi cancelado pelo link acessa a configuração de alertas autenticado
- **THEN** consegue reativá-lo

### Requirement: Alertas existentes sem conta associada

Alertas criados antes desta exigência SHALL ser vinculados à conta cujo e-mail corresponda ao do alerta. Alertas cujo e-mail não corresponda a nenhuma conta SHALL ser desativados, por não haver mais forma de o destinatário administrá-los.

#### Scenario: Alerta cujo e-mail tem conta

- **WHEN** existe alerta ativo com e-mail idêntico ao de uma conta de candidato
- **THEN** o alerta passa a pertencer a essa conta, preservando as preferências

#### Scenario: Alerta órfão

- **WHEN** existe alerta ativo com e-mail que não corresponde a nenhuma conta
- **THEN** o alerta é desativado e deixa de originar envios

## MODIFIED Requirements

### Requirement: Identificação e preferências em áreas separadas

Em telas largas, a página de alertas SHALL apresentar duas áreas lado a lado. À esquerda, em área estreita, o título da página, o e-mail de destino da conta e a ação de envio. À direita, em área larga, os grupos de preferência: áreas de interesse, tipos de vaga e modalidades. Nenhum grupo de preferência SHALL aparecer na área esquerda, e nenhum elemento de identificação ou envio SHALL aparecer na área direita.

O consentimento de tratamento de dados SHALL NOT ser solicitado nesta página, por já ter sido concedido na criação da conta.

#### Scenario: Duas áreas simultâneas

- **WHEN** o candidato abre a página de alertas em uma janela larga
- **THEN** a área de identificação e envio e a área de preferências aparecem lado a lado na mesma tela, sem sobreposição entre elas

#### Scenario: Divisão do conteúdo respeitada

- **WHEN** a página é exibida em duas áreas
- **THEN** o e-mail de destino e o botão de envio estão na área esquerda, e os três grupos de preferência estão na área direita

#### Scenario: Consentimento não é solicitado novamente

- **WHEN** o candidato autenticado abre a página de alertas
- **THEN** nenhum campo de consentimento de tratamento de dados é apresentado

### Requirement: Envio alcançável sem rolar as preferências

Em telas largas, a ação de envio SHALL permanecer visível e acionável enquanto o candidato percorre os grupos de preferência, sem exigir que ele role de volta ao início ou até o fim da página.

#### Scenario: Rolagem pelas preferências

- **WHEN** o candidato rola a página para ver os grupos de preferência em uma janela larga
- **THEN** o botão de envio continua visível e acionável na área esquerda

#### Scenario: Destino acompanha o envio

- **WHEN** o botão de envio está visível durante a rolagem
- **THEN** o e-mail de destino também está visível, para que o candidato saiba onde receberá os alertas antes de confirmar

### Requirement: Empilhamento em telas estreitas

Em telas onde as duas áreas não cabem lado a lado, o conteúdo SHALL empilhar em coluna única, em largura total, na ordem: título e e-mail de destino, grupos de preferência e, por último, a ação de envio. Nenhum conteúdo SHALL ser perdido, cortado ou sobreposto na troca entre as duas apresentações.

#### Scenario: Ordem empilhada

- **WHEN** o candidato abre a página de alertas em uma janela estreita
- **THEN** ele encontra, de cima para baixo, o título e o e-mail de destino, depois os grupos de preferência e por último o botão de envio

#### Scenario: Transição entre apresentações

- **WHEN** a janela é redimensionada entre a largura em que as duas áreas cabem lado a lado e a largura em que não cabem
- **THEN** todo o conteúdo permanece presente e legível, sem sobreposição e sem espaço vazio no lugar de um bloco

### Requirement: Campos, validação e envio preservados

A página SHALL coletar as áreas de interesse, os tipos de vaga e as modalidades, com os mesmos rótulos e textos de apoio já em uso, e um único envio. O e-mail de destino SHALL ser apresentado a partir da conta e SHALL NOT ser coletado como campo editável. As mensagens de erro de validação SHALL aparecer junto do campo a que se referem, em qualquer das duas apresentações.

#### Scenario: Conjunto de dados coletado

- **WHEN** o candidato preenche e envia o formulário de alertas
- **THEN** são enviadas apenas as áreas de interesse, os tipos de vaga e as modalidades escolhidos

#### Scenario: Erro junto do campo

- **WHEN** o envio é recusado por validação, em qualquer largura de tela
- **THEN** a mensagem de erro aparece junto do campo correspondente, e não em outra área da página

#### Scenario: E-mail de destino apresentado

- **WHEN** o candidato autenticado abre a página de alertas
- **THEN** o e-mail da conta é apresentado como destino, sem ser editável

#### Scenario: Deixar preferências em branco

- **WHEN** o candidato envia o formulário sem marcar nenhuma área de interesse
- **THEN** o alerta é criado cobrindo todas as áreas, como antes desta mudança
