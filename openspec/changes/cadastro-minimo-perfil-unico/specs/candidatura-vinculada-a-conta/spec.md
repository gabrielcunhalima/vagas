## Purpose

Define as pré-condições para um candidato se inscrever em uma vaga — conta autenticada, e-mail verificado e perfil completo — o que pertence à candidatura em vez de ao perfil, como os dados são confirmados antes do envio, e como candidaturas anteriores sem vínculo são incorporadas a uma conta nova.

## ADDED Requirements

### Requirement: Candidatura exige conta autenticada com e-mail verificado

O portal SHALL exigir que o candidato esteja autenticado e com o e-mail verificado para se inscrever em uma vaga. Toda candidatura SHALL pertencer a uma conta.

Quando um visitante não autenticado tentar se candidatar, o portal SHALL conduzi-lo ao acesso ou ao cadastro e, concluída a autenticação, SHALL retomar a candidatura da vaga pretendida sem exigir que ele a localize novamente.

#### Scenario: Visitante não autenticado tenta se candidatar

- **WHEN** um visitante sem sessão aciona a candidatura de uma vaga
- **THEN** o portal apresenta as opções de entrar ou criar conta
- **AND** após autenticar-se, o candidato é levado de volta à candidatura daquela vaga

#### Scenario: Candidato autenticado com e-mail não verificado

- **WHEN** um candidato autenticado, mas com e-mail ainda não verificado, aciona a candidatura de uma vaga
- **THEN** o portal informa que a verificação do e-mail é necessária para se candidatar
- **AND** oferece o reenvio do e-mail de verificação

#### Scenario: Candidatura sem conta não é aceita

- **WHEN** um envio de candidatura chega sem uma conta autenticada associada
- **THEN** o portal recusa o envio

### Requirement: Candidatura exige perfil completo

O portal SHALL exigir que o perfil do candidato esteja completo para que a inscrição seja enviada. Quando o perfil estiver incompleto, o portal SHALL indicar quais dados faltam e permitir preenchê-los sem perder a vaga pretendida.

#### Scenario: Candidato com perfil incompleto aciona a candidatura

- **WHEN** um candidato com perfil incompleto aciona a candidatura de uma vaga
- **THEN** o portal apresenta os campos pendentes para preenchimento
- **AND** identifica a vaga para a qual ele está se candidatando

#### Scenario: Perfil completado durante a candidatura

- **WHEN** o candidato preenche os dados pendentes a partir da tela de candidatura
- **THEN** os valores são gravados no perfil da conta
- **AND** o candidato prossegue com a inscrição na mesma vaga, sem recomeçar

#### Scenario: Envio com perfil incompleto é recusado

- **WHEN** um envio de candidatura chega com o perfil do candidato incompleto
- **THEN** o portal recusa o envio e informa os campos pendentes

### Requirement: Conferência dos dados antes do envio

Antes de concluir a inscrição, o portal SHALL apresentar ao candidato os dados do perfil que serão vistos pelo coordenador, permitindo revisá-los e alterá-los ali mesmo.

Alterações feitas nessa conferência SHALL seguir a regra de gravação no perfil da conta definida pela capability `perfil-candidato-unico`.

#### Scenario: Revisão dos dados na candidatura

- **WHEN** o candidato acessa a candidatura de uma vaga com o perfil completo
- **THEN** o portal exibe os dados atuais do perfil para conferência
- **AND** permite alterá-los antes do envio

#### Scenario: Currículo vigente apresentado na conferência

- **WHEN** o portal exibe os dados para conferência
- **THEN** identifica qual currículo será enviado
- **AND** permite substituí-lo por um novo antes do envio

### Requirement: Dados próprios da candidatura

Os dados que variam a cada inscrição SHALL pertencer à candidatura e não ao perfil: carta de apresentação, declaração de conflito de interesse com o respectivo detalhamento, e aceite do código de conduta.

A declaração de conflito de interesse SHALL ser respondida em relação à vaga pretendida, e o aceite do código de conduta SHALL ser registrado a cada inscrição.

#### Scenario: Conflito de interesse respondido por vaga

- **WHEN** um candidato que já declarou não ter conflito de interesse em uma vaga se inscreve em outra
- **THEN** o portal solicita novamente a declaração, referida à nova vaga

#### Scenario: Aceite do código de conduta a cada inscrição

- **WHEN** o candidato envia uma inscrição
- **THEN** o aceite do código de conduta é exigido e registrado para aquela candidatura

#### Scenario: Conflito declarado sem detalhamento

- **WHEN** o candidato declara ter conflito de interesse e não descreve a relação
- **THEN** o portal recusa o envio e solicita o detalhamento

### Requirement: Uma candidatura por vaga por candidato

O portal SHALL impedir que a mesma conta registre mais de uma candidatura para a mesma vaga.

#### Scenario: Segunda tentativa na mesma vaga

- **WHEN** um candidato que já se inscreveu em uma vaga aciona novamente a candidatura dessa vaga
- **THEN** o portal informa que ele já se candidatou
- **AND** o conduz à sua candidatura existente

### Requirement: Registro do envio da candidatura

O envio de uma candidatura SHALL ser registrado com a data e hora da submissão e a identificação da versão de currículo vigente naquele momento.

O registro SHALL NOT copiar dados de identidade do candidato.

#### Scenario: Envio registra a versão de currículo

- **WHEN** o candidato envia uma inscrição
- **THEN** o portal registra qual versão do currículo estava vigente na submissão

#### Scenario: Currículo substituído após o envio

- **WHEN** o candidato envia uma nova versão de currículo depois de já ter se candidatado
- **THEN** o registro da submissão continua identificando a versão que estava vigente no envio

### Requirement: Adoção de candidaturas anteriores sem vínculo

Ao criar uma conta, o portal SHALL incorporar a ela as candidaturas anteriores sem conta associada cujo CPF **e** e-mail coincidam com os informados no cadastro. A coincidência de apenas um dos dois SHALL NOT ser suficiente.

O conteúdo incorporado SHALL ser exibido ao candidato somente após a verificação do e-mail.

#### Scenario: CPF e e-mail coincidem

- **WHEN** um candidato cria conta com CPF e e-mail que constam em candidaturas anteriores sem conta associada
- **THEN** essas candidaturas passam a pertencer à nova conta
- **AND** o portal informa ao candidato que ele já possui candidaturas em andamento

#### Scenario: Apenas o CPF coincide

- **WHEN** um candidato cria conta com um CPF que consta em candidatura anterior, mas com e-mail diferente
- **THEN** a candidatura anterior não é incorporada à nova conta

#### Scenario: Exibição condicionada à verificação

- **WHEN** candidaturas anteriores foram incorporadas a uma conta cujo e-mail ainda não foi verificado
- **THEN** o portal não exibe o conteúdo dessas candidaturas até a verificação ser concluída
