## Purpose

Define o comportamento da etapa de credenciais do cadastro público de candidato — CPF, e-mail, senha e confirmação de senha — determinando quando cada validação é executada, qual retorno o candidato recebe e sob quais condições ele pode avançar para as etapas seguintes.

## ADDED Requirements

### Requirement: Verificação de CPF ao ser preenchido

O portal SHALL avaliar o CPF assim que o campo for preenchido com 11 dígitos, sem exigir que o candidato saia do campo nem que tente avançar de etapa. A avaliação SHALL ocorrer em duas camadas: verificação local dos dígitos verificadores e, apenas quando essa passar, consulta ao servidor sobre a existência de conta.

A consulta ao servidor SHALL ser adiada por um intervalo curto após a última tecla digitada, de modo que a digitação contínua do CPF não gere uma consulta por caractere.

Enquanto o campo tiver menos de 11 dígitos, o portal SHALL NOT exibir mensagem de erro de CPF, tratando o valor como incompleto e não como inválido.

#### Scenario: CPF completado com dígitos verificadores válidos e sem conta

- **WHEN** o candidato termina de digitar um CPF de 11 dígitos cujos dígitos verificadores conferem e que não possui conta no portal
- **THEN** o portal exibe indicação de sucesso no campo confirmando que o CPF está disponível
- **AND** o candidato pode avançar para a etapa seguinte

#### Scenario: CPF completado com dígitos verificadores inválidos

- **WHEN** o candidato termina de digitar um CPF de 11 dígitos cujos dígitos verificadores não conferem
- **THEN** o portal exibe imediatamente a mensagem de erro "CPF inválido." no campo
- **AND** nenhuma consulta ao servidor é realizada

#### Scenario: CPF ainda incompleto

- **WHEN** o candidato digitou menos de 11 dígitos no campo de CPF
- **THEN** o portal não exibe mensagem de erro nem de sucesso para o campo
- **AND** nenhuma consulta ao servidor é realizada

#### Scenario: Digitação contínua não dispara uma consulta por dígito

- **WHEN** o candidato digita ou cola um CPF completo em sequência rápida
- **THEN** o portal realiza no máximo uma consulta ao servidor para o valor final digitado

#### Scenario: CPF corrigido após erro

- **WHEN** o candidato altera um CPF que estava marcado como inválido ou já cadastrado
- **THEN** o portal descarta o retorno anterior e reavalia o novo valor pelas mesmas regras

### Requirement: Aviso de CPF já cadastrado com caminho para entrar

Quando a consulta ao servidor indicar que o CPF já possui conta, o portal SHALL informar o candidato imediatamente e SHALL oferecer, na própria mensagem, um caminho para a tela de login e um para a recuperação de senha, de modo que ele não precise preencher as demais etapas para descobrir que já é cadastrado.

Como login e recuperação de senha identificam o candidato pelo e-mail e não pelo CPF, o CPF digitado SHALL NOT ser propagado para essas telas, e o aviso SHALL oferecer a recuperação de senha como caminho para quem não lembra qual e-mail usou.

#### Scenario: CPF já possui conta

- **WHEN** a consulta ao servidor responde que o CPF informado já está cadastrado
- **THEN** o portal exibe no campo a mensagem "Este CPF já está cadastrado."
- **AND** apresenta um atalho para a tela de login e um atalho para recuperação de senha
- **AND** o candidato é impedido de avançar para a etapa seguinte

#### Scenario: Candidato não lembra o e-mail da conta existente

- **WHEN** o candidato aciona o atalho de recuperação de senha exibido no aviso de CPF já cadastrado
- **THEN** o portal abre a tela de recuperação de senha, que solicita o e-mail da conta

### Requirement: Avanço bloqueado enquanto o CPF não estiver liberado

O portal SHALL impedir a saída da etapa de credenciais enquanto o CPF estiver inválido, já cadastrado, ou com a verificação em andamento. Ao bloquear, o portal SHALL manter visível a razão do bloqueio no campo correspondente.

#### Scenario: Tentativa de avançar com verificação em andamento

- **WHEN** o candidato aciona "Continuar" enquanto a consulta de CPF ainda não retornou
- **THEN** o portal permanece na etapa de credenciais
- **AND** mantém a indicação de que o CPF está sendo verificado

#### Scenario: Tentativa de avançar com CPF já cadastrado

- **WHEN** o candidato aciona "Continuar" com um CPF marcado como já cadastrado
- **THEN** o portal permanece na etapa de credenciais exibindo o aviso e seus atalhos

### Requirement: Tolerância a falha na consulta de CPF

A indisponibilidade do serviço de verificação SHALL NOT impedir o candidato de prosseguir com o cadastro, dado que a validação final de unicidade é feita no envio do formulário.

#### Scenario: Consulta ao servidor falha

