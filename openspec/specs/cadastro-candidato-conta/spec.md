# cadastro-candidato-conta Specification

## Purpose
Define o cadastro público de candidato — quais dados são exigidos para a conta passar a existir (CPF, e-mail, senha e consentimento), quando cada validação é executada, qual retorno o candidato recebe e sob quais condições o cadastro pode ser concluído. Cobre também o estado de e-mail ainda não verificado como estado navegável da conta e quais atos exigem a verificação.
## Requirements

### Requirement: Campos exigidos para criar conta

A criação de conta SHALL exigir exclusivamente e-mail, senha, confirmação de senha, CPF e consentimento de tratamento de dados. Nenhum outro dado pessoal, acadêmico, de endereço ou documento SHALL ser exigido para que a conta passe a existir.

Concluído o cadastro, o candidato SHALL ficar autenticado, sem precisar entrar novamente.

#### Scenario: Cadastro apenas com credenciais

- **WHEN** o candidato informa e-mail, senha válida, confirmação coincidente, CPF disponível e concede o consentimento
- **THEN** a conta é criada
- **AND** o candidato fica autenticado

#### Scenario: Nenhum dado adicional é solicitado

- **WHEN** o candidato acessa a tela de cadastro
- **THEN** o portal não solicita nome, nacionalidade, telefone, endereço, formação, currículo, acessibilidade nem conflito de interesse

#### Scenario: Conta criada tem perfil incompleto

- **WHEN** uma conta acaba de ser criada pelo cadastro
- **THEN** o perfil do candidato é considerado incompleto
- **AND** o candidato pode preenchê-lo quando quiser

### Requirement: Consentimento de tratamento de dados no cadastro

O portal SHALL exigir o consentimento de tratamento de dados no próprio cadastro, por haver coleta de CPF já nesse momento, e SHALL registrar a data e hora em que ele foi concedido.

O aceite do código de conduta SHALL NOT ser exigido no cadastro, por ser um ato do processo seletivo e não da criação de conta.

#### Scenario: Cadastro sem consentimento

- **WHEN** o candidato tenta concluir o cadastro sem conceder o consentimento
- **THEN** o portal recusa a criação da conta e informa que o consentimento é necessário

#### Scenario: Registro do momento do consentimento

- **WHEN** uma conta é criada com o consentimento concedido
- **THEN** o portal registra a data e hora do consentimento

### Requirement: Conta com e-mail não verificado é navegável

O portal SHALL permitir que um candidato com e-mail ainda não verificado navegue pelas vagas, acesse a conta, preencha e altere os dados do próprio perfil e envie currículo.

A consulta às candidaturas SHALL exigir verificação: como candidatar-se já exige e-mail verificado, toda candidatura visível a uma conta não verificada só pode ter vindo da incorporação de histórico anterior, cuja titularidade ainda não foi comprovada.

O portal SHALL manter visível o aviso de que o e-mail ainda não foi verificado e oferecer o reenvio da verificação.

#### Scenario: Preenchimento do perfil antes de verificar

- **WHEN** um candidato com e-mail não verificado acessa os seus dados
- **THEN** consegue preencher e salvar os campos do perfil

#### Scenario: Aviso de verificação pendente

- **WHEN** um candidato com e-mail não verificado está autenticado
- **THEN** o portal exibe o aviso de verificação pendente com a opção de reenviar

### Requirement: Atos que exigem e-mail verificado

O portal SHALL exigir e-mail verificado para se candidatar a uma vaga e para criar ou alterar alertas de vaga. Nenhum outro ato SHALL ser condicionado à verificação.

#### Scenario: Candidatura exige verificação

- **WHEN** um candidato com e-mail não verificado tenta se candidatar
- **THEN** o portal informa que a verificação é necessária e oferece o reenvio

#### Scenario: Alerta exige verificação

- **WHEN** um candidato com e-mail não verificado tenta ativar um alerta
- **THEN** o portal informa que a verificação é necessária e oferece o reenvio

#### Scenario: Verificação concluída libera os atos

- **WHEN** o candidato conclui a verificação do e-mail
- **THEN** passa a poder se candidatar e a gerenciar alertas

### Requirement: Verificação de CPF ao ser preenchido

