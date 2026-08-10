## Purpose

Define o que o coordenador enxerga de uma candidatura enquanto o processo seletivo está em andamento e depois de encerrado, como as transições de status são registradas, e por quanto tempo o acesso aos dados atuais do candidato permanece disponível após a decisão.

## ADDED Requirements

### Requirement: Coordenador lê os dados atuais do candidato

Enquanto o acesso estiver vigente, o coordenador SHALL ver os dados do candidato tal como estão no perfil no momento da consulta, incluindo a versão de currículo vigente.

O portal SHALL indicar quando os dados do candidato foram atualizados pela última vez, de modo que a ficha não seja tomada como um retrato do momento da inscrição.

#### Scenario: Candidato atualiza dados durante o processo

- **WHEN** um candidato atualiza o telefone depois de ter se inscrito e o coordenador abre a candidatura
- **THEN** o coordenador vê o telefone atualizado

#### Scenario: Data da última atualização visível

- **WHEN** o coordenador abre uma candidatura
- **THEN** o portal informa quando os dados daquele candidato foram atualizados pela última vez

#### Scenario: Currículo substituído aparece para todos os processos

- **WHEN** um candidato inscrito em duas vagas envia uma nova versão de currículo
- **THEN** os coordenadores de ambas as vagas passam a acessar a nova versão

### Requirement: Registro das transições de status

Toda mudança de status de uma candidatura SHALL ser registrada com o status anterior, o novo status, quem a realizou, quando ocorreu e qual versão de currículo estava vigente.

O registro SHALL NOT copiar dados de identidade do candidato.

#### Scenario: Mudança de status registrada

- **WHEN** um coordenador altera o status de uma candidatura
- **THEN** o portal registra a transição com autor, data e hora

#### Scenario: Histórico consultável

- **WHEN** o coordenador abre uma candidatura que passou por várias mudanças de status
- **THEN** o portal apresenta a sequência de transições ocorridas

#### Scenario: Decisão registra o currículo avaliado

- **WHEN** uma candidatura é marcada como aprovada ou reprovada
- **THEN** o registro dessa decisão identifica a versão de currículo vigente naquele momento

### Requirement: Decaimento do acesso ao perfil após o encerramento

O acesso do coordenador aos dados atuais do candidato SHALL cessar quando o processo se encerrar, conforme:

- candidatura **reprovada**: o acesso cessa 90 dias após a decisão;
- candidatura **aprovada**: o acesso permanece, por ser necessário à efetivação da contratação;
- candidatura sem estado terminal em vaga encerrada: o acesso cessa 90 dias após o encerramento da vaga.

O portal SHALL impor essa restrição no servidor, aplicando-a a toda forma de acesso aos dados do candidato — visualização, listagem, busca e download de currículo.

#### Scenario: Reprovada dentro da carência

- **WHEN** o coordenador abre uma candidatura reprovada há menos de 90 dias
- **THEN** ainda tem acesso aos dados atuais do candidato

#### Scenario: Reprovada após a carência

- **WHEN** o coordenador abre uma candidatura reprovada há mais de 90 dias
- **THEN** o portal não apresenta os dados atuais do candidato
- **AND** apresenta o registro do processo

#### Scenario: Aprovada não perde o acesso

- **WHEN** o coordenador abre uma candidatura aprovada há mais de 90 dias
- **THEN** continua com acesso aos dados atuais do candidato

#### Scenario: Candidatura parada em vaga encerrada

- **WHEN** uma candidatura nunca alcançou estado terminal e a vaga foi encerrada há mais de 90 dias
- **THEN** o acesso do coordenador aos dados atuais do candidato cessa

#### Scenario: Download de currículo após o decaimento

- **WHEN** o coordenador tenta baixar o currículo de uma candidatura cujo acesso decaiu
- **THEN** o portal recusa o download

#### Scenario: Listagem não contorna a restrição

- **WHEN** o coordenador lista ou busca candidaturas e alguma delas teve o acesso decaído
- **THEN** os dados atuais do candidato não são apresentados nessa listagem

### Requirement: Registro do processo permanece acessível

Cessado o acesso aos dados atuais do candidato, o coordenador SHALL continuar podendo consultar o registro do processo daquela candidatura: vaga, data de inscrição, status, histórico de transições, dados de entrevista e observações internas.

O portal SHALL deixar claro que os dados pessoais não estão mais disponíveis e por quê.

#### Scenario: Consulta ao registro após o decaimento

- **WHEN** o coordenador abre uma candidatura cujo acesso aos dados pessoais cessou
- **THEN** o portal apresenta o registro do processo
- **AND** informa que os dados pessoais não estão mais acessíveis

### Requirement: Candidatura de conta excluída

Quando o candidato excluir a conta, o coordenador SHALL deixar de ter acesso aos seus dados pessoais e a qualquer versão do currículo, independentemente do status da candidatura ou de prazo.

O registro do processo SHALL permanecer consultável.

#### Scenario: Conta excluída durante processo em andamento

- **WHEN** um candidato exclui a conta enquanto sua candidatura está em análise
- **THEN** o coordenador deixa imediatamente de acessar seus dados pessoais e seu currículo
- **AND** continua vendo o registro do processo, identificado como candidato excluído