- **WHEN** a consulta de existência de CPF falha por erro de rede ou erro do servidor
- **THEN** o portal não exibe o CPF como já cadastrado
- **AND** permite que o candidato avance, ficando a verificação de unicidade a cargo do envio final

#### Scenario: CPF duplicado detectado apenas no envio

- **WHEN** o envio final do cadastro é rejeitado porque o CPF já existe
- **THEN** o portal retorna o candidato à etapa de credenciais com a mensagem de CPF já cadastrado no campo

### Requirement: Proteção do serviço de verificação de CPF

O serviço de verificação de CPF SHALL ser limitado por taxa de requisições por origem, por responder se um CPF possui ou não conta no portal.

#### Scenario: Volume de consultas acima do limite

- **WHEN** uma mesma origem excede o limite de consultas de CPF em uma janela de tempo
- **THEN** o serviço recusa as consultas seguintes dessa origem até o fim da janela
- **AND** o formulário trata a recusa como falha de consulta, sem marcar o CPF como já cadastrado

#### Scenario: Consulta com CPF malformado

- **WHEN** o serviço recebe uma consulta cujo valor não é um CPF de 11 dígitos com dígitos verificadores válidos
- **THEN** o serviço responde que o CPF é inválido
- **AND** não realiza busca na base de candidatos

### Requirement: Conferência de senha forte durante a digitação

O portal SHALL exigir que a senha atenda a todos os critérios de força — mínimo de 8 caracteres, ao menos uma letra minúscula, uma maiúscula, um número e um símbolo — e SHALL exibir o cumprimento de cada critério individualmente enquanto a senha é digitada.

O portal SHALL exibir a mensagem de senha insuficiente assim que o candidato sair do campo de senha com uma senha que não cumpra todos os critérios, sem exigir tentativa de avanço. O conjunto de critérios exibido SHALL corresponder ao exigido pela validação do servidor no envio.

#### Scenario: Senha cumprindo todos os critérios

- **WHEN** o candidato digita uma senha que atende aos cinco critérios
- **THEN** o portal marca todos os critérios como cumpridos
- **AND** não exibe mensagem de erro para o campo de senha

#### Scenario: Senha deixada incompleta

- **WHEN** o candidato sai do campo de senha com um valor que não cumpre todos os critérios
- **THEN** o portal exibe a mensagem "A senha não atende a todos os requisitos."
- **AND** os critérios não cumpridos permanecem visualmente distinguíveis dos cumpridos

#### Scenario: Avanço com senha fraca

- **WHEN** o candidato aciona "Continuar" com uma senha que não cumpre todos os critérios
- **THEN** o portal permanece na etapa de credenciais com a mensagem de senha insuficiente

### Requirement: Conferência de igualdade entre senha e confirmação

O portal SHALL comparar a senha e sua confirmação enquanto a confirmação é digitada, informando tanto a coincidência quanto a divergência. A divergência SHALL ser sinalizada assim que o candidato sair do campo de confirmação, sem exigir tentativa de avanço.

Quando a senha for alterada depois de a confirmação já ter sido preenchida, o portal SHALL reavaliar a comparação com o novo valor.

#### Scenario: Confirmação igual à senha

- **WHEN** o candidato preenche a confirmação com valor idêntico ao da senha
- **THEN** o portal exibe indicação de sucesso confirmando que as senhas coincidem

#### Scenario: Confirmação diferente da senha

- **WHEN** o candidato sai do campo de confirmação com valor diferente do da senha
- **THEN** o portal exibe a mensagem "As senhas não coincidem."
- **AND** o candidato é impedido de avançar para a etapa seguinte

#### Scenario: Senha alterada após a confirmação

- **WHEN** o candidato edita o campo de senha depois de já ter preenchido a confirmação
- **THEN** o portal reavalia a igualdade entre os dois campos e atualiza a indicação exibida

### Requirement: Distinção visual entre retorno positivo, neutro e de erro

Os campos da etapa de credenciais SHALL distinguir visualmente três tipos de retorno: confirmação de validação bem-sucedida, informação neutra sobre o estado da verificação, e erro. A confirmação positiva SHALL NOT ser apresentada com o mesmo tratamento visual de um texto de apoio neutro.

#### Scenario: Campo com validação bem-sucedida

- **WHEN** o CPF é confirmado como válido e disponível, ou a confirmação de senha coincide
- **THEN** a mensagem correspondente é apresentada com tratamento visual de sucesso

#### Scenario: Campo em verificação

- **WHEN** a consulta de CPF está em andamento
- **THEN** a mensagem "Verificando CPF…" é apresentada com tratamento visual neutro

#### Scenario: Erro sobrepõe retorno positivo

- **WHEN** um campo possui simultaneamente uma mensagem de erro e uma mensagem de sucesso ou neutra aplicáveis
- **THEN** apenas a mensagem de erro é exibida