O portal SHALL avaliar o CPF assim que o campo for preenchido com 11 dígitos, sem exigir que o candidato saia do campo nem que tente concluir o cadastro. A avaliação SHALL ocorrer em duas camadas: verificação local dos dígitos verificadores e, apenas quando essa passar, consulta ao servidor sobre a existência de conta.

A consulta ao servidor SHALL ser adiada por um intervalo curto após a última tecla digitada, de modo que a digitação contínua do CPF não gere uma consulta por caractere.

Enquanto o campo tiver menos de 11 dígitos, o portal SHALL NOT exibir mensagem de erro de CPF, tratando o valor como incompleto e não como inválido.

#### Scenario: CPF completado com dígitos verificadores válidos e sem conta

- **WHEN** o candidato termina de digitar um CPF de 11 dígitos cujos dígitos verificadores conferem e que não possui conta no portal
- **THEN** o portal exibe indicação de sucesso no campo confirmando que o CPF está disponível
- **AND** o candidato pode concluir o cadastro

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

Quando a consulta ao servidor indicar que o CPF já possui conta, o portal SHALL informar o candidato imediatamente e SHALL oferecer, na própria mensagem, um caminho para a tela de login e um para a recuperação de senha.

Como login e recuperação de senha identificam o candidato pelo e-mail e não pelo CPF, o CPF digitado SHALL NOT ser propagado para essas telas, e o aviso SHALL oferecer a recuperação de senha como caminho para quem não lembra qual e-mail usou.

#### Scenario: CPF já possui conta

- **WHEN** a consulta ao servidor responde que o CPF informado já está cadastrado
- **THEN** o portal exibe no campo a mensagem "Este CPF já está cadastrado."
- **AND** apresenta um atalho para a tela de login e um atalho para recuperação de senha
- **AND** o candidato é impedido de concluir o cadastro

#### Scenario: Candidato não lembra o e-mail da conta existente

- **WHEN** o candidato aciona o atalho de recuperação de senha exibido no aviso de CPF já cadastrado
- **THEN** o portal abre a tela de recuperação de senha, que solicita o e-mail da conta

### Requirement: Avanço bloqueado enquanto o CPF não estiver liberado

O portal SHALL impedir a conclusão do cadastro enquanto o CPF estiver inválido, já cadastrado, ou com a verificação em andamento. Ao bloquear, o portal SHALL manter visível a razão do bloqueio no campo correspondente.

#### Scenario: Tentativa de concluir com verificação em andamento

- **WHEN** o candidato aciona a conclusão do cadastro enquanto a consulta de CPF ainda não retornou
- **THEN** o portal não cria a conta
- **AND** mantém a indicação de que o CPF está sendo verificado

#### Scenario: Tentativa de concluir com CPF já cadastrado

- **WHEN** o candidato aciona a conclusão do cadastro com um CPF marcado como já cadastrado
- **THEN** o portal não cria a conta e mantém o aviso e seus atalhos

### Requirement: Tolerância a falha na consulta de CPF

A indisponibilidade do serviço de verificação SHALL NOT impedir o candidato de concluir o cadastro, dado que a validação final de unicidade é feita no envio do formulário.

#### Scenario: Consulta ao servidor falha

- **WHEN** a consulta de existência de CPF falha por erro de rede ou erro do servidor
- **THEN** o portal não exibe o CPF como já cadastrado
- **AND** permite que o candidato conclua o cadastro, ficando a verificação de unicidade a cargo do envio final

#### Scenario: CPF duplicado detectado apenas no envio

- **WHEN** o envio do cadastro é rejeitado porque o CPF já existe
- **THEN** o portal exibe a mensagem de CPF já cadastrado no campo, preservando os demais valores digitados

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

O portal SHALL exibir a mensagem de senha insuficiente assim que o candidato sair do campo de senha com uma senha que não cumpra todos os critérios. O conjunto de critérios exibido SHALL corresponder ao exigido pela validação do servidor no envio.

#### Scenario: Senha cumprindo todos os critérios

- **WHEN** o candidato digita uma senha que atende aos cinco critérios
- **THEN** o portal marca todos os critérios como cumpridos
- **AND** não exibe mensagem de erro para o campo de senha

#### Scenario: Senha deixada incompleta

- **WHEN** o candidato sai do campo de senha com um valor que não cumpre todos os critérios
- **THEN** o portal exibe a mensagem "A senha não atende a todos os requisitos."
- **AND** os critérios não cumpridos permanecem visualmente distinguíveis dos cumpridos

#### Scenario: Conclusão com senha fraca

- **WHEN** o candidato aciona a conclusão do cadastro com uma senha que não cumpre todos os critérios
- **THEN** o portal não cria a conta e mantém a mensagem de senha insuficiente

### Requirement: Conferência de igualdade entre senha e confirmação

O portal SHALL comparar a senha e sua confirmação enquanto a confirmação é digitada, informando tanto a coincidência quanto a divergência. A divergência SHALL ser sinalizada assim que o candidato sair do campo de confirmação.

Quando a senha for alterada depois de a confirmação já ter sido preenchida, o portal SHALL reavaliar a comparação com o novo valor.

#### Scenario: Confirmação igual à senha

- **WHEN** o candidato preenche a confirmação com valor idêntico ao da senha
- **THEN** o portal exibe indicação de sucesso confirmando que as senhas coincidem

#### Scenario: Confirmação diferente da senha

- **WHEN** o candidato sai do campo de confirmação com valor diferente do da senha
- **THEN** o portal exibe a mensagem "As senhas não coincidem."
- **AND** o candidato é impedido de concluir o cadastro

#### Scenario: Senha alterada após a confirmação

- **WHEN** o candidato edita o campo de senha depois de já ter preenchido a confirmação
- **THEN** o portal reavalia a igualdade entre os dois campos e atualiza a indicação exibida

### Requirement: Distinção visual entre retorno positivo, neutro e de erro

Os campos do cadastro SHALL distinguir visualmente três tipos de retorno: confirmação de validação bem-sucedida, informação neutra sobre o estado da verificação, e erro. A confirmação positiva SHALL NOT ser apresentada com o mesmo tratamento visual de um texto de apoio neutro.

#### Scenario: Campo com validação bem-sucedida

- **WHEN** o CPF é confirmado como válido e disponível, ou a confirmação de senha coincide
- **THEN** a mensagem correspondente é apresentada com tratamento visual de sucesso

#### Scenario: Campo em verificação

- **WHEN** a consulta de CPF está em andamento
- **THEN** a mensagem "Verificando CPF…" é apresentada com tratamento visual neutro

#### Scenario: Erro sobrepõe retorno positivo

- **WHEN** um campo possui simultaneamente uma mensagem de erro e uma mensagem de sucesso ou neutra aplicáveis
- **THEN** apenas a mensagem de erro é exibida

### Requirement: Consulta à Política de Privacidade sem sair do cadastro

Ao acionar o link "Política de Privacidade" exibido junto ao consentimento LGPD no formulário de criação de conta, o portal SHALL exibir o conteúdo da política em um pop-up sobreposto à tela atual, em vez de navegar para outra página. O pop-up SHALL poder ser fechado sem submeter nem descartar o formulário, e os valores já preenchidos (CPF, e-mail, senha e confirmação) SHALL permanecer inalterados após o fechamento.

#### Scenario: Abrir a Política de Privacidade a partir do cadastro

- **WHEN** o candidato aciona o link "Política de Privacidade" no formulário de criação de conta
- **THEN** o portal exibe um pop-up sobreposto à tela com o conteúdo da Política de Privacidade
- **AND** o candidato permanece na mesma tela de criação de conta, sem navegação de página

#### Scenario: Fechar o pop-up preserva o formulário

- **WHEN** o candidato fecha o pop-up da Política de Privacidade (pelo botão de fechar, pela tecla Esc ou clicando fora do pop-up)
- **THEN** o portal retorna o foco ao formulário de criação de conta
- **AND** todos os campos previamente preenchidos mantêm os valores digitados antes da abertura do pop-up

#### Scenario: Conteúdo do pop-up consistente com a página dedicada

- **WHEN** o pop-up da Política de Privacidade é exibido
- **THEN** o texto apresentado é o mesmo conteúdo vigente na página pública dedicada à Política de Privacidade
